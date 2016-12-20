<?php
/**
 * Test the Logger class
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan;

use Zalora\Punyan\Filter\NoFilter;
use Zalora\Punyan\Writer\NoWriter;
use Zalora\Punyan\Formatter\Bunyan;
use Zalora\Punyan\Filter\DiscoBouncer;

/**
 * @package Zalora\Punyan
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $validStackTrace = [
        0 => [
            'file' => '/private/var/www/shop/vendor/zalora/punyan/src/Logger.php',
            'line' => 140,
            'function' => 'log',
            'class' => 'Zalora\\Punyan\\Logger',
            'type' => '->',
        ],
        1 => [
            'file' => '/private/var/www/shop/vendor/zalora/punyan/src/ZLog.php',
            'line' => 45,
            'function' => 'error',
            'class' => 'Zalora\\Punyan\\Logger',
            'type' => '->',
        ],
        2 => [
            'file' => '/private/var/www/shop/application/modules/cms/controllers/IndexController.php',
            'line' => 27,
            'function' => 'error',
            'class' => 'Zalora\\Punyan\\ZLog',
            'type' => '::',
        ],
        3 => [
            'file' => '/private/var/www/shop/library/Zend/Controller/Action.php',
            'line' => 133,
            'function' => 'init',
            'class' => 'Cms_IndexController',
            'type' => '->',
        ],
        4 => [
            'file' => '/private/var/www/shop/local/Rocket/Controller/Dispatcher/Standard.php',
            'line' => 76,
            'function' => '__construct',
            'class' => 'Zend_Controller_Action',
            'type' => '->',
        ],
        5 => [
            'file' => '/private/var/www/shop/library/Zend/Controller/Front.php',
            'line' => 954,
            'function' => 'dispatch',
            'class' => 'Rocket_Controller_Dispatcher_Standard',
            'type' => '->',
        ],
        6 => [
            'file' => '/private/var/www/shop/library/Zend/Application/Bootstrap/Bootstrap.php',
            'line' => 101,
            'function' => 'dispatch',
            'class' => 'Zend_Controller_Front',
            'type' => '->',
        ],
        7 => [
            'file' => '/private/var/www/shop/library/Zend/Application.php',
            'line' => 366,
            'function' => 'run',
            'class' => 'Zend_Application_Bootstrap_Bootstrap',
            'type' => '->',
        ],
        8 => [
            'file' => '/private/var/www/shop/bob/public/index.php',
            'line' => 77,
            'function' => 'run',
            'class' => 'Zend_Application',
            'type' => '->',
        ],
    ];

    /**
     * Create the most basic version of a logger, appName is mandatory however, so it
     * will throw an InvalidArgumentException
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyLogger()
    {
        new Logger('', []);
    }

    /**
     * Empty writers & filters lead to an empty logger
     */
    public function testLoggerWithEmptyOptions()
    {
        $logger = new Logger('PHPUnit', []);

        $this->assertCount(0, $logger->getWriters());
        $this->assertCount(0, $logger->getFilters());
    }

    /**
     * Test combinations of missing options and empty options
     */
    public function testLoggerWithMissingFilters()
    {
        $logger = new Logger('PHPUnit', [
            'writers' => []
        ]);

        $this->assertCount(0, $logger->getWriters());
        $this->assertCount(0, $logger->getFilters());
    }

    /**
     * Test combinations of missing options and empty options
     */
    public function testLoggerWithMissingWriters()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => []
        ]);

        $this->assertCount(0, $logger->getWriters());
        $this->assertCount(0, $logger->getFilters());
    }

    /**
     * Test combinations of missing options and empty options
     */
    public function testValidEmptyLogger()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => [],
            'writers' => []
        ]);

        $this->assertCount(0, $logger->getWriters());
        $this->assertCount(0, $logger->getFilters());
    }

    /**
     * Test a logger with a config where the configuration is not an array
     * @expectedException \InvalidArgumentException
     */
    public function testLoggerWithInvalidWriterConfiguration()
    {
        new Logger('PHPUnit', [
            'filters' => [],
            'writers' => [
                ['NoWriter' => '']
            ]
        ]);
    }

    /**
     * The NoWriter doesn't have a configuration, so it must be possible to pass an empty array, too
     * The filters array is now added empty automatically if it's not set
     */
    public function testLoggerWithEmptyWriterConfiguration()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => [],
            'writers' => [
                ['NoWriter' => []]
            ]
        ]);

        $this->assertCount(1, $logger->getWriters());
    }

    /**
     * Test add, remove and get writers
     */
    public function testWriterManagement()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => [],
            'writers' => []
        ]);

        $writer = new NoWriter(['filters' => []]);
        $logger->addWriter($writer);

        // After adding one writer we're at one
        $this->assertCount(1, $logger->getWriters());

        // After removing it, it's back to zero
        $logger->removeWriter($writer);
        $this->assertCount(0, $logger->getWriters());
    }

    /**
     * Calling getWriters() on the logger returns a clone rather than the original
     * to not accidentally modify the writers
     */
    public function testGetWritersReturnsClone()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => [],
            'writers' => []
        ]);

        $writer = new NoWriter(['filters' => []]);
        $logger->addWriter($writer);

        // After adding one writer we're at one
        $this->assertCount(1, $logger->getWriters());

        $writers = $logger->getWriters();
        $logger->removeWriter($writer);

        // Because the writers were created before we detached the writer object, the count is still 1
        $this->assertCount(1, $writers);

        // Try again with the original
        $writers = $logger->getWriters(false);

        $logger->removeWriter($writer);

        // As we removed it from the linked collection, it should be empty now
        $this->assertCount(0, $writers);
    }

    /**
     * Test add, remove and get filters
     */
    public function testFilterManagement()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => [],
            'writers' => []
        ]);

        $filter = new NoFilter([]);
        $logger->addFilter($filter);

        // After adding one writer we're at one
        $this->assertCount(1, $logger->getFilters());

        // After removing it, it's back to zero
        $logger->removeFilter($filter);
        $this->assertCount(0, $logger->getFilters());
    }

    /**
     * Calling getWriters() on the logger returns a clone rather than the original
     * to not accidentally modify the writers
     */
    public function testGetFiltersReturnsClone()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => [],
            'writers' => []
        ]);

        $filter = new NoFilter([]);
        $logger->addFilter($filter);

        // After adding one filter we're at one
        $this->assertCount(1, $logger->getFilters());

        $filters = $logger->getFilters();
        $logger->removeFilter($filter);

        // Because the filters were created before we detached the filter object, the count is still 1
        $this->assertCount(1, $filters);

        // Try again with the original
        $filters = $logger->getFilters(false);
        $logger->removeFilter($filter);

        // Same as with the writers above, it should be empty now
        $this->assertCount(0, $filters);
    }

    /**
     * Build a writer internally from configuration
     */
    public function testBuildLoggerWithWriter()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => [],
            'writers' => [
                ['NoWriter' => [
                    'filters' => []
                ]]
            ]
        ]);

        $this->assertCount(1, $logger->getWriters());
    }

    /**
     * Before writers were hard-wired to have the Zalora\Punyan\Writer namespace, now everyone who
     * implements IWriter can use his own writers
     */
    public function testCreateWriterWithFullClassName()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => [],
            'writers' => [
                ['Zalora\Punyan\Writer\NoWriter' => [
                    'filters' => []
                ]]
            ]
        ]);

        $writers = $logger->getWriters();
        $this->assertCount(1, $writers);

        $writers->rewind();
        $this->assertInstanceOf('Zalora\Punyan\Writer\NoWriter', $writers->current());
    }

    /**
     * Only classes which implement IWriter can be added to the logger as writers
     * @expectedException \RuntimeException
     */
    public function testCreateLoggerWhereWriterDoesNotImplementIWriter()
    {
        new Logger('PHPUnit', [
            'filters' => [],
            'writers' => [
                ['\stdClass' => [
                    'filters' => []
                ]]
            ]
        ]);
    }

    /**
     * Configuring a non-existing writer leads to a RuntimeException
     * @expectedException \RuntimeException
     */
    public function testBuildLoggerWithNonExistingWriter()
    {
        new Logger('PHPUnit', [
            'filters' => [],
            'writers' => [
                ['NoHaveLaa' => [
                    'filters' => []
                ]]
            ]
        ]);
    }

    /**
     * To get the class where the log call originated, I use debug_backtrace... It looks rather hacky,
     * but AFAIK there's no other way to get this information. This has to be tested with a made up stack trace
     * because it filters out calls from the current namespace
     */
    public function testGetLogOrigin()
    {
        // Arrays with less than 2 entries cannot be valid backtraces, so an empty array is returned
        $this->assertEmpty(Logger::getLogOrigin(array('Single Entry' => 'Invalid Backtrace')));

        // Function calls are immediately returned
        $functionStackTrace = [
            [
                'file' => '',
                'line' => 1,
                'function' => 'I will be skipped anyway'
            ],

            [
                'file' => '/Users/whuesken/Projects/Punyan/Test.php',
                'line' => 42,
                'function' => 'Zalora\UnitTest\getStuffs'
            ]
        ];

        // Second array item is returned
        $functionResult = Logger::getLogOrigin($functionStackTrace);

        $this->assertInternalType('array', $functionResult);
        $this->assertCount(3, $functionResult);

        $this->assertArrayHasKey('file', $functionResult);
        $this->assertArrayHasKey('line', $functionResult);
        $this->assertArrayHasKey('function', $functionResult);
        $this->assertArrayNotHasKey('class', $functionResult);
        $this->assertArrayNotHasKey('type', $functionResult);

        $this->assertEquals($functionStackTrace[1]['file'], $functionResult['file']);
        $this->assertEquals($functionStackTrace[1]['line'], $functionResult['line']);
        $this->assertEquals($functionStackTrace[1]['function'], $functionResult['function']);

        // Use real backtrace
        $validStackTraceResult = Logger::getLogOrigin($this->validStackTrace);

        $this->assertInternalType('array', $validStackTraceResult);
        $this->assertCount(4, $validStackTraceResult);

        $this->assertArrayHasKey('file', $validStackTraceResult);
        $this->assertArrayHasKey('line', $validStackTraceResult);
        $this->assertArrayHasKey('function', $validStackTraceResult);
        $this->assertArrayHasKey('class', $validStackTraceResult);
        $this->assertArrayNotHasKey('type', $validStackTraceResult);

        $this->assertEquals($this->validStackTrace[2]['file'], $validStackTraceResult['file']);
        $this->assertEquals($this->validStackTrace[2]['line'], $validStackTraceResult['line']);
        $this->assertEquals($this->validStackTrace[3]['function'], $validStackTraceResult['function']);
        $this->assertEquals($this->validStackTrace[3]['class'], $validStackTraceResult['class']);
    }

    /**
     * If you don't have writers the logger starts without problems, but it bails out of log() immediately doing nothing
     */
    public function testLogWithoutWritersOrMute()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => [],
            'writers' => []
        ]);

        $logger->log(50, 'Hallo');
    }

    /**
     * If the logger is muted on top level no writer will do anything
     */
    public function testLogWithWriterAndMute()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => [],
            'writers' => [
                ['Stream' => [
                    'url' => 'php://memory',
                    'filters' => []
                ]]
            ],
            'mute' => true
        ]);

        $logger->log(50, 'Hallo');

        $stream = $this->getCurrentStreamFromLogger($logger);
        fseek($stream, 0);

        $this->assertEmpty(stream_get_contents($stream));
    }

    /**
     * Try to log something with an invalid numeric log level
     * @expectedException \InvalidArgumentException
     */
    public function testLogInvalidNumericLogLevel()
    {
        $logger = new Logger('PHPUnit', [
            'filters' => [],
            'writers' => [
                ['Stream' => [
                    'url' => 'php://memory',
                    'filters' => []
                ]]
            ]
        ]);

        $logger->log(0, 'Hallo');
    }

    /**
     * Use the log method under "normal" conditions
     */
    public function testLog()
    {
        $logger = $this->getMemoryLogger();

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, 'PHPUnit', ['time' => $time], 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        $logger->log(ILogger::LEVEL_WARN, 'PHPUnit', ['time' => $time]);

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );

        // Add a blocking filter and check if it still writes
        $logger->addFilter(new DiscoBouncer([]));
        $logger->log(ILogger::LEVEL_WARN, 'Will be blocked');

        $this->assertEmpty(stream_get_contents($stream, -1, strlen($streamContent)));
    }

    /**
     * Test the trace method
     */
    public function testTrace()
    {
        $logger = $this->getMemoryLogger();

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_TRACE, 'PHPUnit', ['time' => $time], 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        $logger->trace('PHPUnit', ['time' => $time]);

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Test the debug method
     */
    public function testDebug()
    {
        $logger = $this->getMemoryLogger();

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_DEBUG, 'PHPUnit', ['time' => $time], 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        $logger->debug('PHPUnit', ['time' => $time]);

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Test the info method
     */
    public function testInfo()
    {
        $logger = $this->getMemoryLogger();

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_INFO, 'PHPUnit', ['time' => $time], 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        $logger->info('PHPUnit', ['time' => $time]);

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Test the warn method
     */
    public function testWarn()
    {
        $logger = $this->getMemoryLogger();

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_WARN, 'PHPUnit', ['time' => $time], 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        $logger->warn('PHPUnit', ['time' => $time]);

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Test the error method
     */
    public function testError()
    {
        $logger = $this->getMemoryLogger();

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'PHPUnit', ['time' => $time], 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        $logger->error('PHPUnit', ['time' => $time]);

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Test the fatal method
     */
    public function testFatal()
    {
        $logger = $this->getMemoryLogger();

        $time = time();
        $stream = $this->getCurrentStreamFromLogger($logger);
        $formatter = new Bunyan();

        $logEvent = LogEvent::create(ILogger::LEVEL_FATAL, 'PHPUnit', ['time' => $time], 'PHPUnit');
        $logEvent['origin'] = Logger::getLogOrigin(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        $logger->fatal('PHPUnit', ['time' => $time]);

        $streamContent = stream_get_contents($stream, -1, 0);

        $this->assertEquals(
            json_decode($formatter->format($logEvent), true),
            json_decode($streamContent, true)
        );
    }

    /**
     * Two writers where the first doesn't bubble, so the second one should stay dry
     */
    public function testBubbling()
    {
        $config = [
            'filters' => [],
            'writers' => [
                [
                    'Stream' => [
                        'bubble' => false,
                        'url' => 'php://memory',
                        'filters' => []
                    ]
                ],
                [
                    'Stream' => [
                        'url' => 'php://memory',
                        'filters' => []
                    ]
                ]
            ]
        ];

        $logger = new Logger('PHPUnit', $config);
        $logger->info('Hello');

        $writers = $logger->getWriters();
        $this->assertCount(2, $writers);

        $content = [];
        foreach ($writers as $writer) {
            $content[] = stream_get_contents($writer->getStream(), -1, 0);
        }

        $this->assertNotEmpty($content[0]);
        $this->assertEmpty($content[1]);

        $logMessage = json_decode($content[0], true);

        $this->assertEquals('Hello', $logMessage['msg']);
    }

    /**
     * Try all valid levels
     */
    public function testGetValidLevelNamesByLevel()
    {
        $this->assertEquals(Logger::getLevelNameByLevel(10), "trace");
        $this->assertEquals(Logger::getLevelNameByLevel(20), "debug");
        $this->assertEquals(Logger::getLevelNameByLevel(30), "info");
        $this->assertEquals(Logger::getLevelNameByLevel(40), "warn");
        $this->assertEquals(Logger::getLevelNameByLevel(50), "error");
        $this->assertEquals(Logger::getLevelNameByLevel(60), "fatal");
    }

    /**
     * Try all valid levels
     * @expectedException \InvalidArgumentException
     */
    public function testGetInvalidLevelNameByLevel()
    {
        Logger::getLevelNameByLevel(666);
    }

    /**
     * In order to give mask some secret data in a few special classes we need an external exception handler
     */
    public function testExternalExceptionHandler() {
        $exHandler = function(\Throwable $ex) {
            $e = (array) $ex;
            $e['exceptionHandler'] = __METHOD__;

            return $e;
        };

        $logger = new Logger('PHPUnit', ['exceptionHandler' => $exHandler]);
        $this->assertTrue(is_callable($logger->getExceptionHandler()));
    }

    /**
     * Test log level validator
     */
    public function testisLogLevelValid()
    {
        $this->assertTrue(Logger::isValidLogLevel(ILogger::LEVEL_TRACE));
        $this->assertTrue(Logger::isValidLogLevel(ILogger::LEVEL_DEBUG));
        $this->assertTrue(Logger::isValidLogLevel(ILogger::LEVEL_INFO));
        $this->assertTrue(Logger::isValidLogLevel(ILogger::LEVEL_WARN));
        $this->assertTrue(Logger::isValidLogLevel(ILogger::LEVEL_ERROR));
        $this->assertTrue(Logger::isValidLogLevel(ILogger::LEVEL_FATAL));

        $this->assertFalse(Logger::isValidLogLevel(11));
        $this->assertFalse(Logger::isValidLogLevel(23));
        $this->assertFalse(Logger::isValidLogLevel(-10));
        $this->assertFalse(Logger::isValidLogLevel(3423423));
        $this->assertFalse(Logger::isValidLogLevel(-342342));
    }

    /**
     * @return Logger
     */
    private function getMemoryLogger()
    {
        return new Logger('PHPUnit', [
            'filters' => [],
            'writers' => [
                ['Stream' => [
                    'url' => 'php://memory',
                    'filters' => []
                ]]
            ]
        ]);
    }

    /**
     * PHP 7 needs to rewind the SPLObjectStorage before you can extract items with current...
     * @param Logger $logger
     * @return resource
     */
    private function getCurrentStreamFromLogger(Logger $logger)
    {
        $writers = $logger->getWriters();
        $writers->rewind();
        return $writers->current()->getStream();
    }
}

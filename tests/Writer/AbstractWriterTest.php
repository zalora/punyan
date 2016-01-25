<?php
/**
 * Testing Abstract Writer, i.e. filtering, muting, init, etc.
 *
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Writer;

use Zalora\Punyan\Filter\NoFilter;
use Zalora\Punyan\ILogger;
use Zalora\Punyan\LogEvent;
use Zalora\Punyan\Formatter\Bunyan;
use Zalora\Punyan\Processor\NoOp;
use Zalora\Punyan\Processor\Web;

/**
 * @package Zalora\Punyan\Writer
 */
class AbstractWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Muted writers should do nothing
     */
    public function testMutedWriter()
    {
        $config = array(
            'mute' => true,
            'url' => 'php://memory',
            'filters' => array()
        );

        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'Shut up', array(), 'PHPUnit');

        $writer = new Stream($config);
        $writer->log($logEvent);

        $stream = $writer->getStream();
        fseek($stream, 0);

        $this->assertEmpty(stream_get_contents($stream));
    }

    /**
     * Use a writer with a passing filter
     */
    public function testPassFilters()
    {
        $config = array(
            'mute' => false,
            'url' => 'php://memory',
            'filters' => array(
                array('NoFilter' => array())
            )
        );

        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'Hello Streams', array(), 'PHPUnit');
        $formatter = new Bunyan();

        $writer = new Stream($config);
        $writer->log($logEvent);

        $stream = $writer->getStream();
        fseek($stream, 0);

        $this->assertSame($formatter->format($logEvent), stream_get_contents($stream));
    }

    /**
     * Using a writer with a blocking filter
     */
    public function testNoPassFilters()
    {
        $config = array(
            'mute' => false,
            'url' => 'php://memory',
            'filters' => array(
                array('DiscoBouncer' => array())
            )
        );

        $logEvent = LogEvent::create(ILogger::LEVEL_ERROR, 'Shut up', array(), 'PHPUnit');

        $writer = new Stream($config);
        $writer->log($logEvent);

        $stream = $writer->getStream();
        fseek($stream, 0);

        $this->assertEmpty(stream_get_contents($stream));
    }

    /**
     * Add and remove filters from a writer object
     */
    public function testManageFiltersAfterInit()
    {
        $config = array(
            'url' => 'php://memory',
            'filters' => array()
        );

        $writer = new NoWriter($config);
        $filters = $writer->getFilters();

        $this->assertCount(0, $filters);

        $filter = new NoFilter(array());
        $writer->addFilter($filter);

        // As filters is a clone, it must be still zero
        $this->assertCount(0, $filters);
        $this->assertCount(1, $writer->getFilters());

        // Remove the filter and count again
        $writer->removeFilter($filter);
        $this->assertCount(0, $filters);
        $this->assertCount(0, $writer->getFilters());
    }

    /**
     * Add and remove processors after init
     */
    public function testManageProcessorsAfterInit()
    {
        $config = array(
            'url' => 'php://memory',
            'filters' => array()
        );

        $writer = new NoWriter($config);
        $processors = $writer->getProcessors();

        $this->assertCount(0, $processors);

        $processor = new NoOp();
        $writer->addProcessor($processor);

        // Processors is a clone, so it's zero
        $this->assertCount(0, $processors);
        $this->assertCount(1, $writer->getProcessors());

        // Remove the processor and count again
        $writer->removeProcessor($processor);
        $this->assertCount(0, $processors);
        $this->assertCount(0, $writer->getProcessors());
    }

    /**
     * Provide a config array and have filters built
     */
    public function testBuildProcessorsFromConfig() {
        $config = array(
            'url' => 'php://memory',
            'filters' => array(),
            'processors' => array(
                '\\Zalora\Punyan\Processor\NoOp',
                'NoOp'
            )
        );

        $writer = new NoWriter($config);
        $processors = $writer->getProcessors();

        $this->assertCount(2, $processors);

        foreach ($processors as $processor) {
            $this->assertInstanceOf('\\Zalora\\Punyan\\Processor\\IProcessor', $processor);
        }
    }

    /**
     * Use a non-existing class in the configuration
     */
    public function testNonExistingProcessor() {
        $this->setExpectedException('\\RuntimeException');

        $config = array(
            'url' => 'php://memory',
            'filters' => array(),
            'processors' => array(
                'Freiuhcinuw4rt78oyw4578yt674werngcauyfwaursgdufxghsig'
            )
        );

        new NoWriter($config);
    }

    /**
     * Use a processor which doesn't implement IProcessor
     */
    public function testInvalidProcessor()
    {
        $this->setExpectedException('\\RuntimeException');

        $config = array(
            'url' => 'php://memory',
            'filters' => array(),
            'processors' => array(
                '\\stdClass'
            )
        );

        new NoWriter($config);
    }
}

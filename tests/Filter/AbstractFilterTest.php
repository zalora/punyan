<?php
/**
 * Test regular expression filter - And yeah I have no idea about Regexp whatsoever...
 * @author Wolfram Huesken <wolfram.huesken@zalora.com>
 */

namespace Zalora\Punyan\Filter;

use Zalora\Punyan\LogEvent;

/**
 * @package Zalora\Punyan\Filter
 */
class AbstractFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogEvent
     */
    protected $logEvent;

    /**
     * @covers Zalora\Punyan\Filter\AbstractFilter::buildFilters
     */
    public function testEmptyFilterConfig()
    {
        $filters = AbstractFilter::buildFilters(array());

        $this->assertInstanceOf('\\SplObjectStorage', $filters);
        $this->assertCount(0, $filters);
    }

    /**
     * @covers Zalora\Punyan\Filter\AbstractFilter::buildFilters
     */
    public function testInvalidFilterConfig() {
        $this->setExpectedException('\\RuntimeException');
        AbstractFilter::buildFilters(array(1, 2, 3, 'CareBear'));
    }

    /**
     * @covers Zalora\Punyan\Filter\AbstractFilter::buildFilters
     */
    public function testAnotherInvalidFilterConfig() {
        $this->setExpectedException('\\RuntimeException');
        AbstractFilter::buildFilters(array(array('MySuperFilter' => 'No have'), array(3 => 4), array(5 => 'CareBear')));
    }

    /**
     * @covers Zalora\Punyan\Filter\AbstractFilter::buildFilters
     */
    public function testBuildFilters() {
        $config = array(
            array('NoFilter' => array()),
            array('NoFilter' => array())
        );

        $filters = AbstractFilter::buildFilters($config);

        $this->assertInstanceOf('\\SplObjectStorage', $filters);
        $this->assertCount(2, $filters);

        foreach ($filters as $filter) {
            $this->assertInstanceOf('Zalora\Punyan\Filter\NoFilter', $filter);
        }
    }
}

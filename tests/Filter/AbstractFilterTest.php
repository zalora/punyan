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
     * An empty filters config array gives you an empty SplObjectStorage
     */
    public function testEmptyFilterConfig()
    {
        $filters = AbstractFilter::buildFilters([]);

        $this->assertInstanceOf('\\SplObjectStorage', $filters);
        $this->assertCount(0, $filters);
    }

    /**
     * Passing in a wrongly structured filter config throws an InvalidArgumentException
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidFilterConfig()
    {
        AbstractFilter::buildFilters([1, 2, 3, 'CareBear']);
    }

    /**
     * A correctly structured, but still wrong filter config throws a RuntimeException
     * @expectedException \RuntimeException
     */
    public function testAnotherInvalidFilterConfig()
    {
        AbstractFilter::buildFilters([['MySuperFilter' => 'No have'], [3 => 4], [5 => 'CareBear']]);
    }

    /**
     * Normal operating builder
     */
    public function testBuildFilters() {
        $config = [
            ['NoFilter' => []],
            ['NoFilter' => []]
        ];

        $filters = AbstractFilter::buildFilters($config);

        $this->assertInstanceOf('\\SplObjectStorage', $filters);
        $this->assertCount(2, $filters);

        foreach ($filters as $filter) {
            $this->assertInstanceOf('Zalora\Punyan\Filter\NoFilter', $filter);
        }
    }

    /**
     * Include pseudo external filter
     */
    public function testBuildFilterWithFullClassName() {
        $config = [['Zalora\Punyan\Filter\NoFilter' => []]];
        $filters = AbstractFilter::buildFilters($config);

        $this->assertInstanceOf('\\SplObjectStorage', $filters);
        $this->assertCount(1, $filters);

        $filters->rewind();
        $this->assertInstanceOf('Zalora\Punyan\Filter\NoFilter', $filters->current());
    }

    /**
     * Include pseudo external filter
     * @expectedException \RuntimeException
     */
    public function testBuildFiltersWithFilterNotImplementingIFilter() {
        $config = [['\stdClass' => []]];
        AbstractFilter::buildFilters($config);
    }
}

<?php

namespace Lyrixx\Lifestream\Tests;

use Lyrixx\Lifestream\Lifestream;

class LifestreamTest extends \PHPUnit_Framework_TestCase
{
    public function testBootWithFilterDontPass()
    {
        $lifestream = new Lifestream($this->getService(5), array($this->getFilter(5, false), $this->getFilter(0)));
        $lifestream->boot();
        $this->assertCount(0, $lifestream->getStream());
    }

    public function testBootWithFilterPass()
    {
        $lifestream = new Lifestream($this->getService(5), array($this->getFilter(5)), array(), $this->getStream(5));
        $lifestream->boot();
    }

    public function testBootWithFormat()
    {
        $lifestream = new Lifestream($this->getService(5), array(), array($this->getFormatter(5)), $this->getStream(5));
        $lifestream->boot();
    }

    /**
     * @expectedException LogicException
     */
    public function testNotBooted()
    {
        $lifestream = new Lifestream($this->getMock('Lyrixx\Lifestream\Service\ServiceInterface'));
        $this->assertEquals(array(), $lifestream->getStream());

    }

    /**
     * @expectedException LogicException
     */
    public function testServiceNotDefined()
    {
        $lifestream = new Lifestream();
        $lifestream->boot();
    }

    public function testServiceDefined()
    {
        $lifestream = new Lifestream();
        $service = $this->getService(1);

        $lifestream->setService($service);
        $this->assertSame($service, $lifestream->getService());

        $lifestream->boot();
    }

    public function testGetStream()
    {
        $lifestream = new Lifestream($this->getService(30), array(), array());
        $lifestream->boot();
        $this->assertCount(30, $lifestream->getStream(null));
    }

    private function getService($nbStatus)
    {
        $status = $this->getMock('Lyrixx\Lifestream\StatusInterface');

        $service = $this->getMock('Lyrixx\Lifestream\Service\ServiceInterface');
        $service
            ->expects($this->exactly(1))
            ->method('getStatuses')
            ->will($this->returnValue(array_fill(0, $nbStatus, $status)))
        ;

        return $service;
    }

    private function getFilter($nbCall, $returnValue = true)
    {
        $filter = $this->getMock('Lyrixx\Lifestream\Filter\FilterInterface');
        $filter
            ->expects($this->exactly($nbCall))
            ->method('isValid')
            ->will($this->returnValue($returnValue))
        ;

        return $filter;
    }

    private function getFormatter($nbCall, $returnValue = true)
    {
        $filter = $this->getMock('Lyrixx\Lifestream\Formatter\FormatterInterface');
        $filter
            ->expects($this->exactly($nbCall))
            ->method('format')
            ->will($this->returnArgument(0))
        ;

        return $filter;
    }

    private function getStream($nbCall, $callGetStream = false)
    {
        $stream = $this->getMock('Lyrixx\Lifestream\StreamInterface');
        $stream
            ->expects($this->exactly($nbCall))
            ->method('addStatus')
        ;

        if ($callGetStream) {
            $stream
                ->expects($this->exactly(1))
                ->method('getStream')
                ->will($this->returnValue($nbCall))
            ;

        }

        return $stream;
    }

}

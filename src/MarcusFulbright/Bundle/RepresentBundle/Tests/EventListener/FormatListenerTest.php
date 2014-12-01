<?php

namespace MarcusFulbright\RepresentBundle\Tests\EventListener;

use MarcusFulbright\Bundle\RepresentBundle\EventListener\FormatListener;
use MarcusFulbright\Bundle\RepresentBundle\Negotiator\FormatNegotiator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class FormatListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnKernelControllerNegotiation()
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $request = new Request();
        $event->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $formatNegotiator = $this->getMockBuilder('MarcusFulbright\Bundle\RepresentBundle\Negotiator\FormatNegotiator')
            ->disableOriginalConstructor()
            ->getMock();
        $formatNegotiator->expects($this->once())
            ->method('getBestMediaType')
            ->will($this->returnValue('application/xml'));
        $listener = new FormatListener($formatNegotiator);
        $listener->onKernelRequest($event);
        $this->assertEquals($request->getRequestFormat(), 'xml');
    }

    public function testOnKernelControllerNegotiationStopped()
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $request = new Request();
        $request->setRequestFormat('xml');
        $event->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $formatNegotiator = new FormatNegotiator();
        $formatNegotiator->add(new RequestMatcher('/'), array('stop' => true));
        $formatNegotiator->add(new RequestMatcher('/'), array('fallback_format' => 'json'));
        $listener = new FormatListener($formatNegotiator);
        $listener->onKernelRequest($event);
        $this->assertEquals($request->getRequestFormat(), 'xml');
    }

    /**
     * @dataProvider useSpecifiedFormatDataProvider
     */
    public function testUseSpecifiedFormat($format, $result)
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $request = new Request();
        if ($format) {
            $request->setRequestFormat($format);
        }
        $event->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $formatNegotiator = $this->getMockBuilder('MarcusFulbright\Bundle\RepresentBundle\Negotiator\FormatNegotiator')
            ->disableOriginalConstructor()
            ->getMock();
        $formatNegotiator->expects($this->any())
            ->method('getBestMediaType')
            ->will($this->returnValue('application/xml'));
        $listener = new FormatListener($formatNegotiator);
        $listener->onKernelRequest($event);
        $this->assertEquals($request->getRequestFormat(), $result);
    }
    public function useSpecifiedFormatDataProvider()
    {
        return array(
            array(null, 'xml'),
            array('json', 'json'),
        );
    }

    public function testSfFragmentFormat()
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $request = new Request();
        $attributes = array ( '_locale' => 'en', '_format' => 'json', '_controller' => 'FooBundle:Index:featured', );
        $request->attributes->add($attributes);
        $request->attributes->set('_route_params', array_replace($request->attributes->get('_route_params', array()), $attributes));
        $event->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $event->expects($this->any())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::MASTER_REQUEST));
        $formatNegotiator = $this->getMockBuilder('MarcusFulbright\Bundle\RepresentBundle\Negotiator\FormatNegotiator')
            ->disableOriginalConstructor()
            ->getMock();
        $formatNegotiator->expects($this->any())
            ->method('getBestMediaType')
            ->will($this->returnValue('application/json'));
        $listener = new FormatListener($formatNegotiator);
        $listener->onKernelRequest($event);
        $this->assertEquals($request->getRequestFormat(), 'json');
    }
}
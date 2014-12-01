<?php

namespace MarcusFulbright\RepresentBundle\Tests\EventListener;

use MarcusFulbright\Bundle\RepresentBundle\EventListener\MimeTypeListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class mimeTypeListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnKernelRequest()
    {
        $formatNegotiator = $this->getMockBuilder('MarcusFulbright\Bundle\RepresentBundle\Negotiator\FormatNegotiator')
            ->disableOriginalConstructor()->getMock();
        $formatNegotiator->expects($this->any())
            ->method('registerFormat')
            ->with('jsonp', array('application/javascript+jsonp'), true)
            ->will($this->returnValue(null));
        $listener = new MimeTypeListener(array('jsonp' => array('application/javascript+jsonp')), $formatNegotiator);
        $request = new Request();
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()->getMock();
        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $this->assertNull($request->getMimeType('jsonp'));
        $listener->onKernelRequest($event);
        $this->assertNull($request->getMimeType('jsonp'));
        $event->expects($this->once())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::MASTER_REQUEST));
        $listener->onKernelRequest($event);
        $this->assertEquals('application/javascript+jsonp', $request->getMimeType('jsonp'));
    }
}
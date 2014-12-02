<?php

namespace Mbright\Bundle\RepresentBundle\Tests\EventListener;

use Mbright\Bundle\RepresentBundle\EventListener\ViewResponseListener;

class ViewResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnKernelView()
    {
        $view       = $this->getRepresentViewMock();
        $event      = $this->getResponseForControllerResultEventMock();
        $request    = $this->getRequestMock();
        $container  = $this->getContainerInterfaceMock();
        $serializer = $this->getSerializerMock();
        $data       = 'data';
        $format     = 'json';
        $context    = null;
        $statusCode = 201;

        $event->shouldReceive('getRequest')->andReturn($request);
        $event->shouldReceive('getControllerResult')->andReturn($view);
        $request->shouldReceive('getRequestFormat')->andReturn($format);
        $container->shouldReceive('get')->with('represent.serializer')->andReturn($serializer);
        $serializer->shouldReceive('supports')->with($format)->andReturn(true);
        $serializer->shouldReceive('serialize')->with($data, $format, $context);
        $view->shouldReceive('getData')->andReturn($data);
        $view->shouldReceive('getFormat')->andReturn($format);
        $view->shouldReceive('getContext')->andReturn($context);
        $view->shouldReceive('getStatusCode')->andReturn($statusCode);
        $view->shouldReceive('hasData')->andReturn(true);
        $view->shouldReceive('getHeaders')->andReturn(array());

        $listener = new ViewResponseListener($container);
        $result   = $listener->onKernelView($event, false);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertEquals($format, $result->headers->get('Content-Type'));
        $this->assertEquals($statusCode, $result->getStatusCode());
        $this->assertEquals($data, $result->getContent());
    }

    public function getContainerInterfaceMock()
    {
        return \Mockery::mock('Symfony\Component\DependencyInjection\ContainerInterface');
    }

    public function getResponseForControllerResultEventMock()
    {
        return \Mockery::mock('Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent');
    }

    public function getRequestMock()
    {
        return \Mockery::mock('Symfony\Component\HttpFoundation\Request');
    }

    public function getRepresentViewMock()
    {
        return \Mockery::mock('Mbright\Bundle\RepresentBundle\Entity\RepresentView');
    }

    public function getSerializerMock()
    {
        return \Mockery::mock('Represent\Serializer\MasterSerializer');
    }
}

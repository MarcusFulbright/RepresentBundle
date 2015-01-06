<?php

namespace Mbright\Bundle\RepresentBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class RepresentExceptionListener
{
    private $debug;

    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $response  = new Response();


        switch (true):
            case $this->debug:
                $message = '{"code":500,"message":"'. $exception->getMessage().'"}';
                break;
            case $exception instanceof HttpExceptionInterface && $exception instanceof \RuntimeException:

                $message = sprintf(
                    '{"code": %s,"message":"%s"}',
                    $exception->getStatusCode(),
                    $exception->getMessage()
                );

                $code = $exception->getStatusCode();

                if (count($exception->getHeaders()) > 0){
                    $response->headers->replace($exception->getHeaders());
                };
                break;
            default:
                $message = '{"code":500,"message":"The server has encountered an error"}';
                $code    = 500;
        endswitch;

        $response->setContent($message);
        $response->setStatusCode($code);
        $event->setResponse($response);
    }
}
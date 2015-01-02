<?php

namespace Mbright\Bundle\RepresentBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class RepresentExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $response  = new Response();

        if ($exception instanceof HttpExceptionInterface && $exception instanceof \RuntimeException) {

            $message = sprintf(
                'Code: %s Message: %s',
                $exception->getStatusCode(),
                $exception->getMessage()
            );

            $code = $exception->getStatusCode();

            if (count($exception->getHeaders()) > 0){
                $response->headers->replace($exception->getHeaders());
            };

        } else {
            $message = 'Code: 500 Message: The server has encountered an error';
            $code    = 500;
        }

        $response->setContent($message);
        $response->setStatusCode($code);
        $event->setResponse($response);
    }
}
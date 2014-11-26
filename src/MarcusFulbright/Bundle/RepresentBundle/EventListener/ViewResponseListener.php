<?php

namespace MarcusFulbright\RepresentBundle\EventListener;

use MarcusFulbright\Bundle\RepresentBundle\Entity\RepresentView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ViewResponseListener
{
    private $container;

    private $formats;

    public function __construct(ContainerInterface $container, $formats)
    {
        $this->container = $container;
        $this->formats   = $formats;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $format  = $request->getRequestFormat();
        $view    = $event->getControllerResult();

        if (!$view instanceof RepresentView) {

           $view = new RepresentView($view);
        }

        if (!isset($this->formats[$format])) {
            throw new \Exception('Format: '.$format.' Not supported');
        }

        $serializer = $this->container->get('represent.serializer');

        if ($serializer->supports($view->getFormat())) {
            $this->container->get('represent.serializer')->serialize($view->getData(), $view->getFormat(), $view->getContext());
        }

        $response =  new Response($view->getData(), $view->getStatusCode(), $view->getHeaders());
        $response->headers->set('Content-Type', $view->getFormat());
        $response->prepare($request);

        return $response;
    }
}
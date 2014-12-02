<?php

namespace Mbright\Bundle\RepresentBundle\EventListener;

use Mbright\Bundle\RepresentBundle\Entity\RepresentView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ViewResponseListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $formats;

    /**
     * @param ContainerInterface $container
     * @param array              $formats
     */
    public function __construct(ContainerInterface $container, array $formats)
    {
        $this->container = $container;
        $this->formats   = $formats;
    }

    /**
     * Handles taking turning a view object into a response, also transforms data return from a controller into a view object
     *
     * @param GetResponseForControllerResultEvent $event
     * @param bool                                $prepare
     * @throws \Exception
     * @return Response
     */
    public function onKernelView(GetResponseForControllerResultEvent $event, $prepare = true)
    {
        $request = $event->getRequest();
        $format  = $request->getRequestFormat();
        $view    = $event->getControllerResult();

        if (!$view instanceof RepresentView) {
           $view = new RepresentView($view);
        }

        if (!in_array($format, $this->formats)) {
            throw new \Exception('Format: '.$format.' not supported');
        }

        $serializer = $this->container->get('represent.serializer');

        if ($serializer->supports($format) && $view->hasData()) {
            $serializer->serialize($view->getData(), $view->getFormat(), $view->getContext());
        }

        $response =  new Response($view->getData(), $view->getStatusCode(), $view->getHeaders());
        $response->headers->set('Content-Type', $view->getFormat());

        !$prepare ?: $response->prepare($request);

        return $response;
    }
}
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
    private $formatMap;

    /**
     * @var string
     */
    private $defaultFormat;

    /**
     * @param ContainerInterface $container
     * @param                    $formatMap
     * @param string             $defaultFormat
     */
    public function __construct(ContainerInterface $container, $formatMap, $defaultFormat = 'json')
    {
        $this->container    = $container;
        $this->defaultFormat = $defaultFormat;
        $this->formatMap    = $formatMap;
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

        if ($view->getFormat() == null) {
            $view->setFormat($format);
        }

        $serializer = $this->container->get($this->formatMap[$format]);

        if ($serializer->supports($format) && $view->hasData()) {
            $view->setData($serializer->serialize($view->getData(), $view->getFormat(), $view->getContext()));
        }

        $response =  new Response($view->getData(), $view->getStatusCode(), $view->getHeaders());
        $response->headers->set('Content-Type', $view->getFormat());

        !$prepare ?: $response->prepare($request);

        $event->setResponse($response);
    }
}

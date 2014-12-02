<?php

namespace Mbright\Bundle\RepresentBundle\EventListener;

/**
 * Thrown to stop the format negotiator from continuing so that no format is set
 */
class StopFormatListenerException extends \Exception
{
}
<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Changer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * ThemeChangerListener writes to response theme changes
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ThemeChangerListener implements EventSubscriberInterface
{
    /**
     * @var ThemeChangerInterface
     */
    private $changer;

    /**
     * Constructor
     *
     * @param ThemeChangerInterface $changer A theme changer
     */
    public function __construct(ThemeChangerInterface $changer)
    {
        $this->changer = $changer;
    }

    /**
     * Writes theme changes
     *
     * @param FilterResponseEvent $event An event
     *
     * @return void
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->isMasterRequest()) {
            return;
        }

        $this->changer->writeResponse($event->getRequest(), $event->getResponse());
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\EventDispatcher\EventSubscriberInterface::getSubscribedEvents()
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => 'onKernelResponse'
        );
    }
}
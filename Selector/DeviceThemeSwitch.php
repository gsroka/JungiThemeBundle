<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Selector;

use Jungi\ThemeBundle\Tag;
use Jungi\ThemeBundle\Tag\Core\TagFactory;
use Jungi\ThemeBundle\Selector\Event\ResolvedThemeEvent;
use Jungi\ThemeBundle\Core\MobileDetect;

/**
 * The class is designed for matching themes for appropriate device
 * that invoked the request
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class DeviceThemeSwitch
{
	/**
	 * @var MobileDetect
	 */
	private $detect;

	/**
	 * Constructor
	 *
	 * @param MobileDetect $mobileDetect A mobile detect instance
	 */
	public function __construct(MobileDetect $mobileDetect)
	{
		$this->detect = $mobileDetect;
	}

	/**
	 * Handles the ResolvedThemeEvent event
	 *
	 * @param ResolvedThemeEvent $event An event
	 *
	 * @return void
	 */
	public function onResolvedTheme(ResolvedThemeEvent $event)
	{
	    // A theme from the event
	    $theme = $event->getTheme();

	    // Only the representative themes will be handled
	    // so the themes which does not have a link tag
	    if ($theme->getTags()->has(Tag\Link::getType())) {
            return;
	    }

	    // Handle a request from the event
	    $this->detect->handleRequest($event->getRequest());

	    // If none of devices had not match, stop
	    $istablet = $this->detect->isTablet();
		if ($this->detect->isMobile()) { // Is a mobile or tablet device?
		    $tag = new Tag\MobileDevices(
		        $this->detect->detectOS(),
		        $istablet ? Tag\MobileDevices::TABLET : Tag\MobileDevices::MOBILE
		    );
		} else {
		    $tag = new Tag\DesktopDevices();
		}

		// Do nothing if a obtained theme has this tag
		if ($theme->getTags()->contains($tag)) {
		    return;
		}

		// Look for a substitute theme
		$substituteTheme = $event->getThemeManager()->getThemesWithTags(array(
			new Tag\Link($theme->getName()),
		    $tag
		), true);

		// Sets a new theme if found
		if (null !== $substituteTheme) {
		    $event->setTheme($substituteTheme);
		}
	}
}
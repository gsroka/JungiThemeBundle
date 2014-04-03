<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Jungi\ThemeBundle\Core\ThemeManagerInterface;
use Symfony\Component\EventDispatcher\Event;
use Jungi\ThemeBundle\Core\ThemeInterface;

/**
 * ThemeEvent is a common theme event
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ThemeEvent extends Event
{
	/**
	 * @var ThemeManagerInterface
	 */
	protected $manager;

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var ThemeInterface
	 */
	protected $theme;

	/**
	 * Constructor
	 *
	 * @param ThemeInterface 		$theme 	 A theme
	 * @param ThemeManagerInterface $manager A theme manager
	 * @param Request				$request A request object
	 */
	public function __construct(ThemeInterface $theme, ThemeManagerInterface $manager, Request $request)
	{
		$this->theme = $theme;
		$this->manager = $manager;
		$this->request = $request;
	}

	/**
	 * Returns the theme manager
	 *
	 * @return ThemeManagerInterface
	 */
	public function getThemeManager()
	{
		return $this->manager;
	}

	/**
	 * Returns the request object
	 *
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Returns a theme
	 *
	 * @return ThemeInterface
	 */
	public function getTheme()
	{
	    return $this->theme;
	}
}
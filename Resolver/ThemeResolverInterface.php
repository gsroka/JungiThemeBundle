<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Resolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The implemented classes returns an appropriate theme name
 * based on a given Request instance
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
interface ThemeResolverInterface
{
	/**
	 * Returns the appropriate theme name for the current request
	 *
	 * @param Request $request A request instance
	 *
	 * @return string|null Returns null if a theme name is not set
	 */
	public function resolveThemeName(Request $request);

	/**
	 * Sets a theme for a given request
	 *
	 * @param string  $themeName A theme name
	 * @param Request $request   A request instance
	 *
	 * @return void
	 */
	public function setThemeName($themeName, Request $request);
}
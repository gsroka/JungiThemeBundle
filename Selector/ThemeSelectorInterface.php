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

use Symfony\Component\HttpFoundation\Request;

/**
 * ThemeSelectorInterface determines which theme will be used for a given request
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
interface ThemeSelectorInterface
{
	/**
	 * Sets the appropriate theme for a given Request
	 *
	 * @param Request $request A request instance
	 *
	 * @return void
	 */
	public function select(Request $request);
}
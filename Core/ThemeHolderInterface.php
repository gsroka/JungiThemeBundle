<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Core;

/**
 * ThemeHolderInterface instances holds the current theme for a request scope
 * 
 * @author Piotr Kugla <piku235@gmail.com>
 */
interface ThemeHolderInterface 
{
	/**
	 * Returns the current theme 
	 * 
	 * @return ThemeInterface|null Null if the theme was not set
	 */
	public function getTheme();
	
	/**
	 * Sets the current theme
	 * 
	 * @param  ThemeInterface $theme A theme
	 * @return void
	 */
	public function setTheme(ThemeInterface $theme);
}
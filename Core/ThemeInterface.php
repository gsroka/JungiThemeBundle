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
 * The basic interface for each new theme instance
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
interface ThemeInterface
{
	/**
	 * Returns a theme name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Returns a theme tag collection
	 *
	 * @return \Jungi\ThemeBundle\Tag\Core\TagCollection
	 */
	public function getTags();

	/**
	 * Returns the path to a theme
	 *
	 * @return string
	 */
	public function getPath();

	/**
	 * Returns a details of a theme
	 *
	 * @return DetailsInterface
	 */
	public function getDetails();
}
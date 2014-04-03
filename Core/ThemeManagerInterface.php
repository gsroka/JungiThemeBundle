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

use Jungi\ThemeBundle\Exception\ThemeNotFoundException;
use Jungi\ThemeBundle\Tag\TagInterface;

/**
 * ThemeManagerInterface instances manages
 * the all themes in the system
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
interface ThemeManagerInterface
{
	/**
	 * Adds a new theme
	 *
	 * @param  ThemeInterface $theme A theme
	 *
	 * @return void
	 */
	public function addTheme(ThemeInterface $theme);

	/**
	 * Checks if a given theme name exists
	 *
	 * @param  string $name A theme name
	 *
	 * @return bool
	 */
	public function hasTheme($name);

	/**
	 * Returns a theme by the name
	 *
	 * @param  string $name A theme name
	 *
	 * @return ThemeInterface
	 *
	 * @throws ThemeNotFoundException
	 */
	public function getTheme($name);

	/**
	 * Returns all themes
	 *
	 * @return ThemeInterface[]
	 */
	public function getThemes();

	/**
	 * Returns all themes which have got the given tags
	 *
	 * @param  TagInterface[]|TagInterface $tags  A one tag or tags
	 * @param  bool                        $first Return a first matched theme? (optional)
	 *
	 * @return ThemeInterface[]
	 */
	public function getThemesWithTags($tags, $first = false);
}
<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tag;

/**
 * Tag simply facilitate matching themes
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
interface TagInterface
{
	/**
	 * Checks if a given tag is equal
	 *
	 * @param  TagInterface $tag A tag
	 * @return bool
	 */
	public function isEqual(TagInterface $tag);

	/**
	 * Gets the tag type
	 *
	 * @return string
	 */
	public static function getType();
}
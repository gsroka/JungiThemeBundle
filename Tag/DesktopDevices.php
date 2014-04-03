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
 * DesktopDevices is a standard tag which identifies
 * themes for desktop devices
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class DesktopDevices implements TagInterface
{
	/**
	 * (non-PHPdoc)
	 * @see \Jungi\ThemeBundle\Tag\TagInterface::isEqual()
	 */
	public function isEqual(TagInterface $tag)
	{
		return $tag instanceof static;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Jungi\ThemeBundle\Tag\TagInterface::getType()
	 */
	public static function getType()
	{
		return 'desktop_devices';
	}
}
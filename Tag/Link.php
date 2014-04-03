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
 * Link tag links multiple themes to behave like a one theme
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class Link implements TagInterface
{
	/**
	 * @var string
	 */
	protected $theme;

	/**
	 * Constructor
	 *
	 * @param string $theme A theme name
	 */
	public function __construct($theme)
	{
		$this->theme = $theme;
	}

	/**
	 * Returns the theme name
	 *
	 * @return string
	 */
	public function getTheme()
	{
		return $this->theme;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Jungi\ThemeBundle\Tag\TagInterface::isEqual()
	 */
	public function isEqual(TagInterface $tag)
	{
		return $tag == $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Jungi\ThemeBundle\Tag\TagInterface::getType()
	 */
	public static function getType()
	{
		return 'link';
	}
}
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
 * MobileDevices tag identifies themes designed for
 * mobile devices
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class MobileDevices implements TagInterface
{
	/**
	 * @var string
	 */
	const MOBILE = 1;

	/**
	 * @var string
	 */
	const TABLET = 2;

	/**
	 * @var string
	 */
	const ALL_DEVICES = 3;

	/**
	 * @var int
	 */
	protected $deviceType;

	/**
	 * @var array
	 */
	protected $systems;

	/**
	 * Constructor
	 *
	 * @param string|array $systems    Operating systems (optional)
	 *  Operating systems should be the same as in the MobileDetect class
	 * @param int          $deviceType A constant device type (optional)
	 */
	public function __construct($systems = array(), $deviceType = self::ALL_DEVICES)
	{
	    $this->deviceType = $deviceType;
		$this->systems = (array) $systems;
	}

	/**
	 * Returns the device type
	 *
	 * @return int
	 */
	public function getDeviceType()
	{
		return $this->deviceType;
	}

	/**
	 * Returns the operating systems
	 *
	 * @return array
	 */
	public function getSystems()
	{
	    return $this->systems;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Jungi\ThemeBundle\Tag\TagInterface::isEqual()
	 */
	public function isEqual(TagInterface $tag)
	{
		return $tag instanceof static && $tag->deviceType & $this->deviceType && ((!$this->systems || !$tag->systems) || array_intersect($this->systems, $tag->systems));
	}

	/**
	 * (non-PHPdoc)
	 * @see \Jungi\ThemeBundle\Tag\Tag::getType()
	 */
	public static function getType()
	{
		return 'mobile_devices';
	}
}
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
 * Details
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class Details implements DetailsInterface
{
	/**
	 * @var string
	 */
	protected $author;

	/**
	 * @var string
	 */
	protected $authorMail;

	/**
	 * @var string
	 */
	protected $authorSite;

	/**
	 * @var string
	 */
	protected $version;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $license;

	/**
	 * Constructor
	 *
	 * @param stirng $name 		  A name
	 * @param string $version	  A version
	 * @param string $description A description
	 * @param string $license	  A license type
	 * @param string $author	  An author name
	 * @param string $authorMail  An author mail
	 * @param string $authorSite  An author site (optional)
	 */
	public function __construct($name, $version, $description = null, $license = null, $author = null, $authorMail = null, $authorSite = null)
	{
		$this->name = $name;
		$this->version = $version;
		$this->description = $description;
		$this->license = $license;
		$this->author = $author;
		$this->authorMail = $authorMail;
		$this->authorSite = $authorSite;
	}

	/**
	 * Returns a name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns an author
	 *
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * Returns an author mail
	 *
	 * @return string
	 */
	public function getAuthorMail()
	{
		return $this->authorMail;
	}

	/**
	 * Returns an author site
	 *
	 * @return string
	 */
	public function getAuthorSite()
	{
		return $this->authorSite;
	}

	/**
	 * Returns a version
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Returns a description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Returns a type of license
	 *
	 * @return string
	 */
	public function getLicense()
	{
		return $this->license;
	}

	/**
	 * Represents the details object
	 *
	 * @return string
	 */
	public function __toString()
	{
		return sprintf('%s, %s (%s)', $this->name, $this->author, $this->authorMail);
	}
}
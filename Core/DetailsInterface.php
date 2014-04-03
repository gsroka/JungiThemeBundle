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
 * DetailsInterface
 * 
 * @author Piotr Kugla <piku235@gmail.com>
 */
interface DetailsInterface
{
	/**
	 * Returns a name
	 *
	 * @return string
	 */
	public function getName();
	
	/**
	 * Returns an author
	 *
	 * @return string
	 */
	public function getAuthor();
	
	/**
	 * Returns an author mail
	 *
	 * @return string
	 */
	public function getAuthorMail();
	
	/**
	 * Returns an author site
	 *
	 * @return string
	 */
	public function getAuthorSite();
	
	/**
	 * Returns a version
	 *
	 * @return string
	 */
	public function getVersion();
	
	/**
	 * Returns a description
	 *
	 * @return string
	 */
	public function getDescription();
	
	/**
	 * Returns a type of license
	 *
	 * @return string
	 */
	public function getLicense();
}
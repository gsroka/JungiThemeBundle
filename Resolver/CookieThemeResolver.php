<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Resolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * The class handles reading/writing theme names using the cookies
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class CookieThemeResolver implements ThemeResolverInterface, ResponseWriterInterface
{
	/**
	 * The cookie name
	 */
	const COOKIE_NAME = '_jungi_theme';

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * Constructor
	 *
	 * @param array $options Options for storing the cookie (optional)
	 */
	public function __construct(array $options = array())
	{
	    $this->options = $options + array(
	        'expire' => time() + 2592000, // +30 days
            'path' => '/',
	        'domain' => null,
	        'secure' => false,
	        'httpOnly' => true
	    );
	}

	/**
	 * (non-PHPdoc)
	 * @see \Jungi\ThemeBundle\Resolver\ThemeResolverInterface::resolve()
	 */
	public function resolveThemeName(Request $request)
	{
		return $request->cookies->get(self::COOKIE_NAME);
	}

	/**
	 * (non-PHPdoc)
	 * @see \Jungi\ThemeBundle\Resolver\ThemeResolverInterface::setTheme()
	 */
	public function setThemeName($themeName, Request $request)
	{
		$request->cookies->set(self::COOKIE_NAME, $themeName);
	}

	/**
	 * Writes the theme changes to a given response
	 *
	 * @param string   $themeName An actual theme name
	 * @param Response $response  A response instance
	 *
	 * @return void
	 */
	public function writeResponse($themeName, Response $response)
	{
	    $response->headers->setCookie(new Cookie(
	        self::COOKIE_NAME,
	        $themeName,
	        $this->options['expire'],
	        $this->options['path'],
	        $this->options['domain'],
	        $this->options['secure'],
	        $this->options['httpOnly']
	    ));
	}
}


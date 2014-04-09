<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tests\Resolver;

use Jungi\ThemeBundle\Tests\TestCase;
use Jungi\ThemeBundle\Resolver\CookieThemeResolver;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * CookieThemeResolver Test Case
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class CookieThemeResolverTest extends TestCase
{
    /**
     * @var CookieThemeResolver
     */
    private $resolver;

    /**
     * @var array
     */
    private $options;

    /**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
        $this->options = array(
            'lifetime' => 86400, // +24h
            'path' => '/foo',
            'domain' => 'fooweb.com',
            'secure' => true,
            'httpOnly' => false
        );
		$this->resolver = new CookieThemeResolver($this->options);
	}

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		$this->resolver = null;
	}

	/**
	 * Tests resolve theme name method
	 */
	public function testResolveThemeName()
	{
	    $desktopReq = $this->createDesktopRequest();
	    $helpReq = $this->createMobileRequest();
	    $this->resolver->setThemeName('footheme', $desktopReq);

	    $this->assertEquals('footheme', $this->resolver->resolveThemeName($desktopReq));
	    $this->assertNull($this->resolver->resolveThemeName($helpReq));
	}

    /**
     * Tests writes to a response
     */
    public function testWriteResponse()
    {
        $response = new Response();
        $this->resolver->writeResponse('footheme', $response);

        $cookies = $response->headers->getCookies();
        $this->assertContains(new Cookie(
            CookieThemeResolver::COOKIE_NAME,
            'footheme',
            time() + $this->options['lifetime'],
            $this->options['path'],
            $this->options['domain'],
            $this->options['secure'],
            $this->options['httpOnly']
        ), $cookies, '', false, false);
    }
}
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
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$this->resolver = new CookieThemeResolver();
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
}
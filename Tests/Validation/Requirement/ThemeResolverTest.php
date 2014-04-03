<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tests\Validation\Requirement;

use Jungi\ThemeBundle\Tests\TestCase;
use Jungi\ThemeBundle\Validation\Requirement\ThemeResolver;
use Jungi\ThemeBundle\Resolver\SessionThemeResolver;
use Jungi\ThemeBundle\Core\ThemeInterface;
use Symfony\Component\HttpFoundation\Request;
use Jungi\ThemeBundle\Resolver\CookieThemeResolver;
use Jungi\ThemeBundle\Resolver\InMemoryThemeResolver;

/**
 * ThemeResolver Test Case
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ThemeResolverTest extends TestCase
{
    /**
     * @var ThemeInterface
     */
    protected $theme;

    /**
     * @var Request
     */
    protected $request;

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
	    $this->theme = $this->getMock('Jungi\ThemeBundle\Core\ThemeInterface');
	    $this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
	}

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		$this->request = null;
		$this->theme = null;
	}

	/**
	 * @dataProvider getThemeResolvers
	 */
    public function testOnFalseCondition($themeResolver)
    {
        $requirement = new ThemeResolver($themeResolver);
        $requirement->add('Jungi\ThemeBundle\Resolver\SessionThemeResolver');
        $requirement->add(new InMemoryThemeResolver());

        $this->assertFalse($requirement->canValidate($this->theme, $this->request));
    }

    /**
     * @dataProvider getThemeResolvers
     */
    public function testOnTrueCondition($themeResolver)
    {
        $requirement = new ThemeResolver($themeResolver);
        $requirement->add('Jungi\ThemeBundle\Resolver\CookieThemeResolver');

        $this->assertTrue($requirement->canValidate($this->theme, $this->request));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getThemeResolvers()
    {
        return array(
            array(new SessionThemeResolver()),
            array(new InMemoryThemeResolver())
        );
    }
}
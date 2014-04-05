<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tests;

use Jungi\ThemeBundle\Changer\StandardThemeChanger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Jungi\ThemeBundle\Core\SimpleThemeHolder;
use Jungi\ThemeBundle\Core\ThemeManager;
use Symfony\Component\Validator\Validator;
use Jungi\ThemeBundle\Tests\Fixtures\Validation\FakeMetadataFactory;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\DefaultTranslator;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Jungi\ThemeBundle\Tests\Fixtures\Validation\Constraints\FakeClassConstraint;
use Jungi\ThemeBundle\Tests\Fixtures\Resolver\FakeThemeResolver;
use Symfony\Component\HttpFoundation\Response;

/**
 * StandardThemeChanger Test Case
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class StandardThemeChangerTest extends TestCase
{
    /**
     * @var StandardThemeChanger
     */
    private $changer;

    /**
     * @var SimpleThemeHolder
     */
    private $holder;

    /**
     * @var ThemeManager
     */
    private $manager;

    /**
     * @var FakeThemeResolver
     */
    private $resolver;

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
	    $this->resolver = new FakeThemeResolver('bootheme', false);
	    $this->holder = new SimpleThemeHolder();
	    $this->manager = new ThemeManager(array(
	    	$this->createThemeMock('footheme'),
	        $this->createThemeMock('bootheme')
	    ));
		$this->changer = new StandardThemeChanger($this->manager, $this->holder, $this->resolver, new EventDispatcher());
	}

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		$this->changer = null;
		$this->manager = null;
		$this->holder = null;
		$this->resolver = null;
	}

	/**
	 * Tests change
	 *
	 * @dataProvider getThemesForChange
	 */
	public function testChange($theme)
	{
        $request = $this->createDesktopRequest();
        $this->changer->change($theme, $request);

        $this->assertEquals('footheme', $this->resolver->resolveThemeName($request));
        $this->assertEquals('footheme', $this->holder->getTheme()->getName());
	}

	/**
	 * Tests validation
	 *
	 * @expectedException Jungi\ThemeBundle\Exception\ThemeValidationException
	 */
	public function testValidation()
	{
        $validator = new Validator(new FakeMetadataFactory(), new ConstraintValidatorFactory(), new DefaultTranslator());

	    // Constraints for the ThemeInterface
	    $metadata = new ClassMetadata('Jungi\ThemeBundle\Core\ThemeInterface');
	    $metadata->addConstraint(new FakeClassConstraint());
	    $validator->getMetadataFactory()->addMetadata($metadata);

	    $this->changer->setValidator($validator);

	    $request = $this->createDesktopRequest();
        $this->changer->change('footheme', $request);
	}

	/**
	 * Tests writeResponse method
	 */
    public function testWriteResponse()
    {
        $request = $this->createDesktopRequest();
        $response = new Response();

        $this->changer->change('footheme', $request);
        $this->changer->writeResponse($request, $response);

        $this->assertEquals('footheme', $response->headers->get('_theme'));
    }

	/**
	 * Data provider
	 *
	 * @return array
	 */
	public function getThemesForChange()
	{
	    return array(
	    	array($this->createThemeMock('footheme')),
	        array('footheme')
	    );
	}
}
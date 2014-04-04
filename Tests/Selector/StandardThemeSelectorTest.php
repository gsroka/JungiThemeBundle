<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tests\Selector;

use Jungi\ThemeBundle\Exception\NullThemeException;
use Jungi\ThemeBundle\Exception\ThemeNotFoundException;
use Jungi\ThemeBundle\Selector\StandardThemeSelector;
use Jungi\ThemeBundle\Tests\TestCase;
use Jungi\ThemeBundle\Core\SimpleThemeHolder;
use Jungi\ThemeBundle\Core\ThemeManagerInterface;
use Jungi\ThemeBundle\Selector\ThemeSelectorEvents;
use Jungi\ThemeBundle\Selector\DeviceThemeSwitch;
use Jungi\ThemeBundle\Core\MobileDetect;
use Jungi\ThemeBundle\Tag\Core\TagCollection;
use Jungi\ThemeBundle\Tag;
use Jungi\ThemeBundle\Core\ThemeManager;
use Jungi\ThemeBundle\Resolver\InMemoryThemeResolver;
use Jungi\ThemeBundle\Tests\Fixtures\Validation\FakeMetadataFactory;
use Jungi\ThemeBundle\Tests\Fixtures\Validation\Constraints\FakeClassConstraint;
use Jungi\ThemeBundle\Validation\ValidatorHelper;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\DefaultTranslator;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\EventDispatcher\EventDispatcher;


/**
 * StandardThemeSelector Test Case
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class StandardThemeSelectorTest extends TestCase
{
    /**
     * @var StandardThemeSelector
     */
    private $selector;

    /**
     * @var SimpleThemeHolder
     */
    private $holder;

    /**
     * @var ThemeManagerInterface
     */
    private $manager;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var InMemoryThemeResolver
     */
    private $resolver;

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
	    $theme = $this->createThemeMock('footheme');
	    $theme
	        ->expects($this->any())
	        ->method('getTags')
	        ->will($this->returnValue(new TagCollection(array(
	        	new Tag\DesktopDevices()
	        ))))
	    ;

	    $this->eventDispatcher = new EventDispatcher();
	    $this->manager = new ThemeManager(array(
	        $theme
	    ));
	    $this->resolver = new InMemoryThemeResolver('footheme', false);
	    $this->holder = new SimpleThemeHolder();
		$this->selector = new StandardThemeSelector($this->manager, $this->holder, $this->eventDispatcher, $this->resolver);
	}

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		$this->selector = null;
		$this->holder = null;
		$this->resolver = null;
		$this->manager = null;
		$this->eventDispatcher = null;
	}

	/**
	 * Tests a event listener (DeviceSwitch) cooperation with StandardThemeSelector
	 */
	public function testDeviceSwitchListener()
	{
	    // Prepare
	    $theme = $this->createThemeMock('footheme_mobile');
	    $theme
    	    ->expects($this->any())
    	    ->method('getTags')
    	    ->will($this->returnValue(new TagCollection(array(
    	        new Tag\Link('footheme'),
    	        new Tag\MobileDevices()
    	    ))))
	    ;
	    $this->manager->addTheme($theme);
        $this->eventDispatcher->addListener(ThemeSelectorEvents::RESOLVED_THEME, array(new DeviceThemeSwitch(new MobileDetect()), 'onResolvedTheme'));

        // The main thread
        $request = $this->createMobileRequest();
        $this->selector->select($request);

        // Assert
        $this->assertEquals('footheme_mobile', $this->holder->getTheme()->getName());
	}

	/**
	 * Tests the validation
	 *
	 * @expectedException \Jungi\ThemeBundle\Exception\ThemeValidationException
	 */
	public function testFailedValidation()
	{
	    $this->selector->setValidatorHelper($this->getValidatorHelper());

	    $request = $this->createDesktopRequest();
	    $this->selector->select($request);
	}

	/**
	 * Tests the fallback functionality when the validation has failed
	 */
	public function testFallbackOnFailedValidation()
	{
	    // Prepare
	    $this->selector->setValidatorHelper($this->getValidatorHelper());

	    // Default theme
	    $this->manager->addTheme($this->createThemeMock('default'));

	    // Prepare the request
	    $request = $this->createDesktopRequest();
	    $this->resolver->setThemeName('footheme', $request);

	    // Sets the fallback theme resolver
	    $this->selector->setFallback(new InMemoryThemeResolver('default'));
	    $this->selector->select($request);

	    // Assert
	    $this->assertEquals('default', $this->holder->getTheme()->getName());
	}

	/**
	 * Tests the fallback functionality when a real theme is not exist
	 */
	public function testFallbackOnEmptyTheme()
	{
	    // Default theme
	    $this->manager->addTheme($this->createThemeMock('default'));

	    // Prepare the request
	    $request = $this->createDesktopRequest();
	    $this->resolver->setThemeName(null, $request);

	    // Sets the fallback theme resolver
	    $this->selector->setFallback(new InMemoryThemeResolver('default'));
	    $this->selector->select($request);

	    // Assert
	    $this->assertEquals('default', $this->holder->getTheme()->getName());
	}

	/**
	 * Tests the fallback functionality when a real theme is not exist
	 */
	public function testFallbackOnNonExistingTheme()
	{
	    // Default theme
        $this->manager->addTheme($this->createThemeMock('default'));

        // Prepare the request
        $request = $this->createDesktopRequest();
        $this->resolver->setThemeName('missing_theme', $request);

        // Sets the fallback theme resolver
        $this->selector->setFallback(new InMemoryThemeResolver('default'));
        $this->selector->select($request);

        // Assert
        $this->assertEquals('default', $this->holder->getTheme()->getName());
	}

	/**
	 * Tests the fallback functionality when a real theme is exist
	 */
	public function testFallbackOnExistingTheme()
	{
	    // Default theme
	    $this->manager->addTheme($this->createThemeMock('default'));

	    // Prepare the request
	    $request = $this->createDesktopRequest();

	    // Sets the fallback theme resolver
	    $this->selector->setFallback(new InMemoryThemeResolver('default'));
	    $this->selector->select($request);

	    // Assert
	    $this->assertNotEquals('default', $this->holder->getTheme()->getName());
	}

	/**
	 * Tests on an existing theme
	 */
	public function testOnExistingTheme()
	{
	    $request = $this->createDesktopRequest();
	    $this->selector->select($request);

	    $this->assertEquals('footheme', $this->holder->getTheme()->getName());
	}

    /**
     * Tests on an empty theme name
     *
     * @expectedException \Jungi\ThemeBundle\Exception\NullThemeException
     */
    public function testOnNullTheme()
    {
        $request = $this->createDesktopRequest();

        $this->resolver->setThemeName(null, $request);
        $this->selector->select($request);
    }

    /**
     * Tests on an empty theme name with enabled "ignore null themes"
     */
    public function testOnNullThemeWithNullThemesIgnore()
    {
        $request = $this->createDesktopRequest();

        $this->resolver->setThemeName(null, $request);
        $this->selector->setIgnoreNullThemes(true);

        try {
            $this->selector->select($request);
        } catch (NullThemeException $e) {
            $this->fail('When the option "ignore null themes" is enabled the NullThemeException should not be thrown.');
        }
    }

    /**
     * Tests on an empty theme name
     *
     * @expectedException \Jungi\ThemeBundle\Exception\NullThemeException
     */
    public function testFallbackOnNullTheme()
    {
        $request = $this->createDesktopRequest();

        $this->resolver->setThemeName('missing_theme', $request);
        $this->selector->setFallback(new InMemoryThemeResolver(null));
        $this->selector->select($request);
    }

    /**
     * Tests on an empty fallback theme name with enabled "ignore null themes"
     */
    public function testFallbackOnNullThemeWithNullThemesIgnore()
    {
        $request = $this->createDesktopRequest();

        $this->resolver->setThemeName('missing_theme', $request);
        $this->selector->setFallback(new InMemoryThemeResolver(null));
        $this->selector->setIgnoreNullThemes(true);

        try {
            $this->selector->select($request);
        } catch (NullThemeException $e) {
            $this->fail('When the option "ignore null themes" is enabled the NullThemeException should not be thrown.');
        }
    }

	/**
	 * Tests on a bad request
	 */
	public function testOnNonExistingTheme()
	{
	    $request = $this->createDesktopRequest();
	    $this->resolver->setThemeName('footheme_missing', $request);

        try {
	        $this->selector->select($request);
        } catch (\RuntimeException $e) {
            if ($e->getPrevious() instanceof ThemeNotFoundException) {
                return;
            }

            $this->fail('An exception with a previous exception "ThemeNotFoundException" should be raised.');
        }
	}

	/**
	 * Returns the configured validator helper
	 *
	 * @return ValidatorHelper
	 */
	private function getValidatorHelper()
	{
	    $validator = new Validator(new FakeMetadataFactory(), new ConstraintValidatorFactory(), new DefaultTranslator());

	    // Constraints for the ThemeInterface
	    $metadata = new ClassMetadata('Jungi\ThemeBundle\Core\ThemeInterface');
	    $metadata->addConstraint(new FakeClassConstraint());
	    $validator->getMetadataFactory()->addMetadata($metadata);

	    return new ValidatorHelper($validator);
	}
}
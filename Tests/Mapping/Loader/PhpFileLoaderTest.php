<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tests\Mapping\Loader;

use Jungi\ThemeBundle\Core\Details;
use Jungi\ThemeBundle\Core\StandardTheme;
use Jungi\ThemeBundle\Core\ThemeManager;
use Jungi\ThemeBundle\Mapping\Loader\PhpFileLoader;
use Jungi\ThemeBundle\Tests\Fixtures\Tag\Own;
use Jungi\ThemeBundle\Tests\TestCase;
use Jungi\ThemeBundle\Tag;
use Jungi\ThemeBundle\Tag\Core\TagCollection;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * PhpFileLoader Test Case
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class PhpFileLoaderTest extends TestCase
{
    /**
     * @var PhpFileLoader
     */
    private $loader;

    /**
     * @var ThemeManager
     */
    private $manager;

    /**
     * @var FileLocator
     */
    private $locator;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->manager = new ThemeManager();
        $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $kernel
            ->expects($this->any())
            ->method('locateResource')
            ->will($this->returnValue(__DIR__ . '/Fixtures/fake_bundle'))
        ;
        $this->locator = new FileLocator($kernel, __DIR__ . '/Fixtures/php');
        $this->loader = new PhpFileLoader($this->manager, $this->locator);
    }

    protected function tearDown()
    {
        $this->manager = null;
        $this->loader = null;
        $this->locator = null;
    }

    /**
     * Tests file load
     */
    public function testLoad()
    {
        $this->loader->load('theme.php');

        $this->assertEquals(new StandardTheme(
            'foo_1',
            $this->locator->locate('@JungiFooBundle/Resources/theme'),
            new Details('A fancy theme', '1.0.0', '<i>foo desc</i>', 'MIT', 'piku235', 'piku235@gmail.com', 'http://test.pl'),
            new TagCollection(array(
                new Tag\DesktopDevices(),
                new Tag\MobileDevices(array('iOS', 'AndroidOS'), Tag\MobileDevices::MOBILE),
                new Own('test')

            ))
        ), $this->manager->getTheme('foo_1'));
    }
} 
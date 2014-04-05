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
use Jungi\ThemeBundle\Tag\Core\TagCollection;
use Jungi\ThemeBundle\Tests\Fixtures\Tag\Own;
use Jungi\ThemeBundle\Tests\TestCase;
use Jungi\ThemeBundle\Tag;

/**
 * AbstractFileLoaderTest
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
abstract class AbstractFileLoaderTest extends TestCase
{
    /**
     * @var ThemeManager
     */
    protected $manager;

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    protected $kernel;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->manager = new ThemeManager();
        $this->kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $this->kernel
            ->expects($this->any())
            ->method('locateResource')
            ->will($this->returnValue(__DIR__ . '/Fixtures/empty'))
        ;
    }

    /**
     * Tear down
     */
    protected function tearDown()
    {
        $this->kernel = null;
        $this->manager = null;
    }

    /**
     * Tests on valid theme mapping
     */
    public function testOnValidThemeMapping()
    {
        $this->loadFile('correct_themes');

        $details = new Details('A fancy theme', '1.0.0', '<i>foo desc</i>', 'MIT', 'piku235', 'piku235@gmail.com', 'http://test.pl');
        $themes = array(
            new StandardTheme('foo_1', __DIR__ . '/Fixtures/empty', $details, new TagCollection(array(
                new Tag\DesktopDevices(),
                new Tag\MobileDevices(array('iOS', 'AndroidOS'), Tag\MobileDevices::MOBILE),
                new Own('test')
            ))),
            new StandardTheme('foo_2', __DIR__ . '/Fixtures/empty', $details, new TagCollection(array(
                new Tag\DesktopDevices()
            ))),
            new StandardTheme('foo_3', __DIR__ . '/Fixtures/empty', $details),
            new StandardTheme('foo_4', __DIR__ . '/Fixtures/empty', new Details('A fancy theme', '1.0.0'))
        );

        foreach ($themes as $theme) {
            $this->assertEquals($theme, $this->manager->getTheme($theme->getName()));
        }
    }

    /**
     * Loads the given file
     *
     * @param string $file A file without ext
     *
     * @return void
     */
    abstract protected function loadFile($file);
}
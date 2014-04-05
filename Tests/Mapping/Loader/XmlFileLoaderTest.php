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

use Jungi\ThemeBundle\Mapping\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\Config\FileLocator;


/**
 * XmlFileLoader Test Case
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class XmlFileLoaderTest extends AbstractFileLoaderTest
{
    /**
     * @var XmlFileLoader
     */
    private $loader;

    /**
     * Set up
     */
    protected function setUp()
    {
        parent::setUp();

        $this->loader = new XmlFileLoader($this->manager, new FileLocator($this->kernel, __DIR__ . '/Fixtures/xml'));
    }

    /**
     * Tear down
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->manager = null;
        $this->loader = null;
    }

    /**
     * Loads the given file
     *
     * @param string $file A file without ext
     *
     * @return void
     */
    protected function loadFile($file)
    {
        $this->loader->load($file . '.xml');
    }
}
 
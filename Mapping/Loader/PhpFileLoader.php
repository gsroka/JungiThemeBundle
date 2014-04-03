<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Mapping\Loader;

/**
 * PhpFileLoader
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class PhpFileLoader extends FileLoader
{
    /**
     * (non-PHPdoc)
     * @see \Jungi\ThemeBundle\Mapping\Loader\FileLoader::supports()
     */
    public function supports($file)
    {
        return 'php' == pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * (non-PHPdoc)
     * @see \Jungi\ThemeBundle\Mapping\Loader\FileLoader::load()
     */
    public function load($file)
    {
        // Vars available for mapping file
        $manager = $this->themeManager;
        $locator = $this->locator;

        // Include
        $path = $this->locator->locate($file);
        include $path;
    }
}
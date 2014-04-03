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

use Symfony\Component\Config\FileLocatorInterface;
use Jungi\ThemeBundle\Core\ThemeManagerInterface;

/**
 * FileLoader is a common class for loading theme mapping files
 * to a ThemeManagerInterface instance
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
abstract class FileLoader
{
    /**
     * @var ThemeManagerInterface
     */
    protected $themeManager;

    /**
     * @var FileLocatorInterface
     */
    protected $locator;

    /**
     * Constructor
     *
     * @param ThemeManagerInterface $themeManager A theme manager
     * @param FileLocatorInterface  $locator      A file locator
     */
    public function __construct(ThemeManagerInterface $themeManager, FileLocatorInterface $locator)
    {
        $this->locator = $locator;
        $this->themeManager = $themeManager;
    }

    /**
     * Loads themes from a given theme mapping file
     * into the current ThemeManagerInterface instance
     *
     * @param string $file A file
     *
     * @return void
     */
    abstract public function load($file);

    /**
     * Checks if FileLoader can handle a given file
     *
     * @param string $file A file
     *
     * @return bool
     */
    abstract public function supports($file);
}
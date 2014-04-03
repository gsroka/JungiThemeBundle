<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Loader;

use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Jungi\ThemeBundle\Core\ThemeReference;
use Jungi\ThemeBundle\Core\ThemeManagerInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Jungi\ThemeBundle\Exception\ThemeNotFoundException;

/**
 * The theme locator will return a full path to a theme resource or if
 * a given template resource is not exist in a theme directory
 * it will use standard locate method of the parent class
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ThemeLocator extends TemplateLocator
{
    /**
     * @var ThemeManagerInterface
     */
    protected $manager;

    /**
     * Constructor
     *
     * @param ThemeManagerInterface $manager  A theme manager
     * @param FileLocatorInterface  $locator  A FileLocatorInterface instance
     * @param string                $cacheDir The cache path (optional)
     */
    public function __construct(ThemeManagerInterface $manager, FileLocatorInterface $locator, $cacheDir = null)
    {
        $this->manager = $manager;

        parent::__construct($locator, $cacheDir);
    }


    /**
     * Returns a full path to a given theme or template file
     *
     * If TemplateReference instance is given or a path to a given
     * theme file can not be found it uses parent locate method
     *
     * @param TemplateReferenceInterface $template    A template
     * @param string                     $currentPath Unused
     * @param Boolean                    $first       Unused
     *
     * @return string
     *
     * @throws \RuntimeException When a theme from a ThemeReference instance
     *                           is not exist in a theme manager
     * @throws \InvalidArgumentException
     */
    public function locate($template, $currentPath = null, $first = true)
    {
        if (!$template instanceof TemplateReferenceInterface) {
            throw new \InvalidArgumentException("The template must be an instance of TemplateReferenceInterface.");
        }

        // I used here checking for a cache entry
        // only for performance purposes
        $key = $this->getCacheKey($template);
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        if ($template instanceof ThemeReference) {
            try {
                $theme = $this->manager->getTheme($template->get('theme'));
                $path = $theme->getPath() . '/' . $template->getPath();

                return $this->cache[$key] = $this->locator->locate($path);
            } catch (ThemeNotFoundException $e) {
                throw new \RuntimeException('The theme locator could not finish his job, see the previous exception.', null, $e);
            } catch (\Exception $e) {
                // Theme file is not exist, instead
                // use TemplateReferenceInterface instance path
                return parent::locate($template->getOrigin(), $currentPath, $first);
            }
        }

        return parent::locate($template, $currentPath, $first);
    }
}

<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class JungiThemeExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // Configuration
        $container->setParameter('jungi.theme.configuration', $config);
        $container->setParameter('jungi.theme.selector.ignore_null_themes', $config['ignore_null_themes']);

        // Class cache
        $this->addClassesToCompile(array(
        	'Jungi\ThemeBundle\CacheWarmer\TemplateFinderChain',
            'Jungi\ThemeBundle\CacheWarmer\ThemeFinder',
            'Jungi\ThemeBundle\Core\MobileDetect',
            'Jungi\ThemeBundle\Core\Details',
            'Jungi\ThemeBundle\Core\ThemeFilenameParser',
            'Jungi\ThemeBundle\Core\StandardTheme',
            'Jungi\ThemeBundle\Core\ThemeManager',
            'Jungi\ThemeBundle\Core\ThemeReference',
            'Jungi\ThemeBundle\Core\ThemeNameParser',
            'Jungi\ThemeBundle\Form\Type\ThemeType',
            'Jungi\ThemeBundle\Changer\StandardThemeChanger',
            'Jungi\ThemeBundle\Changer\ThemeChangerEvents',
            'Jungi\ThemeBundle\Changer\ThemeChangerListener',
            'Jungi\ThemeBundle\Loader\ThemeLocator',
            'Jungi\ThemeBundle\Event\ThemeEvent',
            'Jungi\ThemeBundle\Mapping\Loader\FileLoader',
            'Jungi\ThemeBundle\Mapping\Loader\PhpFileLoader',
            'Jungi\ThemeBundle\Mapping\Loader\YamlFileLoader',
            'Jungi\ThemeBundle\Mapping\SimpleXMLElement',
            'Jungi\ThemeBundle\Selector\StandardThemeSelector',
            'Jungi\ThemeBundle\Selector\DeviceThemeSwitch',
            'Jungi\ThemeBundle\Selector\ThemeSelectorEvents',
            'Jungi\ThemeBundle\Tag\DesktopDevices',
            'Jungi\ThemeBundle\Tag\MobileDevices',
            'Jungi\ThemeBundle\Tag\Link',
            'Jungi\ThemeBundle\Tag\Core\TagCollection'
        ));
    }
}

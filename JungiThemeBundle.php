<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle;

use Jungi\ThemeBundle\Core\Details;
use Jungi\ThemeBundle\Core\StandardTheme;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Jungi\ThemeBundle\DependencyInjection\Compiler\ThemePass;

/**
 * The jungi theme bundle
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class JungiThemeBundle extends Bundle
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\HttpKernel\Bundle\Bundle::build()
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ThemePass());
    }

    /**
     * Adds an empty theme if it's enabled
     *
     * @return void
     */
    public function boot()
    {
        $config = $this->container->getParameter('jungi.theme.configuration');
        if ($config && $config['empty_theme']) {
            $manager = $this->container->get('jungi.theme.manager');
            $manager->addTheme(new StandardTheme('empty_theme', __DIR__ . '/Resources/theme', new Details('Empty Theme', '1.0.0', 'Please disable/remove me (:')));
        }
    }
}

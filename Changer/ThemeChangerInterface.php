<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Changer;

use Jungi\ThemeBundle\Core\ThemeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ThemeChangerInterface allows to change the theme for a given request and
 * also allows save the theme to a given response
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
interface ThemeChangerInterface
{
    /**
     * Changes the current theme with a new one
     *
     * @param string|ThemeInterface $theme   A theme instance or a theme name
	 * @param Request               $request A request instance
	 *
	 * @return void
     */
    public function change($theme, Request $request);

    /**
     * Writes the theme changes to a given response
     *
     * @param Request  $request  A request instance
	 * @param Response $response A response instance
	 *
	 * @return void
     */
    public function writeResponse(Request $request, Response $response);
}
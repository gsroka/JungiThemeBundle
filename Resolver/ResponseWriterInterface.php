<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Resolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The implemented classes will be able to writes theme names
 * to a given Response
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
interface ResponseWriterInterface
{
    /**
     * Writes the theme changes to a given response
     *
     * @param string   $themeName An actual theme name
	 * @param Response $response  A response instance
	 *
	 * @return void
     */
    public function writeResponse($themeName, Response $response);
}
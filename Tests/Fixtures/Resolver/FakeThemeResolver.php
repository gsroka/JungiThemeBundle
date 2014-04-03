<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tests\Fixtures\Resolver;

use Jungi\ThemeBundle\Resolver\InMemoryThemeResolver;
use Jungi\ThemeBundle\Resolver\ResponseWriterInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * FakeThemeResolver with implemented ResponseWriterInterface
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class FakeThemeResolver extends InMemoryThemeResolver implements ResponseWriterInterface
{
    /**
     * (non-PHPdoc)
     * @see \Jungi\ThemeBundle\Resolver\ResponseWriterInterface::writeResponse()
     */
    public function writeResponse($themeName, Response $response)
    {
        $response->headers->set('_theme', $themeName);
    }
}
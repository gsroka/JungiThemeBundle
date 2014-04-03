<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Validation\Requirement;

use Jungi\ThemeBundle\Core\ThemeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Decides if the validation is needed
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
interface ValidationRequirementInterface
{
    /**
     * Checks if a given theme for a given request can be validated
     *
     * @param ThemeInterface $theme   A theme
     * @param Request        $request A request instance
     *
     * @return bool
     */
    public function canValidate(ThemeInterface $theme, Request $request);
}
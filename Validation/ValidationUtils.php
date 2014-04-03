<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Validation;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Jungi\ThemeBundle\Exception\ThemeValidationException;

/**
 * The class provides useful utilities for the validation
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ValidationUtils
{
    /**
     * Returns the well formated ThemeValidationException
     *
     * @param string                           $message    A message
     * @param ConstraintViolationListInterface $violations Violations
     *
     * @return ThemeValidationException
     */
    public static function validationException($message, ConstraintViolationListInterface $violations)
    {
        $message = rtrim($message, '. ') . '.';
        foreach ($violations as $violation) {
            $message .= $violation->getPropertyPath()
                        ? sprintf(' Property %s: %s', $violation->getPropertyPath(), $violation->getMessage())
                        : ' ' . $violation->getMessage()
            ;
        }

        return new ThemeValidationException($message, $violations);
    }
}
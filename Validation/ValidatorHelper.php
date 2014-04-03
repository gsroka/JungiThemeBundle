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

use Jungi\ThemeBundle\Core\ThemeInterface;
use Symfony\Component\Validator\ValidatorInterface;
use Jungi\ThemeBundle\Validation\Requirement\ValidationRequirementInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * The class adds an extra logic on top of Symfony Validator
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ValidatorHelper
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var ValidationRequirementInterface
     */
    protected $requirement;

    /**
     * Construct
     *
     * @param ValidatorInterface             $validator   A validator
     * @param ValidationRequirementInterface $requirement A validation requirement (optional)
     */
    public function __construct(ValidatorInterface $validator, ValidationRequirementInterface $requirement = null)
    {
        $this->validator = $validator;
        $this->requirement = $requirement;
    }

    /**
     * Sets a validation requirement
     *
     * @param ValidationRequirementInterface $requirement A validation requirement
     *
     * @return void
     */
    public function setRequirement(ValidationRequirementInterface $requirement)
    {
        $this->requirement = $requirement;
    }

    /**
     * Executes the validation for a given theme only when a requirement is fulfilled
     * If the requirement was not set validation will be normally executed
     *
     * @param ThemeInterface $theme   A theme
     * @param Request        $request A request instance
     *
     * @return ConstraintViolationListInterface
     *
     * @see Symfony\Component\Validation\ValidatorInterface::validate
     */
    public function validate(ThemeInterface $theme, Request $request)
    {
        if (null !== $this->requirement && !$this->requirement->canValidate($theme, $request)) {
            return new ConstraintViolationList();
        }

        return $this->validator->validate($theme);
    }
}
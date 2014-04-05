<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tests\Validation;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\DefaultTranslator;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints;
use Jungi\ThemeBundle\Tests\TestCase;
use Jungi\ThemeBundle\Core\StandardTheme;
use Jungi\ThemeBundle\Tests\Fixtures\Validation\FakeMetadataFactory;
use Jungi\ThemeBundle\Validation\ValidatorHelper;
use Jungi\ThemeBundle\Tests\Fixtures\Validation\Requirement;
use Symfony\Component\HttpFoundation\Request;
use Jungi\ThemeBundle\Tests\Fixtures\Validation\Constraints\FakeClassConstraint;

/**
 * ValidatorHelper Test Case
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ValidatorHelperTest extends TestCase
{
    /**
     * @var ValidatorHelper
     */
    protected $validator;

    /**
     * @var FakeMetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var StandardTheme
     */
    protected $theme;

    /**
     * @var Request
     */
    protected $request;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->metadataFactory = new FakeMetadataFactory();
        $this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $this->theme = new StandardTheme(
            'footheme', 'path', $this->getMock('Jungi\ThemeBundle\Core\DetailsInterface')
        );

        $validator = new Validator($this->metadataFactory, new ConstraintValidatorFactory(), new DefaultTranslator());
        $this->validator = new ValidatorHelper($validator);
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        $this->metadataFactory = null;
        $this->theme = null;
        $this->validator = null;
        $this->request = null;
    }

    /**
     * Tests failed validation with the requirement
     */
    public function testFailedValidationWithRequirement()
    {
        $metadata = new ClassMetadata('Jungi\ThemeBundle\Core\ThemeInterface');
        $metadata->addGetterConstraint('name', new Constraints\EqualTo('footheme_boo'));
        $this->metadataFactory->addMetadata($metadata);

        $requirement = new Requirement\Logic(false);
        $this->validator->setRequirement($requirement);
        $this->assertCount(0, $this->validator->validate($this->theme, $this->request));

        $requirement->setPass(true);
        $this->assertCount(1, $this->validator->validate($this->theme, $this->request));
    }

    /**
     * Tests succeed validation
     */
    public function testSucceedValidation()
    {
        $metadata = new ClassMetadata('Jungi\ThemeBundle\Core\ThemeInterface');
        $metadata->addGetterConstraint('name', new Constraints\EqualTo('footheme'));
        $this->metadataFactory->addMetadata($metadata);

        $this->assertCount(0, $this->validator->validate($this->theme, $this->request));
    }

    /**
     * Tests failed validation
     */
    public function testFailedValidation()
    {
        $metadata = new ClassMetadata('Jungi\ThemeBundle\Core\ThemeInterface');
        $metadata->addConstraint(new FakeClassConstraint());
        $metadata->addGetterConstraint('name', new Constraints\EqualTo('footheme_boo'));
        $this->metadataFactory->addMetadata($metadata);

        $this->assertCount(2, $this->validator->validate($this->theme, $this->request));
    }
}
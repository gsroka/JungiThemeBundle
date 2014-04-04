<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Jungi\ThemeBundle\Form\Type\ThemeType;
use Jungi\ThemeBundle\Core\ThemeManager;
use Jungi\ThemeBundle\Core\StandardTheme;
use Jungi\ThemeBundle\Core\Details;

/**
 * ThemeType Test Case
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ThemeTypeTest extends TypeTestCase
{
    public function testSubmitValid()
    {
        $first = new StandardTheme('footheme', 'path', new Details('foo super theme', '1.0.0'));
        $second = new StandardTheme('bootheme', 'path', new Details('boo hio theme', '1.0.0'));
        $manager = new ThemeManager(array(
	       $first, $second
        ));
        $actualTheme = 'footheme';
        $field = 'theme';

        $form = $this->factory->create(new ThemeType($manager));
        $form->submit($actualTheme);
        $view = $form->createView();

        // First check
        $this->assertSame($first, $form->getData());

        // Verify theme names
        foreach ($view->vars['choices'] as $choice) {
            $this->assertAttributeEquals($manager->getTheme($choice->value)->getDetails()->getName(), 'label', $choice);
        }
    }
}
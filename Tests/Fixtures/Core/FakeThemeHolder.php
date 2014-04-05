<?php

namespace Jungi\ThemeBundle\Tests\Fixtures\Core;

use Jungi\ThemeBundle\Core\ThemeHolderInterface;
use Jungi\ThemeBundle\Core\ThemeInterface;

/**
 * FakeThemeHolder
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class FakeThemeHolder implements ThemeHolderInterface
{
    public $theme;

    public function setTheme(ThemeInterface $theme)
    {
        $this->theme = $theme;
    }

    public function getTheme()
    {
        return $this->theme;
    }
} 
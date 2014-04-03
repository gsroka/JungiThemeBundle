<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tests\Fixtures\Validation\Requirement;

use Jungi\ThemeBundle\Validation\Requirement\ValidationRequirementInterface;
use Jungi\ThemeBundle\Core\ThemeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Simpe logic requirement
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class Logic implements ValidationRequirementInterface
{
    private $pass;

    public function __construct($pass)
    {
        $this->pass = $pass;
    }

    public function setPass($pass)
    {
        $this->pass = $pass;
    }

	public function canValidate(ThemeInterface $theme, Request $request)
	{
		return $this->pass;
	}
}
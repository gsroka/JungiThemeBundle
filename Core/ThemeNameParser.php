<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Core;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser;

/**
 * ThemeNameParser converts template names from notation "theme#bundle:section:template.format.engine"
 * to ThemeReference. Also he converts from standard notation "bundle:section:template.format.engine"
 * to TemplateReference
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ThemeNameParser extends TemplateNameParser
{
	/**
	 * @var ThemeHolderInterface
	 */
	protected $holder;

	/**
     * Constructor.
     *
     * @param ThemeHolderInterface $holder A theme holder
     * @param KernelInterface 	   $kernel A KernelInterface instance
     */
    public function __construct(ThemeHolderInterface $holder, KernelInterface $kernel)
    {
    	parent::__construct($kernel);

    	$this->holder = $holder;
    }

	/**
	 * Parses a template name to a theme reference
	 *
	 * @param  TemplateReferenceInterface|string $name A template name
	 *
	 * @return ThemeReference|TemplateReference
	 *
	 * @throws \RuntimeException When ThemeHolderInterface instance will
	 *                           return null
	 */
	public function parse($name)
	{
		$theme = $this->holder->getTheme();
		if (null === $theme) {
		    throw new \RuntimeException('There is no ThemeInterface instance returned by the ThemeHolderInterface instance.');
		}

		$reference = null;
		if ($name instanceof TemplateReferenceInterface) {
            $reference = $name;
            $name = $reference->getLogicalName();
        } else if (isset($this->cache[$name])) {
            return $this->cache[$name];
        } else {
            $reference = parent::parse($name);
        }

		return $this->cache[$name] = new ThemeReference($reference, $theme->getName());
	}
}
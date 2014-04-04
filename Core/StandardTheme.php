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

use Jungi\ThemeBundle\Core\ThemeInterface;
use Jungi\ThemeBundle\Tag\Core\TagCollection;

/**
 * StandardTheme is a theme model used for
 * eg. by XmlFileLoader and YamlFileLoader
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class StandardTheme implements ThemeInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var DetailsInterface
     */
    protected $details;

    /**
     * @var TagCollection
     */
    protected $tags;

    /**
     * Constructor
     *
     * @param string           $name    A name
     * @param string           $path    A path
     * @param DetailsInterface $details A details
     * @param TagCollection    $tags    A tag collection (optional)
     */
    public function __construct($name, $path, DetailsInterface $details, TagCollection $tags = null)
    {
        $this->name = $name;
        $this->path = $path;
        $this->details = $details;
        $this->tags = $tags ?: new TagCollection();
    }

    /**
     * (non-PHPdoc)
     * @see \Jungi\ThemeBundle\Core\ThemeInterface::getDetails()
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * (non-PHPdoc)
     * @see \Jungi\ThemeBundle\Core\ThemeInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * (non-PHPdoc)
     * @see \Jungi\ThemeBundle\Core\ThemeInterface::getPath()
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * (non-PHPdoc)
     * @see \Jungi\ThemeBundle\Core\ThemeInterface::getTags()
     */
    public function getTags()
    {
        return $this->tags;
    }
}
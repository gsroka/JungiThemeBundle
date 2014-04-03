<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Mapping\Loader;

use Symfony\Component\Config\Util\XmlUtils;
use Jungi\ThemeBundle\Core\StandardTheme;
use Jungi\ThemeBundle\Tag\Core\TagCollection;
use Jungi\ThemeBundle\Tag;
use Symfony\Component\Validator\Constraints\All;
use Jungi\ThemeBundle\Core\Details;

/**
 * XmlFileLoader
 *
 * It is responsible for creating StandardTheme instances from
 * a xml mapping file
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class XmlFileLoader extends FileLoader
{
    /**
     * (non-PHPdoc)
     *
     * @see \Jungi\ThemeBundle\Mapping\Loader\FileLoader::supports()
     */
    public function supports($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION) == 'xml';
    }

    /**
     * Loads themes from a given xml theme mapping file
     *
     * @param string $file A file
     *
     * @return void
     *
     * @throws \DomainException If a given file is not supported
     */
    public function load($file)
    {
        $path = $this->locator->locate($file);

        if (!$this->supports($path)) {
            throw new \DomainException(sprintf('The given file "%s" is not supported.', $path));
        }

        $xml = $this->loadFile($path);
        foreach ($xml->children() as $child) {
            $this->themeManager->addTheme($this->parseTheme($child));
        }
    }

    /**
     * Loads a xml file data
     *
     * @param string $file A file
     *
     * @return \SimpleXMLElement
     */
    protected function loadFile($file)
    {
        try {
            $doc = XmlUtils::loadFile($file);
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException(sprintf('The problem occurred while loading the file "%s", see the previous exception.', $file), null, $e);
        }

        return simplexml_import_dom($doc, '\Jungi\ThemeBundle\Mapping\SimpleXMLElement');
    }

    /**
     * Parses a theme element from a dom document
     *
     * @param \SimpleXMLElement $elm A dom element
     *
     * @return StandardTheme
     *
     * @throws \InvalidArgumentException If a theme node has some missing
     *         attributes
     */
    protected function parseTheme(\SimpleXMLElement $elm)
    {
        if (! isset($elm['name']) || ! isset($elm['path'])) {
            throw new \InvalidArgumentException('The node theme has some required missing attributes. Have you not forgot to specify attributes "path" and "name" for this node?');
        }

        return new StandardTheme(
            (string) $elm['name'],
            $this->locator->locate((string) $elm['path']),
            $this->parseTags($elm),
            $this->parseDetails($elm)
        );
    }

    /**
     * Parses a details about a theme
     *
     * @param \SimpleXMLElement $elm An element
     *
     * @return Details
     *
     * @throws \InvalidArgumentException If a detail node has not
     *                                   defined attr "name"
     * @throws \InvalidArgumentException If a name of a detail node
     *                                   is invalid
     */
    protected function parseDetails(\SimpleXMLElement $elm)
    {
        $collection = array();
        $valid = array(
            'author.name',
            'author.www',
            'author.email',
            'name',
            'description',
            'version',
            'license'
        );
        foreach ($elm->xpath('//details[1]/detail') as $detail) {
            if (!isset($detail['name'])) {
                throw new \InvalidArgumentException('The detail node has not defined attribute "name". Have you forgot about that?');
            } elseif (!in_array((string) $detail['name'], $valid)) {
                throw new \InvalidArgumentException(sprintf('The name "%s" of a detail node is invalid.', $detail['name']));
            }

            $collection[(string) $detail['name']] = (string) $detail;
        }
        $property = function ($name) use ($collection) {
            return isset($collection[$name]) ? $collection[$name] : null;
        };

        return new Details(
            $property('name'),
            $property('version'),
            $property('description'),
            $property('license'),
            $property('author.name'),
            $property('author.email'),
            $property('author.www')
        );
    }

    /**
     * Parses a theme tags from a given dom element
     *
     * @param \SimpleXMLElement $elm An element
     *
     * @return TagCollection
     *
     * @throws \InvalidArgumentException If a tag node has not defined attr "class"
     * @throws \RuntimeException If a tag is not exist
     */
    protected function parseTags(\SimpleXMLElement $elm)
    {
        $tags = array();
        foreach ($elm->xpath('//tags[1]/tag') as $tag) {
            if (!isset($tag['class'])) {
                throw new \InvalidArgumentException('The tag node has not defined attribute "class". Have you forgot about that?');
            }

            $class = $tag['class'];
            if (!class_exists($class)) {
                $class = '\\Jungi\\ThemeBundle\\Tag\\' . $tag['class'];
                if (!class_exists($class)) {
                    throw new \RuntimeException(sprintf('The tag "%s" is not exist.', $tag['class']));
                }
            }

            $reflection = new \ReflectionClass($class);
            if (!$reflection->implementsInterface('Jungi\ThemeBundle\Tag\TagInterface')) {
                throw new \InvalidArgumentException(sprintf('The tag with class "%s" should implement TagInterface.', $class));
            }
            $instance = count($tag->children())
                        ? $reflection->newInstanceArgs($tag->getArgumentsAsPhp('argument'))
                        : $reflection->newInstance((string) $tag)
            ;

            // Add a new tag
            $tags[] = $instance;
        }

        return new TagCollection($tags);
    }
}

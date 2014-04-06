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

use Jungi\ThemeBundle\Tag\TagInterface;
use Symfony\Component\Config\Util\XmlUtils;
use Jungi\ThemeBundle\Core\StandardTheme;
use Jungi\ThemeBundle\Tag\Core\TagCollection;
use Jungi\ThemeBundle\Tag;
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
            $doc = XmlUtils::loadFile($file, __DIR__ . '/schema/theme-1.0.xsd');
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException(sprintf('The problem has occurred while loading the file "%s", see the previous exception.', $file), null, $e);
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
     * @throws \InvalidArgumentException If a theme node has some missing attributes
     */
    protected function parseTheme(\SimpleXMLElement $elm)
    {
        if (! isset($elm['name']) || ! isset($elm['path'])) {
            throw new \InvalidArgumentException('The node theme has some required missing attributes. Have you not forgot to specify attributes "path" and "name" for this node?');
        }

        // Ns
        $elm->registerXPathNamespace('mapping', 'http://github.com/piku235/JungiThemeBundle/blob/master/Mapping/Loader/schema/theme-1.0.xsd');

        return new StandardTheme(
            (string) $elm['name'],
            $this->locator->locate((string) $elm['path']),
            $this->parseDetails($elm),
            $this->parseTags($elm)
        );
    }

    /**
     * Parses a details about a theme
     *
     * @param \SimpleXMLElement $elm An element
     *
     * @return Details
     *
     * @throws \InvalidArgumentException If a detail node has not defined attr "name"
     * @throws \RuntimeException When something goes wrong while parsing details node
     */
    protected function parseDetails(\SimpleXMLElement $elm)
    {
        $collection = array();
        foreach ($elm->xpath('mapping:details/mapping:detail') as $detail) {
            if (!isset($detail['name'])) {
                throw new \InvalidArgumentException('The detail node has not defined attribute "name". Have you forgot about that?');
            }

            $collection[(string) $detail['name']] = (string) $detail;
        }

        try {
            return LoaderUtils::createDetails($collection);
        } catch (\LogicException $e) {
            throw new \RuntimeException('An exception has occurred while parsing the details node, see the previous exception', null, $e);
        }
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
        foreach ($elm->xpath('mapping:tags/mapping:tag') as $tag) {
            $tags[] = $this->parseTag($tag);
        }

        return new TagCollection($tags);
    }

    /**
     * Parses a theme tags from a given dom element
     *
     * @param \SimpleXMLElement $tag A tag element
     *
     * @return TagInterface
     *
     * @throws \InvalidArgumentException If a tag node has not defined attr "class"
     * @throws \RuntimeException If a tag is not exist
     */
    protected function parseTag(\SimpleXMLElement $tag)
    {
        if (!isset($tag['class'])) {
            throw new \InvalidArgumentException('The tag node has not defined attribute "class". Have you forgot about that?');
        }

        return LoaderUtils::createTag((string) $tag['class'], count($tag->children()) ? $tag->getArgumentsAsPhp('argument') : (string) $tag);
    }
}

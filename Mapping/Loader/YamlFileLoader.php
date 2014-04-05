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

use Symfony\Component\Yaml\Yaml;
use Jungi\ThemeBundle\Core\StandardTheme;
use Jungi\ThemeBundle\Tag\Core\TagCollection;
use Jungi\ThemeBundle\Tag;
use Jungi\ThemeBundle\Core\Details;

/**
 * YamlFileLoader
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class YamlFileLoader extends FileLoader
{
    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * (non-PHPdoc)
     * @see \Jungi\ThemeBundle\Mapping\Loader\FileLoader::supports()
     */
    public function supports($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION) == 'yml';
    }

    /**
     * Loads a yml theme mapping file
     *
     * @param  string $file A file
     *
     * @return void
     *
     * @throws \RuntimeException If a file is not local
     * @throws \RuntimeException If a file can not be found
     * @throws \InvalidArgumentException If a return value from a file is wrong
     */
    public function load($file)
    {
        $path = $this->locator->locate($file);

        if (!stream_is_local($path)) {
            throw new \RuntimeException(sprintf('The "%s" file is not local.', $path));
        }

        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('The file "%s" can not be found.', $path));
        }

        $content = Yaml::parse($path, true);
        if (null === $content) { // If is an empty file
            return;
        }

        // Validate a mapping file
        $this->validate($content, $file);

        // Parameters
        $this->parseParameters($content);

        // Themes
        $this->parseThemes($content);
    }

    /**
     * Parses details specification
     *
     * @param  array $specification A specification
     *
     * @return Details
     *
     * @throws \RuntimeException When something goes wrong while parsing details node
     */
    protected function parseDetails(array $specification)
    {
        $collection = array();
        if (isset($specification['details'])) {
            foreach ($specification['details'] as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $childKey => $childValue) {
                        $collection[$name . '.' . $childKey] = $childValue;
                    }
                } else {
                    $collection[$name] = $value;
                }
            }
        }

        try {
            return LoaderUtils::createDetails($collection);
        } catch (\LogicException $e) {
            throw new \RuntimeException('An exception has occurred while parsing the details node, see the previous exception', null, $e);
        }
    }

    /**
     * Parses tags specification
     *
     * @param  array $specification A specification
     *
     * @return TagCollection
     */
    protected function parseTags(array $specification)
    {
        $tags = array();
        if (isset($specification['tags'])) {
            foreach ($specification['tags'] as $tag) {
                $tags[] = $this->parseTag($tag);
            }
        }

        return new TagCollection($tags);
    }

    /**
     * Parses a tag
     *
     * @param array $tag A tag definition
     *
     * @return Tag\TagInterface
     *
     * @throws \InvalidArgumentException If tag definition is wrong
     * @throws \RuntimeException When tag is not exist
     */
    protected function parseTag(array $tag)
    {
        if (!isset($tag['class'])) {
            throw new \InvalidArgumentException('You must define attribute "class" for tags.');
        }

        $arguments = isset($tag['arguments']) ? $tag['arguments'] : null;
        if (is_array($arguments)) {
            $this->replaceParameters($arguments);
        }

        return LoaderUtils::createTag($tag['class'], $arguments);
    }

    /**
     * Processes parameters
     *
     * @param array $content A file content
     *
     * @return void
     */
    protected function parseParameters(array $content)
    {
        $this->parameters = array();
        if (isset($content['parameters'])) {
            $this->parameters = $content['parameters'];
            array_walk_recursive($this->parameters, function(&$item, $key) {
                // constants
                if (0 === strpos($item, '#const:')) {
                    $const = substr($item, 7);
                    $const = defined($const) ? $const : '\\Jungi\\ThemeBundle\\Tag\\' . $const;
                    if (!defined($const)) {
                        throw new \RuntimeException(sprintf('The constant "%s" is not exist.', $const));
                    }

                    $item = constant($const);
                }
            });
        }
    }

    /**
     * Parses themes
     *
     * @param array $content A configuration file content
     *
     * @return void
     */
    protected function parseThemes(array $content)
    {
        foreach ($content['themes'] as $themeName => $specification) {
            $this->themeManager->addTheme($this->parseTheme($themeName, $specification));
        }
    }

    /**
     * Parses a theme
     *
     * @param string $themeName     A theme name
     * @param array  $specification A theme specification
     *
     * @return StandardTheme
     */
    protected function parseTheme($themeName, array $specification)
    {
        // Validation
        $this->validateSpec($specification);

        return new StandardTheme(
            $themeName,
            $this->locator->locate($specification['path']),
            $this->parseDetails($specification),
            $this->parseTags($specification)
        );
    }

    /**
     * Replaces parameters hooks with their values
     *
     * @param array $arguments Arguments
     *
     * @return void
     */
    protected function replaceParameters(array &$arguments)
    {
        foreach ($arguments as &$arg) {
            if (is_array($arg)) {
                $this->replaceParameters($arg);
            } elseif (preg_match('/^%(.+)%$/', $arg, $matches)) {
                if (!isset($this->parameters[$matches[1]])) {
                    throw new \InvalidArgumentException(sprintf('The parameter "%s" is not defined in the mapping file.', $matches[1]));
                }

                $arg = $this->parameters[$matches[1]];
            }
        }
    }

    /**
     * Validates an entire mapping file
     *
     * @param array  $content YAML file content
     * @param string $file    A mapping file
     *
     * @return void
     */
    protected function validate(array $content, $file)
    {
        if (!is_array($content)) { // Or a file has an illegal type
            throw new \UnexpectedValueException(sprintf('The return value must be of the YAML array type in the mapping file "%s".', $file));
        } elseif (!array_key_exists('themes', $content)) {
            throw new \InvalidArgumentException(sprintf('There is missing "themes" node in the mapping file "%s".', $file));
        }
    }

    /**
     * Validates a theme specification
     *
     * @param  array $specification A specification
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function validateSpec(array $specification)
    {
        if (!isset($specification['path'])) {
            throw new \InvalidArgumentException('There is missing "path" parameter in a theme specification.');
        }
        if (true == $keys = array_diff(array_keys($specification), array('tags', 'path', 'details'))) {
            throw new \InvalidArgumentException(sprintf('The parameters "%s" are illegal in the theme specification.', implode(', ', $keys)));
        }
    }
}
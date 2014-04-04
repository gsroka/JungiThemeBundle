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

use Jungi\ThemeBundle\Core\Details;

/**
 * LoaderUtils provides some useful methods used for eg. in the XmlFileLoader and YamlFileLoader
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class LoaderUtils
{
    /**
     * Creates a Tag instance
     *
     * @param string $class A class
     * @param mixed  $args  Argument(s) to be passed to a tag (optional)
     *
     * @return TagInterface
     */
    public static function createTag($class, $args = null)
    {
        $class = '\\' . ltrim($class, '\\');
        if (!class_exists($class)) {
            $class = '\\Jungi\\ThemeBundle\\Tag' . $class;
            if (!class_exists($class)) {
                throw new \RuntimeException(sprintf('The tag "%s" is not exist.', $class));
            }
        }

        $reflection = new \ReflectionClass($class);
        if (!$reflection->implementsInterface('Jungi\ThemeBundle\Tag\TagInterface')) {
            throw new \InvalidArgumentException(sprintf('The tag with class "%s" should implement "Jungi\ThemeBundle\Tag\TagInterface".', $class));
        }

        if ($args) {
            if (!is_array($args)) {
                return $reflection->newInstance($args);
            }

            return $reflection->newInstanceArgs($args);
        }

        return $reflection->newInstance();
    }

    /**
     * Creates Details instance
     *
     * Format of the $data variable should looks like following:
     * array(
     *  'author.name' => 'foo',
     *  'author.email' => 'foo@boo.com',
     *  'name' => 'foo'
     *  // ...
     *  key => value
     * )
     *
     * @param array $data Data
     *
     * @return Details
     *
     * @throws \InvalidArgumentException If in a given array is placed invalid key
     */
    public static function createDetails(array $data)
    {
        $validKeys = array(
            'author.name',
            'author.www',
            'author.email',
            'name',
            'description',
            'version',
            'license'
        );
        array_walk($data, function($val, $key) use($validKeys) {
            if (!in_array($key, $validKeys)) {
                throw new \InvalidArgumentException(sprintf('The key "%s" for Details instance is invalid.', $key));
            }
        });

        $property = function ($name) use ($data) {
            return isset($data[$name]) ? $data[$name] : null;
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
}
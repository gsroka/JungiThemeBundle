<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Mapping;

use Symfony\Component\Config\Util\XmlUtils;

/**
 * SimpleXMLElement class.
 * It's originates from Symfony\Component\DependencyIncjetion\SimpleXMLElement
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Piotr Kugla <piku235@gmail.com>
 */
class SimpleXMLElement extends \SimpleXMLElement
{
    /**
     * Converts an attribute as a PHP type.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getAttributeAsPhp($name)
    {
        return self::phpize($this[$name]);
    }

    /**
     * Returns arguments as valid PHP types.
     *
     * @param string  $name
     * @param Boolean $lowercase
     *
     * @return mixed
     */
    public function getArgumentsAsPhp($name, $lowercase = true)
    {
        $arguments = array();
        foreach ($this->$name as $arg) {
            if (isset($arg['name'])) {
                $arg['key'] = (string) $arg['name'];
            }
            $key = isset($arg['key']) ? (string) $arg['key'] : (!$arguments ? 0 : max(array_keys($arguments)) + 1);

            switch ($arg['type']) {
                case 'collection':
                    $arguments[$key] = $arg->getArgumentsAsPhp($name, false);
                    break;
                case 'string':
                    $arguments[$key] = (string) $arg;
                    break;
                case 'constant':
                    $const = defined((string) $arg) ? (string) $arg : '\\Jungi\\ThemeBundle\\Tag\\' . (string) $arg;
                    if (!defined($const)) {
                        throw new \RuntimeException(sprintf('The constant "%s" is not exist.', (string) $arg));
                    }

                    $arguments[$key] = constant($const);
                    break;
                default:
                    $arguments[$key] = self::phpize($arg);
            }
        }

        return $arguments;
    }

    /**
     * Converts an xml value to a PHP type.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function phpize($value)
    {
        return XmlUtils::phpize($value);
    }
}

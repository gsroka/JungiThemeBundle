<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tag\Core;

use Jungi\ThemeBundle\Tag\TagInterface;

/**
 * TagCollection provides extra functions for flexible operations on tags
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class TagCollection implements \IteratorAggregate, \Countable
{
	/**
	 * @var TagInterface[]
	 */
	protected $tags;

	/**
	 * Constructor
	 *
	 * @param TagInterface[] $tags Tags (optional)
	 *
	 * @throws \InvalidArgumentException If one of tags is not a Tag instance
	 */
	public function __construct(array $tags = array())
	{
		$this->tags = array();
		foreach ($tags as $tag) {
			if (!$tag instanceof TagInterface) {
				throw new \InvalidArgumentException('The one of tags is not a Tag instance.');
			}

			$this->tags[$tag->getType()] = $tag;
		}
	}

	/**
	 * Returns the iterator with all tags in the collection
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->tags);
	}

	/**
	 * (non-PHPdoc)
	 * @see Countable::count()
	 */
	public function count()
	{
		return count($this->tags);
	}

	/**
	 * Returns a tag by a given type
	 *
	 * @param string $type A type
	 *
	 * @return TagInterface
	 *
	 * @throws \RuntimeException
	 */
	public function get($type)
	{
		if (!isset($this->tags[$type])) {
			throw new \RuntimeException(sprintf('A tag of the type "%s" can not be found in the collection.', $type));
		}

		return $this->tags[$type];
	}

	/**
	 * Checks if a given tag type exists
	 *
	 * Be careful, because it ONLY looks for a given tag type
	 * and it does not check if it's EQUAL to a found tag
	 *
	 * @param string|array $types A type or types
	 *
	 * @return bool
	 */
	public function has($types)
	{
	    foreach ((array) $types as $type) {
	        if (!isset($this->tags[$type])) {
	            return false;
	        }
	    }

		return true;
	}

	/**
	 * Checks if a given tag or collection of tags exists
	 * and if they are EQUAL to the found tag\tags
	 *
	 * @param TagInterface|TagInterface[] $tags A one tag or collection of tags
	 *
	 * @return bool
	 *
	 * @throws \InvalidArgumentException If a given tag\tags has bad type
	 */
	public function contains($tags)
	{
	    if (!is_array($tags)) {
	        $tags = array($tags);
	    }

	    foreach ($tags as $tag) {
	        if (!$tag instanceof TagInterface) {
	            throw new \InvalidArgumentException('Only TagInterface instances are allowed.');
	        } elseif (!isset($this->tags[$tag->getType()]) || !$tag->isEqual($this->tags[$tag->getType()])) {
	            return false;
	        }
	    }

		return true;
	}
}
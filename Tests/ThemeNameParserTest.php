<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tests;

use Jungi\ThemeBundle\Exception\ThemeNotFoundException;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Jungi\ThemeBundle\Core\ThemeReference;
use Jungi\ThemeBundle\Core\ThemeNameParser;

/**
 * ThemeNameParserTest
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ThemeNameParserTest extends TestCase
{
	/**
	 * @var ThemeNameParser
	 */
	private $parser;

	/**
	 * Sets up the environment
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
		$kernel
            ->expects($this->any())
            ->method('getBundle')
            ->will($this->returnCallback(function ($bundle) {
                if (in_array($bundle, array('JungiTestBundle', 'SensioFooBundle', 'SensioCmsFooBundle', 'FooBundle'))) {
                    return true;
                }

                throw new \InvalidArgumentException();
            }))
        ;

        $holder = $this->getMock('Jungi\ThemeBundle\Core\ThemeHolderInterface');
        $holder
        	->expects($this->any())
        	->method('getTheme')
        	->will($this->returnValue($this->createThemeMock('Foo')))
        ;
		$this->parser = new ThemeNameParser($holder, $kernel);
	}

	/**
	 * Tear down
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		$this->parser = null;
	}

	/**
	 * Tests the parse method with valid examples
	 *
     * @dataProvider getValidLogicalNames
     */
    public function testParseValidName($name, $ref)
    {
        $template = $this->parser->parse($name);

        $this->assertEquals($template->getLogicalName(), $ref->getLogicalName());
    }

    /**
     * @dataProvider      getInvalidLogicalNames
     * @expectedException \InvalidArgumentException
     */
    public function testParseInvalidName($name)
    {
        $this->parser->parse($name);
    }

    /**
     * The data provider
     *
     * @return array
     */
    public function getInvalidLogicalNames()
    {
        return array(
            array('BarBundle:Post:index.html.php'),
            array('FooBundle:Post:index'),
            array('FooBundle:Post'),
            array('FooBundle:Post:foo:bar'),
        );
    }

	/**
	 * The data provider
	 *
	 * @return array
	 */
	public function getValidLogicalNames()
	{
		return array(
			array('FooBundle:Default:index.html.twig', new ThemeReference(new TemplateReference('FooBundle', 'Default', 'index', 'html', 'twig'), 'Foo')),
			array('JungiTestBundle::index.html.twig', new ThemeReference(new TemplateReference('JungiTestBundle', null, 'index', 'html', 'twig'), 'Foo')),
			array('::index.html.twig', new ThemeReference(new TemplateReference(null, null, 'index', 'html', 'twig'), 'Foo')),
		    array(':FooBundle:index.html.twig', new ThemeReference(new TemplateReference(null, 'FooBundle', 'index', 'html', 'twig'), 'Foo'))
		);
	}
}
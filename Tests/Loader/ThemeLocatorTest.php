<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Tests\Loader;

use Jungi\ThemeBundle\Loader\ThemeLocator;
use Jungi\ThemeBundle\Core\ThemeReference;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Jungi\ThemeBundle\Tests\TestCase;
use Jungi\ThemeBundle\Core\ThemeManager;

/**
 * A theme locator test case
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class ThemeLocatorTest extends TestCase
{
    /**
     * @var ThemeManagerInterface
     */
    private $manager;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->manager = new ThemeManager(array($this->createThemeMock('Foo', '/foo/theme/path')));
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        $this->manager = null;
    }

    /**
     * Tests on valid theme reference
     */
    public function testValidThemeReference()
    {
        $template = new ThemeReference(new TemplateReference('bundle', 'controller', 'name', 'format', 'engine'), 'Foo');

        $fileLocator = $this->getFileLocator();
        $fileLocator
            ->expects($this->once())
            ->method('locate')
            ->will($this->returnArgument(0))
        ;

        $locator = new ThemeLocator($this->manager, $fileLocator);

        $this->assertEquals('/foo/theme/path/bundle/controller/name.format.engine', $locator->locate($template));
    }

    /**
     * Tests when a given theme is not exist
     * @expectedException \RuntimeException
     */
    public function testWhenThemeIsNotExist()
    {
        $template = new ThemeReference(new TemplateReference('bundle', 'controller', 'name', 'format', 'engine'), 'NonExist');
        $locator = new ThemeLocator($this->manager, $this->getFileLocator());

        $locator->locate($template);
    }

    /**
     * Tests on missing theme files
     * Should be used the parent locate()
     */
    public function testMissingThemeFiles()
    {
        $template = new ThemeReference(new TemplateReference('bundle', 'controller', 'name', 'format', 'engine'), 'Foo');

        $fileLocator = $this->getFileLocator();
        $fileLocator
            ->expects($this->exactly(2))
            ->method('locate')
            ->will($this->returnCallback(function ($arg) use ($template) {
                if ($arg == '/foo/theme/path/bundle/controller/name.format.engine') {
                    throw new \InvalidArgumentException('The file was kidnapped, hehe.');
                } elseif ($arg == $template->getOrigin()->getPath()) {
                    return '/path/to/template';
                }
            }));

        $locator = new ThemeLocator($this->manager, $fileLocator);

        $this->assertEquals('/path/to/template', $locator->locate($template));
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Bundle\FrameworkBundle\Tests\Templating\Loader\TemplateLocatorTest::testLocateATemplate()
     */
    public function testLocateATemplate()
    {
        $template = new TemplateReference('bundle', 'controller', 'name', 'format', 'engine');

        $fileLocator = $this->getFileLocator();

        $fileLocator
            ->expects($this->once())
            ->method('locate')
            ->with($template->getPath())
            ->will($this->returnValue('/path/to/template'))
        ;

        $locator = new ThemeLocator($this->manager, $fileLocator);

        $this->assertEquals('/path/to/template', $locator->locate($template));
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Bundle\FrameworkBundle\Tests\Templating\Loader\TemplateLocatorTest::testThrowsExceptionWhenTemplateNotFound()
     */
    public function testThrowsExceptionWhenTemplateNotFound()
    {
        $template = new TemplateReference('bundle', 'controller', 'name', 'format', 'engine');

        $fileLocator = $this->getFileLocator();

        $errorMessage = 'FileLocator exception message';

        $fileLocator
            ->expects($this->once())
            ->method('locate')
            ->will($this->throwException(new \InvalidArgumentException($errorMessage)))
        ;

        $locator = new ThemeLocator($this->manager, $fileLocator);

        try {
            $locator->locate($template);
            $this->fail('->locate() should throw an exception when the file is not found.');
        } catch (\InvalidArgumentException $e) {
            $this->assertContains(
                $errorMessage,
                $e->getMessage(),
                'TemplateLocator exception should propagate the FileLocator exception message'
            );
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Bundle\FrameworkBundle\Tests\Templating\Loader\TemplateLocatorTest::testThrowsExceptionWhenTemplateNotFound()
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsAnExceptionWhenTemplateIsNotATemplateReferenceInterface()
    {
        $locator = new ThemeLocator($this->manager, $this->getFileLocator());
        $locator->locate('template');
    }

    protected function getFileLocator()
    {
        return $this
            ->getMockBuilder('Symfony\Component\Config\FileLocator')
            ->setMethods(array('locate'))
            ->setConstructorArgs(array('/path/to/fallback'))
            ->getMock()
        ;
    }
}

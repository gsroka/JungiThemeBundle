<?php

use Jungi\ThemeBundle\Core\StandardTheme;
use Jungi\ThemeBundle\Core\Details;
use Jungi\ThemeBundle\Tests\Fixtures\Tag\Own;
use Jungi\ThemeBundle\Tag;
use Jungi\ThemeBundle\Tag\Core\TagCollection;

$manager->addTheme(new StandardTheme(
    'foo_1',
    $locator->locate('@JungiFooBundle/Resources/theme'),
    new Details('A fancy theme', '1.0.0', '<i>foo desc</i>', 'MIT', 'piku235', 'piku235@gmail.com', 'http://test.pl'),
    new TagCollection(array(
        new Tag\DesktopDevices(),
        new Tag\MobileDevices(array('iOS', 'AndroidOS'), Tag\MobileDevices::MOBILE),
        new Own('test')
    ))
));
<?phpuse Jungi\ThemeBundle\Core\StandardTheme;use Jungi\ThemeBundle\Core\Details;$theme = new StandardTheme('zoo', $locator->locate('@JungiMainThemeBundle/Resources/theme'), new Details('zoothemekek', '1.0.0', 'a taki', 'GPL', 'piku234', 'piku235@gmail.com'));$manager->addTheme($theme);
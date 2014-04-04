JungiThemeBundle
================

The JungiThemeBundle is a powerful theme engine designed for the Symfony2. It provides flexible and smart themes
which have so-called tags. Simply these tags allows you for eg. select an appropriate theme (mobile or desktop version)
for a current request (I will cover about it later). You can do with tags whatever you want, there is no limit. Of course
the abilities of this bundle not end here. Read the docs if you want to know more.

[![Build Status](https://travis-ci.org/piku235/JungiThemeBundle.svg?branch=master)](https://travis-ci.org/piku235/JungiThemeBundle)

**The most useful features:**

* Themes with tags support
* The possibility of change the current theme
* Smart theme selecting based on a request
* Flexible themes manage
* Theme mappings (xml, php, yaml)
* Themes validation

Documentation
-------------

All documentations will be listed in the `Resources/doc` directory

[Read the master documentation](https://github.com/piku235/JungiThemeBundle/tree/master/Resources/doc/index.md)

Examples
--------

JungiSimpleEnvironmentBundle

JungiSimpleThemeBundle

Installation
------------

### Step 1: Install JungiThemeBundle using composer

Add JungiThemeBundle in your composer.json:

```js
{
    "require": {
        "jungi/theme-bundle": "~1.0"
    }
}
```

Or run the following command in your project:

```bash
$ php composer.phar require jungi/theme-bundle "~1.0"
```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Jungi\ThemeBundle\JungiThemeBundle(),
    );
}
```

Configuration
-------------

The bundle comes with a default set of configuration which is listed bellow:

```yaml
# app/config/config.yml
jungi_theme:
    empty_theme: true
```

This step is optional, but I guess you'll be not using the **empty_theme** so set the **empty_theme** to false.

```yaml
# app/config/config.yml
jungi_theme:
    empty_theme: false
```

The purpose of the empty theme is only to ensure that project will not blow up (:, but seriously to not execute an exception
after the installation is completed.

License
-------

This bundle is under the [MIT license](https://github.com/piku235/JungiThemeBundle/blob/master/Resources/meta/LICENSE)



<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Selector;

use Jungi\ThemeBundle\Exception\NullThemeException;
use Jungi\ThemeBundle\Exception\ThemeNotFoundException;
use Jungi\ThemeBundle\Core\ThemeManagerInterface;
use Jungi\ThemeBundle\Resolver\ThemeResolverInterface;
use Jungi\ThemeBundle\Core\ThemeHolderInterface;
use Jungi\ThemeBundle\Selector\Event\ResolvedThemeEvent;
use Jungi\ThemeBundle\Exception\ThemeValidationException;
use Jungi\ThemeBundle\Event\ThemeEvent;
use Jungi\ThemeBundle\Core\ThemeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Jungi\ThemeBundle\Validation\ValidationUtils;
use Jungi\ThemeBundle\Validation\ValidatorHelper;

/**
 * StandardThemeSelector generally uses a theme resolver to obtain an appropriate theme for the request
 *
 * But not only theme resolvers decides which theme will be used, the resolved theme can be easily changed
 * in the ResolvedThemeEvent. If some theme will not pass the validation process or cause any other problem
 * then a fallback theme resolver will look for a theme
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class StandardThemeSelector implements ThemeSelectorInterface
{
    /**
     * @var ThemeManagerInterface
     */
    protected $manager;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var ThemeResolverInterface
     */
    protected $resolver;

    /**
     * @var ThemeHolderInterface
     */
    protected $holder;

    /**
     * @var ThemeResolverInterface
     */
    protected $fallback;

    /**
     * @var ValidatorHelper
     */
    protected $validator;

    /**
     * @var boolean
     */
    protected $ignoreNullThemes;

    /**
     * Constructor
     *
     * @param ThemeManagerInterface    $manager          A theme manager
     * @param ThemeHolderInterface     $holder           A theme holder
     * @param EventDispatcherInterface $dispatcher       An event dispatcher
     * @param ThemeResolverInterface   $resolver         A theme resolver
     * @param boolean                  $ignoreNullThemes Whether to ignore empty theme names (optional)
     * @param ValidatorHelper          $validator        A validator helper (optional)
     * @param ThemeResolverInterface   $fallback         A fallback theme resolver (optional)
     */
    public function __construct(ThemeManagerInterface $manager, ThemeHolderInterface $holder, EventDispatcherInterface $dispatcher, ThemeResolverInterface $resolver, $ignoreNullThemes = false, ValidatorHelper $validator = null, ThemeResolverInterface $fallback = null)
    {
        $this->manager = $manager;
        $this->dispatcher = $dispatcher;
        $this->resolver = $resolver;
        $this->holder = $holder;
        $this->fallback = $fallback;
        $this->validator = $validator;
        $this->ignoreNullThemes = $ignoreNullThemes;
    }

    /**
     * Sets a validator helper
     *
     * @param ValidatorHelper $helper A validator helper
     *
     * @return void
     */
    public function setValidatorHelper(ValidatorHelper $helper)
    {
        $this->validator = $helper;
    }

    /**
     * Sets a fallback theme resolver
     *
     * @param ThemeResolverInterface $resolver A fallback theme resolver
     *
     * @return void
     */
    public function setFallback(ThemeResolverInterface $resolver)
    {
        $this->fallback = $resolver;
    }

    /**
     * Sets whether to ignore null theme names
     *
     * @param boolean $cond True or false
     *
     * @return void
     */
    public function setIgnoreNullThemes($cond)
    {
        $this->ignoreNullThemes = $cond;
    }

    /**
     * (non-PHPdoc)
     * @see \Jungi\ThemeBundle\Selector\ThemeSelectorInterface::select()
     *
     * @throws \Exception If something goes wrong
     */
    public function select(Request $request)
    {
        try {
            $theme = $this->matchTheme($request);
        } catch (NullThemeException $e) {
            if ($this->ignoreNullThemes) {
                return;
            }

            throw $e;
        }

        // The event
        $event = new ThemeEvent($theme, $this->manager, $request);

        // Dispatch the event
        $this->dispatcher->dispatch(ThemeSelectorEvents::PRE_SET, $event);

        // If everything is ok set a theme to the holder
        $this->holder->setTheme($theme);

        // Dispatch the event
        $this->dispatcher->dispatch(ThemeSelectorEvents::POST_SET, $event);
    }

    /**
     * Matches the theme for a given request
     *
     * If everything will go well the theme obtained from a leading theme resolver
     * will be returned otherwise theme from the fallback theme resolver will be returned
     *
     * @param Request $request A request
     *
     * @return ThemeInterface
     *
     * @throws \Exception If something goes wrong
     */
    protected function matchTheme(Request $request)
    {
        try {
            $theme = $this->getStandardTheme($request);
        } catch (\Exception $e) {
            // Use a fallback theme?
            if (null === $this->fallback) {
                throw $e;
            }

            $theme = $this->getFallbackTheme($request);
        }

        return $theme;
    }

    /**
     * Returns the standard matched theme for a given request
     * Additionally the theme is validated if the validator was set
     *
     * @param Request $request A request instance
     *
     * @return ThemeInterface
     *
     * @throws ThemeValidationException When validation will fail
     * @throws NullThemeException
     */
    protected function getStandardTheme(Request $request)
    {
        if (null === $themeName = $this->resolver->resolveThemeName($request)) {
            throw new NullThemeException(sprintf("The theme for the request '%s' can not be found.", $request->getPathInfo()));
        }

        // Theme
        $theme = $this->retrieveTheme($themeName, $request);

        // Is the theme valid?
        if (null !== $this->validator) {
            $violations = $this->validator->validate($theme, $request);
            if (count($violations)) {
                throw ValidationUtils::validationException(sprintf('The theme "%s" has failed validation process.', $theme->getName()), $violations);
            }
        }

        return $theme;
    }

    /**
     * Returns the fallback theme for a given request
     *
     * @param Request $request A request instance
     *
     * @return ThemeInterface
     *
     * @throws \RuntimeException If a fallback theme resolver was not set
     * @throws NullThemeException
     */
    protected function getFallbackTheme(Request $request)
    {
        if (null === $this->fallback) {
            throw new \RuntimeException("The fallback theme resolver was not set.");
        }
        if (null === $themeName = $this->fallback->resolveThemeName($request)) {
            throw new NullThemeException(sprintf("The fallback theme for the request '%s' can not be found.", $request->getPathInfo()));
        }

        return $this->retrieveTheme($themeName, $request);
    }

    /**
     * Retrieves the theme instance based on a given theme name
     *
     * @param string  $themeName A theme Name
     * @param Request $request   A request instance
     *
     * @return ThemeInterface
     *
     * @throws \RuntimeException When a theme name is not exist in the theme manager
     * @throws \InvalidArgumentException If a given theme name is blank and ignoreEmptyThemeNames is false
     */
    protected function retrieveTheme($themeName, Request $request)
    {
        try {
            $theme = $this->manager->getTheme($themeName);
        } catch (ThemeNotFoundException $e) {
            throw new \RuntimeException('The theme selector could not fetch the theme, see the previous exception.', null, $e);
        }

        // Dispatch the event
        $event = new ResolvedThemeEvent($theme, $this->manager, $request);
        $this->dispatcher->dispatch(ThemeSelectorEvents::RESOLVED_THEME, $event);

        // Grab a theme from the event
        return $event->getTheme();
    }
}

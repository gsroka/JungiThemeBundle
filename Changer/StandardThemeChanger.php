<?php

/*
 * This file is part of the JungiThemeBundle package.
 *
 * (c) Piotr Kugla <piku235@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jungi\ThemeBundle\Changer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Jungi\ThemeBundle\Core\ThemeManagerInterface;
use Jungi\ThemeBundle\Exception\ThemeNotFoundException;
use Jungi\ThemeBundle\Exception\ThemeValidationException;
use Jungi\ThemeBundle\Event\ThemeEvent;
use Jungi\ThemeBundle\Resolver\ResponseWriterInterface;
use Symfony\Component\Validator\ValidatorInterface;
use Jungi\ThemeBundle\Resolver\ThemeResolverInterface;
use Jungi\ThemeBundle\Validation\ValidationUtils;
use Jungi\ThemeBundle\Core\ThemeInterface;
use Jungi\ThemeBundle\Core\ThemeHolderInterface;

/**
 * StandardThemeChanger
 *
 * @author Piotr Kugla <piku235@gmail.com>
 */
class StandardThemeChanger implements ThemeChangerInterface
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
     * @var ThemeHolderInterface
     */
    protected $holder;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var ThemeResolverInterface
     */
    protected $resolver;

    /**
     * Constructor
     *
     * @param ThemeManagerInterface    $manager    A theme manager
     * @param ThemeHolderInterface     $holder     A theme holder
     * @param ThemeResolverInterface   $resolver   A theme resolver
     * @param EventDispatcherInterface $dispatcher An event dispatcher
     * @param ValidatorInterface       $validator  A validator (optional)
     */
    public function __construct(ThemeManagerInterface $manager, ThemeHolderInterface $holder, ThemeResolverInterface $resolver, EventDispatcherInterface $dispatcher, ValidatorInterface $validator = null)
    {
        $this->manager = $manager;
        $this->holder = $holder;
        $this->validator = $validator;
        $this->dispatcher = $dispatcher;
        $this->resolver = $resolver;
    }

    /**
     * Sets a validator
     *
     * @param ValidatorInterface $validator A validator helper
     *
     * @return void
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * (non-PHPdoc)
     * @see \Jungi\ThemeBundle\Core\ThemeChangerInterface::change()
     */
    public function change($theme, Request $request)
    {
        if (!$theme instanceof ThemeInterface) {
            try {
                $theme = $this->manager->getTheme($theme);
            } catch (ThemeNotFoundException $e) {
                throw new \RuntimeException('The theme can not be changed, see the previous exception.', null, $e);
            }
        }

        // Dispatch the event
        $event = new ThemeEvent($theme, $this->manager, $request);
        $this->dispatcher->dispatch(ThemeChangerEvents::PRE_SET, $event);

        // Is valid?
        if (null !== $this->validator) {
            $violations = $this->validator->validate($theme);
            if (count($violations)) {
                throw ValidationUtils::validationException(sprintf('The theme "%s" has failed validation process.', $theme->getName()), $violations);
            }
        }

        // If everything is ok, change the current theme
        $this->holder->setTheme($theme);
        $this->resolver->setThemeName($theme->getName(), $request);

        // Dispatch the event
        $this->dispatcher->dispatch(ThemeChangerEvents::POST_SET, $event);
    }

    /**
     * (non-PHPdoc)
     * @see \Jungi\ThemeBundle\Changer\ThemeChangerInterface::writeResponse()
     */
    public function writeResponse(Request $request, Response $response)
    {
        if (!$this->resolver instanceof ResponseWriterInterface) {
            return;
        }

        $this->resolver->writeResponse($this->resolver->resolveThemeName($request), $response);
    }
}

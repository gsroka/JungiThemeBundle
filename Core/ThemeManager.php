<?php/* * This file is part of the JungiThemeBundle package. * * (c) Piotr Kugla <piku235@gmail.com> * * For the full copyright and license information, please view the LICENSE * file that was distributed with this source code. */namespace Jungi\ThemeBundle\Core;use Jungi\ThemeBundle\Exception\ThemeNotFoundException;use Jungi\ThemeBundle\Core\ThemeInterface;/** * ThemeManager manages the all themes in the system * * @author Piotr Kugla <piku235@gmail.com> */class ThemeManager implements ThemeManagerInterface{    /**     * @var ThemeInterface[]     */    protected $themes;    /**     * Constructor     *     * @param ThemeInterface[] $themes Themes (optional)     */    public function __construct($themes = array())    {        $this->themes = array();        foreach ($themes as $theme) {            $this->addTheme($theme);        }    }    /**     * (non-PHPdoc)     * @see \Jungi\ThemeBundle\Core\ThemeManagerInterface::addTheme()     */    public function addTheme(ThemeInterface $theme)    {        $this->themes[] = $theme;    }    /**     * (non-PHPdoc)     * @see \Jungi\ThemeBundle\Core\ThemeManagerInterface::hasTheme()     */    public function hasTheme($name)    {        foreach ($this->themes as $theme) {            if ($theme->getName() == $name) {                return true;            }        }        return false;    }    /**     * (non-PHPdoc)     * @see \Jungi\ThemeBundle\Core\ThemeManagerInterface::getTheme()     */    public function getTheme($name)    {        foreach ($this->themes as $theme) {            if ($theme->getName() == $name) {                return $theme;            }        }        throw new ThemeNotFoundException($name);    }    /**     * Returns all themes which have got the given tags     *     * @param  TagInterface[]|Tag $tags A one tag or tags     * @param  bool $first Return a first matched theme? (optional)     *     * @return ThemeInterface[]     */    public function getThemesWithTags($tags, $first = false)    {        $result = array();        foreach ($this->themes as $theme) {            if ($theme->getTags()->contains($tags)) {                if ($first) {                    return $theme;                }                $result[] = $theme;            }        }        return $first ? null : $result;    }    /**     * (non-PHPdoc)     * @see \Jungi\ThemeBundle\Core\ThemeManagerInterface::getThemes()     */    public function getThemes()    {        return $this->themes;    }}
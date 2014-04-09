<?php/* * This file is part of the JungiThemeBundle package. * * (c) Piotr Kugla <piku235@gmail.com> * * For the full copyright and license information, please view the LICENSE * file that was distributed with this source code. */namespace Jungi\ThemeBundle\Validation\Requirement;use Jungi\ThemeBundle\Validation\Requirement\ValidationRequirementInterface;use Jungi\ThemeBundle\Core\ThemeInterface;use Symfony\Component\HttpFoundation\Request;use Jungi\ThemeBundle\Resolver\ThemeResolverInterface;/** * Decides about validation depending on a theme resolver * * This class can be used for eg. to validate CookieThemeResolver for which * we knows that the value of a cookie is stored in a client browser which can be * easly changed. * * @author Piotr Kugla <piku235@gmail.com> */class ThemeResolver implements ValidationRequirementInterface{    /**     * @var array     */    protected $list;    /**     * @var ThemeResolverInterface     */    protected $resolver;    /**     * Constructor     *     * @param ThemeResolverInterface $resolver A theme resolver for check     */    public function __construct(ThemeResolverInterface $resolver)    {        $this->list = array();        $this->resolver = $resolver;    }    /**     * Adds a theme resolver for whom will be performed validation     *     * @param ThemeResolverInterface|string $class An object or name of a class     *     * @return void     *     * @throws \InvalidArgumentException When the $class argument will be wrong     */    public function add($class)    {        if ($class instanceof ThemeResolverInterface) {            $class = get_class($class);        } else if (is_string($class)) {            $ref = new \ReflectionClass($class);            if (!$ref->implementsInterface('Jungi\ThemeBundle\Resolver\ThemeResolverInterface')) {                throw new \InvalidArgumentException(sprintf('The given class "%s" must implement the ThemeResolverInterface.', $class));            }        } else {            throw new \InvalidArgumentException('The $class variable should be a class or an object.');        }        $this->list[] = $class;    }    /**     * Returns theme resolvers which should be validated     *     * @return array     */    public function getAll()    {        return $this->list;    }    /**     * Checks if can validate a given theme depending on the theme resolver     *     * @param ThemeInterface $theme A theme (unused)     * @param Request $request A request instance (unused)     *     * @return bool     */    public function canValidate(ThemeInterface $theme, Request $request)    {        return !in_array(get_class($this->resolver), $this->list);    }}
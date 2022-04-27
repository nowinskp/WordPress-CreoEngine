<?php

namespace Wpce\Components\Abstracts;

use Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Utils\Get;

abstract class DefaultComponent {

  private $rootNamePrefix = 'c-';
  private $classSeparator = '--';

  /**
   * Name of the component that will be applied to its root class
   * and that can be used to construct BEM-like patterns.
   *
   * @var string
   */
  private string $rootName;


  /**
   * Array of root css classes, to be later applied to component's
   * root HTML element by using `getRootClasses` method.
   *
   * @var array
   */
  private array $rootClasses;


  /**
   * Reflection class of the child class, used for eg. directory
   * and template name matching.
   *
   * @var \ReflectionClass
   */
  protected \ReflectionClass $childRef;


  /**
   * Constructor - accepts component props and sets core data.
   *
   * By default, a `rootName` optional prop is always accepted to allow
   * for overwriting the default, which is `<rootNamePrefix><ComponentClassName>`,
   * eg. `c-Button`.
   *
   * Two global constants can be used to overwrite the used default rootNamePrefix
   * and separators of class name parts:
   * - `WPCE_COMPONENT_ROOT_NAME_PREFIX`
   * - `WPCE_COMPONENT_CLASS_SEPARATOR `
   *
   * @param array $props
   */
  public function __construct(array $props = []) {
    /**
     * The following names are used for internal component logic and therefore
     * cannot ever be used as prop names.
     */
    $forbiddenPropNames = [
      'rootNamePrefix',
      'classSeparator',
      'childRef',
      'rootClasses',
    ];
    foreach ($forbiddenPropNames as $forbiddenPropName) {
      if (array_key_exists($forbiddenPropName, $props)) {
        throw new Exception("\"$forbiddenPropName\" cannot be used as prop name.");
      }
    }

    $this->childRef = new \ReflectionClass(get_class($this));

    $customProps = $this->configureParseAndExtractBuiltinProps($props);

    $resolver = new OptionsResolver();
    $this->configureProps($resolver, $customProps);
    $this->assignProps($resolver->resolve($customProps));
    $this->parseProps($resolver->resolve($customProps));
  }


  /**
   * Uses OptionsResolver to validate built-in props, then parses them
   * and removes them from original props array.
   *
   * Built-in classes consist of:
   * @property string 'class' custom CSS class to be added to component root HTML tag
   * @property string 'rootName' overwrites default rootName value (inc. rootNamePrefix)
   *
   * @param array $props
   * @return array props without built-in ones
   */
  private function configureParseAndExtractBuiltinProps(array $props) {
    if (defined('WPCE_COMPONENT_ROOT_NAME_PREFIX')) {
      $this->rootNamePrefix = constant('WPCE_COMPONENT_ROOT_NAME_PREFIX');
    }
    if (defined('WPCE_COMPONENT_CLASS_SEPARATOR')) {
      $this->classSeparator = constant('WPCE_COMPONENT_CLASS_SEPARATOR');
    }

    $builtInProps = [
      'class' => Get::in($props, 'class'),
      'rootName' => Get::in($props, 'rootName', $this->rootNamePrefix.$this->childRef->getShortName()),
    ];

    $this->assignProps($builtInProps);
    $this->rootClasses[] = $this->rootName;
    $this->addToRootClasses($this->class, false);

    return array_diff_key($props, $builtInProps);
  }


  /**
   * Uses OptionsResolver to validate props used to construct the component.
   *
   * @param OptionsResolver $resolver OptionsResolver instance
   * @param array $props
   * @return void
   */
  abstract protected function configureProps(OptionsResolver $resolver, array $props);


  /**
   * Assigns every validated prop as component's public property.
   *
   * @param array $props validated component props
   * @return void
   */
  private function assignProps(array $props) {
    foreach ($props as $prop => $value) {
      $this->$prop = $value;
    }
  }


  /**
   * Optional method for parsing already validated and assigned component props.
   * It can be used to eg. overwrite assigned props or set new props based on
   * their values.
   *
   * @param array $props validated component props
   * @return void
   */
  protected function parseProps(array $props) {}


  /**
   * Helper for getting a list of space-separated classes to be applied
   * to root HTML tag of the component.
   *
   * @return string
   */
  public function getRootClasses() {
    return implode(' ', $this->rootClasses);
  }


  /**
   * Helper for adding a new class to root classes array.
   * Will not add a class if its unset.
   *
   * @param string|null $class
   * @param bool $usePrefix whether to prefix class with rootName
   * @return void
   */
  public function addToRootClasses(?string $class, bool $usePrefix = true) {
    $prefixedClass = $usePrefix ? $this->rootName.$this->classSeparator.$class : $class;
    if (isset($class) && !in_array($prefixedClass, $this->rootClasses)) {
      $this->rootClasses[] = $prefixedClass;
    }
  }


  /**
   * Helper for adding a new class to root classes array if a given
   * condition is met. Will not add a class if its unset.
   *
   * Eg. `$this->addToRootClassesIf($props['button'], 'hasButton')`
   *
   * @param mixed $condition condition evaluated to boolean
   * @param string|null $class
   * @param bool $usePrefix whether to prefix class with rootName
   * @return void
   */
  public function addToRootClassesIf($condition, ?string $class, bool $usePrefix = true) {
    if ($condition) {
      $this->addToRootClasses($class, $usePrefix);
    }
  }


  /**
   * Echoes component's HTML code.
   *
   * @return void
   */
  public function renderHtml() {
    echo $this->getHtml();
  }


  /**
    * Gets component's HTML code
    *
    * @return string|null
    */
  abstract public function getHtml();

}

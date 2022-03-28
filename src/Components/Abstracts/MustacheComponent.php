<?php

namespace Wpce\Components\Abstracts;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Content\Template;
use Wpce\Utils\Get;

abstract class MustacheComponent {

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
  private \ReflectionClass $childRef;


  /**
   * Constructor - accepts component props and sets core properties
   * such as rootName and child class ReflectionClass. Runs child's
   * parseProps method to validate and assign passed component props.
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
  public function __construct(array $props) {
    if (defined('WPCE_COMPONENT_ROOT_NAME_PREFIX')) {
      $this->rootNamePrefix = constant('WPCE_COMPONENT_ROOT_NAME_PREFIX');
    }
    if (defined('WPCE_COMPONENT_CLASS_SEPARATOR')) {
      $this->classSeparator = constant('WPCE_COMPONENT_CLASS_SEPARATOR');
    }
    $this->childRef = new \ReflectionClass(get_class($this));
    $this->rootName = Get::in($props, 'rootName', $this->rootNamePrefix.$this->childRef->getShortName());
    $this->rootClasses[] = $this->rootName;

    $resolver = new OptionsResolver();
    $this->configureProps($resolver, $props);
    $this->assignProps($resolver->resolve($props));
    $this->parseProps($resolver->resolve($props));
  }


  /**
   * Uses OptionsResolver to validate props used to construct the component.
   *
   * @param OptionsResolver $resolver OptionsResolver instance
   * @param array $props
   * @return void
   */
  abstract function configureProps(OptionsResolver $resolver, array $props);


  /**
   * Assigns every validated prop as component's public property.
   *
   * @param array $props validated component props
   * @return void
   */
  function assignProps(array $props) {
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
  function parseProps(array $props) {}


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
   * @param array $options
   * @return void
   */
  public function renderHtml() {
    echo static::getHtml();
  }


  /**
    * Gets component's HTML code
    *
    * @param array $options
    * @return string|null
    */
  public function getHtml() {
    return Template::renderComponent($this->childRef->getShortName(), dirname($this->childRef->getFileName()), $this);
  }
}

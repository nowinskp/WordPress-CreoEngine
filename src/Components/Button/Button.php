<?php

namespace Wpce\Components\Button;

use Wpce\Utils\Get;
use Wpce\Components\Abstracts\MustacheComponent;

class Button extends MustacheComponent {

  public $color;
  public $iconHtml;
  public $label;
  public $sublabel;
  public $target;
  public $url;

  /**
   * @param array $props
   * @property string class - optional additional CSS classes
   * @property string label - button label
   * @property string iconHtml - optional icon html
   * @property string color - button color theme
   * @property string url - target url
   *
   * @return string
   */
  public function parseProps(array $props = []) {
    $this->color = Get::in($props, 'color');
    $this->iconHtml = Get::in($props, 'iconHtml');
    $this->label = Get::in($props, 'label');
    $this->sublabel = Get::in($props, 'sublabel');
    $this->url = Get::in($props, 'url');

    $this->addToRootClasses(Get::in($props, 'class'), false);
    $target = Get::in($props, 'target');
    $this->target = Get::stringIf($target, 'target="'.$target.'"');
    $this->colorClass = $this->addToRootClassesIf($this->color, 'color-'.$this->color);
    $this->iconClass = $this->addToRootClassesIf($this->iconHtml, 'hasIcon');
  }

}

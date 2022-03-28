<?php

namespace Wpce\Components\Svg;

use Wpce\Components\Abstracts\StaticComponent;
use Wpce\Content\Html;
use Wpce\Utils\Get;

/** @todo Document the component. */
abstract class Svg extends StaticComponent {

  /**
   * Gets component HTML code
   *
   * @param array $props
   * @property string color SVG path color if in path mode
   * @property string displayMode 'raw' (default) or 'wrapped'
   * @property string imgName SVG image data file name
   * @property string|array size image size, default 'auto'
   * @property string sizeUnit size unit to use, default 'rem'
   * @property string svgClass CSS class applied to SVG element
   * @property string wrapperClass wrapper class, if `displayMode` is set to `wrapped`
   * @property string wrapperTag wrapper HTML tag to be used, if `displayMode` is set to `wrapped`
   */
  public static function getHtml(array $props) {
    $defaultRootName = 'c-'.(new \ReflectionClass(get_called_class()))->getShortName();

    $color = Get::in($props, 'color');
    $displayMode = Get::in($props, 'displayMode', 'raw');
    $imgName = Get::in($props, 'imgName');
    $size = Get::in($props, 'size', 'auto');
    $sizeUnit = Get::in($props, 'sizeUnit', 'rem');
    $svgClass = Get::in($props, 'svgClass', '');
    $rootName = Get::in($props, 'rootName', $defaultRootName);
    $wrapperClass = Get::in($props, 'wrapperClass', '');
    $wrapperTag = Get::in($props, 'wrapperTag', 'i');

    $imgDataPath = static::getSvgImageDataPath($imgName);
    $imgConfig = static::getSvgImagesConfig($imgName);

    try {
      if ($imgDataPath && $imgConfig) {
        $imgData = file_get_contents($imgDataPath, true);
      } else {
        return '';
      }
    } catch(\Exception $e) {
      return '';
    }

    $parsedSize = is_array($size) ? $size : [$size, $size];
    $svgWidth = $parsedSize[0] === 'auto' ? $parsedSize[0] : $parsedSize[0].$sizeUnit;
    $svgHeight = $parsedSize[1] === 'auto' ? $parsedSize[1] : $parsedSize[1].$sizeUnit;

    $svgClassPrefix = Get::stringIf($displayMode === 'raw', $rootName);

    $svgAttrs = [
      'class' => $svgClassPrefix.Get::stringIf($svgClass, ' '.$svgClass),
      'style' => 'min-width: '.$svgWidth.'; max-width: '.$svgWidth.'; width: '.$svgWidth.'; min-height: '.$svgHeight.'; max-height: '.$svgHeight.'; height: '.$svgHeight.';',
      'viewBox' => Get::in($imgConfig, 'viewbox', '0 0 16 16'),
    ];

    if ($imgConfig['type'] === 'path') {
      $appliedColor = $color ?: Get::in($imgConfig, 'defaultColor', 'currentColor');
      $svgContent = '<path d="'.$imgData.'" fill="'.$appliedColor.'" />';
    } else {
      $svgContent = $imgData;
    }

    $svgHtml = Html::getElement('svg', $svgAttrs, $svgContent);

    if ($displayMode === 'raw') {
      return $svgHtml;
    }

    if ($displayMode === 'wrapped') {
      return '<'.$wrapperTag.' class="'.$rootName.Get::stringIf($wrapperClass, ' '.$wrapperClass).'">'.$svgHtml.'</'.$wrapperTag.'>';
    }

    return '';
  }


  /**
   * Gets svg image config file.
   * Meant to be overwritteb with method providing proper path.
   *
   * @param string imgName name of the image config file
   * @return string path to file
   */
  abstract static function getSvgImageDataPath($imgName);


  abstract static function getSvgImagesConfig($imgName);

}

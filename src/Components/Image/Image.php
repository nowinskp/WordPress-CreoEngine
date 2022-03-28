<?php

namespace Wpce\Components\Image;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Components\Abstracts\MustacheComponent;
use Wpce\Content\Html;

class Image extends MustacheComponent {

  /**
   * Get component HTML code
   *
   * @param array $params
   * @property string alt
   * @property string caption
   * @property string imageClass
   * @property string imageHtml - if provided, alt, src and imageClass
   *                  params are skipped
   * @property string photoswipeHeight
   * @property string photoswipeSrc
   * @property string photoswipeWidth
   * @property string src
   * @property string target
   * @property string url
   * @property string wrapperClass
   *
   * @return void
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    $resolver->setDefaults([
      'alt' => null,
      'caption' => null,
      'imageClass' => null,
      'imageHtml' => null,
      'photoswipeHeight' => null,
      'photoswipeSrc' => null,
      'photoswipeWidth' => null,
      'src' => null,
      'target' => null,
      'url' => null,
    ]);
  }

  protected function parseProps(array $props) {
    $photoswipeAttrs = [];
    if ($this->photoswipeSrc && $this->photoswipeWidth && $this->photoswipeHeight) {
      $photoswipeAttrs['data-original-src'] = $this->photoswipeSrc;
      $photoswipeAttrs['data-original-src-width'] = $this->photoswipeWidth;
      $photoswipeAttrs['data-original-src-height'] = $this->photoswipeHeight;
    }

    $imageHtml = $this->imageHtml ?: Html::getElement('img', array_merge([
      'alt' => $this->alt,
      'class' => $this->imageClass,
      'src' => $this->src,
    ], $photoswipeAttrs));

    if ($this->url) {
      $imageHtml = Html::getElement('a', [
        'href' => $this->url,
        'target' => $this->target,
      ], $imageHtml);
    }

    $this->caption = strip_tags($this->caption);
    $this->imageHtml = $imageHtml;
  }

}

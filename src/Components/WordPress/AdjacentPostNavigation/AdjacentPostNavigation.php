<?php

namespace Wpce\Components\WordPress\AdjacentPostNavigation;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Components\Abstracts\MustacheComponent;

class AdjacentPostNavigation extends MustacheComponent {

  /**
   * @param array $params
   * @property string class CSS class name
   * @property array linkNext link element definition
   * @property array linkIndex link element definition
   * @property array linkPrev link element definition
   *
   * @return void
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    $resolver->setDefaults([
      'linkNext' => function (OptionsResolver $resolver) {
        $this->configureLinkResolver($resolver);
      },
      'linkIndex' =>  function (OptionsResolver $resolver) {
        $this->configureLinkResolver($resolver);
      },
      'linkPrev' =>  function (OptionsResolver $resolver) {
        $this->configureLinkResolver($resolver);
      },
    ]);
  }

  /**
   * Common link element definition:
   * @property string labelText label above link
   * @property string linkUrl link target
   * @property string linkText link label
   *
   * @return void
   */
  protected function configureLinkResolver(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'labelText' => null,
      'linkUrl' => null,
      'linkText' => null,
    ]);
    $resolver->setAllowedTypes('labelText', ['null', 'string']);
    $resolver->setAllowedTypes('linkUrl', ['null', 'string']);
    $resolver->setAllowedTypes('linkText', ['null', 'string']);
  }

  /**
   * Sets default props for next/prev posts using
   * get_adjacent_post function.
   *
   * @param array $params
   * @property bool isSameTerm whether post should be in a same taxonomy term
   * @property array|string excludedTerms array or comma-separated list of excluded term IDs
   * @property string taxonomy taxonomy, if isSameTerm is true
   *
   * @return void
   */
  public function setDefaultPropsForPost(array $options) {
    $resolver = new OptionsResolver();
    $resolver->setDefaults([
      'isSameTerm' => false,
      'excludedTerms' => '',
      'taxonomy' => 'category',
      'labelPrev' => null,
      'labelNext' => null,
    ]);

    $resolver->setAllowedTypes('isSameTerm', 'boolean');
    $resolver->setAllowedTypes('excludedTerms', ['string', 'int[]']);
    $resolver->setAllowedTypes('taxonomy', 'string');
    $resolver->setAllowedTypes('labelPrev', ['null', 'string']);
    $resolver->setAllowedTypes('labelNext', ['null', 'string']);

    $options = $resolver->resolve($options);

    $prevPost = get_adjacent_post($options['isSameTerm'], $options['excludedTerms'], true, $options['taxonomy']);
    $nextPost = get_adjacent_post($options['isSameTerm'], $options['excludedTerms'], false, $options['taxonomy']);

    if ($prevPost) {
      $this->linkPrev = [
        'labelText' => $options['labelPrev'],
        'linkUrl' => get_permalink($prevPost),
        'linkText' => get_the_title($prevPost),
      ];
    }
    if ($nextPost) {
      $this->linkNext = [
        'labelText' => $options['labelNext'],
        'linkUrl' => get_permalink($nextPost),
        'linkText' => get_the_title($nextPost),
      ];
    }
  }
}

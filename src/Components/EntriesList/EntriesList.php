<?php

namespace Wpce\Components\EntriesList;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Components\Abstracts\MustacheComponent;
use Wpce\OptionsResolver\AllowedValues;

class EntriesList extends MustacheComponent {

  /**
   * @param array $props
   * @property array 'entries'
   * array of list entries, can be one of the following type:
   * - a child of DefaultComponent class
   * - a HTML string
   * @property string 'noEntriesHtml'
   * HTML of an element to show when list is empty.
   * If not present, empty list will not render at all.
   * @property string 'tag' ul or ol
   *
   * @return void
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    $resolver->setDefaults([
      'entries' => [],
      'noEntriesHtml' => null,
      'tag' => 'ul',
    ]);
    $resolver->setAllowedValues('entries', function($value) {
      return AllowedValues::arrayOfComponentsOrStrings($value);
    });
    $resolver->setAllowedTypes('noEntriesHtml', ['null', 'string']);
    $resolver->setAllowedValues('tag', ['ol', 'ul']);
  }

  protected function parseProps(array $props) {
    $this->hasEntries = count($this->entries) > 0;
  }

}

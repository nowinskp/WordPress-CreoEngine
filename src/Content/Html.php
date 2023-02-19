<?php

namespace Wpce\Content;

class Html {

  const HTML_BOOLEAN_ATTRIBUTES = [
    'allowfullscreen',
    'allowpaymentrequest',
    'async',
    'autofocus',
    'autoplay',
    'checked',
    'controls',
    'default',
    'defer',
    'disabled',
    'formnovalidate',
    'hidden',
    'ismap',
    'itemscope',
    'loop',
    'multiple',
    'muted',
    'nomodule',
    'novalidate',
    'open',
    'playsinline',
    'readonly',
    'required',
    'reversed',
    'selected',
    'truespeed',
  ];

  /**
   * Returns a string of {atribute}="{value}" pairs.
   *
   * Skips attributes with `null` values.
   * For boolean attributes, skips attributes with falsy values.
   *
   * @param array $attrs
   * @return string
   */
  static function getAttrsString(array $attrs = []) {
    $attributesString = '';

    foreach ($attrs as $name => $value) {
      if (in_array(strtolower($name), self::HTML_BOOLEAN_ATTRIBUTES)) {
        if ($value) {
          $attributesString .= " $name";
        }
      } else if ($value !== null) {
        $value = ('href' === $name) ? esc_url($value) : esc_attr($value);
        $attributesString .= " $name=\"$value\"";
      }
    }

    return $attributesString;
  }


	/**
	 * Returns an HTML element. Automatically make it self-closed
   * if content is explicitly set as null.
	 *
	 * @param string $tag
	 * @param array  $attrs
	 * @param string|null $content
	 *
	 * @return string
	 */
	static function getElement(string $tag, array $attrs = [], ?string $content = null) {
    $html = "<$tag";
		$html .= self::getAttrsString($attrs);
    if ($content === null) {
      $html .= ' />';
    } else {
      $html .= ">$content</$tag>";
    }
		return $html;
	}


	/**
	 * Echoes an HTML element. Automatically make it self-closed
   * if content is explicitly set as null.
	 *
	 * @param string $tag
	 * @param array  $attrs
	 * @param string|null $content
	 *
	 * @return void
	 */
	static function renderElement(string $tag, array $attrs = [], ?string $content = null) {
		echo self::getElement($tag, $attrs, $content);
  }


  /**
   * Returns formated <a> tag with e-mail link
   *
   * @param string $mail
   * @param string|null $label
   * @return void
   */
  static function getMailLink(string $mail, ?string $label = null) {
    $label = $label ?: $mail;
    $attrs = [
      'href' => 'mailto:'.$mail,
      'target' => '_blank',
    ];
		return self::getElement('a', $attrs, $label);
  }


  /**
   * Echoes formated <a> tag with e-mail link
   *
   * @param string $mail
   * @param string|null $label
   * @return void
   */
  static function renderMailLink(string $mail, ?string $label = null) {
    echo self::getMailLink($mail, $label);
  }


  /**
   * Returns formated <a> tag with phone link
   *
   * @param string $phone
   * @param string|null $label
   * @return string
   */
  static function getPhoneLink(string $phone, ?string $label = null) {
    $label = $label ? $label : $phone;
    $attrs = [
      'href' => 'tel:'.str_replace(' ', '', $phone),
    ];
		return self::getElement('a', $attrs, $label);
  }


  /**
   * Echoes formated <a> tag with phone link
   *
   * @param string $phone
   * @param string|null $label
   * @return void
   */
  static function renderPhoneLink(string $phone, ?string $label = null) {
    echo self::getPhoneLink($phone, $label);
  }


	/**
	 * Set CSS background image attr, returning style's
   * background-image attr, in a format:
	 * `"background-image: url('{imageUrl');"`
   *
   * @param string $imageUrl url to image file
   * @param string|null $defaultImageUrl default url to image file
   * @return string
   */
	static function getBackgroundImageAttr(string $imageUrl, ?string $defaultImageUrl = null) {
		$url = $imageUrl ?: $defaultImageUrl;
		return $url ? "background-image: url($url);" : '';
	}
}

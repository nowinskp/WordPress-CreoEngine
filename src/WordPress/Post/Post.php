<?php

namespace Wpce\WordPress\Post;

use Wpce\WordPress\Post\Abstracts\DefaultType;

class Post extends DefaultType {

  protected function getValidPostType(): string {
    return 'post';
  }

}

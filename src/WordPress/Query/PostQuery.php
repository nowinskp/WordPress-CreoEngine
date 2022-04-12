<?php

namespace Wpce\WordPress\Query;

use Wpce\WordPress\Post\Post;
use Wpce\WordPress\Query\Abstracts\ArchiveQuery;

class PostQuery extends ArchiveQuery {

  protected function getPostType(): string {
    return 'post';
  }

  protected function wrapPost($post): Post {
    return new Post($post);
  }

  /**
   * Returns found posts.
   *
   * When using intellisense, it's worth to override
   * this function just for the sake of redefining
   * the returned type here, in phpdoc block.
   *
   * @return Post[]
   */
  public function getPosts(): array {
    return $this->posts;
  }
}

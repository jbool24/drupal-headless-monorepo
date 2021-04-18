<?php

declare(strict_types = 1);

namespace Drupal\pages\Wrappers\Response;

use Drupal\Core\Entity\EntityInterface;
use Drupal\graphql\GraphQL\Response\Response;

/**
 * Type of response used when an page is returned.
 */
class PageResponse extends Response {

  /**
   * The page to be served.
   *
   * @var \Drupal\Core\Entity\EntityInterface|null
   */
  protected $page;

  /**
   * Sets the content.
   *
   * @param \Drupal\Core\Entity\EntityInterface|null $page
   *   The page to be served.
   */
  public function setPage(?EntityInterface $page): void {
    $this->page = $page;
  }

  /**
   * Gets the page to be served.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The page to be served.
   */
  public function page(): ?EntityInterface {
    return $this->page;
  }

}

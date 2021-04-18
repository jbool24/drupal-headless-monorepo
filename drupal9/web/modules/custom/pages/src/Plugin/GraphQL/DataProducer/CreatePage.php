<?php

namespace Drupal\pages\Plugin\GraphQL\DataProducer;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\graphql\Plugin\GraphQL\DataProducer\DataProducerPluginBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\pages\GraphQL\Response\PagesResponse;

/**
 * Creates a new page entity.
 *
 * @DataProducer(
 *   id = "create_page",
 *   name = @Translation("Create Page"),
 *   description = @Translation("Creates a new Page."),
 *   produces = @ContextDefinition("any",
 *     label = @Translation("Page")
 *   ),
 *   consumes = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Page data")
 *     )
 *   }
 * )
 */
class CreatePage extends DataProducerPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user')
    );
  }

  /**
   * CreatePage constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }

  /**
   * Creates an Page.
   *
   * @param array $data
   *   The submitted values for the article.
   *
   * @return \Drupal\graphql_composable\GraphQL\Response\PageResponse
   *   The newly created article.
   *
   * @throws \Exception
   */
  public function resolve(array $data) {
    $response = new PageResponse();
    if ($this->currentUser->hasPermission("create page content")) {
      $values = [
        'type' => 'page',
        'name' => $data['name'],
        'body' => $data['description'],
      ];
      $node = Node::create($values);
      $node->save();
      $response->setPage($node);
    }
    else {
      $response->addViolation(
        $this->t('You do not have permissions to create pages.')
      );
    }
    return $response;
  }

}

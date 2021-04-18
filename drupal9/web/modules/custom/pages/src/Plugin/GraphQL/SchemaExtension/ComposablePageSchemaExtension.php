<?php

namespace Drupal\pages\Plugin\GraphQL\SchemaExtension;

use Drupal\graphql\GraphQL\ResolverBuilder;
use Drupal\graphql\GraphQL\ResolverRegistryInterface;
use Drupal\graphql\GraphQL\Response\ResponseInterface;
use Drupal\graphql\Plugin\GraphQL\SchemaExtension\SdlSchemaExtensionPluginBase;
use Drupal\pages\GraphQL\Response\PageResponse;

/**
 * @SchemaExtension(
 *   id = "pages",
 *   name = "Pages Composable",
 *   description = "Adds Pages nodes and related fields to the Graph.",
 *   schema = "composable"
 * )
 */
class ComposablePageSchemaExtension extends SdlSchemaExtensionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function registerResolvers(ResolverRegistryInterface $registry) {
    $builder = new ResolverBuilder();

    $registry->addFieldResolver('Query', 'page',
      $builder->produce('entity_load')
        ->map('type', $builder->fromValue('node'))
        ->map('bundles', $builder->fromValue(['page']))
        ->map('id', $builder->fromArgument('id'))
    );

    // Create page mutation.
    $registry->addFieldResolver('Mutation', 'createPage',
      $builder->produce('create_page')
        ->map('data', $builder->fromArgument('data'))
    );

    $registry->addFieldResolver('PageResponse', 'page',
      $builder->callback(function (PageResponse $response) {
        return $response->page();
      })
    );

    $registry->addFieldResolver('PageResponse', 'errors',
      $builder->callback(function (PageResponse $response) {
        return $response->getViolations();
      })
    );

    $registry->addFieldResolver('Page', 'id',
      $builder->produce('entity_id')
        ->map('entity', $builder->fromParent())
    );

    $registry->addFieldResolver('Page', 'title',
      $builder->compose(
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    $registry->addFieldResolver('Page', 'author',
      $builder->compose(
        $builder->produce('entity_owner')
          ->map('entity', $builder->fromParent()),
        $builder->produce('entity_label')
          ->map('entity', $builder->fromParent())
      )
    );

    // Response type resolver.
    $registry->addTypeResolver('Response', [
      __CLASS__,
      'resolveResponse',
    ]);
  }

  /**
   * Resolves the response type.
   *
   * @param \Drupal\graphql\GraphQL\Response\ResponseInterface $response
   *   Response object.
   *
   * @return string
   *   Response type.
   *
   * @throws \Exception
   *   Invalid response type.
   */
  public static function resolveResponse(ResponseInterface $response): string {
    // Resolve content response.
    if ($response instanceof PageResponse) {
      return 'PageResponse';
    }
    throw new \Exception('Invalid response type.');
  }

}

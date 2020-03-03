<?php

/**
 * @file
 * Contains Drupal\spacebase_core\Plugin\QueueWorker\ContentNotificationsBase.php
 */

namespace Drupal\spacebase_core\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\spacebase_core;


/**
 * Provides base functionality for the ContentNotifications Queue Workers.
 */
abstract class ContentNotificationsBase extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * Creates a new ContentNotificationsBase object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   The node storage.
   */
  public function __construct(EntityStorageInterface $node_storage) {
    $this->nodeStorage = $node_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $container->get('entity.manager')->getStorage('node')
    );
  }



  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    /** @var NodeInterface $node */
    $node = $this->nodeStorage->load($data->nid);
    return spacebase_core_send_new_content_notifications($data->module, $data->key, $node);
  }
}

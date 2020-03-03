<?php

/**
 * @file
 * Contains spacebase_core\spacebase_core.views.inc..
 * Provide a custom views field data that isn't tied to any other module. */

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Render\Markup;
use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\system\ActionConfigEntityInterface;

/**
* Implements hook_views_data().
*/
function spacebase_core_views_data() {

    $data['views']['table']['group'] = t('Custom Global');
    $data['views']['table']['join'] = [
      '#global' => [],
    ];

    $data['views']['group_member_count_field'] = [
      'title' => t('Group Member Count Field'),
      'help' => t('Outputs member count for a group'),
      'field' => [
        'id' => 'group_member_count_field',
      ],
    ];

    $data['views']['group_join_status_link_field'] = [
        'title' => t('Group Join Status Link Field'),
        'help' => t('Outputs a join status or link for the logged in user for a given group'),
        'field' => [
            'id' => 'group_join_status_link_field',
        ],
    ];

    return $data;
}

<?php

namespace Drupal\spacebase_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\group\Entity\GroupContent;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;

/**
 * Provides a 'OrgAdvancedSearchBlock' block.
 *
 * @Block(
 *  id = "org_advanced_search",
 *  admin_label = @Translation("Org Advanced Search Block"),
 * )
 */
class OrgAdvancedSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $links = [];
    $links[] = $this->fieldLink('group_name', "Name");
    $links[] = $this->fieldLink('group_description', "Description");

    $active = [];
    foreach($links as $link) {
      if ($link['active']) {
        $active[] = $link['link']['#title'];
      }
    }

    if (empty($active)) {
      $active = ['All'];
    }

    return [
      '#links' => $links,
      '#active' => join(', ', $active),
      '#theme' => 'org_advanced_search',
      '#attached' => array(
        'library' => array(
          'spacebase_core/sb-search',
        ),
      ),
      '#cache' => [
        'max-age' => 0,
      ],
      '#attributes' => [
        'style' => 'display: none;',
      ]
    ];
  }

  private function fieldLink($key, $label) {
    $path = \Drupal::service('path.current')->getPath();

    $active = FALSE;
    $q = $_GET;
    if (!empty($_GET['sf'][$key])) {
      $active = TRUE;
      unset($q['sf'][$key]);
    }
    else {
      $q['sf'][$key] = $key;
    }

    return [
      'active' => $active,
      'link' => [
        '#type' => 'link',
        '#title' => $label,
        '#url' => Url::fromUserInput($path, ['query' => $q]),
      ],
    ];
  }

}

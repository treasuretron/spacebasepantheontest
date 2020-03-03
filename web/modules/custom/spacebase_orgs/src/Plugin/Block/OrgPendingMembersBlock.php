<?php

namespace Drupal\spacebase_orgs\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a 'OrgPendingMembersBlock' block.
 *
 * @Block(
 *  id = "org_pending_members_block",
 *  admin_label = @Translation("Pending Members"),
 * )
 */
class OrgPendingMembersBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    if ($gid = (int) \Drupal::routeMatch()->getParameter('group')) {
      if ($group = \Drupal\group\Entity\Group::load($gid)) {
        $account = \Drupal::currentUser();
        if ($group->hasPermission('administer members', $account)) {
          $build = \Drupal::formBuilder()->getForm('Drupal\spacebase_orgs\Form\OrgPendingMembersForm');
        }
      }
    }
    $build['#cache']['max-age'] = 0;
    return $build;
  }

}

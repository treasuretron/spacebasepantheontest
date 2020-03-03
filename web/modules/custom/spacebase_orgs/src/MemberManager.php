<?php

namespace Drupal\spacebase_orgs;

class MemberManager {

  public static function rejectMembership($id, &$context){
    /** @var \Drupal\group\Entity\GroupContent $membership */
    $membership = \Drupal::entityTypeManager()->getStorage('group_content')->load($id);

    $context['sandbox']['progress']++;
    MemberManager::notifyStatus('membership_rejected', $membership);
    $membership->delete();
    $context['message'] = "Rejected {$membership->label->value}";
    $context['results'][] = $id;
  }

  public static function approveMembership($id, $admin = FALSE, &$context){
    /** @var \Drupal\group\Entity\GroupContent $membership */
    $membership = \Drupal::entityTypeManager()->getStorage('group_content')->load($id);
    /** @var \Drupal\group\Entity\Group $group */
    $group = $membership->getGroup();

    // Get the admin and verified group role labels.
    $admin_role = $group->getGroupType()->id() . '-admin';
    $verified_role = $group->getGroupType()->id() . '-verified';

    $context['sandbox']['progress']++;
    $membership->group_roles->setValue($verified_role);
    if ($admin) {
      $membership->group_roles->setValue([$admin_role, $verified_role]);
    }
    if ( $membership->save() == 2 ) {
      MemberManager::notifyStatus('membership_approved', $membership);
    }
    $context['message'] = "Approved {$membership->label->value}";
    $context['results'][] = $id;
  }

  public static function notifyStatus($key, $membership) {
    $account = \Drupal\user\Entity\User::load($membership->uid->target_id);
    $params = [
      'account' => $account,
      'group' => $membership->getGroup(),
    ];
    spacebase_orgs_send_email($key, $params);
  }

  public static function moderateMembershipFinishedCallback($success, $results, $operations) {
    $message = t('Finished with an error.');
    if ($success) {
      $message = \Drupal::translation()->formatPlural(count($results), 'One membership processed.', '@count memberships processed.');
    }
    drupal_set_message($message);
  }
}
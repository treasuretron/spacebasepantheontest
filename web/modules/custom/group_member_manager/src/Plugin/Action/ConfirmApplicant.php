<?php

namespace Drupal\group_member_manager\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;

/**
 * Provides a 'ConfirmApplicant' action.
 *
 * @Action(
 *  id = "applicant_confirm",
 *  label = @Translation("Confirm Applicant"),
 *  type = "group_content",
 * )
 */


/* type is the group module's glue to a user 'group_content,' not an actual user, @ToDo
    is it group_content, or group_content_type_569a59a77cd78  */

class ConfirmApplicant extends ViewsBulkOperationsActionBase {

  //use Drupal\Core\StringTranslation\StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function execute($group_content_membership = NULL) {
    // entity is a $group_content.
    $gcid = $group_content_membership->id(); // group_content
    $config = $this->getConfiguration();
    // Get the radio button that matches the entity's id:
    foreach ($config['id-action'] as $id => $action) {
      if ($gcid == $id) {
        if ($action == 'reject') {
          $email = $this->_prep_email($group_content_membership);
          $params = array();
          $params['message'] = $email['username'] . ', '
            . t("<p>Your request to join ")
            . $email['group']->label()
            . t(" was declined.</p>");
          $params['subject'] = "Sorry, your application to " . $email['group']->label() . " (at SpaceBase) was rejected.";


          // reject this member: simply delete the group_content.
          $group_content_membership->delete();

          $err_msg = $this->_send_email($params, $email, $email['username']);
          if ($err_msg) {
            return "Rejected " . $group_content_membership->label() . " — $err_msg";
          } else {
            return "Rejected " . $group_content_membership->label();
          }
        } elseif ($action == 'confirm') {
          // And add the role 'verified' (internal-facing name only)
          // As we move forward, only verified members count as anything, whenever membership
          // permissions are considered. This is a bit of a mess, evolving from specs that
          // didn't differentiate... Groups module does not have pending for D8, though it's being
          // worked on.
          $group_content_membership->group_roles->setValue('organization_group-verified');
          if ( $group_content_membership->save() == 2 ) { // returns 2, SAVED_UPDATED, if working. Add test?
            $email = $this->_prep_email($group_content_membership);
            $params = array();
            $params['message'] = $email['username'] . ', '
              . t("<p>Welcome to ")
              . $email['group']->label()
              . t(". Your request to join was accepted.</p>");
            $params['subject'] = "Your application to " . $email['group']->label() . " (at SpaceBase) was accepted.";
            $err_msg = $this->_send_email($params, $email, $email['username']);
            if ($err_msg) {
              return "Accepted " . $group_content_membership->label() . " — $err_msg";
            } else {
              return "Accepted " . $group_content_membership->label();
            }

          } else {
            return "Failed to accept " . $group_content_membership->label();
          }
        } else {
          return $group_content_membership->label() . " is still pending";
        }
      }
    }

    // @ToDo: get t workiong
    //return $this->t('Confirm this user - not built yet');
    return "Tried to execute Confirm Applicant";
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    return true; /* @ToDo     Did this ever work? Keep going on radios for now */
    $access = $object->status->access('edit', $account, TRUE)
      ->andIf($object->access('update', $account, TRUE));

    return $return_as_object ? $access : $access->isAllowed();
  }

  /**
   * private function _prep_email($group_content_membership)
   *
   * Get values from the group_content for membership,
   * typically before it is delete.
   */ 
  private function _prep_email($group_content_membership) {
    $email = [];
    $email['username'] = $group_content_membership->label->value;
    $email['group'] = $group_content_membership->getGroup();
    $uid =      $group_content_membership->uid->target_id;
    $account = \Drupal\user\Entity\User::load($uid);
    $email['account'] = $account;
    $email['langcode'] = $account->getPreferredLangcode();
    $email['module'] = 'group_member_manager';
    $email['email'] = $account->getEmail();
    return $email;
  }

  /**
   * private function _send_email($params, $email)
   *
   * Send the email. Note that the values may be pulled out of group_content,
   * then group_content deleted, then email sent.
   */
  private function _send_email($params, $email, $who) {
    $result = \Drupal::service('plugin.manager.mail')->mail(
      $email['module'],
      'notice', 
      $email['email'], 
      $email['langcode'], 
      $params
    );
    if ($result['result'] !== true) {
      return "We attempted to email $who, but there was an error.";
    } else {
      return NULL;
    }
  }

}

<?php

namespace Drupal\spacebase_orgs\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Class OrgPendingMembersForm.
 */
class OrgPendingMembersForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'org_pending_members_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $members = [];
    if ($gid = \Drupal::routeMatch()->getParameter('group')) {
      //$group = Drupal\group\Entity\Group::load($gid);
      $ids = \Drupal::entityQuery('group_content')
        ->condition('type',['group_content_type_569a59a77cd78','project_group-group_membership'], 'IN')
        ->condition('gid', $gid)
        ->condition('group_roles', NULL, 'IS NULL')
        ->execute();

      if (!empty($ids)) {
        $storage = \Drupal::entityTypeManager()->getStorage('group_content');
        $memberships = $storage->loadMultiple($ids);
        foreach($memberships as $id => $membership) {
          $account = $membership->getEntity();
          $members[$id] = [
            "name" => trim("{$account->field_first_name_user->value} {$account->field_last_name_user->value}") ?: $account->getUsername(),
            "url" => $account->url(),
            "created" =>	format_date($membership->getCreatedTime(), 'medium'),
            "picture" => $account->user_picture->view('member_thumb_small'),
          ];
        }
      }
    }

    $form['#tree'] = TRUE;

    $form['#attached']['library'][] = 'spacebase_orgs/pending_members';

    $form['#members_count'] = count($members);

    if (!empty($members)) {
      foreach ($members as $id => $member) {
        $form['members'][$id]['#member'] = $member;
        $form['members'][$id]['state'] = [
          '#type' => 'radios',
          '#options' => [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
          ],
          '#default_value' => 'pending',
          'pending'  => [ '#attributes' => ['data-eid' => $id]],
          'approved' => [ '#attributes' => ['data-eid' => $id]],
          'rejected' => [ '#attributes' => ['data-eid' => $id]],
        ];
        $form['members'][$id]['admin'] = [
          '#title' => 'Make Admin',
          '#type' => 'checkbox',
          '#attributes' => [
            'data-eid' => $id,
            'value' => 'admin',
          ],
        ];
      }

      $form['submit'] = [
        '#type' => 'submit',
        '#attributes' => array(
          'class' => array(
            'btn-primary',
            'btn-small',
          ),
        ),
        '#value' => $this->t('Apply Changes'),
      ];

    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $operations = [];
    foreach ($values['members'] as $id => $value) {
      switch ($value['state']) {
        case 'rejected':
          $operations[] = ['\Drupal\spacebase_orgs\MemberManager::rejectMembership', [$id]];
          break;
        case 'approved':
          $operations[] = ['\Drupal\spacebase_orgs\MemberManager::approveMembership', [$id, $value['admin']]];
          break;
      }
    }

    if ($operations) {
      $batch = [
        'title' => t('Moderating @num memberships', ['@num' => count($operations)]),
        'operations' => $operations,
        'finished' => '\Drupal\spacebase_orgs\MemberManager::moderateMembershipFinishedCallback',
        'init_message' => t('Starting moderation'),
        'progress_message' => t('Processed @current out of @total.'),
      ];

      batch_set($batch);
    }
  }

}

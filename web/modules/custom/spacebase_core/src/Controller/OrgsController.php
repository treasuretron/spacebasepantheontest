<?php

namespace Drupal\spacebase_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class OrgsController.
 */
class OrgsController extends ControllerBase {
  
  // The resources page was basically built in Twig Tweak,
  // so currently there's not much happening here, yet.
  public function resources($gid) {
    if (!is_numeric($gid)) {
      new AccessDeniedHttpException();
    }

    // Load the group entitiy from passed id.
    $group = \Drupal\group\Entity\Group::load($gid);

    $build = [
      '#theme' => 'group_resources', // this is the theme hook
      '#group_id' => $gid,
      '#resource_type' =>  _get_resource_type($group->bundle()),
    ];
    /* @ToDo (trivial): refactor? Needed some of what comes from preprocess groups:
     * cut and pasted from, now, _set_the_group_links -- but only a little
     *  of that is used here.
     */
    $account = \Drupal::currentUser();
    $id = 'resources';

    $build['#group_links'] = [];
    if ($group->hasPermission("create $id entity", $account)) {
      $plugin_id = 'group_node:' . $id;
      $route_params = ['group' => $gid, 'plugin_id' => $plugin_id];
      $url = new \Drupal\Core\Url('entity.group_content.create_form', $route_params);
      $current_uri = \Drupal::request()->getRequestUri();
      $build['#group_links']['group_create_'. $id] =
        $url->toString() . '?destination=' . $current_uri;

    }
    return $build;
  }
}

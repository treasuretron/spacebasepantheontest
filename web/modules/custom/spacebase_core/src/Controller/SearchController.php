<?php

namespace Drupal\spacebase_core\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class SearchController.
 */
class SearchController extends ControllerBase {

  public function search() {
    $return = [
      '#fullpage' => FALSE,
    ] + $this->searchReturn();

    $view = $this->searchResults(['entity:group'], 0);
    $return['#org_count'] = count($view->result);
    $view->result = array_slice($view->result, 0, 3);
    $return['#orgs'] = $view->render();

    $view = $this->searchResults(['entity:user'], 0);
    $return['#people_count'] = count($view->result);
    $view->result = array_slice($view->result, 0, 3);
    $return['#people'] = $view->render();

    return $return;
  }

  public function searchPeople() {
    $return = $this->searchReturn();
    $view = $this->searchResults(['entity:user'], 10);
    $return['#people_count'] = count($view->result);
    $return['#people'] = $view->render();
    return $return;
  }

  public function searchOrganizations() {
    $return = $this->searchReturn();
    $view = $this->searchResults(['entity:group'], 10);
    $return['#org_count'] = count($view->result);
    $return['#orgs'] = $view->render();
    return $return;
  }

  private function searchReturn() {
    return [
      '#theme' => 'sb_search_page',
      '#keywords' => !empty($_GET['keywords']) ? $_GET['keywords'] : '',
      '#fullpage' => TRUE,
    ];
  }

  private function searchResults($args, $items_per_page = 3, $count = FALSE, $pager = FALSE) {
    $view = \Drupal\views\Views::getView('sitewide_search');
    $view->setDisplay('search');
    $view->setArguments($args);
    $view->setItemsPerPage($items_per_page);
    $view->preExecute();
    $view->execute();
    return $view;
  }

}

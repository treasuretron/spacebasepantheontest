<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }



  /**
   * Hold the execution until the page is/resource are completely loaded OR timeout
   *
   * @Given /^I wait until the page (?:loads|is loaded)$/
   * @param object $callback
   *   The callback function that needs to be checked repeatedly
   */
  public function iWaitUntilThePageLoads($callback = NULL) {
    // Manual timeout in seconds
    $timeout = 60;
    // Default callback
    if (empty($callback)) {
      if ($this->getSession()
          ->getDriver() instanceof Behat\Mink\Driver\GoutteDriver
      ) {
        $callback = function ($context) {
          // If the page is completely loaded and the footer text is found
          if (200 == $context->getSession()->getDriver()->getStatusCode()) {
            return TRUE;
          }
          return FALSE;
        };
      }
      else {
        // Convert $timeout value into milliseconds
        // document.readyState becomes 'complete' when the page is fully loaded
        $this->getSession()
          ->wait($timeout * 1000, "document.readyState == 'complete'");
        return;
      }
    }
    if (!is_callable($callback)) {
      throw new Exception('The given callback is invalid/doesn\'t exist');
    }
    // Try out the callback until $timeout is reached
    for ($i = 0, $limit = $timeout / 2; $i < $limit; $i++) {
      if ($callback($this)) {
        return TRUE;
      }
      // Try every 2 seconds
      sleep(2);
    }
    throw new Exception('The request is timed out');
  }


  /**
   * Waits a while, for debugging or avoiding spam blocking
   *
   * @param int $seconds
   *   How long to wait.
   *
   * @When I wait :seconds second(s)
   */

  public function IWait($seconds) {
    sleep($seconds);
  }

  /**
   * @Then I click the link containing child element :arg1
   *
   * Intended for fa graphic links
   * Link is immediate parent
   */
  public function iClickTheLinkContainingChildElement($selector)
  {
      $page = $this->getSession()->getPage();
      $inner_element = $page->find('css', $selector);
 
      if (empty($inner_element) || !$inner_element ) {
        throw new Exception("No html element found for the selector ('$selector')");
      } elseif (empty($inner_element->getParent())) {
        throw new Exception("No parent element found for the selector ('$selector')");
      }
      $inner_element->getParent()->click();
  }


/**
 * @When /^I visit "([^"]*)" node tab "([^"]*) of type "([^"]*)"$/
 *
 * ex: When I visit "Text Example" node of type "page" tab "edit"
 * I visit "Test Event javascript, edited" node tab "edit" of type "event"

public function iVisitNodeTabOfType($title, $tab, $type) {
  $query = new entityFieldQuery();
  $result = $query
    ->entityCondition('entity_type', 'node')
    ->entityCondition('bundle', strtolower($type))
    ->propertyCondition('title', $title)
    ->propertyCondition('status', NODE_PUBLISHED)
    ->range(0, 1)
    ->execute();

  if (empty($result['node'])) {
    $params = array(
      '@title' => $title,
      '@type' => $type,
    );
    throw new Exception(format_string("Node @title of @type not found.", $params));
  }

  $nid = key($result['node']);
  // Use Drupal Context 'I am at'.
  return new Given("I go to \"node/$nid/$tab\"");
}
 */



/**
 * @When /^I visit "([^"]*)" node$/
 *
 * ex: When I visit "Text Example" node of type "page" tab "edit"
 * I visit "Test Event javascript, edited" node tab "edit" of type "event"
 */
  public function iVisitNode($title) {
    $tab = "edit";
    $type = "event";
    $nodes = \Drupal::entityTypeManager()
  ->getStorage('node')
  ->loadByProperties(['title' => $title]);

    if (empty($nodes)) {
      $params = array(
        '@title' => $title,
        '@type' => $type,
      );
      throw new Exception(format_string("Node @title of @type not found.", $params));
    }
    $nid = key($nodes); // the key is the nid, or we have the whole node
    // Use Drupal Context 'I am at'.
    //return new Given("I go to \"node/$nid/$tab\"");
    $this->getSession()->visit($this->locatePath("/node/$nid/$tab"));
  }


  /**
   * Fills in specified field with date
   * Example: When I fill in "field_ID" with date "now"
   * Example: When I fill in "field_ID" with date "-7 days"
   * Example: When I fill in "field_ID" with date "+7 days"
   * Example: When I fill in "field_ID" with date "-/+0 weeks"
   * Example: When I fill in "field_ID" with date "-/+0 years"
   *
   * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with date "(?P<value>(?:[^"]|\\")*)"$/
   */
  public function iFillInWithDate($field, $value) {
    $newDate = strtotime("$value");

    $dateToSet = date("d/m/Y", $newDate);
    // @ToDo: make sure this fails properly.
    if ($this->getSession()->getPage()->fillField($field, $dateToSet)) {
      throw new Exception("Failed to fill in the date");
    }
  }




  /**
   * Print message to use, more generic than existing Print
   *
   * @When /^(?:|I )printDebug "([^"]*)"/
   */
  public static function printDebug($string) {
    print "$string\n";
  }
}

@api
Feature: Test organizations. Status:WIP
  As a user with an account, I want to be able to create an organization and add resources.

  Background:
  Given users:
    | name              | password    | username   | mail          |
    | Behat TestFounder | passw345534 | BehatTstTF | tf@example.com |
    | Behat TestJoiner  | passw654891 | BehatTstJ  | j@example.com  |


  Scenario: Create organization and add Discussion, then edit Discussion. Confirm honeypot.
    Given I am logged in as "Behat TestFounder"
    And I am on "group/add/organization_group"
    When I fill in the following:
      | label[0][value] | Behat Test Org |
      | edit-field-description-0-value | Description of Acme Test Org |
      | field_industry_segment[] | 34 |
    And I press "Create Organization"
    Then I fill in the following:
      | edit-field-organization-role-0-value | Role at Behat Test Org |
    And I press "Save group and membership"
    # Anti-spam security is supposed to block automated submission:
    Then I should see "There was a problem with your form submission. Please wait 6 seconds and try again."
    And I wait 6 seconds
    And I press "Save group and membership"
    Then I should see "has been created."
    # you'll land on the Members page... Go to discussion forum and add one:


    #Scenario: Edit my own Organization. Note: doing this now jumps past a
    #reported bug that aliases are not created. 
    #  CLick the fa.edit: needed a new feature context, so wrote a function
    #  in ./bootstrap/FeatureContext.php
    Then I click the link containing child element ".fa-home"
    Then I click "Edit Group"
    When I fill in the following:
      | edit-field-description-0-value | Description of Acme Test Org - Edited |
      | field_industry_segment[] | 32 | 
    And I wait 6 seconds
    And I press "Save"
    Then I should see "has been updated"


    # Once the alias bug is fixed, move this up, before "Edit my own Org.."
  Scenario: Test auto alias
    Given I am on "/org/behat-test-org"
    Then I should not see "The requested page could not be found."

  Scenario: Add Discussion
    Given I am logged in as "Behat TestFounder"
    Given I am on "/org/behat-test-org"
    Then I should see "Invite New Members"
    And I click "discussions"
    And I should see "Welcome to your organization's discussion forum."
    Then I click "behat-new-post"
    # @ToDoDiscuss: there's no title on this page.
    And I fill in the following:
      | edit-title-0-value | Behat Test Discussion Title |
    And I wait 6 seconds
    And I press "Save"
    Then I should see "has been created"
    And I should see "Behat Test Discussion Title"
    # On the discussions page
    Then I should see "Welcome to your organization"

  Scenario: Edit that discussion, authorized
    And I click "Behat Test Discussion Title"
    Then I click the link containing child element ".fa-edit"
    And I fill in the following:
      | edit-title-0-value | Behat EDITED Test Discussion Title |
    And I wait 11 seconds
    And I press "Save"
    Then I should see "has been updated"




  Scenario: Authenticated User can see the above discussion
    # Problem: log in as Authenticated takes me to that page.
    # Have to get back to this org! /org/
    Given I am logged in as a user with the "Authenticated user" role
    ## Hard to find the org — Go to the org owner, go from there:
    When I am on "/u/Behat-TestFounder"
    And I click "Behat Test Org"
    Then I should see "Behat EDITED Test Discussion Title"


  Scenario: View organization as basic user, via alias, no Edit option
    Given I am logged in as a user with the "Authenticated user" role
    And I am on "/org/behat-test-org"
    Then I should see "Description"
    And I should see "Behat Test Org"
    And I should not see "Edit"

  Scenario: Add resource to organization -- old test, not working, @ToDo
    Given I am logged in as "Behat TestFounder"
    # Make sure you don't see the info for anon users:
    And I should not see "Join SpaceBase, then join the organizations you are interested in"
    And I am on "/org/behat-test-org"

    # can't use the following because the button isn't identifiable by any of these: id|name|title|alt|value
    # And I am on "/group/95/resources#Communication"
    # When I press the "btn btn-primary btn-small pull-right" button
    # Instead, going straight to the add resource page...
    #
    # The 91 has to be replaced.
    #
    #
    And I am on "/group/91/content/create/group_node%3Aresources?destination=/group/91/resources"
    Then I should not see "You are not authorized to access this page"

    #TODO Troubleshoot filling in this form, and clicking "90"
    When I fill in the following:
      | edit-field-resource-link-0-uri | http://mylink.org         |
      | edit-title-0-value             | My Communication resource |

    # "90" is the Communication radio button
    #TODO Troubleshoot clicking the comm radio button
    And I click "edit-field-tab-90"
    And I press the "edit-submit" button
    Then I should see "has been created"



  # We want to test search functions around the new content. 
  # This requires running cron! (This was the "flow" test,
  # from Orgs to cron to search all working together.)
  Scenario: People Search for substring
    # This sometimes fails. Why? Ever work?
    Given I run cron
    # To get above into search
    And I am on "/search"
    #And I enter "TestJoin" for "edit-keywords"
    And I enter "TestJoiner" for "edit-search-api-fulltext"
    #When I press the "edit-submit-sitewide-search" button
    When I press the "Search" button
    # It's not finding TestJoin
    Then I should see "Behat TestJoiner"
    And I should see "screenshot"


  @javascript
  Scenario: Owner can edit organization data
    # Have problems here... I think setting @api to drush 
    # has more reqs than drupal ... not sure though, maybe this old test
    # never worked. "Notice: Undefined property: stdClass::$mail"
    Given users:
      | name           | password | username      |
      | Kurt UserAtest | passw0rd | KurtuserAtest |
    Given I am logged in as "Kurt UserAtest"
    #TODO need to find a way to identify the org created in previous scenario
    #Acme Org Test on my local
    And I am on "/group/149"
    #TODO not seeing the link "Edit Group" on screen for some reason
    Then I click "Edit Group"


  Scenario: Anon cannot edit organization data
    Given I am an anonymous user
    #Acme Org Test on my local
    And I am on "/group/149"
    #TODO not seeing the link "Edit Group" on screen for some reason
    Then I should not see "Edit Group"
    And I should see "Join SpaceBase"


  Scenario: Non-owner cannot edit organization data
    Given I am logged in as a user with the "Authenticated user" role
    #Acme Org Test on my local
    And I am on "/group/149"
    #TODO not seeing the link "Edit Group" on screen for some reason
    Then I should not see "Edit Group"
    And I should not see "Join SpaceBase"




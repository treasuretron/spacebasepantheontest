# @ToDo: want to test access issues. Users lack edit links unless they have
# admin-level access, even though they can edit with less than that. 
# Took the approach of going directly to the edit URL (custom FeatureContext),
# but perhaps we want those links to show?

@api
Feature: Test calendar. Status:main:clean.
  Anonymous user cannot update, but can read and search for all events.
  As an authenticated user, I should be able to create, read & search for events. Updating and deleting events is not allowed.
  As an org-owner, I should be able to accept/reject events.
  Test both non-javascript, and javascript
  
Background:
  Given users:
    | name         | password    | username | mail           |
    | Behat Tester | passw345534 | BehatTst | btx3@example.com |
    | Behat TestFounder | passw345534 | BehatTstTF | tfx3@example.com |
    | Behat TestJoiner  | passw654891 | BehatTstJ  | tjx3@example.com  |

  Scenario: Site-wide calendar exists
    Given I am on "/"
    When I click "Events"
    Then print current URL
    Then I should see "New Zealand Space Calendar"
    #Then I break

  #TODO no group calendar events yet
  #Scenario: Group calendar exists
    # Bazco org = 89
    #Given I am on "/group/89"
    #When click "Events" link
    #Then I should see “Previous"

  Scenario: Anonymous user cannot create a site-wide event
    # @ToDo: open grid menu
    Given I am on "/"
    When I click "Events"
    #TODO need access to the Add Event button
    #the following isn't sufficient without the above TODO
    Then I should not see "Add Event"



  # Something wrong with creating accounts. Going in circles.
  
  # The Edit button, of all things, is javascript depedent:
  Scenario: Authenticated user can create a site-wide event
    Given I am logged in as "Behat Tester"
    #Given I am logged in as an "authenticated user"
    #Given I am on "/events/month"
    # Another funky not-normal link:    @ToReview
    # Even using @javascript, this link doesn't seem to appear for behat.
    #And I click the link containing child element "fa-plus-circle"
    Given I am on "/node/add/event"
    Then I should see "Title"
    And I should not see "You are not authorized to access this page"

    And I should see "START"
    ## warning: javascript on will mess up this form!
    Then I fill in the following:
      | Title     | Test Event zx |
    And I fill in "edit-field-evt-start-0-value-date" with "2019-02-16 11:09:39"
    And I fill in "edit-field-evt-end-0-value-date" with "2019-03-15 12:10:40"
    And I press "Save"
    Then I should see "has been created"
    # Note, even as admin, the "Edit" button is, I believe, javascript
    # dependent.

    # This is a bit of a hickup, that javascript makes the whole thing 
    # run differently as date forms are completed:
  @javascript  
  Scenario: Authenticated user can create and update own site-wide event and other user cannot update
    Then printDebug "Currently, automatic log-in in via javascript driver while using drush is expected to fail as-is"
    Given I am logged in as a user with the "administrator" role
    Given I am on "/node/add/event"
    Then I should see "Title"
    And I should see "Start"
    And I should not see "You are not authorized to access this page"
    ## warning: javascript on will mess up this form!
    Then I fill in the following:
      | Title     | Test Event javascr |
    And I fill in "edit-field-evt-start-0-value-date" with "02012019"
    And I fill in "edit-field-evt-end-0-value-date" with "02042019"
    And I fill in "edit-field-evt-end-0-value-time" with "12:11:40PM"
    And I fill in "edit-field-evt-start-0-value-time" with "12:11:41am"
    #And I should see "screenshot"
    # Mostly looks ok?
    And I press "Save"
    Then I should see "has been created"


    #@ToDo: not sure if this tab being missing is a bug. And I follow "Edit" in the "tabs" region
    Then I visit "Test Event javascr" node
    And I fill in "Title" with "Test Event Javascript - edited"
    And I press "Save"
    And I should see "has been updated"
    And I should see "Test Event Javascript - edited"
    And I save screenshot

    Given I am logged in as "Behat TestJoiner"
    Then I visit "Test Event Javascript - edited" node
    And I should see "You are not authorized to access this page."

  #TODO no group calendar events yet
  #Scenario: Authenticated user can create a group event
    #Given I am on "/group/89"
    #And I am logged in as an "authenticated user"
    #When I press the "Events" link
    #Then I should see “Create Event"


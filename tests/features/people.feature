@api

Feature: Test user profiles
  As a user with an account, I want to be able to test
  view profile, edit profile, check profile, anonymous user cannot edit, but can view.
  Other logged in users cannot edit, but can view.

# Not sure the email is taking. Not important yet, but watch for problems.
Background:
  Given users:
    | name              | password    | username   | mail          |
    | Behat TestFounder | passw345534 | BehatTstTF | tf@example.com |
    | Behat Test@~!Joiner  | passw654891 | BehatTstJ  | j@example.com  |

  Scenario: Forms exist
    Given I am on "/"
    When I click "Join"
    Then I should see "Sign up for SpaceBase"
    When I am on "/"
    And I click "Sign In"
    Then I should see "Password"

  Scenario: Login as authenticated user
    Given I am logged in as "Behat TestFounder"
    When I am on "/u/Behat-TestFounder"
    Then I should see the link "Log out"

    #change the following to reflect specs in #175
    #the following presumes this user already exists... created above
    #And presumes they are in the search index, would they be?
    #Seems to work where search works, not on localhost. @ToDo, known bug?

    And I am on "https://spacebase.lndo.site/search?keywords=testfounder"
    Then I should see "Behat TestFounder"
    
    #  Scenario: See Bio page
    #Given I am logged in as a user with the "Authenticated user" role
    #And I am on "https://spacebase.lndo.site/search?keywords=UserAtest"

  Scenario: Edit own profile
    # this user should already exist in the environment
    Given I am logged in as "Behat TestFounder"
    When I am on "/u/Behat-TestFounder"
    Then I should see the link "Log out"
    And I should see "Bio"
    And I should see the link "Edit"
    And I wait 6 seconds
    #TODO can't press Edit Profile button, so instead resorting to going directly to URL
    # @ToDo -- why not above?
    # @ToDo -- No no no....
    #When I am on "/user/125/edit"
    And I click "Account"
    #TODO and the following errors...
    And I fill in the following:
      | edit-field-position-0-value | my edited position |
    And I wait 6 seconds
    And I press the "Save" button
    Then I should see "The changes have been saved"

  # why is email address required just for this edit?
  # We require it. When I do this via browser, the email is filled in by
  # default. Why is not here? 
  #
  # @ToDo: can users somehow access their meta data?

  Scenario: Anon cannot edit another profile
    Given I am an anonymous user
    When I am on "/u/Behat-TestFounder"
    Then I should not see the link "Log out"
    And I should see "Bio"
    And I should not see the link "Edit"
    # Without using an existing user, since there should be no link to
    # edit, there is no way to get this page without writing 
    # a context. User 1 always exists, and we're not going to change it.
    # Really bad error if you can edit!
    And I am on "/user/125/edit"
    Then I should see "You are not authorized to access this page"


  Scenario: Authenticated user cannot edit another profile
    Given I am logged in as a user with the "Authenticated user" role
    When I am on "/u/Behat-TestFounder"
    And I should see "Bio"
    And I should not see the link "Edit"
    And I am on "/user/1/edit"
    Then I should see "You are not authorized to access this page"


  Scenario: Fill in sign up form
    Given I am on "/user/register"
    When I fill in the following:
      # this user should NOT already exists in the environment
      | edit-mail | BehatTest1h@mailinator.com |
      | edit-name | BehatTest9h |
      | edit-field-first-name-user-0-value | Behat |
      | edit-field-last-name-user-0-value | Test |
      | edit-field-position-0-value | Director |
      | edit-field-tagline-user-0-value | Snappy |
      | edit-field-bio-user-0-value | My bio here. |

      #| field_home_location_user[0][address][country_code] | NZ |
      #TODO: when selecting the country, the form changes to reflect new fields.
      #  the following doesn't work.
      #| field_home_location_user[0][address][address_line1] | 90 Cable St |
      #| field_home_location_user[0][address][address_line2] | Te Aro |
      #| field_home_location_user[0][address][locality] | Wellington |
    And I wait 6 seconds
    And I press the "Create new account" button
    Then I should not see text matching "is already taken"
    And I should see "An email has been sent to you"
    #TODO need mailhog integration
    #And I should see an email with subject "Account details for"
    # make sure the user you created shows up in search results and that the link traverses to the correct profile data
    # also https://gitlab.com/spacebase/spacebase/issues/175
    And I am on "/search/people?keywords=BehatTest9h"
    And I should see "BehatTest9h"



  Scenario: Fill in sign up form with special characters
    Given I am on "/user/register"
    When I fill in the following:
      # this user should NOT already exists in the environment
      | edit-mail | BehatTest_2h@mailinator.com |
      | edit-name | BehatTest@2h |
      | edit-field-first-name-user-0-value | Behat |
      | edit-field-last-name-user-0-value | Test |
      | edit-field-position-0-value | Director |
      | edit-field-tagline-user-0-value | Snappy |
      | edit-field-bio-user-0-value | My bio here. |

      #| field_home_location_user[0][address][country_code] | NZ |
      #TODO: when selecting the country, the form changes to reflect new fields.
      #  the following doesn't work.
      #| field_home_location_user[0][address][address_line1] | 90 Cable St |
      #| field_home_location_user[0][address][address_line2] | Te Aro |
      #| field_home_location_user[0][address][locality] | Wellington |

      # @ToDo: 2 seconds was not enough! Are we sure?  
    Then I wait 12 seconds
    And I press the "Create new account" button
    Then I should not see text matching "is already taken"
    And I should not see text matching "The username contains an illegal character"
    And I should see "An email has been sent to you"
    And I save screenshot

  @javascript
  Scenario: Cleanup the accounts just created, testing this too
    Given I am logged in as an administrator
    When I am on "/u/behattest2h"
    And I click "toolbar-item-administration"
    # weird, so look at other version:
    #When I am on "/u/behattest9h"
    And I wait 6 seconds
    
    # Requested page could not be found
    And I save screenshot
    
    And I click "Edit"
    #But a lot of limks the admin sees say Edit..
    And I save screenshot
    # nopes: The website encountered an unexpected error. Please try again later.
    #
    And I wait 2 seconds
    Then I press "Cancel account"
    And I select "user_cancel_block_unpublish" from "edit-user-cancel-method"
    #And press the radio button "edit-user-cancel-method-user-cancel-block-unpublish"    
    Then I press "Cancel account"
    And I should see "screenshot"

  Scenario: Login with username that has special characters
    Given I am logged in as "Behat Test@~!Joiner"
    When I follow "behat-edit-profile" 
    Then I should see "Bio"


  Scenario: Anonymous visitor should not be able to edit a user
    Given I am not logged in
    # this user should already exists in the environment
    When I am on "/u/Behat-TestFounder"
    Then I should not see "Edit Profile"
    And I should not see "The requested page could not be found"
    And I should see "My Organizations"


  # https://gitlab.com/spacebase/spacebase/issues/175
  # Scenario: User must enter first and last names
  #   Given I am not logged in


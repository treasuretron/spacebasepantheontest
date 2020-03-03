@api
Feature: Test home page. Status:main:clean.
  As a user, I want to be able to load the home page, check for very basic text.

  Scenario: Home page loads
    Given I am on "/"
    Then I should see "SpaceBase is co-creating a global Space Ecosystem to serve entrepreneurs in emerging space industries"
    And I should see "New Zealand Space Directory"
    And I should see "More About Us"
    And I should see "Sign In"
    And I should see "Join"

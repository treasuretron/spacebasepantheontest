@api
Feature: Test home page
  As a user, I want to be able to load the home page.

  Scenario: Home page loads
    Given I am on "/"
    Then I should see "SpaceBase is co-creating a global Space Ecosystem to serve entrepreneurs in emerging space industries"

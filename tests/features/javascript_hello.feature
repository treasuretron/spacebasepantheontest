@javascript
# Intended as a "hello world" for javascript tsting, 
# and to indicate if javascript tests are badly broken
# Maybe erase this
Feature: Test home page using javascript. Status:javacript.
  As a user, I want to be able to load the home page.

  Scenario: Home page loads
    Given I am on "/"
    Then I should see "SpaceBase is co-creating a global Space Ecosystem to serve entrepreneurs in emerging space industries"



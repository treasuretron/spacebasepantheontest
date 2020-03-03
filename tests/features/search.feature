@api
Feature: Test search. Status:main:clean-req:content
  As a User, I want to be able to search for content.
  req:content = searches for specific content SpaceBase expects to be there.

  Scenario: Home Page Search
    Given I am on "/"
    And I enter "Space" for "edit-keywords"
    When I press the "edit-submit-sitewide-search" button
    #Then I should see "Search results for"    Not anymore?
    Then I should see "Displaying"
    And I should see "<strong>space</strong>"



  Scenario: People Search
    Given I am on "/search/people"
    Then I should see "People"

  Scenario: People Search for substring
    Given I am on "/search"
    And I enter "hig" for "edit-search-api-fulltext"
    When I press the "Search" button
    Then I should see "Kurt Higgins"


  Scenario: Organization Search
    Given I am on "/search/organizations"
    Then I should see "Organizations"

  @javascript
  #req:content = searches for specific content SpaceBase expects to be there.
  Scenario: Org Search using city and industry facets, javascript:
    # Seems to fail (the site, not the test) on my localhost,
    # where I believe search is not working correctly. 
    #Given I am on "https://spacebase.co/search/organizations?keywords=space"
    Given I am on "/search/organizations?keywords=space"
    When I check the box "city-wellington"
    And I wait until the page loads
    Then I should see "SpaceBase"
    And I should see "SpaceLaunch"
    And I check the box "Outreach and Education"
    And I wait until the page loads
    Then I should see "SpaceBase"
    And I should see "Space & Science Festival"
    And I should not see "SpaceLaunch"



#@TODO
# make sure search results of people have links that are valid
# Reference #175

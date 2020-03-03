@api
Feature: Test discussions. Status:WIP
  These require a forum be created. They should not, it's not reliable. 
  It's better to create a forum, perhaps leaving a footprint, and later
  development can delete that forum and it should be clean.

  As an org-member, I should be able to create and read posts in my group's discussion.
  As an org-owner, I should be able to create, read, update and delete posts in my group's discussion.
  Anonymous users can see discussions but not edit.

  # these tests presume the example discussion already exists

  Scenario: Anonymous user cannot edit
    Given I am an anonymous user
    #TestOrgA Forum
    And I am on "/group/91/forum"
    And I see the text "My Test Discussion "
    #TODO need to identify the following link by any of these: id|name|title|alt|value
    Then I should see "Join SpaceBase"


  Scenario: Group discussion exists
    Given users:
      | name           | password | username      |
      | Kurt UserAtest | passw0rd | KurtuserAtest |
    Given I am logged in as "Kurt UserAtest"
    And I am on "/group/91/forum"
    And I wait until the page loads
    Then I should not see "Join SpaceBase, then join the organizations you are interested in"
    # 1. tried looking for "Welcome to your organization" but that was failing mysteriously
    # 2. this test presumes the example discussion already exists
    And I should see "My Test Discussion"


  Scenario: Org-member user can edit
    # this user should already exists in the environment
    Given users:
      | name           | password | username      |
      | Kurt UserAtest | passw0rd | KurtuserAtest |
    Given I am logged in as "Kurt UserAtest"
    And I am on "/group/91/forum"
    And I see the text "My Test Discussion "
    Then I should not see "Join SpaceBase, then join the organizations you are interested in"
    # Note: name="forem-edit-discussion" should be added in the view for Group forum > Content: Link to edit content > rewrite
    # however, this doesn't render in goutte so the following doesn't appear on the page
    #TODO figure out why admin-links aren't displaying (screenshot doesn't show them)
    # And I click "forum-edit-discussion"
    # Then I should see "Topic"


  Scenario: Create a new post
    # this user should already exists in the environment
    Given users:
      | name           | password | username      |
      | Kurt UserAtest | passw0rd | KurtuserAtest |
    Given I am logged in as "Kurt UserAtest"
    And I am on "/group/91/forum"
    #TODO "New Post" isn't rendering in the goutte 
    When I click "New Post" 
    Then I should see "save"


  Scenario: Non-org-member cannot unpin a discussion
    #TODO reference https://gitlab.com/spacebase/spacebase/issues/132

  Scenario: Org-member can pin a discussion
    #TODO reference https://gitlab.com/spacebase/spacebase/issues/132

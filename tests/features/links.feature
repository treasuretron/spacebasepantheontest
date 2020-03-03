@api
Feature: Test social media links
  As a visitor to the site, the social media and feedback links should work.

    # edit /admin/structure/block/simple-block to add IDs like "footer-facebook"
    Scenario: Facebook link
    Given I am on "/"
    When I click "footer-facebook"
    Then I should see "SpaceBase NZ"


    Scenario: Twitter link
    Given I am on "/"
    When I click "footer-twitter"
    Then I should see "SpaceBase is a social enterprise with a vision to democratize space for everyone"


    Scenario: YouTube link
    Given I am on "/"
    When I click "footer-youtube"
    Then I should see "Videos"


    Scenario: LinkedIn link
    Given I am on "/"
    # When I click "footer-linkedin"
    # The following results in the LinkedIn authentication screen
    When I am on "https://www.linkedin.com/company/spacebase-nz/"
    Then I should see "SpaceBase is a social enterprise founded by three Edmund Hillary Fellows"




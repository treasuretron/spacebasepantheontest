@api
Feature: Basic test of social media links. Status:main:clean.
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


    # @ToDo, low low priority, fix the LinkedIn link check
    #Scenario: LinkedIn link
    #Given I am on "/"
    # When I click "footer-linkedin"
     # The above results in the LinkedIn authentication screen
     #  or Page Not Found
     #  and somehow doesn't work. 
    # Below is pointless, not testing our site in any way...
    #When I am on "https://www.linkedin.com/company/spacebase-nz/"
    #
    #Then I should see "SpaceBase is a social enterprise founded by three Edmund Hillary Fellows"




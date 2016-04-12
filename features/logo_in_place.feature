Feature: logo in place
  In order to be sure the site is looks good
  As anon oser
  I want to see a logo on homepage

  Scenario: open homepage as anon user
    Given I am a user with "anon" role
    When I open page "homepage"
    Then I see on page element with selector "#logo"

  Scenario: open homepage as admin user
    Given I am a user with "admin" role
    When I open page "homepage"
    Then I see on page element with selector "#logo"

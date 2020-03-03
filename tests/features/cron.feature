@api
Feature: Test cron. Status:main:clean.
  Run cron. This test might not need to be on the "main" if it's in another
  test we always run.

  Scenario: Run cron
    And I run cron

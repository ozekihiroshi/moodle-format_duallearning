@format @format_duallearning @javascript
Feature: Learners follow subsection display order
  In order to study a course in its intended order
  As a learner
  I need the next activity link to follow nested course content

  Scenario Outline: Next item opens the subsection material before the following parent activity
    Given the following config values are set as admin:
      | enablecompletion | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | learner | Test | Learner | learner@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format | numsections | initsections | enablecompletion | learningmode |
      | Nested course | NESTED | 0 | duallearning | 1 | 1 | 1 | <mode> |
    And the following "course enrolments" exist:
      | user | course | role |
      | learner | NESTED | student |
    And the following "activities" exist:
      | activity | name | course | idnumber | section | completion | content |
      | subsection | Unit | NESTED | unit | 1 | 0 | |
      | page | Inside unit | NESTED | inside | 2 | 1 | Nested lesson content |
      | page | After unit | NESTED | after | 1 | 1 | Later lesson content |
    When I log in as "learner"
    And I am on "Nested course" course homepage
    Then "Next item to check：Inside unit" "link" should exist
    And "Next item to check：After unit" "link" should not exist
    When I click on "Next item to check：Inside unit" "link"
    Then I should see "Nested lesson content"

    Examples:
      | mode |
      | path |
      | guided |

@format @format_duallearning @javascript
Feature: Completion changes refresh the learner overview
  In order to see my next available material without reloading
  As a student
  I need the overview to follow standard completion controls

  Scenario Outline: Complete and undo a material on the same course page
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | learner | Test | Learner | learner@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format | numsections | initsections | learningmode | enablecompletion |
      | Live overview | LIVE | 0 | duallearning | 1 | 1 | <mode> | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | learner | LIVE | student |
    And the following "activities" exist:
      | activity | name | course | section | content | completion |
      | page | Test material | LIVE | 1 | Read this material | 1 |
    When I log in as "learner"
    And I am on "Live overview" course homepage
    Then I should see "Materials marked complete: 0 / 1"
    When I press "Mark Test material as done"
    Then I should see "Materials marked complete: 1 / 1"
    And I should see "Currently available tracked activities are complete."
    When I press "Test material is marked as done. Press to undo."
    Then I should see "Materials marked complete: 0 / 1"
    And I should see "Next item to check"
    And I should not see "Currently available tracked activities are complete."

    Examples:
      | mode |
      | path |
      | guided |

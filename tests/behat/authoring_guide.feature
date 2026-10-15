@format @format_duallearning @format_duallearning_authoring_guide @javascript
Feature: Guided authoring and standard editing share the same unit
  In order to choose the appropriate editing workflow
  As a teacher
  I can move between the guide and Moodle editing without publishing my draft

  Scenario Outline: A draft keeps its identity and visibility across both views
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | author | Test | Author | author@example.com |
      | learner | Test | Learner | learner@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format | numsections | initsections | learningmode |
      | Guide course | GUIDE | 0 | duallearning | 2 | 1 | <mode> |
    And the following "course enrolments" exist:
      | user | course | role |
      | author | GUIDE | editingteacher |
      | learner | GUIDE | student |
    And the following "activities" exist:
      | activity | name | course | section | content | visible |
      | page | Draft explanation | GUIDE | 1 | Original content | 0 |
    And the Dual Learning section "1" in course "GUIDE" is hidden
    When I log in as "author"
    And I open Dual Learning authoring for section "1" in course "GUIDE"
    Then I should see "<hint>"
    And I should see "Saved materials: 1"
    And I should see "Unit: hidden"
    And "Edit content/settings: Draft explanation" "link" should not be visible
    When I press "Open standard course editing"
    Then I should see "Draft explanation"
    And "Back to unit authoring" "link" should exist
    When I follow "Back to unit authoring"
    Then I should see "Saved materials: 1"
    And I should see "Unit: hidden"
    When I click on "Draft explanation" "text" in the "#guide-existing + p + ul" "css_element"
    And I follow "Edit content/settings: Draft explanation"
    Then the field "Name" matches value "Draft explanation"
    When I press "Cancel"
    And I log out
    And I log in as "learner"
    And I am on "Guide course" course homepage
    Then I should not see "Draft explanation"
    And "Open standard course editing" "button" should not exist

    Examples:
      | mode | hint |
      | path | Self paced: provide explanations |
      | guided | Teacher guided: prepare what learners need |

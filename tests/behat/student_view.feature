@format @format_duallearning @format_duallearning_student_view @javascript
Feature: Authors check student-role visibility and return to their draft
  In order to check access without publishing unfinished material
  As an editing teacher
  I need a truthful role check and a route back to my unit

  Scenario Outline: A hidden draft stays hidden while the teacher checks and returns
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | author | Test | Author | author@example.com |
      | learner | Test | Learner | learner@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format | numsections | initsections | learningmode |
      | Authoring course | AUTHOR | 0 | duallearning | 2 | 1 | <mode> |
    And the following "course enrolments" exist:
      | user | course | role |
      | author | AUTHOR | editingteacher |
      | learner | AUTHOR | student |
    And the following "activities" exist:
      | activity | name | course | section | content | visible |
      | page | Private draft lesson | AUTHOR | 1 | Private draft body | 0 |
      | page | Available lesson | AUTHOR | 2 | Available lesson body | 1 |
    And the Dual Learning section "1" in course "AUTHOR" is hidden
    When I log in as "author"
    And I open Dual Learning authoring for section "1" in course "AUTHOR"
    Then I should see "Unit: hidden"
    When I press "Check with role: Student"
    Then I should see "You are checking with a switched role"
    And I should not see "Private draft lesson"
    And I should see "Available lesson"
    When I press "Return to my role and unit authoring"
    Then I should see "Continue building a unit"
    And I should see "Private draft lesson"
    And I should see "Unit: hidden"
    And "Edit content/settings" "link" should exist
    When I follow "Edit content/settings"
    Then the field "Name" matches value "Private draft lesson"
    When I press "Cancel"
    When I log out
    And I log in as "learner"
    And I am on "Authoring course" course homepage
    Then I should not see "Private draft lesson"
    And I should see "Available lesson"
    And "Return to my role and unit authoring" "button" should not exist

    Examples:
      | mode |
      | path |
      | guided |

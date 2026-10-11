@format @format_duallearning @format_duallearning_authoring_recovery @javascript
Feature: Teachers can start and recover authoring without knowing edit mode
  In order to prepare a lesson without losing my place
  As an editing teacher
  I need a visible creation entry, reversible introduction clearing and saved draft links

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | author | Test | Author | author@example.com |
      | learner | Test | Learner | learner@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format | numsections | initsections | learningmode |
      | Authoring recovery | RECOVER | 0 | duallearning | 0 | 1 | path |
    And the following "course enrolments" exist:
      | user | course | role |
      | author | RECOVER | editingteacher |
      | learner | RECOVER | student |

  Scenario: Create a hidden unit with editing off and reopen it from the new-unit form
    When I log in as "author"
    And I am on "Authoring recovery" course homepage
    Then "Create a unit" "link" should exist
    And "Open units and drafts" "link" should exist
    When I follow "Create a unit"
    And I set the following fields to these values:
      | Unit title | Daily temperatures |
      | Unit introduction | A learner introduction. |
    And I press "Clear introduction"
    Then the field "Unit title" matches value "Daily temperatures"
    And the field "Unit introduction" matches value ""
    When I press "Undo clear"
    Then the field "Unit introduction" matches value "A learner introduction."
    When I press "Create hidden draft"
    Then I should see "Saved draft"
    And I should see "No separate learning materials have been added yet."
    When I follow "Create a unit"
    And I follow "Open units and drafts"
    Then I should see "Draft — hidden from learners"
    When I click on "a[href*='/course/format/duallearning/unit.php'][href*='sectionid=']" "css_element"
    Then "Edit the unit introduction" "link" should exist
    And "Add an explanation" "link" should exist
    When I log out
    And I log in as "learner"
    And I am on "Authoring recovery" course homepage
    Then I should not see "Daily temperatures"
    And "Create a unit" "link" should not exist
    And "Open units and drafts" "link" should not exist

  Scenario: Undo clear never overwrites text entered after clearing
    When I log in as "author"
    And I am on "Authoring recovery" course homepage
    And I follow "Create a unit"
    And I set the following fields to these values:
      | Unit title | Keep my work |
      | Unit introduction | Original introduction. |
    And I press "Clear introduction"
    And I set the field "Unit introduction" to "New introduction."
    And I press "Undo clear"
    Then the field "Unit introduction" matches value "New introduction."
    And I should see "New text has been entered. It has been kept"
    When I press "Clear introduction"
    And I press "Undo clear"
    Then the field "Unit introduction" matches value "New introduction."

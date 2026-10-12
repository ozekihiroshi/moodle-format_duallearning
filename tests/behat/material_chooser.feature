@format @format_duallearning @javascript @_file_upload
Feature: Choose standard learning resources for the same hidden unit
  Teachers can select a Page, PDF file or URL with Moodle's chooser.

  Scenario Outline: Save three resource types and cancel a fourth using the standard forms
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | author | Test | Author | author@example.com |
      | learner | Test | Learner | learner@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Resource comparison | COMPARE | <format> | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | author | COMPARE | editingteacher |
      | learner | COMPARE | student |
    And the Dual Learning section "1" in course "COMPARE" is hidden
    When I log in as "author"
    And I choose "Page" for section "1" in "COMPARE" through its authoring entry
    And I set the following fields to these values:
      | Name | Temperature explanation |
      | Page content | Monday is 18 C. Tuesday is 21 C. |
    And I press "Save and return to course"
    And I choose "File" for section "1" in "COMPARE" through its authoring entry
    And I set the field "Name" to "Temperature PDF"
    And I upload "course/format/duallearning/tests/fixtures/temperatures.pdf" file to "Select files" filemanager
    And I press "Save and return to course"
    And I choose "URL" for section "1" in "COMPARE" through its authoring entry
    And I set the following fields to these values:
      | Name | Temperature reference |
      | External URL | https://example.org/temperatures |
    And I press "Save and display"
    Then I should see "Temperature reference"
    When I choose "Page" for section "1" in "COMPARE" through its authoring entry
    And I set the field "Name" to "Cancelled material"
    And I press "Cancel"
    Then the three comparison materials belong to hidden section "1" in "COMPARE"
    When I log out
    And I log in as "learner"
    And I am on "Resource comparison" course homepage
    Then I should not see "Temperature explanation"
    And I should not see "Temperature PDF"
    And I should not see "Temperature reference"

    Examples:
      | format |
      | topics |
      | duallearning |

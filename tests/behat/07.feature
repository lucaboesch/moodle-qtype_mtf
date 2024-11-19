@qtype @qtype_mtf @qtype_mtf_7
Feature: Step 7

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | c1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | c1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | c1        | Default for c1 |
    And the following "questions" exist:
      | questioncategory | qtype | name             | template     |
      | Default for c1   | mtf   | MTF-Question-001 | question_one |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript
  Scenario: Testcase 10, 11 A for Moodle for Moodle ≤ 4.2
    Given the site is running Moodle version 4.2 or lower

  # Change scoring Method to MTF1/0 and test evaluation.

    When I choose "Edit question" action for "MTF-Question-001" in the question bank
    And I click on "Scoring method" "link"
    And I click on "id_scoringmethod_mtfonezero" "radio"
    And I press "id_updatebutton"
    And I click on "Preview" "link"
    And I switch to "questionpreview" window
    And I click on "Preview options" "link"
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=2]" "css_element"
    And I press "Check"
    Then I should see "Mark 1.00 out of 1.00"
    And I press "Start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=1]" "css_element"
    And I press "Check"
    Then I should see "Mark 0.00 out of 1.00"

  @javascript
  Scenario: Testcase 10, 11 A for Moodle ≥ 4.3 and Moodle ≤ 4.4
    Given the site is running Moodle version 4.3 or higher
    And the site is running Moodle version 4.4 or lower

  # Change scoring Method to MTF1/0 and test evaluation.

    When I choose "Edit question" action for "MTF-Question-001" in the question bank
    And I click on "Scoring method" "link"
    And I click on "id_scoringmethod_mtfonezero" "radio"
    And I press "id_updatebutton"
    And I click on "Preview" "link"
    And I switch to "questionpreview" window
    And I click on "Preview options" "link"
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=2]" "css_element"
    And I press "Check"
    Then I should see "Mark 1.00 out of 1.00"
    And I press "Start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=1]" "css_element"
    And I press "Check"
    Then I should see "Mark 0.00 out of 1.00"


  @javascript
  Scenario: Testcase 10, 11 A for Moodle ≥ 4.5
    Given the site is running Moodle version 4.5 or higher

  # Change scoring Method to MTF1/0 and test evaluation.

    When I choose "Edit question" action for "MTF-Question-001" in the question bank
    And I click on "Scoring method" "link"
    And I click on "id_scoringmethod_mtfonezero" "radio"
    And I press "id_updatebutton"
    And I click on "Preview" "link"
    And I switch to "questionpreview" window
    And I press "Preview options"
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=2]" "css_element"
    And I press "Check"
    Then I should see "Mark 1.00 out of 1.00"
    And I press "Start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=1]" "css_element"
    And I press "Check"
    Then I should see "Mark 0.00 out of 1.00"

  @javascript
  Scenario: Testcase 10, 11 A for Moodle ≤ 4.2
    Given the site is running Moodle version 4.2 or lower

  # Change scoring Method to MTF1/0 and test evaluation.

    When I choose "Edit question" action for "MTF-Question-001" in the question bank
    And I click on "Scoring method" "link"
    And I click on "id_scoringmethod_mtfonezero" "radio"
    And I press "id_updatebutton"
    And I click on "Preview" "link"
    And I switch to "questionpreview" window
    And I click on "Preview options" "link"
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=2]" "css_element"
    And I press "Check"
    Then I should see "Mark 1.00 out of 1.00"
    And I press "Start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=1]" "css_element"
    And I press "Check"
    Then I should see "Mark 0.00 out of 1.00"

  @javascript
  Scenario: Testcase 10, 11 A for Moodle ≥ 4.3 and Moodle ≤ 4.4
    Given the site is running Moodle version 4.3 or higher
    And the site is running Moodle version 4.4 or lower

  # Change scoring Method to MTF1/0 and test evaluation.

    When I choose "Edit question" action for "MTF-Question-001" in the question bank
    And I click on "Scoring method" "link"
    And I click on "id_scoringmethod_mtfonezero" "radio"
    And I press "id_updatebutton"
    And I click on "Preview" "link"
    And I switch to "questionpreview" window
    And I click on "Preview options" "link"
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=2]" "css_element"
    And I press "Check"
    Then I should see "Mark 1.00 out of 1.00"
    And I press "Start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=1]" "css_element"
    And I press "Check"
    Then I should see "Mark 0.00 out of 1.00"

  @javascript
  Scenario: Testcase 10, 11 A for Moodle ≥ 4.5
    Given the site is running Moodle version 4.5 or higher

  # Change scoring Method to MTF1/0 and test evaluation.

    When I choose "Edit question" action for "MTF-Question-001" in the question bank
    And I click on "Scoring method" "link"
    And I click on "id_scoringmethod_mtfonezero" "radio"
    And I press "id_updatebutton"
    And I click on "Preview" "link"
    And I switch to "questionpreview" window
    And I press "Preview options"
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=2]" "css_element"
    And I press "Check"
    Then I should see "Mark 1.00 out of 1.00"
    And I press "Start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=1]" "css_element"
    And I press "Check"
    Then I should see "Mark 0.00 out of 1.00"

  @javascript
  Scenario: Testcase 10, 11 B for Moodle ≤ 4.2
    Given the site is running Moodle version 4.2 or lower
  # Change scoring Method to Subpoints and test evaluation.

    When I choose "Edit question" action for "MTF-Question-001" in the question bank
    And I click on "Scoring method" "link"
    And I click on "id_scoringmethod_subpoints" "radio"
    And I press "id_updatebutton"
    And I click on "Preview" "link"
    And I switch to "questionpreview" window
    And I click on "Preview options" "link"
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=2]" "css_element"
    And I press "Check"
    Then I should see "Mark 1.00 out of 1.00"
    And I press "Start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=1]" "css_element"
    And I press "Check"
    Then I should see "Mark 0.50 out of 1.00"

  @javascript
  Scenario: Testcase 10, 11 B for Moodle ≥ 4.3 and Moodle ≤ 4.4
    Given the site is running Moodle version 4.3 or higher
    And the site is running Moodle version 4.4 or lower

  # Change scoring Method to Subpoints and test evaluation.

    When I choose "Edit question" action for "MTF-Question-001" in the question bank
    And I click on "Scoring method" "link"
    And I click on "id_scoringmethod_subpoints" "radio"
    And I press "id_updatebutton"
    And I click on "Preview" "link"
    And I switch to "questionpreview" window
    And I click on "Preview options" "link"
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=2]" "css_element"
    And I press "Check"
    Then I should see "Mark 1.00 out of 1.00"
    And I press "Start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=1]" "css_element"
    And I press "Check"
    Then I should see "Mark 0.50 out of 1.00"

  @javascript
  Scenario: Testcase 10, 11 B for Moodle ≥ 4.5
    Given the site is running Moodle version 4.5 or higher

  # Change scoring Method to Subpoints and test evaluation.

    When I choose "Edit question" action for "MTF-Question-001" in the question bank
    And I click on "Scoring method" "link"
    And I click on "id_scoringmethod_subpoints" "radio"
    And I press "id_updatebutton"
    And I click on "Preview" "link"
    And I switch to "questionpreview" window
    And I press "Preview options"
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=2]" "css_element"
    And I press "Check"
    Then I should see "Mark 1.00 out of 1.00"
    And I press "Start again"
    And I click on ".qtype_mtf_row:contains('option text 1') input[value=1]" "css_element"
    And I click on ".qtype_mtf_row:contains('option text 2') input[value=1]" "css_element"
    And I press "Check"
    Then I should see "Mark 0.50 out of 1.00"

<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package qtype_mtf
 * @author Martin Hanusch martin.hanusch@let.ethz.ch
 * @copyright ETHz 2018 martin.hanusch@let.ethz.ch
 */

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->dirroot . '/lib/moodlelib.php');
require_once($CFG->dirroot . '/question/type/mtf/lib.php');

$courseid = optional_param('courseid', 0, PARAM_INT);
$categoryid = optional_param('categoryid', 0, PARAM_INT);
$all = optional_param('all', 0, PARAM_INT);
$dryrun = optional_param('dryrun', 1, PARAM_INT);

@set_time_limit(0);
@ini_set('memory_limit', '3072M'); // Whooping 3GB due to huge number of questions text size.

require_login();

if (!is_siteadmin()) {
    echo 'You are not a Website Administrator!';
    die();
}

$starttime = microtime(1);

$sql = "SELECT q.*
        FROM {question} q
        WHERE q.qtype = 'mtf'
        and q.id in (select questionid from {qtype_mtf_options})
        ";
$params = array();

// **********************************************************************************************************************
// General Page Setup
echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' .
'<style>body{font-family: "Courier New", Courier, monospace; font-size: 12px; background: #f9f9f9; color: #4d4d4d;}</style></head>';

echo "================================================================================<br/>\n";
echo "M I G R A T I O N :: MTF to Multichoice<br/>\n";
echo "================================================================================<br/>\n";

if (!$all && (!($courseid > 0 || $categoryid > 0))) {
    echo "
    <br/>\nParameters:<br/><br/>\n\n
    ================================================================================<br/>\n
    You need to specifiy ONE of the following three parameters in the url:
    <ul>
        <li><b>courseid</b></li>
        <li><b>categoryid</b></li>
        <li><b>all</b> (values: 0,1)</li>
    </ul>
    <b>IMPORTANT AND STRONGLY RECOMMENDED:</b><br/>\n
    <ul>
        <li><b>dryrun</b> (values: 0,1)</li>
    </ul>
    The Dryrun Option is enabled by default.<br/>\n
    With Dryrun enabled no changes will be made to the database.<br/>\n
    To disable Dryrun, add it as parameter to the url and set the value to 0.<br/><br/>\n\n
    ================================================================================<br/><br/>\n\n
    Examples:<br/><br/>\n\n
    ================================================================================<br/>\n
	<ul>
        <li><strong>Migrate MTF Questions in a specific course</strong>:<br/>\n
        MOODLE_URL/question/type/mtf/bin/mig_mtf_to_multichoice.php?<b>courseid=55</b>
        <li><strong>Migrate MTF Questions in a specific category</strong>:<br/>\n
        MOODLE_URL/question/type/mtf/bin/mig_mtf_to_multichoice.php?<b>categoryid=1</b>
        <li><strong>Migrate all MTF Questions</strong>:<br/>\n
        MOODLE_URL/question/type/mtf/bin/mig_mtf_to_multichoice.php?<b>all=1</b>
        <li><strong>Disable Dryrun</strong>:<br/>\n
        MOODLE_URL/question/type/mtf/bin/mig_mtf_to_multichoice.php?all=1<b>&dryrun=0</b>
	</ul>
    <br/>\n";
    die();
}

// **********************************************************************************************************************
// Dry Run info
if ($dryrun) {
    echo "--------------------------------------------------------------------------------<br/><br/>\n\n";
    echo "Dryrun enabled: NO changes to the database will be made!<br/><br/>\n\n";
    echo "--------------------------------------------------------------------------------<br/>\n";
    echo "================================================================================<br/>\n";
}

// **********************************************************************************************************************
// Get the categories : Case 1
if($all == 1) {
    if ($categories = $DB->get_records('question_categories', array())) {
        echo "Migration of all MTF Questions<br/>\n";
    } else {
        echo "<br/><font color='red'>Could not get categories</font><br/>\n";
        die();
    }
}
// Get the categories : Case 2
if ($courseid > 0) {
    if (!$course = $DB->get_record('course', array('id' => $courseid
    ))) {
        echo "<br/><font color='red'>Course with ID $courseid  not found...!</font><br/>\n";
        die();
    }
    $coursecontext = context_course::instance($courseid);
    $categories = $DB->get_records('question_categories',
            array('contextid' => $coursecontext->id
            ));
    $catids = array_keys($categories);
    if (!empty($catids)) {
        echo "Migration of MTF Questions within courseid " . $courseid . " <br/>\n";
        list($csql, $params) = $DB->get_in_or_equal($catids);
        $sql .= " AND category $csql ";
    } else {
        echo "<br/><font color='red'>No question categories for course found... weird!</font><br/>\n";
        echo "I'm not doing anything without restrictions!\n";
        die();
    }
}
// Get the categories : Case 3
if ($categoryid > 0) {
    if ($categories[$categoryid] = $DB->get_record('question_categories', array('id' => $categoryid
    ))) {
        echo 'Migration of MTF questions within category "' . $categories[$categoryid]->name . "\"<br/>\n";
        $sql .= ' AND category = :category ';
        $params = array('category' => $categoryid
        );
    } else {
        echo "<br/><font color='red'>Question category with ID $categoryid  not found...!</font><br/>\n";
        die();
    }
}

// **********************************************************************************************************************
// Get the questions based on the previous set parameters
$sql .= " ORDER BY category ASC";
$questions = $DB->get_records_sql($sql, $params);
echo 'Questions found: ' . count($questions) . "<br/>\n";
echo "================================================================================<br/><br/>\n\n";

// **********************************************************************************************************************
$transaction = $DB->start_delegated_transaction();

// **********************************************************************************************************************
// Processing the single questions
echo "Migrating questions...<br/>\n";
$num_migrated = 0;
$num_categories = 0;
$category_map = [];

foreach ($questions as $question) {
    set_time_limit(600);
    $question->oldid = $question->id;
    $question->oldname =  $question->name; 
    // *****************************************************
    // Getting related question data
    $question_columns = $DB->get_records('qtype_mtf_columns', array('questionid' => $question->id), ' id ASC ');
    $question_options = $DB->get_record('qtype_mtf_options', array('questionid' => $question->id));
    $question_rows = $DB->get_records('qtype_mtf_rows', array('questionid' => $question->id), ' id ASC ');
    $question_weights = $DB->get_records('qtype_mtf_weights', array('questionid' => $question->id), ' id ASC ');

    // *****************************************************
    // Checking for possible errors before doing anyting
    // Getting question weights
    // If weights are not mapable, skip the whole question
    $question_weights = get_weights($question_weights);
    if ($question_weights["error"]) {
        echo '[<font style="color:#ff0909;">ERROR</font>] question "' . $question->oldname . 
        '" (ID: ' . "<a href='" . $CFG->wwwroot . "/question/preview.php?id=". $question->oldid . 
        "' target='_blank'>".$question->oldid. "</a>" ."): " . $question_weights["message"] . "<br/>\n";
        continue;
    } else {
        $num_migrated++;
    }

    // *****************************************************
    // Duplicating a category
    if (!array_key_exists($question->category, $category_map)) {
        $category_to_insert = clone $categories[$question->category];
        $category_to_insert->name .= " (MTF to MC)"; 
        $category_to_insert->stamp = make_unique_id_code();
        unset($category_to_insert->id);
        $category_map[$question->category] = $DB->insert_record('question_categories', $category_to_insert);
        $num_categories++;
        echo '[<font style="color:#228d00;">ADDED</font>] category "' . $categories[$question->category]->name . 
        '" >>> "' . $category_to_insert->name . '" (ID: ' . $category_map[$question->category] . ")<br/>\n";
    }
    
    // *****************************************************
    // Duplicating  mdl_question
    // --->         mdl_question
    unset($question->id);
    $question->category = $category_map[$question->category];
    $question->name = substr($question->name . " (MC " . date("Y-m-d H:i:s") . ")", 0, 255);
    $question->qtype = "multichoice";
    $question->stamp = make_unique_id_code();
    $question->version = make_unique_id_code();
    $question->timecreated = time();
    $question->timemodified = time();
    $question->modifiedby = $USER->id;
    $question->createdby = $USER->id;
    $question->id = $DB->insert_record('question', $question);

    // *****************************************************
    // Tansferring  md_qtype_mtf_rows + mdl_qtype_mtf_weights         
    // --->         mdl_question_answers
    foreach ($question_rows as $key => $row) {
        $entry = new stdClass();
        $entry->question = $question->id;
        $entry->answer = $question_rows[$key]->optiontext;
        $entry->answerformat = $question_rows[$key]->optiontextformat;
        $entry->fraction = $question_weights["message"][$row->number];
        $entry->feedback = $question_rows[$key]->optionfeedback;
        $entry->feedbackformat = $question_rows[$key]->optionfeedbackformat;
        $DB->insert_record('question_answers', $entry);
        unset($entry);
    }

    // *****************************************************
    // Transferring md_qtype_mtf_options 
    // --->         md_qtype_multichoice_options
    $entry = new stdClass();
    $entry->questionid = $question->id;
    $entry->layout = 0;
    $entry->single = 0;
    $entry->shuffleanswers = $question_options->shuffleanswers;
    $entry->correctfeedback = "Your answer is correct"; // ?
    $entry->correctfeedbackformat = 1;
    $entry->partiallycorrectfeedback = "Your answer is partially correct"; // ?
    $entry->partiallycorrectfeedbackformat = 1;
    $entry->incorrectfeedback = "Your answer is incorrect"; // ?
    $entry->incorrectfeedbackformat = 1;
    $entry->answernumbering = $question_options->answernumbering;
    $entry->shownumcorrect = 1; // ?
    $DB->insert_record('qtype_multichoice_options', $entry);
    unset($entry);

    // *****************************************************
    // Output: Question Migration Success
    echo '[<font style="color:#228d00;">ADDED</font>] › question "' . $question->oldname . 
    '" (ID: ' . $question->oldid . ') >>> "' . $question->name . '" (ID: ' . $question->id . ")<br/>\n";
}

// **********************************************************************************************************************
// Save changes to the database
if ($dryrun == 0) {
    $transaction->allow_commit();
}

echo "<br/>\n";
echo "================================================================================<br/>\n";
echo "SCRIPT DONE: Time needed: " . round(microtime(1) - $starttime, 4) . " seconds.<br/>\n";
echo $num_categories . " categories duplicated<br/>\n";
echo $num_migrated . "/" . count($questions) . " questions migrated<br/>\n";
echo "================================================================================<br/>\n";

// **********************************************************************************************************************
// The mapping function we all need but do not deserve
function get_weights($table) {
    $percentage_valid = array( // Todo: currently hardcoded. If possible -> replace with function call
        1.0000000, 0.9000000, 0.8333333, 0.8000000, 0.7500000, 0.7000000, 0.6666667, 0.6000000,
        0.5000000, 0.4000000, 0.3333333, 0.3000000, 0.2500000, 0.2000000, 0.1666667, 0.1428571, 
        0.1250000, 0.1111111, 0.1000000, 0.0500000,-0.9000000,-0.8333333,-0.8000000,-0.7500000, 
        -0.7000000,-0.6666667,-0.6000000,-0.5000000,-0.4000000,-0.3333333,-0.3000000,-0.2500000, 
        -0.2000000,-0.1666667,-0.1428571,-0.1250000,-0.1111111,-0.1000000,-0.0500000,-1.0000000
    );

    // *****************************************************
    // Creating the answers array
    $answers = [];
    $num_correct = 0;
    foreach ($table as $record) {
        if ($record->columnnumber == 1) {
            $answers[$record->rownumber] = $record->weight;
            $record->weight > 0 ? $num_correct++ : null;  
        }
    }

    // *****************************************************
    // Error - Case 1: All answers are marked as incorrect
    if ($num_correct == 0) {
        return array("error"=>true, "message"=>"all answers are incorrect");
    }

    // *****************************************************
    // Creating percentages
    $percentage_correct = 1 / $num_correct;
    $percentage_incorrect = -1 / sizeOf($answers);

    // *****************************************************
    // Error - Case 2: Percentage value does not exist
    if (!in_array($percentage_correct, $percentage_valid) && !in_array($percentage_incorrect, $percentage_valid) ) {
        // Todo: Do something here
    }

    // *****************************************************
    // Adding percentages to the answers array
    foreach ($answers as $key => $record) {
        $answers[$key] > 0 ? $answers[$key] = $percentage_correct : $answers[$key] = $percentage_incorrect;
    }
    return array("error"=>false, "message"=>$answers);
}
















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
 * English strings for local_sectionbulk.
 *
 * @package    local_sectionbulk
 * @copyright  2026 SiteEcuador - Msg. Franklin Moya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Bulk sections and quizzes manager';
$string['sectionbulk:manage'] = 'Manage sections and quizzes in bulk';
$string['intro'] = 'Preview and apply bulk changes to course sections and quizzes. A preview is always required before changes can be applied.';
$string['credits'] = 'Developed by SiteEcuador · Msg. Franklin Moya';
$string['scope'] = 'Scope';
$string['scope_course'] = 'Specific course IDs';
$string['scope_category'] = 'Course category';
$string['courseids'] = 'Course IDs';
$string['courseids_help'] = 'One or more course IDs separated by commas, for example: 12,15,18.';
$string['categoryid'] = 'Category';
$string['recursive'] = 'Include subcategories';
$string['sectiontarget'] = 'Section target';
$string['sectionmode'] = 'Find section by';
$string['sectionmode_number'] = 'Section number';
$string['sectionmode_name'] = 'Exact section name';
$string['sectionnumber'] = 'Section number';
$string['sectionname'] = 'Section name';
$string['allmatches'] = 'Process all matching sections when a course has duplicate section names';
$string['operation'] = 'Operation';
$string['op_date_from_set'] = 'Section — add/update Date from';
$string['op_date_until_set'] = 'Section — add/update Date until';
$string['op_date_range_set'] = 'Section — add/update Date from + Date until';
$string['op_date_from_remove'] = 'Section — remove Date from';
$string['op_date_until_remove'] = 'Section — remove Date until';
$string['op_date_all_remove'] = 'Section — remove all date restrictions';
$string['op_profile_set'] = 'Section — add/update user profile field restriction';
$string['op_completion_remove'] = 'Section — remove Activity completion restrictions';
$string['op_section_create'] = 'Section — create a new section';
$string['op_quiz_open_set'] = 'Quiz — set Open the quiz';
$string['op_quiz_close_set'] = 'Quiz — set Close the quiz';
$string['op_quiz_range_set'] = 'Quiz — set Open + Close';
$string['op_quiz_open_remove'] = 'Quiz — remove Open the quiz';
$string['op_quiz_close_remove'] = 'Quiz — remove Close the quiz';
$string['op_quiz_dates_remove'] = 'Quiz — remove Open + Close';
$string['op_quiz_attempts_set'] = 'Quiz — set allowed attempts';
$string['op_quiz_grademethod_set'] = 'Quiz — set grading method';
$string['op_quiz_highest_multiattempt'] = 'Quiz — use Highest grade when 2+ attempts are allowed';
$string['fromdate'] = 'Date from';
$string['untildate'] = 'Date until';
$string['profilefield'] = 'Custom profile field';
$string['profileoperator'] = 'Profile operator';
$string['profilevalue'] = 'Profile value';
$string['showcondition'] = 'Show the restriction information when unavailable';
$string['newsectionname'] = 'New section name';
$string['newsectionsummary'] = 'New section summary';
$string['newsectionposition'] = 'Position';
$string['newsectionposition_help'] = 'Use 0 to add the section at the end. Use 1 or greater to insert it at that section number.';
$string['newsectionvisible'] = 'New section visible';
$string['allowduplicates'] = 'Allow creating a section with a duplicate name';
$string['quiztarget'] = 'Quiz target';
$string['quiztargetmode'] = 'Find quizzes by';
$string['quiztarget_all'] = 'All quizzes in the selected courses';
$string['quiztarget_name'] = 'Exact quiz name';
$string['quiztarget_idnumber'] = 'Activity ID number';
$string['quizname'] = 'Exact quiz name';
$string['quizidnumber'] = 'Quiz activity ID number';
$string['quizsectionnumber'] = 'Optional section number filter';
$string['quizsectionnumber_help'] = 'Leave empty to search all sections in each course. Enter a section number to limit quiz matching to that section.';
$string['quizallmatches'] = 'Process all matching quizzes if a course contains more than one match';
$string['quizopen'] = 'Open the quiz';
$string['quizclose'] = 'Close the quiz';
$string['quizattempts'] = 'Allowed attempts';
$string['quizattempts_help'] = 'Use 0 for unlimited attempts, 1 for one attempt, 2 for two attempts, and so on.';
$string['quizgrademethod'] = 'Grading method';
$string['quizgrademethod_highest'] = 'Highest grade';
$string['quizgrademethod_average'] = 'Average grade';
$string['quizgrademethod_first'] = 'First attempt';
$string['quizgrademethod_last'] = 'Last attempt';
$string['quizincludeunlimited'] = 'Treat unlimited attempts as multiple attempts';
$string['quizregrade'] = 'Recalculate final quiz grades and update the gradebook after changing the grading method';
$string['preview'] = 'Preview';
$string['applychanges'] = 'Apply changes';
$string['cancel'] = 'Cancel';
$string['previewtitle'] = 'Preview';
$string['resulttitle'] = 'Result';
$string['course'] = 'Course';
$string['target'] = 'Target';
$string['section'] = 'Section';
$string['status'] = 'Status';
$string['details'] = 'Details';
$string['changed'] = 'Change required';
$string['unchanged'] = 'No change';
$string['skipped'] = 'Skipped';
$string['error'] = 'Error';
$string['applied'] = 'Applied';
$string['confirmation'] = 'Review the preview carefully. The next button will apply these changes to Moodle.';
$string['nothingtoapply'] = 'There are no changes to apply.';
$string['invalidpreview'] = 'The preview has expired or is invalid. Generate it again.';
$string['coursesfound'] = 'Courses found: {$a}';
$string['changesfound'] = 'Proposed changes: {$a}';
$string['changesapplied'] = 'Changes applied: {$a}';
$string['dryrunnote'] = 'Preview mode does not write any changes to the database.';
$string['profile_contains'] = 'contains';
$string['profile_doesnotcontain'] = 'does not contain';
$string['profile_isequalto'] = 'is equal to';
$string['profile_startswith'] = 'starts with';
$string['profile_endswith'] = 'ends with';
$string['profile_isempty'] = 'is empty';
$string['profile_isnotempty'] = 'is not empty';
$string['validation_courseids'] = 'Enter at least one valid course ID.';
$string['validation_category'] = 'Select a category.';
$string['validation_sectionnumber'] = 'Enter a valid section number.';
$string['validation_sectionname'] = 'Enter the exact section name.';
$string['validation_profilefield'] = 'Select a custom profile field.';
$string['validation_profilevalue'] = 'Enter a value for this operator.';
$string['validation_daterange'] = 'Date from must be earlier than Date until.';
$string['validation_newsectionname'] = 'Enter the new section name.';
$string['validation_quizname'] = 'Enter the exact quiz name.';
$string['validation_quizidnumber'] = 'Enter the quiz activity ID number.';
$string['validation_quizsectionnumber'] = 'Enter a valid section number or leave the field empty.';
$string['validation_quizrange'] = 'Open the quiz must be earlier than Close the quiz.';
$string['validation_quizattempts'] = 'Allowed attempts must be 0 or greater.';
$string['eventbulkchangeapplied'] = 'Bulk section and quiz changes applied';
$string['eventdescription'] = 'The user with id {$a} applied bulk course changes using the Bulk sections and quizzes manager.';
$string['detail_sectionnotfound'] = 'The target section was not found in this course.';
$string['detail_duplicate'] = '{$a} matching sections were found; skipped for safety.';
$string['detail_formatnosections'] = 'Course format {$a} does not use sections.';
$string['detail_sectionexists'] = 'A section with this name already exists.';
$string['detail_sectioncreate'] = 'A new section would be created at position {$a} (0 means at the end).';
$string['detail_sectioncreated'] = 'New section created.';
$string['detail_datefromset'] = 'Date from will be added or updated; all other restrictions are preserved.';
$string['detail_dateuntilset'] = 'Date until will be added or updated; all other restrictions are preserved.';
$string['detail_daterangeset'] = 'Date from and Date until will be added or updated; all other restrictions are preserved.';
$string['detail_alreadyconfigured'] = 'The requested value is already configured.';
$string['detail_removedcount'] = '{$a} matching date restriction(s) will be removed; all other restrictions are preserved.';
$string['detail_removedcompletion'] = '{$a} Activity completion restriction(s) will be removed; all other restrictions are preserved.';
$string['detail_nothingfound'] = 'No matching restriction was found.';
$string['detail_profileset'] = 'The user profile field restriction will be added or updated; all other restrictions are preserved.';
$string['detail_quiznotfound'] = 'No quiz matched the selected target in this course.';
$string['detail_quizduplicate'] = '{$a} matching quizzes were found; skipped for safety. Enable processing of all matches if intended.';
$string['detail_quizopenset'] = 'Open the quiz will be updated.';
$string['detail_quizcloseset'] = 'Close the quiz will be updated.';
$string['detail_quizrangeset'] = 'Open the quiz and Close the quiz will be updated.';
$string['detail_quizopenremoved'] = 'Open the quiz will be removed.';
$string['detail_quizcloseremoved'] = 'Close the quiz will be removed.';
$string['detail_quizdatesremoved'] = 'Open the quiz and Close the quiz will be removed.';
$string['detail_quizattemptsset'] = 'Allowed attempts will be set to {$a} (0 means unlimited).';
$string['detail_quizgrademethodset'] = 'The quiz grading method will be changed to {$a}.';
$string['detail_quizhighestset'] = 'The grading method will be changed to Highest grade.';
$string['detail_quiznotmultiattempt'] = 'This quiz does not meet the 2+ attempts criterion.';
$string['detail_quizinvalidrange'] = 'The resulting Open the quiz date must be earlier than Close the quiz.';
$string['quizlabel'] = 'Quiz: {$a->quiz} · {$a->section}';
$string['detail_unknownoperation'] = 'Unknown operation.';
$string['error_invalidavailability'] = 'Availability contains invalid JSON: {$a}';
$string['error_encodeavailability'] = 'Could not generate availability JSON.';
$string['privacy:metadata'] = 'The Bulk sections and quizzes manager does not store personal data.';

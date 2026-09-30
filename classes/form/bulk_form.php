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
 * Bulk manager form.
 *
 * @package    local_sectionbulk
 * @copyright  2026 SiteEcuador - Msg. Franklin Moya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sectionbulk\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Main bulk operations form.
 */
class bulk_form extends \moodleform {
    public function definition(): void {
        global $DB;

        $mform = $this->_form;

        $mform->addElement('header', 'scopehdr', get_string('scope', 'local_sectionbulk'));
        $mform->addElement('select', 'scope', get_string('scope', 'local_sectionbulk'), [
            'course' => get_string('scope_course', 'local_sectionbulk'),
            'category' => get_string('scope_category', 'local_sectionbulk'),
        ]);
        $mform->setDefault('scope', 'category');

        $mform->addElement('text', 'courseids', get_string('courseids', 'local_sectionbulk'));
        $mform->setType('courseids', PARAM_TEXT);
        $mform->addHelpButton('courseids', 'courseids', 'local_sectionbulk');
        $mform->hideIf('courseids', 'scope', 'neq', 'course');

        $categories = \core_course_category::make_categories_list();
        $mform->addElement('select', 'categoryid', get_string('categoryid', 'local_sectionbulk'), $categories);
        $mform->hideIf('categoryid', 'scope', 'neq', 'category');

        $mform->addElement('advcheckbox', 'recursive', get_string('recursive', 'local_sectionbulk'));
        $mform->setDefault('recursive', 0);
        $mform->hideIf('recursive', 'scope', 'neq', 'category');

        $mform->addElement('header', 'operationhdr', get_string('operation', 'local_sectionbulk'));
        $operations = [
            'date_from_set' => get_string('op_date_from_set', 'local_sectionbulk'),
            'date_until_set' => get_string('op_date_until_set', 'local_sectionbulk'),
            'date_range_set' => get_string('op_date_range_set', 'local_sectionbulk'),
            'date_from_remove' => get_string('op_date_from_remove', 'local_sectionbulk'),
            'date_until_remove' => get_string('op_date_until_remove', 'local_sectionbulk'),
            'date_all_remove' => get_string('op_date_all_remove', 'local_sectionbulk'),
            'profile_set' => get_string('op_profile_set', 'local_sectionbulk'),
            'completion_remove' => get_string('op_completion_remove', 'local_sectionbulk'),
            'section_create' => get_string('op_section_create', 'local_sectionbulk'),
            'quiz_open_set' => get_string('op_quiz_open_set', 'local_sectionbulk'),
            'quiz_close_set' => get_string('op_quiz_close_set', 'local_sectionbulk'),
            'quiz_range_set' => get_string('op_quiz_range_set', 'local_sectionbulk'),
            'quiz_open_remove' => get_string('op_quiz_open_remove', 'local_sectionbulk'),
            'quiz_close_remove' => get_string('op_quiz_close_remove', 'local_sectionbulk'),
            'quiz_dates_remove' => get_string('op_quiz_dates_remove', 'local_sectionbulk'),
            'quiz_attempts_set' => get_string('op_quiz_attempts_set', 'local_sectionbulk'),
            'quiz_grademethod_set' => get_string('op_quiz_grademethod_set', 'local_sectionbulk'),
            'quiz_highest_multiattempt' => get_string('op_quiz_highest_multiattempt', 'local_sectionbulk'),
        ];
        $mform->addElement('select', 'operation', get_string('operation', 'local_sectionbulk'), $operations);

        $quizoperations = [
            'quiz_open_set', 'quiz_close_set', 'quiz_range_set', 'quiz_open_remove', 'quiz_close_remove',
            'quiz_dates_remove', 'quiz_attempts_set', 'quiz_grademethod_set', 'quiz_highest_multiattempt',
        ];
        $sectionoperations = array_values(array_diff(array_keys($operations), $quizoperations));

        $mform->addElement('header', 'sectionhdr', get_string('sectiontarget', 'local_sectionbulk'));
        $mform->hideIf('sectionhdr', 'operation', 'in', array_merge($quizoperations, ['section_create']));
        $mform->addElement('select', 'sectionmode', get_string('sectionmode', 'local_sectionbulk'), [
            'all' => get_string('sectionmode_all', 'local_sectionbulk'),
            'number' => get_string('sectionmode_number', 'local_sectionbulk'),
            'numbers' => get_string('sectionmode_numbers', 'local_sectionbulk'),
            'name' => get_string('sectionmode_name', 'local_sectionbulk'),
        ]);
        $mform->setDefault('sectionmode', 'number');
        $mform->hideIf('sectionmode', 'operation', 'in', array_merge($quizoperations, ['section_create']));

        $mform->addElement('text', 'sectionnumber', get_string('sectionnumber', 'local_sectionbulk'));
        $mform->setType('sectionnumber', PARAM_INT);
        $mform->setDefault('sectionnumber', 1);
        $mform->hideIf('sectionnumber', 'sectionmode', 'neq', 'number');
        $mform->hideIf('sectionnumber', 'operation', 'in', array_merge($quizoperations, ['section_create']));

        $mform->addElement('text', 'sectionnumbers', get_string('sectionnumbers', 'local_sectionbulk'));
        $mform->setType('sectionnumbers', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('sectionnumbers', 'sectionnumbers', 'local_sectionbulk');
        $mform->hideIf('sectionnumbers', 'sectionmode', 'neq', 'numbers');
        $mform->hideIf('sectionnumbers', 'operation', 'in', array_merge($quizoperations, ['section_create']));

        $mform->addElement('text', 'sectionname', get_string('sectionname', 'local_sectionbulk'));
        $mform->setType('sectionname', PARAM_TEXT);
        $mform->hideIf('sectionname', 'sectionmode', 'neq', 'name');
        $mform->hideIf('sectionname', 'operation', 'in', array_merge($quizoperations, ['section_create']));

        $mform->addElement('advcheckbox', 'allmatches', get_string('allmatches', 'local_sectionbulk'));
        $mform->setDefault('allmatches', 0);
        $mform->hideIf('allmatches', 'sectionmode', 'neq', 'name');
        $mform->hideIf('allmatches', 'operation', 'in', array_merge($quizoperations, ['section_create']));

        $mform->addElement('date_time_selector', 'fromdate', get_string('fromdate', 'local_sectionbulk'));
        $mform->hideIf('fromdate', 'operation', 'in', array_diff(array_keys($operations), ['date_from_set', 'date_range_set']));

        $mform->addElement('date_time_selector', 'untildate', get_string('untildate', 'local_sectionbulk'));
        $mform->hideIf('untildate', 'operation', 'in', array_diff(array_keys($operations), ['date_until_set', 'date_range_set']));

        $fields = ['' => get_string('choosedots')];
        foreach ($DB->get_records('user_info_field', null, 'name ASC', 'id,name,shortname') as $field) {
            $fields[$field->shortname] = $field->name . ' (' . $field->shortname . ')';
        }
        $mform->addElement('select', 'profilefield', get_string('profilefield', 'local_sectionbulk'), $fields);
        $mform->hideIf('profilefield', 'operation', 'neq', 'profile_set');

        $operators = [
            'contains' => get_string('profile_contains', 'local_sectionbulk'),
            'doesnotcontain' => get_string('profile_doesnotcontain', 'local_sectionbulk'),
            'isequalto' => get_string('profile_isequalto', 'local_sectionbulk'),
            'startswith' => get_string('profile_startswith', 'local_sectionbulk'),
            'endswith' => get_string('profile_endswith', 'local_sectionbulk'),
            'isempty' => get_string('profile_isempty', 'local_sectionbulk'),
            'isnotempty' => get_string('profile_isnotempty', 'local_sectionbulk'),
        ];
        $mform->addElement('select', 'profileoperator', get_string('profileoperator', 'local_sectionbulk'), $operators);
        $mform->setDefault('profileoperator', 'contains');
        $mform->hideIf('profileoperator', 'operation', 'neq', 'profile_set');

        $mform->addElement('text', 'profilevalue', get_string('profilevalue', 'local_sectionbulk'));
        $mform->setType('profilevalue', PARAM_TEXT);
        $mform->hideIf('profilevalue', 'operation', 'neq', 'profile_set');
        $mform->hideIf('profilevalue', 'profileoperator', 'in', ['isempty', 'isnotempty']);

        $mform->addElement('advcheckbox', 'showcondition', get_string('showcondition', 'local_sectionbulk'));
        $mform->setDefault('showcondition', 1);
        $mform->hideIf('showcondition', 'operation', 'in', array_diff(array_keys($operations), [
            'date_from_set', 'date_until_set', 'date_range_set', 'profile_set',
        ]));

        $mform->addElement('text', 'newsectionname', get_string('newsectionname', 'local_sectionbulk'));
        $mform->setType('newsectionname', PARAM_TEXT);
        $mform->hideIf('newsectionname', 'operation', 'neq', 'section_create');

        $mform->addElement('editor', 'newsectionsummary', get_string('newsectionsummary', 'local_sectionbulk'), null, ['maxfiles' => 0]);
        $mform->setType('newsectionsummary', PARAM_RAW);
        $mform->hideIf('newsectionsummary', 'operation', 'neq', 'section_create');

        $mform->addElement('text', 'newsectionposition', get_string('newsectionposition', 'local_sectionbulk'));
        $mform->setType('newsectionposition', PARAM_INT);
        $mform->setDefault('newsectionposition', 0);
        $mform->addHelpButton('newsectionposition', 'newsectionposition', 'local_sectionbulk');
        $mform->hideIf('newsectionposition', 'operation', 'neq', 'section_create');

        $mform->addElement('advcheckbox', 'newsectionvisible', get_string('newsectionvisible', 'local_sectionbulk'));
        $mform->setDefault('newsectionvisible', 1);
        $mform->hideIf('newsectionvisible', 'operation', 'neq', 'section_create');

        $mform->addElement('advcheckbox', 'allowduplicates', get_string('allowduplicates', 'local_sectionbulk'));
        $mform->setDefault('allowduplicates', 0);
        $mform->hideIf('allowduplicates', 'operation', 'neq', 'section_create');

        $mform->addElement('header', 'quizhdr', get_string('quiztarget', 'local_sectionbulk'));
        $mform->hideIf('quizhdr', 'operation', 'in', $sectionoperations);
        $mform->addElement('select', 'quiztargetmode', get_string('quiztargetmode', 'local_sectionbulk'), [
            'all' => get_string('quiztarget_all', 'local_sectionbulk'),
            'name' => get_string('quiztarget_name', 'local_sectionbulk'),
            'idnumber' => get_string('quiztarget_idnumber', 'local_sectionbulk'),
        ]);
        $mform->setDefault('quiztargetmode', 'all');
        $mform->hideIf('quiztargetmode', 'operation', 'in', $sectionoperations);

        $mform->addElement('text', 'quizname', get_string('quizname', 'local_sectionbulk'));
        $mform->setType('quizname', PARAM_TEXT);
        $mform->hideIf('quizname', 'quiztargetmode', 'neq', 'name');
        $mform->hideIf('quizname', 'operation', 'in', $sectionoperations);

        $mform->addElement('text', 'quizidnumber', get_string('quizidnumber', 'local_sectionbulk'));
        $mform->setType('quizidnumber', PARAM_TEXT);
        $mform->hideIf('quizidnumber', 'quiztargetmode', 'neq', 'idnumber');
        $mform->hideIf('quizidnumber', 'operation', 'in', $sectionoperations);

        $mform->addElement('text', 'quizsectionnumber', get_string('quizsectionnumber', 'local_sectionbulk'));
        $mform->setType('quizsectionnumber', PARAM_RAW_TRIMMED);
        $mform->addHelpButton('quizsectionnumber', 'quizsectionnumber', 'local_sectionbulk');
        $mform->hideIf('quizsectionnumber', 'operation', 'in', $sectionoperations);

        $mform->addElement('advcheckbox', 'quizallmatches', get_string('quizallmatches', 'local_sectionbulk'));
        $mform->setDefault('quizallmatches', 0);
        $mform->hideIf('quizallmatches', 'quiztargetmode', 'eq', 'all');
        $mform->hideIf('quizallmatches', 'operation', 'in', $sectionoperations);

        $mform->addElement('date_time_selector', 'quizopen', get_string('quizopen', 'local_sectionbulk'));
        $mform->hideIf('quizopen', 'operation', 'in', array_diff(array_keys($operations), ['quiz_open_set', 'quiz_range_set']));

        $mform->addElement('date_time_selector', 'quizclose', get_string('quizclose', 'local_sectionbulk'));
        $mform->hideIf('quizclose', 'operation', 'in', array_diff(array_keys($operations), ['quiz_close_set', 'quiz_range_set']));

        $mform->addElement('text', 'quizattempts', get_string('quizattempts', 'local_sectionbulk'));
        $mform->setType('quizattempts', PARAM_INT);
        $mform->setDefault('quizattempts', 2);
        $mform->addHelpButton('quizattempts', 'quizattempts', 'local_sectionbulk');
        $mform->hideIf('quizattempts', 'operation', 'neq', 'quiz_attempts_set');

        $mform->addElement('select', 'quizgrademethod', get_string('quizgrademethod', 'local_sectionbulk'), [
            1 => get_string('quizgrademethod_highest', 'local_sectionbulk'),
            2 => get_string('quizgrademethod_average', 'local_sectionbulk'),
            3 => get_string('quizgrademethod_first', 'local_sectionbulk'),
            4 => get_string('quizgrademethod_last', 'local_sectionbulk'),
        ]);
        $mform->setDefault('quizgrademethod', 1);
        $mform->hideIf('quizgrademethod', 'operation', 'neq', 'quiz_grademethod_set');

        $mform->addElement('advcheckbox', 'quizincludeunlimited', get_string('quizincludeunlimited', 'local_sectionbulk'));
        $mform->setDefault('quizincludeunlimited', 1);
        $mform->hideIf('quizincludeunlimited', 'operation', 'neq', 'quiz_highest_multiattempt');

        $mform->addElement('advcheckbox', 'quizregrade', get_string('quizregrade', 'local_sectionbulk'));
        $mform->setDefault('quizregrade', 1);
        $mform->hideIf('quizregrade', 'operation', 'in', array_diff(array_keys($operations), [
            'quiz_grademethod_set', 'quiz_highest_multiattempt',
        ]));

        // Keep the global Preview action outside the last collapsible fieldset.
        $mform->closeHeaderBefore('preview');
        $mform->addElement('submit', 'preview', get_string('preview', 'local_sectionbulk'), ['class' => 'btn-primary']);
    }

    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if ($data['scope'] === 'course') {
            $ids = preg_split('/\s*,\s*/', trim((string)$data['courseids']), -1, PREG_SPLIT_NO_EMPTY);
            $valid = array_filter($ids, static fn($id) => ctype_digit((string)$id) && (int)$id > 0);
            if (!$valid) {
                $errors['courseids'] = get_string('validation_courseids', 'local_sectionbulk');
            }
        } elseif (empty($data['categoryid'])) {
            $errors['categoryid'] = get_string('validation_category', 'local_sectionbulk');
        }

        $operation = (string)$data['operation'];
        $isquiz = str_starts_with($operation, 'quiz_');

        if (!$isquiz && $operation !== 'section_create') {
            if ($data['sectionmode'] === 'number' && (!isset($data['sectionnumber']) || (int)$data['sectionnumber'] < 0)) {
                $errors['sectionnumber'] = get_string('validation_sectionnumber', 'local_sectionbulk');
            }
            if ($data['sectionmode'] === 'numbers') {
                $rawsections = trim((string)($data['sectionnumbers'] ?? ''));
                if ($rawsections === '' || !preg_match('/^\s*\d+(?:\s*-\s*\d+)?(?:\s*,\s*\d+(?:\s*-\s*\d+)?)*\s*$/', $rawsections)) {
                    $errors['sectionnumbers'] = get_string('validation_sectionnumbers', 'local_sectionbulk');
                } else {
                    foreach (preg_split('/\s*,\s*/', $rawsections) as $part) {
                        if (str_contains($part, '-')) {
                            [$start, $end] = array_map('intval', preg_split('/\s*-\s*/', $part));
                            if ($start > $end) {
                                $errors['sectionnumbers'] = get_string('validation_sectionnumbers', 'local_sectionbulk');
                                break;
                            }
                        }
                    }
                }
            }
            if ($data['sectionmode'] === 'name' && trim((string)$data['sectionname']) === '') {
                $errors['sectionname'] = get_string('validation_sectionname', 'local_sectionbulk');
            }
        }

        if ($operation === 'date_range_set' && (int)$data['fromdate'] >= (int)$data['untildate']) {
            $errors['untildate'] = get_string('validation_daterange', 'local_sectionbulk');
        }

        if ($operation === 'profile_set') {
            if (empty($data['profilefield'])) {
                $errors['profilefield'] = get_string('validation_profilefield', 'local_sectionbulk');
            }
            if (!in_array($data['profileoperator'], ['isempty', 'isnotempty'], true)
                    && trim((string)$data['profilevalue']) === '') {
                $errors['profilevalue'] = get_string('validation_profilevalue', 'local_sectionbulk');
            }
        }

        if ($operation === 'section_create' && trim((string)$data['newsectionname']) === '') {
            $errors['newsectionname'] = get_string('validation_newsectionname', 'local_sectionbulk');
        }

        if ($isquiz) {
            if (($data['quiztargetmode'] ?? 'all') === 'name' && trim((string)($data['quizname'] ?? '')) === '') {
                $errors['quizname'] = get_string('validation_quizname', 'local_sectionbulk');
            }
            if (($data['quiztargetmode'] ?? 'all') === 'idnumber' && trim((string)($data['quizidnumber'] ?? '')) === '') {
                $errors['quizidnumber'] = get_string('validation_quizidnumber', 'local_sectionbulk');
            }
            if (trim((string)($data['quizsectionnumber'] ?? '')) !== ''
                    && (!ctype_digit((string)$data['quizsectionnumber']) || (int)$data['quizsectionnumber'] < 0)) {
                $errors['quizsectionnumber'] = get_string('validation_quizsectionnumber', 'local_sectionbulk');
            }
            if ($operation === 'quiz_range_set' && (int)$data['quizopen'] >= (int)$data['quizclose']) {
                $errors['quizclose'] = get_string('validation_quizrange', 'local_sectionbulk');
            }
            if ($operation === 'quiz_attempts_set' && (!isset($data['quizattempts']) || (int)$data['quizattempts'] < 0)) {
                $errors['quizattempts'] = get_string('validation_quizattempts', 'local_sectionbulk');
            }
        }

        return $errors;
    }
}

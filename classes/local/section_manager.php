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
 * Bulk section and quiz operation manager.
 *
 * @package    local_sectionbulk
 * @copyright  2026 SiteEcuador - Msg. Franklin Moya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sectionbulk\local;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * Executes bulk section and quiz operations.
 */
class section_manager {
    /** @var \moodle_database */
    private $db;

    public function __construct() {
        global $DB;
        $this->db = $DB;
    }

    public function process(\stdClass $data, bool $apply = false): array {
        $courses = $this->get_courses($data);
        $rows = [];
        $changes = 0;
        $changedcourses = [];
        $isquizoperation = str_starts_with((string)$data->operation, 'quiz_');

        foreach ($courses as $course) {
            if ($isquizoperation) {
                $quizzes = $this->find_quizzes($course->id, $data);
                if (!$quizzes) {
                    $rows[] = $this->row_target($course, '-', 'skipped',
                        get_string('detail_quiznotfound', 'local_sectionbulk'), false);
                    continue;
                }

                if (count($quizzes) > 1 && ($data->quiztargetmode ?? 'all') !== 'all' && empty($data->quizallmatches)) {
                    $rows[] = $this->row_target($course, '-', 'skipped',
                        get_string('detail_quizduplicate', 'local_sectionbulk', count($quizzes)), false);
                    continue;
                }

                foreach ($quizzes as $quiz) {
                    $row = $this->process_quiz($course, $quiz, $data, $apply);
                    $rows[] = $row;
                    if ($row['changed']) {
                        $changes++;
                        if ($apply) {
                            $changedcourses[$course->id] = true;
                        }
                    }
                }
                continue;
            }

            if ($data->operation === 'section_create') {
                $row = $this->process_create_section($course, $data, $apply);
                $rows[] = $row;
                if ($row['changed']) {
                    $changes++;
                    if ($apply) {
                        $changedcourses[$course->id] = true;
                    }
                }
                continue;
            }

            $sections = $this->find_sections($course->id, $data);
            if (!$sections) {
                $rows[] = $this->row($course, null, 'skipped',
                    get_string('detail_sectionnotfound', 'local_sectionbulk'), false);
                continue;
            }

            if ($data->sectionmode === 'name' && count($sections) > 1 && empty($data->allmatches)) {
                $rows[] = $this->row($course, null, 'skipped',
                    get_string('detail_duplicate', 'local_sectionbulk', count($sections)), false);
                continue;
            }

            foreach ($sections as $section) {
                $row = $this->process_section($course, $section, $data, $apply);
                $rows[] = $row;
                if ($row['changed']) {
                    $changes++;
                    if ($apply) {
                        $changedcourses[$course->id] = true;
                    }
                }
            }
        }

        if ($apply) {
            foreach (array_keys($changedcourses) as $courseid) {
                rebuild_course_cache($courseid, true);
            }
        }

        return ['courses' => count($courses), 'changes' => $changes, 'rows' => $rows];
    }

    private function get_courses(\stdClass $data): array {
        if ($data->scope === 'course') {
            $ids = preg_split('/\s*,\s*/', trim((string)$data->courseids), -1, PREG_SPLIT_NO_EMPTY);
            $ids = array_values(array_unique(array_map('intval', array_filter($ids, static function($id) {
                return ctype_digit((string)$id) && (int)$id > 0;
            }))));
            if (!$ids) {
                return [];
            }
            [$sql, $params] = $this->db->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'cid');
            $params['siteid'] = SITEID;
            return $this->db->get_records_select('course', "id {$sql} AND id <> :siteid", $params, 'fullname ASC');
        }

        $categoryid = (int)$data->categoryid;
        $category = $this->db->get_record('course_categories', ['id' => $categoryid], '*', MUST_EXIST);
        $categoryids = [$categoryid];

        if (!empty($data->recursive)) {
            $likesql = $this->db->sql_like('path', ':categorypath', false);
            $children = $this->db->get_records_select(
                'course_categories', $likesql, ['categorypath' => $category->path . '/%'], 'id ASC', 'id'
            );
            foreach ($children as $child) {
                $categoryids[] = (int)$child->id;
            }
        }

        [$sql, $params] = $this->db->get_in_or_equal(
            array_values(array_unique($categoryids)), SQL_PARAMS_NAMED, 'cat'
        );
        $params['siteid'] = SITEID;
        return $this->db->get_records_select('course', "category {$sql} AND id <> :siteid", $params, 'fullname ASC');
    }

    private function find_sections(int $courseid, \stdClass $data): array {
        if ($data->sectionmode === 'all') {
            return $this->db->get_records_select(
                'course_sections',
                'course = :courseid AND section > 0',
                ['courseid' => $courseid],
                'section ASC'
            );
        }

        if ($data->sectionmode === 'number') {
            return $this->db->get_records('course_sections', [
                'course' => $courseid,
                'section' => (int)$data->sectionnumber,
            ], 'section ASC');
        }

        if ($data->sectionmode === 'numbers') {
            $numbers = $this->parse_section_numbers((string)($data->sectionnumbers ?? ''));
            if (!$numbers) {
                return [];
            }
            [$insql, $params] = $this->db->get_in_or_equal($numbers, SQL_PARAMS_NAMED, 'section');
            $params['courseid'] = $courseid;
            return $this->db->get_records_select(
                'course_sections',
                "course = :courseid AND section {$insql}",
                $params,
                'section ASC'
            );
        }

        $name = trim((string)$data->sectionname);
        $sections = $this->db->get_records('course_sections', [
            'course' => $courseid,
            'name' => $name,
        ], 'section ASC');

        foreach ($sections as $key => $section) {
            if ((string)$section->name !== $name) {
                unset($sections[$key]);
            }
        }
        return $sections;
    }

    private function find_quizzes(int $courseid, \stdClass $data): array {
        $quizmodule = $this->db->get_record('modules', ['name' => 'quiz'], 'id', MUST_EXIST);
        $params = [
            'courseid' => $courseid,
            'moduleid' => $quizmodule->id,
        ];
        $where = [
            'q.course = :courseid',
            'cm.course = q.course',
            'cm.module = :moduleid',
            'cm.instance = q.id',
            'cm.deletioninprogress = 0',
        ];

        $targetmode = (string)($data->quiztargetmode ?? 'all');
        if ($targetmode === 'name') {
            $where[] = 'q.name = :quizname';
            $params['quizname'] = trim((string)$data->quizname);
        } elseif ($targetmode === 'idnumber') {
            $where[] = 'cm.idnumber = :quizidnumber';
            $params['quizidnumber'] = trim((string)$data->quizidnumber);
        }

        if (isset($data->quizsectionnumber) && trim((string)$data->quizsectionnumber) !== '') {
            $where[] = 'cs.section = :quizsectionnumber';
            $params['quizsectionnumber'] = (int)$data->quizsectionnumber;
        }

        $sql = "SELECT q.*, cm.id AS cmid, cm.idnumber AS cmidnumber,
                       cs.section AS sectionnumber, cs.name AS sectionname
                  FROM {quiz} q
                  JOIN {course_modules} cm ON cm.instance = q.id
                  JOIN {course_sections} cs ON cs.id = cm.section
                 WHERE " . implode(' AND ', $where) . "
              ORDER BY cs.section ASC, q.name ASC, q.id ASC";

        $records = $this->db->get_records_sql($sql, $params);

        if ($targetmode === 'name') {
            $name = trim((string)$data->quizname);
            foreach ($records as $key => $quiz) {
                if ((string)$quiz->name !== $name) {
                    unset($records[$key]);
                }
            }
        }

        return $records;
    }

    private function process_create_section(\stdClass $course, \stdClass $data, bool $apply): array {
        if (!course_format_uses_sections($course->format)) {
            return $this->row($course, null, 'skipped',
                get_string('detail_formatnosections', 'local_sectionbulk', $course->format), false);
        }

        $name = trim((string)$data->newsectionname);
        if (empty($data->allowduplicates)
                && $this->db->record_exists('course_sections', ['course' => $course->id, 'name' => $name])) {
            return $this->row($course, null, 'unchanged',
                get_string('detail_sectionexists', 'local_sectionbulk'), false);
        }

        $position = max(0, (int)$data->newsectionposition);
        if (!$apply) {
            return $this->row($course, null, 'changed',
                get_string('detail_sectioncreate', 'local_sectionbulk', $position), true);
        }

        try {
            $section = course_create_section($course, $position);
            $summary = '';
            if (isset($data->newsectionsummary) && is_array($data->newsectionsummary)) {
                $summary = $data->newsectionsummary['text'] ?? '';
            } elseif (isset($data->newsectionsummary_editor) && is_array($data->newsectionsummary_editor)) {
                $summary = $data->newsectionsummary_editor['text'] ?? '';
            }
            course_update_section($course, $section, [
                'name' => $name,
                'summary' => $summary,
                'summaryformat' => FORMAT_HTML,
                'visible' => !empty($data->newsectionvisible) ? 1 : 0,
            ]);
            return $this->row($course, $section, 'applied',
                get_string('detail_sectioncreated', 'local_sectionbulk'), true);
        } catch (\Throwable $e) {
            return $this->row($course, null, 'error', $e->getMessage(), false);
        }
    }

    private function process_section(\stdClass $course, \stdClass $section, \stdClass $data, bool $apply): array {
        $old = $section->availability;
        $new = $old;
        $message = '';

        try {
            switch ($data->operation) {
                case 'date_from_set':
                    [$new, $changed] = $this->set_date($old, '>=', (int)$data->fromdate, !empty($data->showcondition));
                    $message = $changed ? get_string('detail_datefromset', 'local_sectionbulk')
                        : get_string('detail_alreadyconfigured', 'local_sectionbulk');
                    break;

                case 'date_until_set':
                    [$new, $changed] = $this->set_date($old, '<', (int)$data->untildate, !empty($data->showcondition));
                    $message = $changed ? get_string('detail_dateuntilset', 'local_sectionbulk')
                        : get_string('detail_alreadyconfigured', 'local_sectionbulk');
                    break;

                case 'date_range_set':
                    [$intermediate, $changed1] = $this->set_date($old, '>=', (int)$data->fromdate, !empty($data->showcondition));
                    [$new, $changed2] = $this->set_date($intermediate, '<', (int)$data->untildate, !empty($data->showcondition));
                    $changed = $changed1 || $changed2;
                    $message = $changed ? get_string('detail_daterangeset', 'local_sectionbulk')
                        : get_string('detail_alreadyconfigured', 'local_sectionbulk');
                    break;

                case 'date_from_remove':
                    [$new, $removed] = $this->remove_conditions($old, static function($node) {
                        return is_object($node) && isset($node->type, $node->d)
                            && $node->type === 'date' && $node->d === '>=';
                    });
                    $changed = $removed > 0;
                    $message = $changed ? get_string('detail_removedcount', 'local_sectionbulk', $removed)
                        : get_string('detail_nothingfound', 'local_sectionbulk');
                    break;

                case 'date_until_remove':
                    [$new, $removed] = $this->remove_conditions($old, static function($node) {
                        return is_object($node) && isset($node->type, $node->d)
                            && $node->type === 'date' && $node->d === '<';
                    });
                    $changed = $removed > 0;
                    $message = $changed ? get_string('detail_removedcount', 'local_sectionbulk', $removed)
                        : get_string('detail_nothingfound', 'local_sectionbulk');
                    break;

                case 'date_all_remove':
                    [$new, $removed] = $this->remove_conditions($old, static function($node) {
                        return is_object($node) && isset($node->type) && $node->type === 'date';
                    });
                    $changed = $removed > 0;
                    $message = $changed ? get_string('detail_removedcount', 'local_sectionbulk', $removed)
                        : get_string('detail_nothingfound', 'local_sectionbulk');
                    break;

                case 'completion_remove':
                    [$new, $removed] = $this->remove_conditions($old, static function($node) {
                        return is_object($node) && isset($node->type) && $node->type === 'completion';
                    });
                    $changed = $removed > 0;
                    $message = $changed ? get_string('detail_removedcompletion', 'local_sectionbulk', $removed)
                        : get_string('detail_nothingfound', 'local_sectionbulk');
                    break;

                case 'profile_set':
                    [$new, $changed] = $this->set_profile(
                        $old,
                        (string)$data->profilefield,
                        (string)$data->profileoperator,
                        (string)$data->profilevalue,
                        !empty($data->showcondition)
                    );
                    $message = $changed ? get_string('detail_profileset', 'local_sectionbulk')
                        : get_string('detail_alreadyconfigured', 'local_sectionbulk');
                    break;

                default:
                    return $this->row($course, $section, 'error',
                        get_string('detail_unknownoperation', 'local_sectionbulk'), false);
            }
        } catch (\UnexpectedValueException $e) {
            return $this->row($course, $section, 'error', $e->getMessage(), false);
        }

        if (!$changed) {
            return $this->row($course, $section, 'unchanged', $message, false);
        }

        if ($apply) {
            $update = (object)[
                'id' => (int)$section->id,
                'availability' => $new,
                'timemodified' => time(),
            ];
            $this->db->update_record('course_sections', $update);
            return $this->row($course, $section, 'applied', $message, true);
        }

        return $this->row($course, $section, 'changed', $message, true);
    }

    private function process_quiz(\stdClass $course, \stdClass $quiz, \stdClass $data, bool $apply): array {
        $newopen = (int)$quiz->timeopen;
        $newclose = (int)$quiz->timeclose;
        $newattempts = (int)$quiz->attempts;
        $newgrademethod = (int)$quiz->grademethod;
        $changed = false;
        $dateschanged = false;
        $grademethodchanged = false;
        $message = '';

        switch ((string)$data->operation) {
            case 'quiz_open_set':
                $newopen = (int)$data->quizopen;
                $changed = $newopen !== (int)$quiz->timeopen;
                $dateschanged = $changed;
                $message = get_string('detail_quizopenset', 'local_sectionbulk');
                break;

            case 'quiz_close_set':
                $newclose = (int)$data->quizclose;
                $changed = $newclose !== (int)$quiz->timeclose;
                $dateschanged = $changed;
                $message = get_string('detail_quizcloseset', 'local_sectionbulk');
                break;

            case 'quiz_range_set':
                $newopen = (int)$data->quizopen;
                $newclose = (int)$data->quizclose;
                $changed = $newopen !== (int)$quiz->timeopen || $newclose !== (int)$quiz->timeclose;
                $dateschanged = $changed;
                $message = get_string('detail_quizrangeset', 'local_sectionbulk');
                break;

            case 'quiz_open_remove':
                $newopen = 0;
                $changed = (int)$quiz->timeopen !== 0;
                $dateschanged = $changed;
                $message = get_string('detail_quizopenremoved', 'local_sectionbulk');
                break;

            case 'quiz_close_remove':
                $newclose = 0;
                $changed = (int)$quiz->timeclose !== 0;
                $dateschanged = $changed;
                $message = get_string('detail_quizcloseremoved', 'local_sectionbulk');
                break;

            case 'quiz_dates_remove':
                $newopen = 0;
                $newclose = 0;
                $changed = (int)$quiz->timeopen !== 0 || (int)$quiz->timeclose !== 0;
                $dateschanged = $changed;
                $message = get_string('detail_quizdatesremoved', 'local_sectionbulk');
                break;

            case 'quiz_attempts_set':
                $newattempts = max(0, (int)$data->quizattempts);
                $changed = $newattempts !== (int)$quiz->attempts;
                $message = get_string('detail_quizattemptsset', 'local_sectionbulk', $newattempts);
                break;

            case 'quiz_grademethod_set':
                $newgrademethod = (int)$data->quizgrademethod;
                $changed = $newgrademethod !== (int)$quiz->grademethod;
                $grademethodchanged = $changed;
                $message = get_string('detail_quizgrademethodset', 'local_sectionbulk',
                    $this->grade_method_name($newgrademethod));
                break;

            case 'quiz_highest_multiattempt':
                $eligible = (int)$quiz->attempts >= 2
                    || (!empty($data->quizincludeunlimited) && (int)$quiz->attempts === 0);
                if (!$eligible) {
                    return $this->quiz_row($course, $quiz, 'unchanged',
                        get_string('detail_quiznotmultiattempt', 'local_sectionbulk'), false);
                }
                $newgrademethod = 1;
                $changed = (int)$quiz->grademethod !== 1;
                $grademethodchanged = $changed;
                $message = get_string('detail_quizhighestset', 'local_sectionbulk');
                break;

            default:
                return $this->quiz_row($course, $quiz, 'error',
                    get_string('detail_unknownoperation', 'local_sectionbulk'), false);
        }

        if ($newopen > 0 && $newclose > 0 && $newopen >= $newclose) {
            return $this->quiz_row($course, $quiz, 'error',
                get_string('detail_quizinvalidrange', 'local_sectionbulk'), false);
        }

        if (!$changed) {
            return $this->quiz_row($course, $quiz, 'unchanged',
                get_string('detail_alreadyconfigured', 'local_sectionbulk'), false);
        }

        if (!$apply) {
            return $this->quiz_row($course, $quiz, 'changed', $message, true);
        }

        try {
            $quizrecord = $this->db->get_record('quiz', ['id' => $quiz->id], '*', MUST_EXIST);
            $quizrecord->timeopen = $newopen;
            $quizrecord->timeclose = $newclose;
            $quizrecord->attempts = $newattempts;
            $quizrecord->grademethod = $newgrademethod;
            $quizrecord->timemodified = time();
            $this->db->update_record('quiz', $quizrecord);

            if ($dateschanged) {
                quiz_update_events($quizrecord);
                if (function_exists('quiz_update_open_attempts')) {
                    quiz_update_open_attempts(['quizid' => $quizrecord->id]);
                }
            }

            if ($grademethodchanged && !empty($data->quizregrade)) {
                $this->recompute_quiz_grades($quizrecord);
            }

            return $this->quiz_row($course, $quiz, 'applied', $message, true);
        } catch (\Throwable $e) {
            return $this->quiz_row($course, $quiz, 'error', $e->getMessage(), false);
        }
    }

    private function recompute_quiz_grades(\stdClass $quiz): void {
        if (class_exists('\\mod_quiz\\quiz_settings')) {
            $gradecalculator = \mod_quiz\quiz_settings::create($quiz->id)->get_grade_calculator();
            $gradecalculator->recompute_all_final_grades();
        } elseif (function_exists('quiz_update_all_final_grades')) {
            quiz_update_all_final_grades($quiz);
        }

        if (function_exists('quiz_update_grades')) {
            quiz_update_grades($quiz);
        }
    }

    private function grade_method_name(int $method): string {
        return match ($method) {
            1 => get_string('quizgrademethod_highest', 'local_sectionbulk'),
            2 => get_string('quizgrademethod_average', 'local_sectionbulk'),
            3 => get_string('quizgrademethod_first', 'local_sectionbulk'),
            4 => get_string('quizgrademethod_last', 'local_sectionbulk'),
            default => (string)$method,
        };
    }

    private function set_date(?string $availability, string $operator, int $timestamp, bool $show): array {
        $tree = $this->decode_tree($availability);
        $changed = false;
        $found = 0;
        $this->walk_update($tree, function(&$node) use ($operator, $timestamp, &$changed, &$found) {
            if (is_object($node) && isset($node->type, $node->d) && $node->type === 'date' && $node->d === $operator) {
                $found++;
                if (!isset($node->t) || (int)$node->t !== $timestamp) {
                    $node->t = $timestamp;
                    $changed = true;
                }
            }
        });

        if ($found === 0) {
            $condition = class_exists('\\availability_date\\condition')
                ? \availability_date\condition::get_json($operator, $timestamp)
                : (object)['type' => 'date', 'd' => $operator, 't' => $timestamp];
            $tree = $this->append_condition($tree, $condition, $show);
            $changed = true;
        }

        return [$this->encode_tree($tree), $changed];
    }

    private function set_profile(
        ?string $availability,
        string $field,
        string $operator,
        string $value,
        bool $show
    ): array {
        $tree = $this->decode_tree($availability);
        $changed = false;
        $found = 0;

        $this->walk_update($tree, function(&$node) use ($field, $operator, $value, &$changed, &$found) {
            if (is_object($node) && isset($node->type, $node->cf, $node->op)
                    && $node->type === 'profile' && $node->cf === $field && $node->op === $operator) {
                $found++;
                if (in_array($operator, ['isempty', 'isnotempty'], true)) {
                    if (property_exists($node, 'v')) {
                        unset($node->v);
                        $changed = true;
                    }
                } elseif (!isset($node->v) || (string)$node->v !== $value) {
                    $node->v = $value;
                    $changed = true;
                }
            }
        });

        if ($found === 0) {
            if (class_exists('\\availability_profile\\condition')) {
                $condition = \availability_profile\condition::get_json(
                    true,
                    $field,
                    $operator,
                    in_array($operator, ['isempty', 'isnotempty'], true) ? null : $value
                );
            } else {
                $condition = (object)['type' => 'profile', 'cf' => $field, 'op' => $operator];
                if (!in_array($operator, ['isempty', 'isnotempty'], true)) {
                    $condition->v = $value;
                }
            }
            $tree = $this->append_condition($tree, $condition, $show);
            $changed = true;
        }

        return [$this->encode_tree($tree), $changed];
    }

    private function remove_conditions(?string $availability, callable $matcher): array {
        if ($availability === null || trim($availability) === '') {
            return [null, 0];
        }
        $tree = $this->decode_tree($availability);
        $removed = 0;
        $tree = $this->remove_recursive($tree, $matcher, $removed);
        return [$tree === null ? null : $this->encode_tree($tree), $removed];
    }

    private function remove_recursive($node, callable $matcher, int &$removed) {
        if ($matcher($node)) {
            $removed++;
            return null;
        }

        if (is_object($node) && isset($node->c) && is_array($node->c)) {
            $oldchildren = $node->c;
            $oldshowc = isset($node->showc) && is_array($node->showc) ? $node->showc : [];
            $newchildren = [];
            $newshowc = [];

            foreach ($oldchildren as $index => $child) {
                $clean = $this->remove_recursive($child, $matcher, $removed);
                if ($clean !== null) {
                    $newchildren[] = $clean;
                    $newshowc[] = array_key_exists($index, $oldshowc) ? (bool)$oldshowc[$index] : true;
                }
            }

            if (!$newchildren) {
                return null;
            }
            $node->c = array_values($newchildren);
            $node->showc = array_values($newshowc);
            return $node;
        }

        if (is_array($node)) {
            $cleaned = [];
            foreach ($node as $child) {
                $clean = $this->remove_recursive($child, $matcher, $removed);
                if ($clean !== null) {
                    $cleaned[] = $clean;
                }
            }
            return $cleaned ?: null;
        }

        return $node;
    }

    private function decode_tree(?string $availability) {
        if ($availability === null || trim($availability) === '') {
            return null;
        }
        $tree = json_decode($availability);
        if (json_last_error() !== JSON_ERROR_NONE || !is_object($tree)) {
            throw new \UnexpectedValueException(
                get_string('error_invalidavailability', 'local_sectionbulk', json_last_error_msg())
            );
        }
        return $tree;
    }

    private function encode_tree($tree): ?string {
        if ($tree === null) {
            return null;
        }
        $json = json_encode($tree, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new \UnexpectedValueException(get_string('error_encodeavailability', 'local_sectionbulk'));
        }
        return $json;
    }

    private function append_condition($tree, \stdClass $condition, bool $show): \stdClass {
        if ($tree === null) {
            if (class_exists('\\core_availability\\tree')) {
                return \core_availability\tree::get_root_json(
                    [$condition], \core_availability\tree::OP_AND, [$show]
                );
            }
            return (object)['op' => '&', 'c' => [$condition], 'showc' => [$show]];
        }

        if (isset($tree->op, $tree->c) && $tree->op === '&' && is_array($tree->c)) {
            $count = count($tree->c);
            if (!isset($tree->showc) || !is_array($tree->showc)) {
                $tree->showc = array_fill(0, $count, true);
            }
            while (count($tree->showc) < $count) {
                $tree->showc[] = true;
            }
            if (count($tree->showc) > $count) {
                $tree->showc = array_slice($tree->showc, 0, $count);
            }
            $tree->c[] = $condition;
            $tree->showc[] = $show;
            return $tree;
        }

        return (object)[
            'op' => '&',
            'c' => [$tree, $condition],
            'showc' => [true, $show],
        ];
    }

    private function walk_update(&$node, callable $callback): void {
        if ($node === null) {
            return;
        }
        $callback($node);
        if (is_object($node)) {
            foreach ($node as &$value) {
                if (is_object($value) || is_array($value)) {
                    $this->walk_update($value, $callback);
                }
            }
            unset($value);
        } elseif (is_array($node)) {
            foreach ($node as &$value) {
                if (is_object($value) || is_array($value)) {
                    $this->walk_update($value, $callback);
                }
            }
            unset($value);
        }
    }

    private function row(
        \stdClass $course,
        ?\stdClass $section,
        string $status,
        string $details,
        bool $changed
    ): array {
        if ($section) {
            $target = !empty($section->name)
                ? $section->name . ' (#' . $section->section . ')'
                : get_string('section') . ' #' . $section->section;
        } else {
            $target = '-';
        }
        return $this->row_target($course, $target, $status, $details, $changed);
    }

    private function quiz_row(
        \stdClass $course,
        \stdClass $quiz,
        string $status,
        string $details,
        bool $changed
    ): array {
        $section = !empty($quiz->sectionname)
            ? format_string($quiz->sectionname) . ' (#' . (int)$quiz->sectionnumber . ')'
            : get_string('section') . ' #' . (int)$quiz->sectionnumber;
        $target = get_string('quizlabel', 'local_sectionbulk', [
            'quiz' => format_string($quiz->name),
            'section' => $section,
        ]);
        return $this->row_target($course, $target, $status, $details, $changed);
    }

    private function row_target(
        \stdClass $course,
        string $target,
        string $status,
        string $details,
        bool $changed
    ): array {
        return [
            'courseid' => (int)$course->id,
            'course' => format_string($course->fullname),
            'target' => $target,
            'status' => $status,
            'details' => $details,
            'changed' => $changed,
        ];
    }
}

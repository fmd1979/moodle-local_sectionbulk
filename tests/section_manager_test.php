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

/**
 * Tests for the bulk operation manager.
 *
 * @package    local_sectionbulk
 * @copyright  2026 SiteEcuador - Msg. Franklin Moya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sectionbulk;

use local_sectionbulk\local\section_manager;

final class section_manager_test extends \advanced_testcase {
    public function test_remove_date_from_preserves_other_conditions(): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['numsections' => 2]);
        $section = $DB->get_record('course_sections',
            ['course' => $course->id, 'section' => 1], '*', MUST_EXIST);

        $section->availability = json_encode((object)[
            'op' => '&',
            'c' => [
                (object)['type' => 'date', 'd' => '>=', 't' => 1900000000],
                (object)['type' => 'date', 'd' => '<', 't' => 1901000000],
                (object)['type' => 'profile', 'cf' => 'Code_Book', 'op' => 'contains', 'v' => 'BUCK'],
            ],
            'showc' => [true, true, true],
        ]);
        $DB->update_record('course_sections', $section);

        $data = (object)[
            'scope' => 'course',
            'courseids' => (string)$course->id,
            'operation' => 'date_from_remove',
            'sectionmode' => 'number',
            'sectionnumber' => 1,
            'allmatches' => 0,
        ];

        $manager = new section_manager();
        $result = $manager->process($data, true);
        $this->assertSame(1, $result['changes']);

        $updated = $DB->get_record('course_sections', ['id' => $section->id], '*', MUST_EXIST);
        $tree = json_decode($updated->availability);
        $this->assertCount(2, $tree->c);
        $this->assertSame('<', $tree->c[0]->d);
        $this->assertSame('profile', $tree->c[1]->type);
        $this->assertSame('Code_Book', $tree->c[1]->cf);
    }

    public function test_add_date_from_to_section(): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);
        $timestamp = 1900000000;

        $data = (object)[
            'scope' => 'course',
            'courseids' => (string)$course->id,
            'operation' => 'date_from_set',
            'sectionmode' => 'number',
            'sectionnumber' => 1,
            'allmatches' => 0,
            'fromdate' => $timestamp,
            'showcondition' => 1,
        ];

        $manager = new section_manager();
        $result = $manager->process($data, true);
        $this->assertSame(1, $result['changes']);

        $section = $DB->get_record('course_sections',
            ['course' => $course->id, 'section' => 1], '*', MUST_EXIST);
        $tree = json_decode($section->availability);
        $this->assertSame('date', $tree->c[0]->type);
        $this->assertSame('>=', $tree->c[0]->d);
        $this->assertSame($timestamp, (int)$tree->c[0]->t);
    }

    public function test_multiple_section_numbers_target_only_requested_sections(): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['numsections' => 4]);
        $timestamp = 1900000000;

        $data = (object)[
            'scope' => 'course',
            'courseids' => (string)$course->id,
            'operation' => 'date_from_set',
            'sectionmode' => 'numbers',
            'sectionnumbers' => '1,3-4',
            'allmatches' => 0,
            'fromdate' => $timestamp,
            'showcondition' => 1,
        ];

        $manager = new section_manager();
        $result = $manager->process($data, true);
        $this->assertSame(3, $result['changes']);

        foreach ([1, 3, 4] as $number) {
            $section = $DB->get_record('course_sections',
                ['course' => $course->id, 'section' => $number], '*', MUST_EXIST);
            $this->assertNotEmpty($section->availability);
        }

        $section2 = $DB->get_record('course_sections',
            ['course' => $course->id, 'section' => 2], '*', MUST_EXIST);
        $this->assertEmpty($section2->availability);
    }

    public function test_all_sections_excludes_general_section_zero(): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['numsections' => 3]);
        $timestamp = 1900000000;

        $data = (object)[
            'scope' => 'course',
            'courseids' => (string)$course->id,
            'operation' => 'date_from_set',
            'sectionmode' => 'all',
            'allmatches' => 0,
            'fromdate' => $timestamp,
            'showcondition' => 1,
        ];

        $manager = new section_manager();
        $result = $manager->process($data, true);
        $this->assertSame(3, $result['changes']);

        $general = $DB->get_record('course_sections',
            ['course' => $course->id, 'section' => 0], '*', MUST_EXIST);
        $this->assertEmpty($general->availability);
    }

    public function test_quiz_highest_grade_for_multiple_attempts(): void {
        global $DB;
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance([
            'course' => $course->id,
            'name' => 'Bulk grading test',
            'attempts' => 2,
            'grademethod' => 2,
        ]);

        $data = (object)[
            'scope' => 'course',
            'courseids' => (string)$course->id,
            'operation' => 'quiz_highest_multiattempt',
            'quiztargetmode' => 'all',
            'quizsectionnumber' => '',
            'quizallmatches' => 0,
            'quizincludeunlimited' => 1,
            'quizregrade' => 0,
        ];

        $manager = new section_manager();
        $result = $manager->process($data, true);
        $this->assertSame(1, $result['changes']);
        $this->assertSame(1, (int)$DB->get_field('quiz', 'grademethod', ['id' => $quiz->id], MUST_EXIST));
    }
}

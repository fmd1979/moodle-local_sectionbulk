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
 * Bulk section and quiz manager UI.
 *
 * @package    local_sectionbulk
 * @copyright  2026 SiteEcuador - Msg. Franklin Moya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_sectionbulk\form\bulk_form;
use local_sectionbulk\local\section_manager;

admin_externalpage_setup('local_sectionbulk');
require_capability('local/sectionbulk:manage', context_system::instance());

$PAGE->set_url(new moodle_url('/local/sectionbulk/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_sectionbulk'));
$PAGE->set_heading(get_string('pluginname', 'local_sectionbulk'));

function local_sectionbulk_render_credits(): string {
    return html_writer::div(get_string('credits', 'local_sectionbulk'), 'small text-muted mt-4 mb-3');
}

function local_sectionbulk_render_result(array $result, bool $applied): string {
    $table = new html_table();
    $table->head = [
        get_string('course', 'local_sectionbulk'),
        get_string('target', 'local_sectionbulk'),
        get_string('status', 'local_sectionbulk'),
        get_string('details', 'local_sectionbulk'),
    ];
    $table->attributes['class'] = 'generaltable table-striped';

    foreach ($result['rows'] as $row) {
        $statuskey = $row['status'];
        $status = get_string($statuskey, 'local_sectionbulk');
        $table->data[] = [
            s($row['course']) . ' [' . (int)$row['courseid'] . ']',
            s($row['target'] ?? '-'),
            s($status),
            s($row['details']),
        ];
    }

    $out = html_writer::div(get_string('coursesfound', 'local_sectionbulk', $result['courses']), 'alert alert-info');
    $key = $applied ? 'changesapplied' : 'changesfound';
    $out .= html_writer::div(
        get_string($key, 'local_sectionbulk', $result['changes']),
        $result['changes'] ? 'alert alert-warning' : 'alert alert-secondary'
    );
    $out .= html_writer::table($table);
    return $out;
}

$manager = new section_manager();
$confirm = optional_param('confirm', 0, PARAM_BOOL);

if ($confirm) {
    require_sesskey();
    $token = required_param('token', PARAM_ALPHANUMEXT);
    $preview = $SESSION->local_sectionbulk_preview ?? null;

    if (!$preview || empty($preview['token']) || !hash_equals($preview['token'], $token)
            || empty($preview['time']) || (time() - (int)$preview['time']) > 1800) {
        unset($SESSION->local_sectionbulk_preview);
        redirect(
            new moodle_url('/local/sectionbulk/index.php'),
            get_string('invalidpreview', 'local_sectionbulk'),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }

    $data = (object)$preview['data'];
    $result = $manager->process($data, true);
    unset($SESSION->local_sectionbulk_preview);

    \local_sectionbulk\event\bulk_change_applied::create([
        'context' => context_system::instance(),
        'other' => [
            'operation' => (string)$data->operation,
            'changes' => (int)$result['changes'],
            'courses' => (int)$result['courses'],
        ],
    ])->trigger();

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('resulttitle', 'local_sectionbulk'));
    echo local_sectionbulk_render_result($result, true);
    echo html_writer::div(
        html_writer::link(
            new moodle_url('/local/sectionbulk/index.php'),
            get_string('pluginname', 'local_sectionbulk'),
            ['class' => 'btn btn-primary']
        ),
        'mt-3'
    );
    echo local_sectionbulk_render_credits();
    echo $OUTPUT->footer();
    exit;
}

$form = new bulk_form();
$data = $form->get_data();

echo $OUTPUT->header();
echo html_writer::div(get_string('intro', 'local_sectionbulk'), 'alert alert-info');

if ($data && !empty($data->preview)) {
    unset($data->preview);
    $result = $manager->process($data, false);

    echo $OUTPUT->heading(get_string('previewtitle', 'local_sectionbulk'));
    echo html_writer::div(get_string('dryrunnote', 'local_sectionbulk'), 'alert alert-success');
    echo local_sectionbulk_render_result($result, false);

    if ($result['changes'] > 0) {
        $token = random_string(24);
        $SESSION->local_sectionbulk_preview = [
            'token' => $token,
            'time' => time(),
            'data' => (array)$data,
        ];

        echo html_writer::div(get_string('confirmation', 'local_sectionbulk'), 'alert alert-danger mt-3');

        $action = new moodle_url('/local/sectionbulk/index.php');
        echo html_writer::start_tag('form', [
            'method' => 'post',
            'action' => $action->out(false),
            'class' => 'd-flex gap-2 mb-4',
        ]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'confirm', 'value' => '1']);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'token', 'value' => $token]);
        echo html_writer::empty_tag('input', [
            'type' => 'submit',
            'class' => 'btn btn-danger',
            'value' => get_string('applychanges', 'local_sectionbulk'),
        ]);
        echo html_writer::link(
            new moodle_url('/local/sectionbulk/index.php'),
            get_string('cancel', 'local_sectionbulk'),
            ['class' => 'btn btn-secondary']
        );
        echo html_writer::end_tag('form');
    } else {
        unset($SESSION->local_sectionbulk_preview);
        echo html_writer::div(get_string('nothingtoapply', 'local_sectionbulk'), 'alert alert-secondary');
    }

    echo $OUTPUT->heading(get_string('pluginname', 'local_sectionbulk'), 3);
}

$form->display();
echo local_sectionbulk_render_credits();
echo $OUTPUT->footer();

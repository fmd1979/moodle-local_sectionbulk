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
 * Admin navigation for local_sectionbulk.
 *
 * @package    local_sectionbulk
 * @copyright  2026 SiteEcuador - Msg. Franklin Moya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig || has_capability('local/sectionbulk:manage', context_system::instance())) {
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_sectionbulk',
        get_string('pluginname', 'local_sectionbulk'),
        new moodle_url('/local/sectionbulk/index.php'),
        'local/sectionbulk:manage'
    ));
}

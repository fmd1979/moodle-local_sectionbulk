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
 * Event fired after bulk changes are applied.
 *
 * @package    local_sectionbulk
 * @copyright  2026 SiteEcuador - Msg. Franklin Moya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sectionbulk\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Bulk changes applied event.
 */
class bulk_change_applied extends \core\event\base {
    /**
     * Initialize event properties.
     */
    protected function init(): void {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = null;
    }

    /**
     * Return event name.
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('eventbulkchangeapplied', 'local_sectionbulk');
    }

    /**
     * Return event description.
     *
     * @return string
     */
    public function get_description(): string {
        return get_string('eventdescription', 'local_sectionbulk', $this->userid);
    }
}

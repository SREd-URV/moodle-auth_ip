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
 * IP authentication plugin upgrade code.
 *
 * @package    auth
 * @subpackage ip
 * @copyright  2016 Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Perform upgrade.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_ip_upgrade($oldversion) {

    if ($oldversion < 2016070701) {

        $validips = get_config('auth_ip', 'valid_ips');

        if (!empty($validips)) {
            $updatedvalidips = '';
            $ips = explode(",", $validips);

            foreach ($ips as $ip) {
                $ip = trim($ip);

                if (!empty($ip)) {
                    $updatedvalidips .= $ip . "\r\n";
                }
            }

            set_config('valid_ips', $updatedvalidips, 'auth_ip');
        }

        upgrade_plugin_savepoint(true, 2016070700, 'auth', 'ip');
    }

    return true;
}

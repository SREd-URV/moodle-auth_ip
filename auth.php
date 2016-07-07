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
 * auth.php - IP authentication plugin.
 *
 * This plugin allows access for only the given IPs.
 *
 * @package    auth
 * @subpackage ip
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Robert Boloc <robert.boloc@urv.cat>
 * @author     Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 onwards Servei de Recursos Educatius (http://www.sre.urv.cat)
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/auth/manual/auth.php');

/**
 * Auth plugin to allow login only from restricted IPs.
 */
class auth_plugin_ip extends auth_plugin_manual {

    public function __construct() {
        $this->authtype = 'ip';
        $this->config   = get_config('auth_ip');
    }

    /**
     * Tells a login success when the user is logged in correctly and from one of the given IPs.
     * Cannot login when username and password are not correct, or from other IPs than those restricted ones.
     *
     * @param string $username username
     * @param string $password password
     * @return bool
     */
    public function user_login($username, $password) {
        global $DB, $CFG;
        if (($user = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id)))) {

            if (remoteip_in_list($this->config->valid_ips)) {
                return validate_internal_user_password($user, $password);
            } else {
                return false;
            }
        }
        // If no valid username, we do not allow to create a new user using this auth type.
        return false;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    public function is_internal() {
        return false;
    }

}

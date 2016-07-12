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

        if ($this->should_display_error()) {
            $this->print_error_message();
        } else {
            if (($user = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id)))) {
                if (remoteip_in_list($this->config->valid_ips)) {
                    return validate_internal_user_password($user, $password);
                }
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

    /**
     * Implements loginpage_hook().
     */
    public function loginpage_hook() {
        if ($this->should_display_error()) {
            $this->print_error_message();
        }
    }

    /**
     * Implements pre_loginpage_hook().
     */
    public function pre_loginpage_hook() {
        if ($this->should_display_error()) {
            $this->print_error_message();
        }
    }

    /**
     * Check if we should display error message to a user.
     *
     * @return bool True | false.
     */
    public function should_display_error() {
        if (!$this->config->check_before_login) {
            return false;
        }

        if (remoteip_in_list($this->config->valid_ips)) {
            return false;
        }

        return true;
    }

    /**
     * Prints an error message.
     */
    public function print_error_message() {
        global $SITE, $PAGE, $OUTPUT, $SESSION;

        header('HTTP/1.0 403 Forbidden');

        if (!isset($PAGE->context)) {
            $PAGE->set_context(context_system::instance());
        }

        if (!isset($PAGE->url)) {
            if (isset($SESSION->wantsurl)) {
                $PAGE->set_url($SESSION->wantsurl);
            } else {
                $PAGE->set_url('/');
            }
        }

        $PAGE->set_pagetype('maintenance-message');
        $PAGE->set_pagelayout('standard');
        $PAGE->set_title(strip_tags($SITE->fullname));

        echo $OUTPUT->header();

        $renderer = $PAGE->get_renderer('auth_ip');

        if (isset($this->config->error_text) and !html_is_blank($this->config->error_text)) {
            echo $renderer->render_error_message($this->config->error_text);
        }

        echo $OUTPUT->footer();

        die;
    }

    /**
     * Check if provided IP is in provided list of IPs.
     *
     * @param string $list A list of IPs or subnet addresses.
     * @param string $ip IP address.
     *
     * @return bool
     */
    public static function is_ip_in_list($list, $ip) {
        $inlist = false;

        $list = explode("\n", $list);
        foreach ($list as $subnet) {
            $subnet = trim($subnet);
            if (address_in_subnet($ip, $subnet)) {
                $inlist = true;
                break;
            }
        }

        return $inlist;
    }

    /**
     * Return SQL data to get all active user sessions from DB.
     *
     * @return array Array of the first element is SQL and the second element is params.
     */
    protected function get_active_sessions_sql_data() {
        global $CFG;

        $sql = "SELECT s.id, s.sid, s.userid, s.timecreated, s.timemodified, s.firstip, s.lastip
                FROM {sessions} s
                WHERE s.timemodified > :activebefore";

        $params = array(
            'activebefore' => time() - $CFG->sessiontimeout,
        );

        return array($sql, $params);
    }

    /**
     * Return a record set of all active user sessions.
     *
     * @return moodle_recordset A moodle_recordset instance of active sessions.
     */
    public function get_active_sessions_recordset() {
        global $DB;
        $sqldata = $this->get_active_sessions_sql_data();

        return $DB->get_recordset_sql($sqldata[0], $sqldata[1]);
    }

    /**
     * Return a number of currently active user sessions.
     *
     * @return int A number of sessions.
     */
    public function count_active_sessions() {
        global $DB;

        $sqldata = $this->get_active_sessions_sql_data();

        return $DB->count_records_select('sessions', 'timemodified > :activebefore', $sqldata[1]);
    }

    /**
     * Check if provided user session should be killed.
     *
     * @param object $session A record from {sessions} table.
     *
     * @return bool
     */
    public function should_kill_session($session) {
        global $USER;

        if ($session->userid == $USER->id) {
            return false;
        }

        if (self::is_ip_in_list($this->config->valid_ips, $session->lastip)) {
            return false;
        }

        return true;
    }

    /**
     * Kill all required active sessions.
     */
    public function kill_active_sessions() {
        $sessions = $this->get_active_sessions_recordset();

        foreach ($sessions as $session) {
            if ($this->should_kill_session($session)) {
                \core\session\manager::kill_session($session->sid);
            }
        }

        $sessions->close();
    }

}

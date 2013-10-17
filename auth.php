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

require_once($CFG->libdir.'/authlib.php');

/**
 * Auth plugin to allow login only from restricted IPs.
 */
class auth_plugin_ip extends auth_plugin_base {

    /**
     * Constructor
     */
    function __construct() {
        $this->authtype = 'ip';
        $this->config = get_config('auth_ip');
    }

    /**
     * Tells a login success when the user is logged in correctly and from one of the given IPs.
     * Cannot login when username and password are not correct, or from other IPs than those restricted ones.
     *
     * @param string $username username
     * @param string $password password
     * @return bool
     */
    function user_login($username, $password) {
        global $DB, $CFG;
        if (($user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id)))) {
            $valid_ips = explode(',', $this->config->valid_ips);
            //check if IP is one of the restricted ones.
            if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $valid_ips)) {
                return validate_internal_user_password($user, $password);
            } else {
                return false;
            }
        }
        // if no valid username, we do not allow to create a new user using this auth type.
        return false;
    }

    /**
     * Updates the user's password.
     *
     * called when the user password is updated.
     *
     * @param  object  $user        User table object  (with system magic quotes)
     * @param  string  $newpassword Plaintext password (with system magic quotes)
     * @return boolean result
     *
     */
    function user_update_password($user, $newpassword) {
        $user = get_complete_user_data('id', $user->id);
        return update_internal_user_password($user, $newpassword);
    }

    function prevent_local_passwords() {
        return false;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return false;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return true;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return string
     */
    function change_password_url() {
        return '';
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password() {
        return true;
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        include "config.html";
    }

    /**
     * Updates the list of IPs and sends a notification by email.
     *
     * @param object $config configuration settings
     * @return boolean always true.
     */
    function process_config($config) {

        global $CFG;

        // set to defaults if undefined
        if (!isset ($config->valid_ips)) {
            $config->valid_ips = '';
        }

        //saving new configuration settings
        set_config('valid_ips', str_replace(' ', '', $config->valid_ips), 'auth_ip');

        //notify administrator for the settings changed for security.
        mail($CFG->supportemail, get_string('auth_ipmailsubject', 'auth_ip'),
                get_string('auth_ipmailtext', 'auth_ip').' : '.$config->valid_ips);

        return true;
    }
}

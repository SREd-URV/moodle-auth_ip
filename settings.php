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
 * Configuration settings form
 *
 * @package    auth
 * @subpackage ip
 * @copyright  2016 Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$url = new moodle_url('/auth/ip/users.php');
$ADMIN->add('authsettings', new admin_externalpage('auth_ip_sessions',
    get_string('auth_iplogoutheading', 'auth_ip'), $url->out(), 'moodle/site:config', true));

if ($ADMIN->fulltree) {

    require_once($CFG->dirroot . '/auth/ip/renderer.php');
    require_once($CFG->dirroot . '/auth/ip/auth.php');

    $renderer = $PAGE->get_renderer('auth_ip');

    $options = array(get_string('no'), get_string('yes'));
    $placeholders = implode(', ', array_keys($renderer->get_placeholders_data()));
    $auth = new auth_plugin_ip();

    if (isset($auth->config->check_before_login) && !empty($auth->config->check_before_login)) {
        $settings->add(new admin_setting_heading('auth_ip/warning', '',
            $renderer->get_your_ip_not_in_range_error_message()));

        $settings->add(new admin_setting_heading('auth_ip/logoutuserstext', '',
            $renderer->get_settings_user_logout_page_link_description()));
    }

    $settings->add(new admin_setting_configiplist('auth_ip/valid_ips',
        new lang_string('auth_ipvalidips', 'auth_ip'),
        new lang_string('auth_ipdescription', 'auth_ip'), ''));

    $settings->add(new admin_setting_configselect('auth_ip/check_before_login',
        get_string('auth_ipcheckbeforelogin', 'auth_ip'),
        get_string('auth_ipcheckbeforelogin_desc', 'auth_ip'), 0, $options));

    $settings->add(new admin_setting_confightmleditor('auth_ip/error_text',
        new lang_string('auth_iperrortext', 'auth_ip'),
        new lang_string('auth_iperrortext_desc', 'auth_ip', $placeholders), ''));
}

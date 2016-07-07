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
 * Removing active user sessions.
 *
 * @package    auth
 * @subpackage ip
 * @copyright  2016 Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require(dirname((dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('auth.php');

$action  = optional_param('action', '', PARAM_ALPHA);

require_login(null, false);
require_capability('moodle/site:config', context_system::instance());

$auth = new auth_plugin_ip();

if (!$auth->config->check_before_login) {
    $settingsurl = new moodle_url('/admin/settings.php', array('section' => 'authsettingip'));
    print_error('auth_ipcheckbeforelogindisabled', 'auth_ip', $settingsurl->out());
}

admin_externalpage_setup('auth_ip_sessions');

$output = $PAGE->get_renderer('auth_ip');

echo $output->header();
echo $output->heading(get_string('auth_iplogoutheading', 'auth_ip'));

if ($action === 'remove') {
    require_sesskey();
    $PAGE->set_cacheable(false);
    $progressbar = new progress_bar();
    $progressbar->create();
    core_php_time_limit::raise(HOURSECS);
    raise_memory_limit(MEMORY_EXTRA);
    $auth->kill_active_sessions($progressbar);
    echo $output->continue_button(new moodle_url('/admin/settings.php', array('section' => 'authsettingip')), 'get');
    echo $output->footer();
    exit;
} else {
    echo $output->get_your_ip_not_in_range_error_message();
    echo $output->get_user_logout_description($auth->count_active_sessions());
    echo $output->single_button(new moodle_url($PAGE->url, array('action' => 'remove')), get_string('auth_iplogoutbutton', 'auth_ip'), 'post');
    echo $output->footer();
    exit;
}


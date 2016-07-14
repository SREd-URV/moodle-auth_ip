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
 * CLI script to enable/disable "Check IP before logging in" setting.
 *
 * @package    auth
 * @subpackage ip
 * @copyright  2016 Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->libdir.'/clilib.php');

list($options, $unrecognized) = cli_get_params(
    array(
        'enable'   => false,
        'disable'  => false,
        'help'     => false,
    ),
    array(
        'h' => 'help'
    )
);

$status = !empty($options['enable']) ? 1 : 0;

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}


if ($options['help'] || (empty($options['enable']) && empty($options['disable']))) {
    $help = "
Command line script to enable/disable \"Check IP before logging in\" setting for auth/ip plugin.

Options:
--enable       Enable the setting
--disable      Disable the setting
-h, --help     Print out this help

Example:
\$sudo -u www-data /usr/bin/php auth/ip/cli/check_before_logging.php --disable
";
    echo $help;
    die;
}

set_config('check_before_login', $status, 'auth_ip');

echo get_string('auth_ipclistatuschanged', 'auth_ip', $status)."\n";

exit(0);

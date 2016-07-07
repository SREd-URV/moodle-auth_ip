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
 * English strings
 *
 * @package    auth
 * @subpackage ip
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Robert Boloc <robert.boloc@urv.cat>
 * @author     Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 onwards Servei de Recursos Educatius (http://www.sre.urv.cat)
 */

$string['auth_ipdescription'] = 'Put every entry on one line. Valid entries are either full IP address (such as <b>192.168.10.1</b>) which matches a single host; or partial address (such as <b>192.168</b>) which matches any address starting with those numbers; or CIDR notation (such as <b>231.54.211.0/20</b>); or a range of IP addresses (such as <b>231.3.56.10-20</b>) where the range applies to the last part of the address. Text domain names (like \'example.com\') are not supported. IPV6 address are not supported. Blank lines are not supported.';
$string['auth_ipvalidips'] = 'Valid IPs';
$string['auth_ipcheckbeforelogin'] = 'Check IP before logging in';
$string['auth_ipcheckbeforelogin_desc'] = 'If this setting is enabled then users will see error message before they saw a login page. You will be able to log out  anyone currently logged in who\'s ip address doesn\'t match valid IPs.';
$string['auth_iperrortext'] = 'Error text';
$string['auth_iperrortext_desc'] = 'This text will be displayed to users if "Check IP before logging in" option is enabled. <br /> Placeholders can be used: {$a}';
$string['auth_ipcheckbeforelogindisabled'] = '"Check IP before logging in" setting is disabled. Please enable this settings and come back to the page.';
$string['auth_iplogoutuserstext'] = '"Check IP before logging in" setting is enabled. You can log out anyone currently logged in who\'s ip address doesn\'t match valid IPs. Please use following link {$a}';
$string['auth_iplogoutheading'] = 'Log out active users';
$string['auth_iplogoutlink'] = 'Log out active users';
$string['auth_iplogoutbutton'] = 'Log out active users';
$string['auth_iplogoutinprogress'] = 'Logging out active users ';
$string['auth_iplogoutdone'] = 'Completed. Total users: {$a}.';
$string['auth_iplogoutdescription'] = 'You can logout anyone currently logged in who\'s ip address doesn\'t match valid IPs. <b>This will not affect your current user session.</b> <br />Total number of all active users: {$a}';
$string['auth_iplogoutwarning'] = 'Your IP {$a} is not in Valid IPs list. You will not be able to login once you are logged out.';
$string['auth_ipclistatuschanged'] = 'The setting "Check IP before logging in" is set to {$a}';
$string['pluginname'] = 'Authentication by IP';

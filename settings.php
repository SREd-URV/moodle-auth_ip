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
 * @author Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2017 Servei de Recursos Educatius (http://www.sre.urv.cat)
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(
        new admin_setting_heading(
            'auth_ip/auth_ipdescription',
            '',
            new lang_string('auth_ipdescription', 'auth_ip')
        )
    );

    $settings->add(
        new admin_setting_configtextarea(
            'auth_ip/valid_ips',
            new lang_string('auth_ipvalidips', 'auth_ip'),
            new lang_string('auth_ipexampleips', 'auth_ip'),
            ''
        )
    );
}

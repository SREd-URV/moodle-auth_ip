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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/auth/ip/auth.php');

class auth_ip_testcase extends advanced_testcase {
    protected $authplugin;

    protected function setUp() {
        $this->authplugin = new auth_plugin_ip();
    }

    /**
     * Test that valid IPs or IPs in range are detected as valid.
     *
     * @dataProvider is_ip_valid_data_provider
     *
     * @param array   $valid_ips
     * @param string  $ip
     * @param boolean $is_valid
     */
    public function test_is_ip_valid_detects_valid_ips($valid_ips, $ip, $is_valid) {
        $this->authplugin->config->valid_ips = $valid_ips;

        $this->assertEquals(
            $is_valid,
            $this->authplugin->is_ip_valid($ip)
        );
    }

    /**
     * @return array
     */
    public static function is_ip_valid_data_provider() {
        return array(
            array('192.168.1.1,192.168.1.2', '192.168.1.1', true),
            array('192.168.1.1,192.168.1.2', '192.168.1.3', false),
            array('192.168.0.0/24', '192.168.0.200', true),
            array('111.112.0.0/12,96.0.0.0/6', '192.168.0.200', false),
            array('111.112.0.0/12,96.0.0.0/6', '99.255.255.254', true),
            array('111.112.0.0/12,10.40.22.0/24,96.0.0.0/6', '10.40.22.50', true),
            array('111.112.0.0/12, 10.40.22.0/24, 96.0.0.0/6', '10.40.22.50', true),
            array('  111.112.0.0  ', '111.112.0.0', true), // Extra spaces for testing
        );
    }

    /**
     * Test range detection
     *
     * @dataProvider is_cidr_data_provider
     *
     * @param string  $ip_or_cidr
     * @param boolean $is_cidr
     */
    public function test_is_cidr_detects_cidrs($ip_or_cidr, $is_cidr) {
        $this->assertEquals(
            $this->authplugin->is_cidr($ip_or_cidr),
            $is_cidr
        );
    }

    /**
     * @return array
     */
    public static function is_cidr_data_provider() {
        return array(
            array('192.168.1.1', false),
            array('192.168.1.1/24', true),
            array('10.15.1.1', false),
            array('10.15.1.1/2', true),
        );
    }

    /**
     * Test the plugin is not marked as internal.
     */
    public function test_is_not_internal() {
        $this->assertFalse($this->authplugin->is_internal());
    }
}

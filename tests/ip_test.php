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
require_once($CFG->dirroot.'/auth/ip/renderer.php');

class auth_ip_testcase extends advanced_testcase {
    /**
     * An instance of auth_plugin_ip class.
     * @var object
     */
    protected $authplugin;

    protected function setUp() {
        $this->authplugin = new auth_plugin_ip();
        $this->resetAfterTest(true);
    }

    /**
     * Test that error message displayed correctly if check_before_login is enabled.
     *
     * @dataProvider should_not_display_error_data_provider
     *
     * @param array   $ips
     * @param string  $ip
     * @param boolean $display
     */
    public function test_should_display_error_if_check_before_login_enabled($ips, $ip, $display) {
        $this->authplugin->config->valid_ips = $ips;
        $this->authplugin->config->check_before_login = true;

        $_SERVER['HTTP_CLIENT_IP'] = $ip;

        $this->assertEquals(
            $display,
            $this->authplugin->should_display_error()
        );

        $this->authplugin->config->check_before_login = false;

        $this->assertFalse(
            $this->authplugin->should_display_error()
        );
    }

    /**
     * Test that error message are not displayed in any cases if check_before_login is disabled.
     *
     * @dataProvider should_not_display_error_data_provider
     *
     * @param array   $ips
     * @param string  $ip
     * @param boolean $display
     */
    public function test_should_not_display_error_if_check_before_login_disabled($ips, $ip, $display) {
        $this->authplugin->config->valid_ips = $ips;
        $this->authplugin->config->check_before_login = false;

        $_SERVER['HTTP_CLIENT_IP'] = $ip;

        $this->assertFalse(
            $this->authplugin->should_display_error()
        );
    }

    /**
     * A list of data to test displaying an error message against.
     *
     * @return array
     */
    public static function should_not_display_error_data_provider() {
        return array(
            array("192.168.1.1\n192.168.1.2", '192.168.1.1', false),
            array("192.168.1.1\n192.168.1.2", '192.168.1.3', true),
            array("192.168.0.0/24", '192.168.0.200', false),
            array("111.112.0.0/12\n96.0.0.0/6", '192.168.0.200', true),
            array("111.112.0.0/12\n96.0.0.0/6", '99.255.255.254', false),
            array("111.112.0.0/12\n10.40.22.0/24\n96.0.0.0/6", '10.40.22.50', false),
            array("111.112.0.0/12\n 10.40.22.0/24\n 96.0.0.0/6", '10.40.22.50', false),
            array("  111.112.0.0  ", '111.112.0.0', false),
            array("192.168.1.1-200", '192.168.1.50', false),
            array("192.168.1.1-200", '192.168.1.201', true),
        );
    }

    /**
     * Test the plugin is not marked as internal.
     */
    public function test_is_not_internal() {
        $this->assertFalse($this->authplugin->is_internal());
    }

    /**
     * Test that placeholder data is correct.
     */
    public function test_placeholders_data() {
        $placeholders = auth_ip_renderer::get_placeholders_data();

        $this->assertTrue(is_array($placeholders));
        $this->assertEquals(2, count($placeholders));
        $this->assertTrue(array_key_exists('[[valid_ips]]', $placeholders));
        $this->assertTrue(array_key_exists('[[your_ip]]', $placeholders));
    }

    /**
     * Test valid_ips placeholder if valid_ips configuration is not set.
     */
    public function test_valid_ips_placeholder_if_config_is_not_set() {
        $placeholders = auth_ip_renderer::get_placeholders_data();

        $expected = '';
        $actual = $placeholders['[[valid_ips]]'];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test valid_ips placeholder if valid_ips configuration is set.
     */
    public function test_valid_ips_placeholder_if_config_is_set() {
        set_config('valid_ips', '192.168.1.1, 192.168.1.2', 'auth_ip');
        $placeholders = auth_ip_renderer::get_placeholders_data();

        $expected = '192.168.1.1, 192.168.1.2';
        $actual = $placeholders['[[valid_ips]]'];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test your_ip placeholder value.
     */
    public function test_your_ip_placeholder_value() {
        $_SERVER['HTTP_CLIENT_IP'] = '192.168.1.2';
        $placeholders = auth_ip_renderer::get_placeholders_data();

        $expected = '192.168.1.2';
        $actual = $placeholders['[[your_ip]]'];

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that renderer can replace placeholders in a message.
     */
    public function test_render_replace_placeholders() {
        global $PAGE;

        set_config('valid_ips', '192.168.1.1', 'auth_ip');
        $_SERVER['HTTP_CLIENT_IP'] = '192.168.1.3';
        $message = "Your IP is [[your_ip]] [[your_ip]] and valid ips are [[valid_ips]] [[valid_ips]]. Other placeholder [[*]] [[vi]] [[ip]]";
        $renderer = $PAGE->get_renderer('auth_ip');

        $expected = "Your IP is 192.168.1.3 192.168.1.3 and valid ips are 192.168.1.1 192.168.1.1. Other placeholder [[*]] [[vi]] [[ip]]";
        $actual = $renderer->replace_placeholders($message);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that render display an error message.
     */
    public function test_render_displays_error_message() {
        global $PAGE;

        $renderer = $PAGE->get_renderer('auth_ip');

        $expected = "<div class=\"box generalbox\">Test error message</div>";
        $actual   = $renderer->render_error_message('Test error message');

        $this->assertEquals($expected, $actual);
    }
}

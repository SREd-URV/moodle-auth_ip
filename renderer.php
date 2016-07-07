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
 * Renderer code.
 *
 * @package    auth
 * @subpackage ip
 * @copyright  2016 Dmitrii Metelkin (dmitriim@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class auth_ip_renderer extends plugin_renderer_base {
    /**
     * Render an error message.
     *
     * @param string $message Error message HTML text.
     *
     * @return string HTML to display.
     */
    public function render_error_message($message) {
        $html = '';

        $html .= $this->output->box_start();
        $html .= $this->replace_placeholders($message);
        $html .= $this->output->box_end();

        return $html;
    }

    /**
     * Return a list placeholders with their values.
     *
     * @return array An array of placeholders.
     */
    public function get_placeholders_data() {
        $config = get_config('auth_ip');

        return array(
            '[[valid_ips]]' => isset($config->valid_ips) ? $config->valid_ips : '',
            '[[your_ip]]' => getremoteaddr(),
        );
    }

    /**
     * Replace placeholders by related values in the provided message.
     *
     * @param string $message A message HTML text.
     *
     * @return string A message after replacing the placeholders.
     */
    public function replace_placeholders($message) {
        $placeholders = array_keys($this->get_placeholders_data());
        $values = array_values($this->get_placeholders_data());

        $message = str_replace($placeholders, $values, $message);

        return $message;
    }

    /**
     * Get a link to a page for logging out all active users.
     *
     * @return string HTML link.
     */
    public function get_user_logout_page_link() {
        $url = new moodle_url('/auth/ip/users.php');
        $link = html_writer::link($url, get_string('auth_iplogoutlink', 'auth_ip'));

        return $link;
    }

    /**
     * Get description for the Log out active users page.
     *
     * @return string
     */
    public function get_settings_user_logout_page_link_description() {
        return $this->output->notification(
            get_string('auth_iplogoutuserstext', 'auth_ip', $this->get_user_logout_page_link()),
            'info');
    }

    /**
     * Get a error message to display.
     *
     * @return string
     */
    public function get_your_ip_not_in_range_error_message() {
        $html = '';

        $config = get_config('auth_ip');
        $ip = getremoteaddr();
        $list = isset($config->valid_ips) ? $config->valid_ips : '';

        if (!auth_plugin_ip::is_ip_in_list($list, $ip)) {
            $html = $this->output->notification(
                get_string('auth_iplogoutwarning', 'auth_ip', $ip),
                'error'
            );
        }

        return $html;
    }

    /**
     * Get description for the Log out active users page.
     *
     * @return string
     */
    public function get_user_logout_description($activeusers) {
        $html = '';
        $html .= html_writer::tag('p', get_string('auth_iplogoutdescription', 'auth_ip', $activeusers),
            array('class' => 'auth-ip-logout-descr'));

        return $html;
    }

}

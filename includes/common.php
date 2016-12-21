<?php
/**
 * Created by PhpStorm.
 * User: zhensheng
 * Date: 12/21/16
 * Time: 8:07 PM
 */
namespace ccdgut_cs;

abstract class common {
    static function get_session_file_name($system_url, $username) {
        return "/tmp/" . str_replace("/", "_", $system_url) . "_session_" . $username;
    }

    static function get_view_state_file_name($system_url, $username) {
        return  "/tmp/" . str_replace("/", "_", $system_url) . "_view_state_" . $username;
    }
}
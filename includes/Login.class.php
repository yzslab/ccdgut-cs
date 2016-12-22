<?php

/**
 * Created by PhpStorm.
 * User: Zhensheng
 * Date: 2016/6/22
 * Time: 19:03
 */
namespace CCDGUT_ClassSelector;

class Login extends \Thread {
    static private $username;
    static private $password;

    private $session_id;
    private $view_state;
    private $sleep_time;
    private $system_url;
    private $success = false;

    public function __construct() {
        $argc = func_num_args();
        $argv = func_get_args();
        switch ($argc) {
            case 4:
                $this->thread_constructor(...$argv);
                break;
            case 3:
            case 5:
                $this->main_constructor(...$argv);
                break;
            default:
                throw new \Exception("An unclear method was called\n");
        }
    }

    private function main_constructor($system_url, $username, $password, $session_id = "", $view_state = "") {
        $this->system_url = $system_url;
        if (empty($session_id) || empty($view_state)) {
            echo "开始获取Session ID\n";
            $curl_opt_array = array(
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $this->system_url,
                CURLOPT_TIMEOUT => TIMEOUT
            );
            $ch = curl_init();
            curl_setopt_array($ch, $curl_opt_array);
            $return_content = curl_exec($ch);
            if ($return_content === false) {
                exit("Curl请求失败: ". curl_error($ch)."\n");
            }
            $return_content_array = explode("\n", $return_content);
            $location_array = preg_grep("#^Location: /#", $return_content_array);
            list($key, $location) = each($location_array);
            $session_id = trim(str_replace(array("Location: /", "/default2.aspx"), array("", ""), $location));
            if (preg_match("#^\([a-zA-Z0-9]+\)$#", $session_id)) {
                echo "Session ID获取成功: ".$session_id."\n开始获取__VIEW_STATE\n";
            } else {
                exit("获取Session ID失败\n");
            }
            $curl_opt_array = array(
                CURLOPT_USERAGENT => USER_AGENT,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $this->system_url."/".$session_id."/default2.aspx",
                CURLOPT_TIMEOUT => TIMEOUT
            );
            curl_setopt_array($ch, $curl_opt_array);
            $return_content = curl_exec($ch);
            if ($return_content === false) {
                exit("Curl请求失败: ". curl_error($ch)."\n");
            }
            $return_content_array = explode("\n", $return_content);
            $view_state_array = preg_grep("#__VIEWSTATE#", $return_content_array);
            list($key, $view_state) = each($view_state_array);
            $view_state = str_replace("<input type=\"hidden\" name=\"__VIEWSTATE\" value=\"", "", $view_state);
            $view_state = str_replace("\" />", "", $view_state);
            $view_state = trim($view_state);
            if (preg_match("/^[0-9a-zA-Z]{1,}/", $view_state)) {
                echo "VIEW_STATE获取成功: ".$view_state."\n";
            } else {
                exit("VIEW_STATE获取失败\n");
            }
        }
        self::$username = $username;
        self::$password = $password;
        $this->session_id = $session_id;
        $this->view_state = $view_state;
        file_put_contents(Common::get_session_file_name($system_url, self::$username), $session_id);
        file_put_contents(Common::get_view_state_file_name($system_url, self::$username), $view_state);
        echo "Please select class via ".$this->system_url.$this->session_id."/xs_main.aspx?xh=".self::$username."\n";
    }

    private function thread_constructor(&$system_url, &$session_id, &$view_state, $sleep_time) {
        $this->system_url = $system_url;
        $this->session_id = $session_id;
        $this->view_state = $view_state;
        $this->sleep_time = $sleep_time;
    }

    public function start_logion($thread_num = 10) {
        $pthreads_objs = [];
        for ($i = 1; $i <= $thread_num; $i++) {
            // echo "创建第".$i."个登录线程，总共".$thread_num."个\n";
            $pthreads_objs[$i] = new self($this->system_url, $this->session_id, $this->view_state, $thread_num);
        }
        foreach ($pthreads_objs as $index => $obj) {
            // echo "Start thread " . $index . "\n";
            $obj->start();
            sleep(2);
        }
    }

    public function run() {
        $fields = array(
            "__VIEWSTATE" => $this->view_state,
            "TextBox1" => self::$username,
            "TextBox2" => self::$password,
            "RadioButtonList1" => "%D1%A7%C9%FA",
            "Button1" => "",
            "lbLanguage" => ""
        );
        $ch = curl_init();
        $curl_opt_array = array(
            CURLOPT_USERAGENT => USER_AGENT,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $this->system_url."/".$this->session_id."/default2.aspx",
            CURLOPT_TIMEOUT => TIMEOUT,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_REFERER => $this->system_url."/".$this->session_id."/default2.aspx"
        );
        curl_setopt_array($ch, $curl_opt_array);
        while (true) {
            $login_return = curl_exec($ch);
            if ($login_return === false) {
                echo("Curl请求失败: " . curl_error($ch) . "\n");
                continue;
            }
            if (preg_match("/xs_main\.aspx\?xh=" . self::$username . "/", $login_return)) {
                if (!$this->success) {
                    // echo "[线程" . $this->getThreadId() . "(" . $this->system_url . ")]登录成功\n";
                    // echo "[线程" . $this->getThreadId() . "(" . $this->system_url . ")]登录成功\n" . "\tPlease select class via ".$this->system_url.$this->session_id."/xs_main.aspx?xh=".self::$username."\n";
                    $this->success = true;
                }
            } else {
                $this->success = false;
                echo "[线程" . $this->getThreadId() . "]" . $this->system_url . " Login failed\n";
            }
            sleep($this->sleep_time);
        }
    }
}
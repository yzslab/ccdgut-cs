<?php

/**
 * Created by PhpStorm.
 * User: Zhensheng
 * Date: 2016/6/22
 * Time: 21:27
 */
namespace CCDGUT_ClassSelector;

class ClassSelector extends \Thread {
    static private $username;
    static private  $post_url;

    private $system_url;
    private $session_id;
    private $raw_fields;
    private $raw_field_sha1;
    private $sleep_time;

    public function __construct() {
        $argc = func_num_args();
        $argv = func_get_args();
        switch ($argc) {
            case 6:
                $this->thread_constructor(...$argv);
                break;
            case 5:
                $this->main_constructor(...$argv);
                break;
            default:
                throw new \Exception("An unclear method was called\n");
        }
    }

    private function main_constructor($system_url, $username, $session_id, $post_url, $raw_fields) {
        $this->system_url = $system_url;
        self::$username = $username;
        $this->session_id = $session_id;
        self::$post_url = $post_url;
        $this->raw_fields = $raw_fields;
        $this->raw_field_sha1 = sha1($this->raw_fields);
    }

    private function thread_constructor($system_url, $session_id, $raw_fields, $raw_field_sha1, $sleep_time, $thread = true) {
        $this->system_url = $system_url;
        $this->session_id = $session_id;
        $this->raw_fields = $raw_fields;
        $this->sleep_time = $sleep_time;
        $this->raw_field_sha1 = $raw_field_sha1;
    }

    public function start_cs($thread_num = 10) {
        echo "接收表单URL: ".self::$post_url."\n";
        $pthreads_objs = [];
        for ($i = 1; $i <= $thread_num; $i++) {
            echo "创建第" . $i . "个登录线程，总共" . $thread_num . "个\n";
            $pthreads_objs[$i] = new self($this->system_url, $this->session_id, $this->raw_fields, $this->raw_field_sha1, $this->sleep_time, true);
        }
        foreach ($pthreads_objs as $obj) {
            $obj->start();
            sleep(2);
        }
    }

    public function run() {
        $ch = curl_init();
        $curl_opt_array = array(
            CURLOPT_USERAGENT => USER_AGENT,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $this->system_url."/".$this->session_id."/".self::$post_url,
            CURLOPT_TIMEOUT => TIMEOUT,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $this->raw_fields,
            CURLOPT_REFERER => $this->system_url."/".$this->session_id."/".self::$post_url
        );
        curl_setopt_array($ch, $curl_opt_array);
        while (true) {
            $cs_return = curl_exec($ch);
            if ($cs_return === false) {
                echo "[线程".$this->getThreadId()."(".$this->system_url.")]"."Curl请求失败: ". curl_error($ch)."\n";
                continue;
            }
            $cs_return_array =  explode("\n", $cs_return);
            $cs_return_array = preg_grep("/>alert\(/", $cs_return_array);
            if (count($cs_return_array))
                echo "[线程".$this->getThreadId()."(".$this->system_url.")]Class select request ". $this->raw_field_sha1 ." submitted successfully, timestamp: ".time()."\n\tServer reply: \n";
            else
                echo "[线程".$this->getThreadId()."(".$this->system_url.")]Class select request ". $this->raw_field_sha1 ." submitted failed, timestamp: ".time()."\n\tServer reply: \n";
            foreach ($cs_return_array as $alert) {
                echo "\t" . iconv("gbk", "utf-8", $alert) . "\n";
            }
            sleep($this->sleep_time);
        }
    }
}
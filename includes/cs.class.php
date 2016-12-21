<?php

/**
 * Created by PhpStorm.
 * User: Zhensheng
 * Date: 2016/6/22
 * Time: 21:27
 */
class cs extends Thread {
    protected $username;
    protected $password;
    protected $session_id;
    protected $sleep_time;
    protected $pthreads_obj = array();
    protected $raw_fields; // Static not work in thread safe version
    protected $post_url;
    protected $system_url;

    public function __construct($system_url, $username, $session_id, $post_url, $raw_fields, $sleep_time = 1) {
        $this->system_url = $system_url;
        $this->username = $username;
        $this->session_id = $session_id;
        $this->post_url = $post_url;
        $this->raw_fields = $raw_fields;
        $this->sleep_time = $sleep_time;
    }

    public function start_cs($thread_num = 10) {
        echo "接收表单URL: ".$this->post_url."\n";
        for ($i = 1; $i <= $thread_num; $i++) {
            echo "创建第" . $i . "个登录线程，总共" . $thread_num . "个\n";
            $this->pthreads_obj[$i] = new cs($this->system_url, $this->username, $this->session_id, $this->post_url, $this->raw_fields, $this->sleep_time);
            $this->pthreads_obj[$i]->start();
            sleep(2);
        }
    }

    public function run()
    {
        $ch = curl_init();
        $curl_opt_array = array(
            CURLOPT_USERAGENT => USER_AGENT,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $this->system_url."/".$this->session_id."/".$this->post_url,
            CURLOPT_TIMEOUT => TIMEOUT,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $this->raw_fields,
            CURLOPT_REFERER => $this->system_url."/".$this->session_id."/".$this->post_url
        );
        curl_setopt_array($ch, $curl_opt_array);
        while (true) {
            echo "[线程".$this->getThreadId()."(".$this->system_url.")]".time()."\n";
            $cs_return = curl_exec($ch);
            if ($cs_return === false) {
                echo("Curl请求失败: ". curl_error($ch)."\n");
                continue;
            }
            $cs_return_array =  explode("\n", $cs_return);
            $cs_return_array = preg_grep("/alert\(/", $cs_return_array);
            foreach ($cs_return_array as $alert)
                echo iconv("gbk", "utf-8", $alert). "\n";
            sleep($this->sleep_time);
        }
    }
}
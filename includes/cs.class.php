<?php

/**
 * Created by PhpStorm.
 * User: Zhensheng
 * Date: 2016/6/22
 * Time: 21:27
 */
class cs extends Thread
{
    protected $username;
    protected $password;
    protected $session_id;
    protected $sleep_time;
    protected $pthreads_obj = array();
    public $raw_fields;
    public $post_url;

    public function __construct($sleep_time = 1)
    {
        if ($_SERVER["argc"] <= 3)
            exit("使用方法: ".$_SERVER["argv"][0]. "用户名 接收表单URL 表单内容一 [表单内容二 ...]\n");
        if (($this->session_id = file_get_contents(__DIR__ . "/session_id_".$_SERVER["argv"][1])) === false) {
            exit("Session ID文件(".__DIR__ . "/session_id_".$_SERVER["argv"][1].")不存在\n");
        }
        $this->sleep_time = $sleep_time;
    }

    public function start_cs($thread_num = 10) {
        echo "接收表单URL: ".$_SERVER["argv"][2]."\n";
        for ($arg_i = 3; $arg_i < $_SERVER["argc"]; $arg_i++) {
            echo "正在为第".($arg_i - 2)."个表单数据创建线程\n";
            if (($form_data = file_get_contents($_SERVER["argv"][2])) == false) {
                echo "文件".$_SERVER["argv"][2]."不存在，取消本表单的线程创建\n";
                continue;
            }
            for ($i = 1; $i <= $thread_num; $i++) {
                echo "[表单".($arg_i - 2)."]创建第" . $i . "个登录线程，总共" . $thread_num . "个\n";
                $this->pthreads_obj[$i] = new cs($thread_num);
                $this->pthreads_obj[$i]->raw_fields = $_SERVER["argv"][$arg_i];
                $this->pthreads_obj[$i]->post_url = $form_data;
                $this->pthreads_obj[$i]->start();
                sleep(2);
            }
        }
    }

    public function run()
    {
        $ch = curl_init();
        $curl_opt_array = array(
            CURLOPT_USERAGENT => USER_AGENT,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => SYSTEM_URL."/".$this->session_id."/".$this->post_url,
            CURLOPT_TIMEOUT => TIMEOUT,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $this->raw_fields,
            CURLOPT_REFERER => SYSTEM_URL."/".$this->session_id."/".$this->post_url
        );
        curl_setopt_array($ch, $curl_opt_array);
        while (true) {
            echo "[线程".$this->getThreadId()."]".time()."\n";
            $cs_return = curl_exec($ch);
            if ($cs_return === false) {
                echo("Curl请求失败: ". curl_error($ch)."\n");
                continue;
            }
            $cs_return_array =  explode("\n", $cs_return);
            $cs_return_array = preg_grep("/<script language='javascript'>alert\(/", $cs_return_array);
            list($key, $value) = each($cs_return_array);
            echo iconv("gbk", "utf-8", $value). "\n";
            sleep($this->sleep_time);
        }
    }
}
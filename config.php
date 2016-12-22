<?php
/**
 * Created by PhpStorm.
 * User: Zhensheng
 * Date: 2016/6/22
 * Time: 19:06
 */
namespace CCDGUT_ClassSelector;

define("SYSTEM_URLS", [
    "11" => "http://10.20.208.11:8088/",
    "12" => "http://10.20.208.12/",
    "xk" => "http://xk.ccdgut.edu.cn/"
]); // 教务系统地址
define("TIMEOUT", 10); // 模拟请求超时时间
define("USER_AGENT", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36"); // User agent请求头
define("LOGIN_THREAD_COUNT", 10);
define("CLASS_SELECT_THREAD_COUNT", 30);

require __DIR__ . "/includes/Common.class.php";
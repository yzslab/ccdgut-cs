<?php
/**
 * Created by PhpStorm.
 * User: Zhensheng
 * Date: 2016/6/22
 * Time: 19:00
 */
require __DIR__ . "/config.php";
require __DIR__ . "/includes/login.class.php";

$login_obj = new login($_SERVER["argv"][1], $_SERVER["argv"][2], $_SERVER["argv"][3], $_SERVER["argv"][4]);
$login_obj->start_logion(1);
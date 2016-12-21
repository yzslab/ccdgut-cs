<?php
/**
 * Created by PhpStorm.
 * User: Zhensheng
 * Date: 2016/6/22
 * Time: 19:00
 */
require __DIR__ . "/config.php";
require __DIR__ . "/includes/login.class.php";
require __DIR__ . "/includes/thread_holder.php";

$objs = [];
foreach (SYSTEM_URLS as $system_url) {
    echo "Start: " . $system_url . "\n";
    $objs[] = new thread_holder(new login($system_url, $_SERVER["argv"][1], $_SERVER["argv"][2], $_SERVER["argv"][3], $_SERVER["argv"][4]), "start_logion", LOGIN_THREAD_COUNT);
}
foreach ($objs as $obj)
    $obj->start();
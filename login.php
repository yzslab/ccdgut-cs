<?php
/**
 * Created by PhpStorm.
 * User: Zhensheng
 * Date: 2016/6/22
 * Time: 19:00
 */
namespace CCDGUT_ClassSelector;

require __DIR__ . "/config.php";
require __DIR__ . "/includes/Login.class.php";
require __DIR__ . "/includes/ThreadHolder.class.php";

$objs = [];
foreach (SYSTEM_URLS as $system_url)
    $objs[] = new ThreadHolder(new Login($system_url, $_SERVER["argv"][1], $_SERVER["argv"][2], $_SERVER["argv"][3], $_SERVER["argv"][4]), "start_logion", LOGIN_THREAD_COUNT);

foreach ($objs as $obj)
    $obj->start();
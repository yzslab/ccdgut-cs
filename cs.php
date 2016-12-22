<?php
/**
 * Created by PhpStorm.
 * User: Zhensheng
 * Date: 2016/6/22
 * Time: 22:53
 */
namespace CCDGUT_ClassSelector;

require __DIR__."/config.php";
require __DIR__ . "/includes/ClassSelector.class.php";
require __DIR__ . "/includes/ThreadHolder.class.php";

if ($_SERVER["argc"] <= 3)
    exit("使用方法: ".$_SERVER["argv"][0]. " 用户名 接收表单URL SYSTEM_URL_NAME|SYSTEM_URL,POST_FIELDS_FILE_PATH1[,POST_FIELDS_FILE_PATH2 ...] [...]\n");
$serverName = $argv[1];
$urls = SYSTEM_URLS;
$usernName = $argv[1];
$postUrl = $argv[2];


$groups = [];
for ($i = 3; $i < $argc; ++$i)
    $groups[] = $argv[$i];
$objs = [];

// For each system url and its post fields
foreach ($groups as $group_member) {
    $group_array = explode(",", $group_member);
    $system_url = $group_array[0];
    if (($session_id = file_get_contents(Common::get_session_file_name($system_url, $usernName))) === false)
        exit("Session ID文件(".Common::get_session_file_name($system_url, $usernName).")不存在\n");
    $count = count($group_array);

    // For each system url's post fields
    for ($i = 1; $i < $count; ++$i) {
        $file_path = $group_array[$i];
        if (file_exists($file_path))
            $objs[] = new ThreadHolder(new ClassSelector($system_url, $usernName, $session_id, $postUrl, file_get_contents($file_path)), "start_cs", CLASS_SELECT_THREAD_COUNT);
        else
            echo "File " . $file_path . " not found.\n";
    }

}

foreach ($objs as $obj)
    $obj->start();
<?php
/**
 * Created by PhpStorm.
 * User: Zhensheng
 * Date: 2016/6/22
 * Time: 22:53
 */
require __DIR__."/config.php";
require __DIR__ . "/includes/cs.class.php";

if ($_SERVER["argc"] <= 4)
    exit("使用方法: ".$_SERVER["argv"][0]. " SYSTEM_URL_NAME|SYSTEM_URL 用户名 接收表单URL 表单内容一 [表单内容二 ...]\n");
$serverName = $argv[1];
$urls = SYSTEM_URLS;
if (isset($urls[$serverName]))
    $system_url = $urls[$serverName];
else
    $system_url = $serverName;
$usernName = $argv[2];
$postUrl = $argv[3];
$form_data_file_paths = [];
for ($i = 4; $i < $argc; ++$i)
    $form_data_file_paths[] = $argv[$i];
if (($session_id = file_get_contents(ccdgut_cs\common::get_session_file_name($system_url, $usernName))) === false) {
    exit("Session ID文件(".ccdgut_cs\common::get_session_file_name($system_url, $usernName).")不存在\n");
}
$objs = [];
foreach ($form_data_file_paths as $file_path) {
    $objs[] = new cs($system_url, $usernName, $session_id, $postUrl, file_get_contents($file_path));
}

foreach ($objs as $obj)
    $obj->start_cs(CLASS_SELECT_THREAD_COUNT);
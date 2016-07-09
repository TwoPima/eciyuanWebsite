<?php

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
//定义网站根目录
define('WEB_PATH',dirname(__FILE__));
define('BIND_MODULE', 'Mobile'); // 绑定Mobile模块到当前入口文件
define('BIND_CONTROLLER','Index'); // 绑定Index控制器到当前入口文件
define('APP_PATH','./App/');
define('APP_DEBUG',true); // 开启调试模式
//目录安全
define('BUILD_DIR_SECURE',true);
define('DIR_SECURE_FILENAME', 'index.html');
define('DIR_SECURE_CONTENT', 'deney Access!');
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

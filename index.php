<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
//定义网站根目录
define('WEB_PATH',dirname(__FILE__));
// 定义应用目录
define('APP_PATH','./App/');
define('APP_DEBUG',true); // 开启调试模式
//目录安全
define('BUILD_DIR_SECURE',true);
define('DIR_SECURE_FILENAME', 'index.html');
define('DIR_SECURE_CONTENT', 'deney Access!');
//微信配置
//include("weixin.php");
define("TOKEN", "weixin");//开发模式的TOKEN码
define('DEBUG', true);//是否开启调试模式
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

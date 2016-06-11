<?php
/**
*
* 版权所有：恰维网络<qwadmin.qiawei.com>
* 作    者：寒川<hanchuan@qiawei.com>
* 日    期：2015-09-15
* 版    本：1.0.0
* 功能说明：配置文件。
*
**/
return array(
	'URL' =>'http://www.eciyuan.net', //网站根URL
	//数据库链接配置
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => 'localhost', // 服务器地址
	'DB_NAME'   => 'a0516214320', // 数据库名
	'DB_USER'   => 'a0516214320', // 用户名
	'DB_PWD'    => '4a45fdd1', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => 'eci_', // 数据库表前缀
	'DB_CHARSET'=>  'utf8',      // 数据库编码默认采用utf8
    'SHOW_PAGE_TRACE' =>false, // 显示页面Trace信息
	//备份配置
	'DB_PATH_NAME'=> 'db',        //备份目录名称,主要是为了创建备份目录
	'DB_PATH'     => './db/',     //数据库备份路径必须以 / 结尾；
	'DB_PART'     => '20971520',  //该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M
	'DB_COMPRESS' => '1',         //压缩备份文件需要PHP环境支持gzopen,gzwrite函数        0:不压缩 1:启用压缩
	'DB_LEVEL'    => '9',         //压缩级别   1:普通   4:一般   9:最高
	
   'TMPL_PARSE_STRING'=>array(
			'__HOMECSS__'=>__ROOT__.'/Public/Home/Css',
			'__HOMEJS__'=>__ROOT__.'/Public/Home/Js',
			'__HOMEIMAGES__'=>__ROOT__.'/Public/Home/Images',
	),
	//phpmail邮件发送
		'THINK_EMAIL' => array(
				'SMTP_HOST'   => 'smtp.126.com', //SMTP服务器
				'SMTP_PORT'   => '465', //SMTP服务器端口
				'SMTP_USER'   => 'lianaiyao99@126.com', //SMTP服务器用户名
				'SMTP_PASS'   => '85777126', //SMTP服务器密码
				'FROM_EMAIL'  => 'lianaiyao99@126.com', //发件人EMAIL
				'FROM_NAME'   => '宁夏亿次元科技有限公司', //发件人名称
				'REPLY_EMAIL' => '857773627@qq.com', //回复EMAIL（留空则为发件人EMAIL）
				'REPLY_NAME'  => '宁夏亿次元科技有限公司', //回复名称（留空则为发件人名称）
		),
    // 设置禁止访问的模块列表
    'MODULE_DENY_LIST'      =>  array('Common','Runtime','Public'),
);
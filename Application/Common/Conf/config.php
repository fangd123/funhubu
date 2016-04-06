<?php
return array(
	//'配置项'=>'配置值'
	'MODULE_ALLOW_LIST' => array('Home','Admin','Weixin','Addon'),

	//数据库配置

	'db_type'  => 'mysql',
	'db_user'  => 'root',
	'db_pwd'   => '',
	'db_host'  => 'localhost',
	'db_port'  => '3306',
	'db_name'  => 'test',

	'URL_MODEL' => 2,
	
	'DEFAULT_THEME' => 'default',	//默认模板
	
	'TOKEN'	=> 'weixin',
	'APPID' => 'appid',
	'APPSECRET' => 'appsecret',
	
	//外部cdn文件
	'JQUERY_JS' => 'http://cdn.bootcss.com/jquery/2.1.4/jquery.min.js',
	'BOOTSTRAP_CSS' => 'http://cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css',
	'BOOTSTRAP_JS' => 'http://cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js',
	'BOOTSTRAP_THEME_CSS' => 'http://cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css',
	'JQUERYMOBILE_CSS' => 'http://cdn.bootcss.com/jquery-mobile/1.4.5/jquery.mobile.min.css',
	'JQUERYMOBILE_JS' => 'http://cdn.bootcss.com/jquery-mobile/1.4.5/jquery.mobile.min.js',
	'JQUERYMOBILE_STRUCTURE_CSS' => 'http://cdn.bootcss.com/jquery-mobile/1.4.5/jquery.mobile.structure.min.css',
	'JQUERYMOBILE_THEME_CSS' => 'http://cdn.bootcss.com/jquery-mobile/1.4.5/jquery.mobile.theme.min.css',

	'JWC_JS' => '/Public/js/jwc.js',
	'JWC_CSS' => '/Public/css/jwc.css',
	'LOADING_IMG' => '/Public/img/loading.gif',
	'BOARD_JS' => '/Public/js/board.js',
	'BOARD_CSS' => '/Public/css/board.css',
	
	'DATE_CACHE_TIME' => '1800',
	'DATA_CACHE_PREFIX' => 'wx',
	'DATA_CACHE_TYPE' => 'memcached',
	'MEMCACHED_SERVER' => array(array('127.0.0.1', '11211', 0)),
);

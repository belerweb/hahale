<?php
/*
$File:		Wordpress Mu 所使用的config.php文件 $
$Date:		2012-09-01 00:18:21  $
$Author: enj0y $
*/

// 重命名为 "config.php"，在管理后台就不必再重复配置了。适用于WordPress MU环境
$Enj0yWordPressossConfig = array(
	'key' => '', // API ID
	'secret' => '', // 密钥
	'bucket' => '', // oss Bucket
	'cname' => 'storage.aliyun.com', // cname
	'wp-uploads' => true, // 使用OSS
	'hidealiyunossUploadTab' => false, // 隐藏上传标签
	'expires' => 315360000, // 设置缓存过期时间
	'wpstore' => 0, //不开启WP本地存储（开启将不便于备份）
	);
?>
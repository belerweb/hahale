<?php
/*
Plugin Name: aliyun oss for WordPress
Plugin URI: http://bbs.aliyun.com/read.php?tid=112953
Description: 使用本插件您可以在blogging时上传图片附件到您的OSS储存空间，享受阿里云的第三方储存服务的快感.&nbsp;&nbsp;&nbsp;使用教程:&nbsp;1.点击左下角“启用”，&nbsp;&nbsp;&nbsp;2.进入<a href="options-general.php?page=oss4wp/wordpress-oss/class-plugin.php">设置页</a>配置oss api id和密钥，bucket等信息，&nbsp;3.<a href="post-new.php">发表文章</a>，Enj0y it!
Author: Enj0y
Author URI: mailto:hackes@outlook.com
Version: 1.5

使用过程中遇到问题请到http://bbs.aliyun.com/read.php?tid=112953
本次发布开源协议(GNU GENERAL PUBLIC LICENSE 2.0):

Copyright (C) 2012 enj0y

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA


------------------
改进记录
------------------

上传到相应日期的文件夹			实现于v1.1版

Cache-Control控制头未实现BUG	修改于v1.1版

Cache-Control控制到具体时间		改进于v1.2版

Cache-Control可选预设时间		改进于v1.2版

自动判断bucket的可读性，若非
公众可读在插件设置页面设置
bucket时此bucket将被禁用		改进于v1.2版

UI美化，后台加入更新自动提示
功能							改进于v1.2版

错误，提示完善					改进于v1.2版

新增wpstore选项，解决WP本地
保留储存无法自定义的问题		改进于v1.3版

缩略图404的BUG					修复于v1.4版

媒体中心BUG						修复于v1.4版

Smush.it!压缩功能				实现于v1.4版	(大陆服务器的博客使用将异常缓慢，建议若非必要选择禁用)

OSS同步删除功能					实现于v1.5版

在oss4wp创建Bucket				实现于v1.5版

中文文件名BUG					修复于v1.5版

去除Smush.it!Gif无用选项		去除于v1.5版


*.特别鸣谢：Joe Tan (本插件的前身是amazon s3 for wordpress)。为了尊重原作者，
保留部分代码（包含amazon s3 class lib）和原作者使用说明及开源协议。


Copyright (C) 2008 Joe Tan

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA


Project Page:
http://code.google.com/p/wordpress-s3/

Changlog:
http://code.google.com/p/wordpress-s3/wiki/ChangeLog

$Revision: 89650 $
$Date: 2009-01-27 02:01:33 +0000 (Tue, 27 Jan 2009) $
$Author: joetan $
*/
if (class_exists('Enj0yWordPressossPlugin')) return;

// oss lib requires php5
if (strpos($_SERVER['REQUEST_URI'], '/wp-admin/') >= 0) { // just load in admin
	$ver = get_bloginfo('version');
    if (version_compare(phpversion(), '5.0', '>=') && version_compare($ver, '2.1', '>=')) {
        require_once(dirname(__FILE__).'/wordpress-oss/class-plugin.php');
        $Enj0yWordPressossPlugin = new Enj0yWordPressossPlugin();
	} elseif (ereg('wordpress-mu-', $ver)) {
        require_once(dirname(__FILE__).'/wordpress-oss/class-plugin.php');
        $Enj0yWordPressossPlugin = new Enj0yWordPressossPlugin();
    } else {
        class Enj0yWordPressossError {
        function Enj0yWordPressossError() {add_action('admin_menu', array(&$this, 'addhooks'));}
        function addhooks() {add_options_page('Aliyun oss', 'Aliyun oss', 10, __FILE__, array(&$this, 'admin'));}
        function admin(){include(dirname(__FILE__).'/wordpress-oss/admin-version-error.html');}
        }
        $error = new Enj0yWordPressossError();
    }
} else {
    require_once(dirname(__FILE__).'/wordpress-oss/class-plugin-public.php');
    $Enj0yWordPressossPlugin = new Enj0yWordPressossPluginPublic();
}
?>
=== Plugin Name ===
Contributors: enj0y
Tags: oss4wp, cloud storage,uploads, aliyun, oss, s3, mirror
Donate link: https://me.alipay.com/lovlov
Requires at least: 2.3
<<<<<<< .mine
Tested up to: 3.4.2
Stable tag: 1.5
=======
Tested up to: 3.4.1
Stable tag: 1.4
>>>>>>> .r595911
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

上传您博客的图片到阿里云的开放储存服务，享受阿里云带给您的高速稳定的云中漫步快感。

== Description ==

这是一个Wordpress（下称WP）插件，您可以使用其方便地让你的WP博客中的图片、附件托管在阿里云OSS服务器上，享受强大的阿里云的高速、稳定、快捷的云端档案服务。

阿里云OSS（the Open Storage Service of Aliyun）是廉价、高效的开放储存服务，您在使用OSS过后只需支付您所消耗的费用。
欲查看阿里云OSS最新收费标准请访问 [阿里云OSS官方网站](http://www.aliyun.com/product?type=oss&from=wordpress-oss#price). 

一旦您安装并正确配置了本插件，您在安装之后发布的文章的所有图片等文件将自动上传到阿里云OSS您的Bucket（包含各种size的图片和缩略图）。


== Installation ==

1.上传 `oss4wp` 文件夹（确保里面可以直接看到 `wordpress-oss.php`，而不是文件夹里面还有一个 `oss4wp` ）到 `/wp-content/plugins/` ，
2.在Wordpress “插件” 菜单里启用本插件
3.在Wordpress “选项” 里找到 "oss for wordpress" 并正确配置。

*若已装过1.1及之前版本的插件，请手工卸载并安装新版插件。

## Documentation
安装教程 [阿里云论坛OSS4WP安装教程](http://bbs.aliyun.com/read.php?tid=112953&from=wordpress-oss). 



名称：阿里云OSS Wordpress插件
<<<<<<< .mine
版本：1.5
=======
版本：1.4
>>>>>>> .r595911
作者：Enj0y 
特别鸣谢：joetan && Han Lin Yap

== Changelog ==
1.5
OSS同步删除功能(WP上删除图片可以在OSS上同步删除),
在oss4wp创建Bucket(新用户不必去OSS控制台创建了),
中文文件名BUG(为更好地使用本插件，建议博主选择中文版WP),
去除Smush.it!Gif无用选项。
测试与WordPress 3.4.2的兼容性。

1.4
修复1.2版因自定义路径导致缩略图404的BUG（同时路径修改为只能分月存放），修复媒体中心BUG。
加入可选的Smush.it!压缩功能，为OSS节省流量。

1.3
修正WP储存文件为一个功能选项，用户可以自主选择在上传到OSS的同时是否要在WP博客上保留上传的文件。
因阿里云官网更新，api ID和key的地址已更换，已于本版修改。

1.2
Cache-control可控制到具体时间，也可以方便地选择预设的时间。
自动判断bucket的可读性，若非公众可读在插件设置页面设置bucket时此bucket将被禁用（已加入缓存保护，防止重复提交设置浪费不必要的请求）。
UI美化，后台加入更新自动提示功能。
错误提示完善。

1.1
完善Cname域指向功能，个性化你的OSS。教程：http://bbs.aliyun.com/read.php?tid=118601
完善上传“路径”，按日期自动归档，便于管理
修复HTTP头的Cache-control不能成功设置的BUG
SDK仍基于20120719的版未更新，因为SDK20120817更新是解决生成GET sign时设置expires的异常，本插件上传的文件GET时未用到expires。

== Upgrade Notice ==

请参照== Changelog ==


== Screenshots ==

请见使用教程帖中截图


== Frequently Asked Questions ==

Q：阿里云OSS免费么？如果不免费是怎么收费的？
A：阿里云OSS是收费服务，但有免费额度，详情请见http://www.aliyun.com/product/oss#price

Q：这是阿里云官方的插件么？
A：不是的。我只是一位PHPer，喜欢阿里云OSS服务于是做了这个插件。欢迎其他PHPer加入oss4wp的开发更新工作

Q：本插件免费么？如果我不会用怎么办？
A：本插件基于GPL2.0协议开源发布，完全免费的。如果您使用过程中遇到任何问题请去阿里云论坛发帖，我会尽我所能给您答复
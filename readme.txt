=== Bosima WeChat Page Sharing ===
Contributors: bossma
Donate link: http://blog.bossma.cn/
Tags: wechat, sharing, 微信, 分享
Requires at least: 4.0
Tested up to: 4.8
Stable tag: 0.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

你可以控制Wordpress页面的微信分享内容，包括Url、标题、图片和内容。

== Description ==

本插件通过集成微信提供的JS-SDK，从而实现自定义网页分享的内容，包括Url、标题、图片和内容。

1、本着简单的原则，分享的内容将全部从Wordpress原生的数据结构中提取，尽量不让用户再填写。
2、目前仅实现了文章的分享给朋友和分享到朋友圈，后续会增加其它页面的分享功能。
3、对于文章分享：标题为文章标题、图片为文章内容中的第一张图片，内容为文章摘要。

== Installation ==

1、使用此插件需要首先注册一个微信公众号，访问地址：https://mp.weixin.qq.com；
2、在“微信公众平台”-“安全中心”-“IP白名单”中填写您网站所在服务器的外网出口IP；
3、在“微信公众平台”-“公众号设置”-“功能设置”-“JS接口安全域名”中填写您网站的域名；
4、在您的Wordpress管理后台安装本插件或者通过上传文件夹到Wordpress插件目录的方式安装本插件；
5、启用插件；
6、在“Wordpress管理后台”-“设置”-“微信分享设置”中填写您微信公众号的AppID和AppSecrect（在“微信公众平台”-“基本配置”中）。

安装完毕。

== Frequently Asked Questions ==

= 如何获取网站所在服务器的外网出口IP？ =

1、直接询问您的主机服务商；
2、在“Wordpress管理后台”-“设置”-“微信分享设置”中查看。

= 微信公众号可以是个人账号吗？ =

可以。

= 这个插件和缓存插件冲突吗？ =

分享用的签名数据每次都通过javascript动态请求获得，和页面缓存没有冲突。

== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 0.2.0 =
* 第一个版本，仅支持文章分享给朋友和分享到朋友圈。

= 0.2.1 =
* 增加显示服务器出口IP功能，方便在微信“安全中心”-“IP白名单”中进行配置。


== Upgrade Notice ==

= 0.2.0 =
* 第一个版本，仅支持文章分享给朋友和分享到朋友圈。

= 0.2.1 =
* 增加显示服务器出口IP功能，方便在微信“安全中心”-“IP白名单”中进行配置。


== Dependency ==

= WeChat JS-SDK =
https://res.wx.qq.com/open/js/jweixin-1.2.0.js
This plugin depends on the WeChat JS-SDK, which is a remote JS file and provided by WeChat.
It provides initialization method of WeChat services, and provide some functions, including share to friends, share to circle of friends.
The JS-SDK is a service.

== ip.chinaz.com ==
http://ip.chinaz.com/getip.aspx
This plugin depends on the ip.chinaz.com, which can return the exit IP of the wordpress server.
The exit IP needs to be added to the WeChat IP whitelist.
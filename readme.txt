=== Bosima WeChat Page Sharing ===
Contributors: bossma
Donate link: http://blog.bossma.cn/
Tags: wechat, sharing, 微信, 分享
Requires at least: 4.0
Tested up to: 4.9.2
Stable tag: 0.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

你可以控制Wordpress页面的微信分享内容，包括Url、标题、图片和内容。
由于我的失误，<strong>从0.2.0版本升级到其它版本后需重新配置AppId和AppSecrect</strong>。

== Description ==

本插件通过集成微信提供的JS-SDK，从而实现自定义网页分享的内容，包括Url、标题、图片和内容。

1、本着简单的原则，分享的内容将全部从Wordpress原生的数据结构中提取，尽量不让用户再填写。
2、目前已实现首页、分类页、文章页、标签页、搜索页、存档页以及单独页面分享给朋友和分享到朋友圈。
3、对于文章分享：标题为文章标题、图片为文章内容中的第一张图片，描述为文章摘要。
4、对于单独页面分享：标题为页面标题+站点名称、图片为页面内容中的第一张图片。
5、对于其它页面分享：标题为页面标题+站点名称，图片为页面中第一张长和宽都大于100像素的图片。
6、对于文章之外的页面分享：描述首先从html的name=description的meta标签获取，如果没有则使用本插件自动生成的一句话。

== Installation ==

1、在您的Wordpress管理后台安装本插件或者通过上传文件夹到Wordpress插件目录的方式安装本插件；
2、启用插件；
3、注册一个微信公众号，访问地址：https://mp.weixin.qq.com；
4、在“Wordpress管理后台”-“设置”-“微信分享设置”中获取服务器出口IP（如果未出现，多刷新几次），填写到“微信公众平台”-“安全中心”-“IP白名单”中；
5、在“微信公众平台”-“公众号设置”-“功能设置”-“JS接口安全域名”中填写您网站的域名；
6、在“Wordpress管理后台”-“设置”-“微信分享设置”中填写您微信公众号的AppID和AppSecrect（在“微信公众平台”-“基本配置”中）。

安装完毕。

<strong>特别说明：</strong>
由于0.2.0版本将微信的配置参数保存到了插件目录下（当然也不会被外部访问到），导致控制台升级后配置会丢失。
从0.2.1版本后配置参数保存位置进行了修改，防止升级导致丢失。
因此0.2.0版本升级后请重新设置微信的AppId和AppSecrect。

== Frequently Asked Questions ==

= 微信IP白名单中需要的IP是域名解析到的IP吗？ =

这两个IP可能是同一个，也可能不是。
微信IP白名单中的IP是网站所在服务器的外网出口IP，也就是服务器访问外部网络资源时暴漏的IP。

= 如获取获取微信IP白名单中需要的IP？ =

1、直接询问您的主机服务商服务器的外网出口IP；
2、在“Wordpress管理后台”-“设置”-“微信分享设置”中查看。

= 微信公众号可以是个人账号吗？ =

现在不可以，需要是认证过的企业账号。
如果你是很久之前注册的，有可能具备分享接口权限，请登陆微信公众号平台查看。

= 这个插件和缓存插件冲突吗？ =

没有冲突，对于启用缓存插件的WordPress，本插件将自动使用Ajax的方式实现页面分享。

== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 0.3.0 = 
* 解决了Url中含有中文时签名无效的问题;
* 增加了首页、分类页、标签页、搜索页、存档页以及单独页面的分享。

= 0.2.2 = 
* 修改Exit IP为Outbound IP;
* 注释签名包中调试用的url和原始签名字符串，防止信息泄露。


= 0.2.1 =
* 增加显示服务器出口IP功能，方便在微信“安全中心”-“IP白名单”中进行配置。
* 更改微信配置参数保存位置，防止控制台升级后丢失；但从0.2.0升级后仍会丢失，需重新配置。

= 0.2.0 =
* 第一个版本，仅支持文章分享给朋友和分享到朋友圈。


== Upgrade Notice ==

= 0.3.0 = 
* 解决了Url中含有中文时签名无效的问题;
* 增加了首页、分类页、标签页、搜索页、存档页以及单独页面的分享。

= 0.2.2 = 
* 修改Exit IP为Outbound IP;
* 注释签名包中调试用的url和原始签名字符串，防止信息泄露。

= 0.2.1 =
* 增加显示服务器出口IP功能，方便在微信“安全中心”-“IP白名单”中进行配置。
* 更改微信配置参数保存位置，防止控制台升级后丢失；但从0.2.0升级后仍会丢失，需重新配置。

= 0.2.0 =
* 第一个版本，仅支持文章分享给朋友和分享到朋友圈。


== Dependency ==

= WeChat JS-SDK =
https://res.wx.qq.com/open/js/jweixin-1.2.0.js
This plugin depends on the WeChat JS-SDK, which is a remote JS file and provided by WeChat.
It provides initialization method of WeChat services, and provide some functions, including share to friends, share to circle of friends.
The JS-SDK is a service.

= ip.chinaz.com =
http://ip.chinaz.com/getip.aspx
This plugin depends on the ip.chinaz.com, which can return the exit IP of the user server.
The exit IP needs to be added to the WeChat IP whitelist.
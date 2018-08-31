=== Bosima WeChat Page Sharing ===
Contributors: bossma
Donate link: http://blog.bossma.cn/
Tags: wechat, sharing, 微信, 分享
Requires at least: 4.4
Tested up to: 4.9.8
Stable tag: 0.3.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

您可以控制Wordpress页面的分享内容，包括Url、标题、图片和描述，支持分享到微信朋友、微信朋友圈、QQ和QQ空间。
<strong>请注意，0.2.x版本升级后需重新配置AppId和AppSecrect</strong>。

== Description ==

本插件通过集成微信提供的JS-SDK，从而实现自定义网页分享的内容，包括Url、标题、图片和内容。

1、本插件简单灵活，分享的内容可全部从页面提取，也可在后台自定义。
2、目前已实现首页、分类页、文章页、标签页、搜索页、存档页以及单独页面的分享。
3、分享目标支持微信朋友、微信朋友圈、QQ、QQ空间。
4、分享标题、图片、描述等内容首先从自定义模板提取，如果未设置模板则从页面提取，如果页面上提取不到则根据当前页面内容自动生成。
5、从页面提取时，各个分享内容的来源：标题来源于html->head->title，描述从html->head->meta[name='description']提取，图片取正文中的第一张图。
6、分享url目前只能是当前url，未提供自定义设置功能。

== Installation ==

1、在您的Wordpress管理后台安装本插件或者通过上传文件夹到Wordpress插件目录的方式安装本插件；
2、启用插件；
3、注册一个微信公众号，访问地址：https://mp.weixin.qq.com；
4、在“Wordpress管理后台”-“设置”-“微信分享设置”中获取服务器出口IP，填写到“微信公众平台”-“安全中心”-“IP白名单”中；
5、在“微信公众平台”-“公众号设置”-“功能设置”-“JS接口安全域名”中填写您网站的域名；
6、在“Wordpress管理后台”-“设置”-“微信分享设置”中填写您微信公众号的AppID和AppSecrect（从“微信公众平台”-“基本配置”中获取）。

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

1. 使用说明、微信设置和模板设置

2、页面上生成的微信分享代码

3、分享到微信的效果

== Changelog ==

= 0.3.2 = 
* 增加插件截图
* 在插件后台增加使用说明
* 修正错误的作者url

= 0.3.1 = 
* 增加了分享标题和描述的模板设置功能，让分享更个性化；
* 增加了首页分享使用网站Icon的选择；
* 增加了单篇文章分享使用特色图片的选择；
* 更换了从正文提取图片url时调用的方法为当前类的静态方法;
* 更换了通过ajax方式生成签名时检查url的函数为esc_url_raw；
* 为后台管理界面编写了多语言文件；
* 更新了判断https协议的方法；
* 更换了获取服务器出口IP的方法。

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

= 0.3.2 = 
* 增加插件截图，方便用户安装前了解插件功能
* 在插件后台增加使用说明，方便用户尽快完成配置

= 0.3.1 = 
* 增加了分享标题和描述的模板设置功能，让分享更个性化；
* 增加了首页分享使用网站Icon的选择；
* 增加了单篇文章分享使用特色图片的选择；
* 解决了从正文提取图片url时调用了主题方法的bug;
* 解决了搜索页分享时获取当前url不正确的bug；
* 后台管理界面增加了对多语言的支持；
* 增强了对https协议的支持；
* 提升了获取服务器出口IP的速度。

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

= myip.fireflysoft.net =
http://myip.fireflysoft.net
This plugin depends on the myip.fireflysoft.net, which can return the outbound IP of the user server.
The exit IP needs to be added to the WeChat IP whitelist.
<?php
/*
Plugin Name: Bosima WeChat Page Sharing
Plugin URI: https://github.com/bosima/wechat-page-sharing
Description: 你可以控制Wordpress页面的微信分享内容，包括Url、标题、图片和内容。由于我的失误，<strong>从0.2.0版本升级到其他版本后需重新配置AppId和AppSecrect</strong>。
Version: 0.3.2
Author: 波斯码(bossma)
Author URI: http://blog.bossma.cn
*/

/*  Copyright 2017  波斯码(bossma) (email : bossma@yeah.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ABSPATH')) {
    exit();
}

if (defined('WP_INSTALLING') && WP_INSTALLING) {
    return;
}

define('BOSIMA_WECHAT_PAGE_SHARING__PLUGIN_DIR', plugin_dir_path(__FILE__));

if (is_admin()) {
    require_once BOSIMA_WECHAT_PAGE_SHARING__PLUGIN_DIR.'sharing-admin.php';
    add_action('init', array('Bosima_WeChat_Page_Sharing_Admin', 'init'));
}

require_once BOSIMA_WECHAT_PAGE_SHARING__PLUGIN_DIR.'sharing-page.php';
add_action('wp_footer', array('Bosima_WeChat_Page_Sharing_Page', 'render_js'));

Bosima_WeChat_Page_Sharing_Page::init();
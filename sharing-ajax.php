<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once 'wechat.php';

class Bosima_WeChat_Page_Sharing_Ajax
{
    private static $initiated = false;

    public static function init()
    {
        if (!self::$initiated) {
            self::$initiated = true;

            add_action('wp_ajax_getWeChatJsSign', array('Bosima_WeChat_Page_Sharing_Ajax', 'getWeChatJsSign'));
            add_action('wp_ajax_nopriv_getWeChatJsSign', array('Bosima_WeChat_Page_Sharing_Ajax', 'getWeChatJsSign'));
        }
    }

    /**
     * Ajax获取微信签名信息.
     */
    public static function getWeChatJsSign()
    {
        $cur_url = sanitize_text_field(urldecode($_GET['cur_url']));
        $weChat = Bosima_WeChat::getInstance();
        $signPackage = $weChat->getSign($cur_url);

        wp_send_json($signPackage);
    }
}

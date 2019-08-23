<?php
if (!defined('ABSPATH')) {
    exit();
}

/**
 * 微信访问配置.
 */
class Bosima_WeChat_AccessConfig
{
    public $appId = '';
    public $appSecrect = '';
    public $accessToken = '';
    public $accessTokenDeadline = '';
    public $jsTicket = '';
    public $jsTicketDeadline = '';
}
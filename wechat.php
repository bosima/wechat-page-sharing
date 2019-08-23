<?php

if (!defined('ABSPATH')) {
    exit();
}

require_once 'sharing-config.php';

// 将数据放到wp-content目录下，免得控制台升级造成配置数据被删除
// WeChat数据目录
define('BOSIMA_WECHAT_DATAPATH', WP_CONTENT_DIR.'/bps-wechat/config/');

/**
 * 微信帮助类.
 */
class Bosima_WeChat
{
    // 微信访问配置
    private $config = null;

    //静态变量保存全局实例
    private static $instance = null;

    /**
     * 私有构造函数，防止外界实例化对象
     */
    private function __construct()
    {
        if (!is_dir(BOSIMA_WECHAT_DATAPATH)) {
            mkdir(BOSIMA_WECHAT_DATAPATH, 0700, $recursive = true);
        }

        $this->initConfig();
    }

    /**
     * 单例入口.
     */
    public static function getInstance()
    {
        if (is_null(self::$instance) || !isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 获取微信配置.
     */
    public function getWeChatConfig()
    {
        return $this->config;
    }

    /**
     * 设置微信AppId.
     */
    public function updateStaticConfig($appId, $appSecrect)
    {
        $this->config->appId = $appId;
        $this->config->appSecrect = $appSecrect;
        $this->updateConfigFile();
    }

      /**
     * 设置微信AppId.
     */
    public function updateAllConfig()
    {
        $this->updateConfigFile();
    }

    /**
     * 获取JS用签名等相关信息.
     */
    public function getSign($url)
    {
        $jsapiTicket = $this->getJsTicket();
        $timestamp = time();
        $nonceStr = $this->getNonceString();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $signStr = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($signStr);

        $signPackage = array(
        'appId' => $this->config->appId,
        'nonceStr' => $nonceStr,
        'timestamp' => $timestamp,
        //'url' => $url,
        'signature' => $signature,
        //'rawString' => $signStr,
        );

        return $signPackage;
    }
    
    /**
     * 获取服务器出口IP.
     */
    public static function getOutIp(){
        $response = wp_remote_get("http://myip.fireflysoft.net/");
        return wp_remote_retrieve_body($response);
    }

    /**
     * 从缓存获取配置信息.
     */
    private function initConfig()
    {
        $this->config = new Bosima_WeChat_AccessConfig();

        if (empty($this->config->appId)) {
            $this->initConfigFormFile();
        }
    }

    /**
     * 从配置文件初始化配置信息.
     */
    private function initConfigFormFile()
    {
        $configPath = BOSIMA_WECHAT_DATAPATH.'data.php';
        if (file_exists($configPath)) {
            $configContent = trim(substr(file_get_contents($configPath), 15));

            if ($configContent) {
                $this->config = json_decode($configContent);
            }

            if ($this->config->appId && $this->config->appSecrect) {
                $this->checkAccessToken();
                $this->checkJsTicket();
            }
        }
    }

    /**
     * 更新微信配置文件.
     */
    private function updateConfigFile()
    {
        // 对于写文件进行加锁
        $lock = BOSIMA_WECHAT_DATAPATH.'config.lck';

        try {
            while (true) {
                if (file_exists($lock)) {
                    usleep(100);
                } else {
                    touch($lock);
                    $configPath = BOSIMA_WECHAT_DATAPATH.'data.php';
                    $content = json_encode($this->config);
                    file_put_contents($configPath, '<?php exit();?>'.$content);

                    if (file_exists($lock)) {
                        unlink($lock);
                    }

                    break;
                }
            }
        } catch (Exception $ex) {
            if (file_exists($lock)) {
                unlink($lock);
            }
        }
    }

    /**
     * 获取随机数.
     */
    private function getNonceString($length = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        $len = strlen($chars);
        for ($i = 0; $i < $length; ++$i) {
            $str .= substr($chars, mt_rand(0, $len - 1), 1);
        }

        return $str;
    }

    /**
     * 获取微信接口AccessToken.
     */
    private function getAccessToken()
    {
        $this->checkAccessToken();

        return $this->config->accessToken;
    }

    /**
     * 获取微信接口JS Ticket.
     */
    private function getJsTicket()
    {
        $this->checkJsTicket();

        return $this->config->jsTicket;
    }

    /**
     * 检查Access Token，如果无效，则获取.
     */
    private function checkAccessToken()
    {
        if (empty($this->config->accessToken) || $this->config->accessTokenDeadline < time()) {
            $this->setAccessTokenFromRemote();
        }
    }

    /**
     * 检查JS Ticket，如果无效，则获取.
     */
    private function checkJsTicket()
    {
        if (empty($this->config->jsTicket) || $this->config->jsTicketDeadline < time()) {
            $this->setJSTicketFromRemote();
        }
    }

    /**
     * 通过远程接口设置Access Token.
     */
    private function setAccessTokenFromRemote()
    {
        // 从远程接口获取
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&'.
            'appid='.$this->config->appId.
            '&secret='.$this->config->appSecrect;

        $result = $this->doCurl($url);

        if ($result) {
            $j = json_decode($result);

            if (isset($j->access_token)) {
                // 设置到缓存
                $this->config->accessToken = $j->access_token;
                $this->config->accessTokenDeadline = time() + 7000;

                // 保存文件
                $this->updateConfigFile();
            }
        }
    }

    /**
     * 通过远程接口设置JS Ticket.
     */
    private function setJSTicketFromRemote()
    {
        $tmpToken = $this->getAccessToken();

        // 从远程接口获取
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$tmpToken.'&type=jsapi';

        $result = $this->doCurl($url);

        if ($result) {
            $j = json_decode($result);
            if (isset($j->errcode) && $j->errcode == '0') {
                // 设置到缓存
                $this->config->jsTicket = $j->ticket;
                $this->config->jsTicketDeadline = time() + 7000;

                // 保存文件
                $this->updateConfigFile();
            }
        }
    }

    /**
     * curl get请求
     *
     * @param url 请求地址
     *
     * @return 请求响应数据
     */
    private function doCurl($url)
    {
        $response = wp_remote_get($url);
        $body = wp_remote_retrieve_body($response);

        return $body;
    }
}
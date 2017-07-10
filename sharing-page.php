<?php
if (!defined('ABSPATH')) {
    exit();
}

require_once 'wechat.php';

class Bosima_WeChat_Page_Sharing_Page
{
    private static $initiated = false;

	public static function init() {
		if( !self::$initiated ) {
			self::$initiated = true;

			if( defined('WP_CACHE') && WP_CACHE ) {
                require_once(BOSIMA_WECHAT_PAGE_SHARING__PLUGIN_DIR . 'sharing-ajax.php');
			    Bosima_WeChat_Page_Sharing_Ajax::init();
            }
		}
	}

    /**
    * 获取文章中第一张图
    */
    private static function get_post_img_url($post_content) {   
        $first_img = '';   
        
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);

        if(isset($matches[1][0])&&!empty($matches[1][0])){
            $first_img = $matches[1][0];  
        } else{
            $first_img =  plugins_url('',__FILE__) .'/images/random/article'.rand(1,5).'.jpg';
        }
        
        return $first_img;   
    } 

    public static function render_config_js(){
         global $post, $posts;
?>
        wx.config({
            debug: false,
            appId: weChatJsSign.appId,
            timestamp: weChatJsSign.timestamp,
            nonceStr: weChatJsSign.nonceStr,
            signature: weChatJsSign.signature,
            jsApiList: [
            'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'onMenuShareQZone',
            ]
        });

        wx.ready(function () {
            wx.onMenuShareTimeline({
                title: '<?php echo $post->post_title ?>',
                link: '<?php the_permalink() ?>',
                imgUrl: '<?php echo self::get_post_img_url($post->post_content) ?>'
            });

            wx.onMenuShareAppMessage({
                title: '<?php echo $post->post_title ?>',
                link: '<?php the_permalink() ?>',
                imgUrl: '<?php echo self::get_post_img_url($post->post_content) ?>',
                desc:'<?php echo wp_strip_all_tags( $post->post_excerpt); ?>'
            });
        });
<?php
    }

    /**
    * 输出微信Js
    */
    public static function render_js(){
        if(is_single()){

            wp_enqueue_script('weixin-js','https://res.wx.qq.com/open/js/jweixin-1.2.0.js');

            $cur_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
            $cur_url = "$cur_protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    ?>
            <script>
            jQuery(document).ready(function(){
                
            <?php
            if( defined('WP_CACHE') && WP_CACHE ) {
                $ajax_url = admin_url('admin-ajax.php');
            ?>
                var weChatJsSign = '';
                var data={
                    action:'getWeChatJsSign',
                    cur_url:'<?php echo urlencode($cur_url);?>'
                }
                jQuery.get('<?php echo $ajax_url;?>', data, function(response) {
                    weChatJsSign = response;
                    if(weChatJsSign){
                        <?php Bosima_WeChat_Page_Sharing_Page::render_config_js(); ?>
                    }
                },'json');
            <?php
            }else{
                $weChat = Bosima_WeChat::getInstance();
                $signPackage = $weChat->getSign($cur_url);
                echo 'var weChatJsSign = '.json_encode($signPackage).';';
?>
                if(weChatJsSign){
                   <?php Bosima_WeChat_Page_Sharing_Page::render_config_js(); ?>
                }
<?php
            }
            ?>

                
            });
            </script>
<?php
        }
    }
}
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

    /**
     * 判断字符串结尾
     */
    public static function endWith($haystack, $needle) {   
        $length = strlen($needle);  
        if($length == 0)
        {    
            return true;  
        }  
        return (substr($haystack, -$length) === $needle);
    }

    /**
     * 输出微信分享配置JS
     */
    public static function render_config_js(){
        global $post, $posts;
        global $wp;

        $share_title=wp_title('|',false,'right').get_bloginfo('name');
        $share_link= home_url(add_query_arg(array(),$wp->request));
        $share_img_url='';
        $share_desc='';
        $default_share_desc='';
        $default_img_url = plugins_url('',__FILE__) .'/images/random/article'.rand(1,5).'.jpg';

        if(is_single()){
            $share_title=$post->post_title;
            $share_img_url=get_post_img_url($post->post_content);
            $share_desc=wp_strip_all_tags( $post->post_excerpt);
        }elseif (is_page()) {
            $share_img_url=get_post_img_url($post->post_content);
            $default_share_desc="想知道【".$post->post_title."】的更多内容吗？现在就点我吧。";
        }elseif(is_home() || is_front_page()){
            $share_title.=' | '.get_bloginfo('description');
        }elseif(is_category()){
            $share_desc=wp_strip_all_tags(category_description());
        }elseif(is_tag()){
            // todo:让用户自定义
            // todo:过滤引号
            $default_share_desc="想了解更多关于【".single_term_title( '', false )."】的内容吗？现在就点我吧。";
        }elseif ( is_archive() ) {
            $m = get_query_var( 'm' );
            $year = get_query_var( 'year' );
            $monthnum = get_query_var( 'monthnum' );
            $day      = get_query_var( 'day' );
            $archive_date='';
            if(! empty( $m ) ){
                $my_year  = substr( $m, 0, 4 );
                $my_month = $wp_locale->get_month( substr( $m, 4, 2 ) );
                $my_day   = intval( substr( $m, 6, 2 ) );
                $archive_date    = $my_year .'年'. ( $my_month ?  $my_month.'月' : '' ) . ( $my_day ?  $my_day.'日' : '' );
            }

            if(! empty( $year )){
                $archive_date = $year.'年';
                if ( ! empty( $monthnum ) ) {
                    $archive_date .= $monthnum.'月';
                }

                if ( ! empty( $day ) ) {
                    $archive_date .=  zeroise( $day, 2 ).'日';
                }
            }

            $default_share_desc="想知道".$archive_date."都有什么吗？现在就点我吧。";
        }elseif ( is_search() ) {
            $search   = get_query_var( 's' );
            $my_search = strip_tags( $search );
            $default_share_desc="想知道更多关于【".$my_search."】的内容吗？点我开始搜索吧。";
        }
?>
        var default_img_url='<?php echo $default_img_url ?>';
        var share_img_url='<?php echo $share_img_url ?>';
        var share_desc="<?php echo $share_desc ?>";
        var default_share_desc="<?php echo $default_share_desc ?>";

        if(share_img_url==''){
            var imgs = document.getElementsByTagName("img");
            for(var i=0;i<imgs.length;i++){
                if(imgs[i].width>=100&&imgs[i].height>=100){
                    share_img_url=imgs[i].src;
                    break;
                }
            }

            if(share_img_url==''){
                share_img_url=default_img_url;
            }
        }

        if(share_desc==''){
            var meta = document.getElementsByTagName('meta');
            for(i in meta){
                if(typeof meta[i].name!="undefined" && meta[i].name.toLowerCase()=="description"){
                    share_desc = meta[i].content;
                    break;
                }
            }
        }

        if(share_desc==''){
            share_desc=default_share_desc;
        }

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
                title: '<?php echo $share_title ?>',
                link: '<?php echo $share_link ?>',
                imgUrl: share_img_url
            });

            wx.onMenuShareAppMessage({
                title: '<?php echo $share_title ?>',
                desc: share_desc,
                link: '<?php echo $share_link ?>',
                imgUrl: share_img_url
            });
        });
<?php
    }

    /**
    * 输出微信Js
    */
    public static function render_js(){
            wp_enqueue_script('weixin-js','https://res.wx.qq.com/open/js/jweixin-1.2.0.js');
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
                    cur_url:encodeURIComponent(location.href.split('#')[0])
                }
                jQuery.get('<?php echo $ajax_url;?>', data, function(response) {
                    weChatJsSign = response;
                    if(weChatJsSign){
                        <?php Bosima_WeChat_Page_Sharing_Page::render_config_js(); ?>
                    }
                },'json');
            <?php
            }else{
                $cur_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
                $cur_url = "$cur_protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
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
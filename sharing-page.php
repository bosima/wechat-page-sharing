<?php
if (!defined('ABSPATH')) {
    exit();
}

require_once 'wechat.php';

class Bosima_WeChat_Page_Sharing_Page
{
    private static $initiated = false;

    public static function init()
    {
        if (!self::$initiated) {
            self::$initiated = true;
            require_once(BOSIMA_WECHAT_PAGE_SHARING__PLUGIN_DIR . 'sharing-ajax.php');
            Bosima_WeChat_Page_Sharing_Ajax::init();
        }
    }

    /**
     * 获取文章的缩略图
     */
    private static function get_post_img_url($post_content, $config)
    {
        $post_thumbnail = '';
        if (empty($config->sharing_img_option)) {
            $config->sharing_img_option = "0";
        }

        if ($config->sharing_img_option == "1" && has_post_thumbnail()) {
            $post_thumbnail = get_the_post_thumbnail_url(null, 'thumbnail');
        }

        if ($config->sharing_img_option == "0" || empty($post_thumbnail)) {
            $output = preg_match_all('/<img[^>]+?src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);
            if (isset($matches[1][0]) && !empty($matches[1][0])) {
                $post_thumbnail = $matches[1][0];
            }
        }

        if (empty($post_thumbnail)) {
            $post_thumbnail = plugins_url('', __FILE__) . '/images/random/article' . rand(1, 5) . '.jpg';
        }

        return $post_thumbnail;
    }

    /**
     * 判断字符串结尾
     */
    public static function endWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    /**
     * 获取当前页面Url
     */
    private static function curPageURL()
    {
        $current_url = 'http://';
        $current_port = '';
        if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
            $current_url = 'https://';
            if ($_SERVER['SERVER_PORT'] != '443') {
                $current_port = $_SERVER['SERVER_PORT'];
            }
        }

        if ($current_url == 'http://' && $_SERVER['SERVER_PORT'] != '80') {
            $current_port = $_SERVER['SERVER_PORT'];
        }

        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        $current_url .= $host. $_SERVER['REQUEST_URI'];

        return $current_url;
    }

    /**
     * 输出微信分享配置JS
     */
    public static function render_config_js($config)
    {
        global $post, $posts;
        global $wp;

        $share_title = '';
        $default_share_title = get_bloginfo('name');
        $share_img_url = '';
        $share_desc = '';
        $default_share_desc = "想知道【" . get_bloginfo('name') . "】的更多内容吗？现在就点我吧。";
        $default_img_url = plugins_url('', __FILE__) . '/images/random/article' . rand(1, 5) . '.jpg';
        $use_icon_as_share_img = '0';
        $site_lang = explode('_', get_locale())[0];

        if (is_single()) {
            $default_share_title = $post->post_title;
            $share_img_url = Bosima_WeChat_Page_Sharing_Page::get_post_img_url($post->post_content, $config);
            $default_share_desc = wp_strip_all_tags($post->post_excerpt);
            $description_template_vars = array(
                '{blog_name}' => get_bloginfo('name'),
                '{blog_sub_name}' => get_bloginfo('description'),
                '{post_title}' => $post->post_title,
                '{post_excerpt}' => $default_share_desc
            );
            if (!empty($config->sharing_single_description)) {
                $share_desc = strtr($config->sharing_single_description, $description_template_vars);
            }
            $title_template_vars = array(
                '{blog_name}' => get_bloginfo('name'),
                '{blog_sub_name}' => get_bloginfo('description'),
                '{post_title}' => $post->post_title,
            );
            if (!empty($config->sharing_single_title)) {
                $share_title = strtr($config->sharing_single_title, $title_template_vars);
            }
        } elseif (is_page()) {
            $template_vars = array(
                '{blog_name}' => get_bloginfo('name'),
                '{blog_sub_name}' => get_bloginfo('description'),
                '{post_title}' => $post->post_title
            );
            $share_img_url = Bosima_WeChat_Page_Sharing_Page::get_post_img_url($post->post_content, $config);
            $default_share_title = $post->post_title . ' | ' . get_bloginfo('name');
            $default_share_desc = "想知道【" . $post->post_title . "】的更多内容吗？现在就点我吧。";
            if (!empty($config->sharing_page_description)) {
                $share_desc = strtr($config->sharing_page_description, $template_vars);
            }
            if (!empty($config->sharing_page_title)) {
                $share_title = strtr($config->sharing_page_title, $template_vars);
            }
        } elseif (is_home() || is_front_page()) {
            $template_vars = array(
                '{blog_name}' => get_bloginfo('name'),
                '{blog_sub_name}' => get_bloginfo('description')
            );
            $default_share_desc = "这是【" . get_bloginfo('name') . "】的官方网站，请注意收藏。";

            $default_share_title = get_bloginfo('name') . ' | ' . get_bloginfo('description');
            if (!empty($config->sharing_home_title)) {
                $share_title = strtr($config->sharing_home_title, $template_vars);
            }
            if (!empty($config->sharing_home_description)) {
                $share_desc = strtr($config->sharing_home_description, $template_vars);
            }
            if (!empty($config->sharing_home_img_useicon)) {
                $use_icon_as_share_img = $config->sharing_home_img_useicon;
            }
        } elseif (is_category()) {
            $cate_name = single_cat_title('', false);
            $template_vars = array(
                '{blog_name}' => get_bloginfo('name'),
                '{blog_sub_name}' => get_bloginfo('description'),
                '{category_name}' => $cate_name
            );
            $default_share_desc = "想知道【" . $cate_name . "】的更多内容吗？现在就点我吧。";
            $default_share_title = $cate_name . ' | ' . get_bloginfo('name');
            $category_description = wp_strip_all_tags(category_description());
            if (!empty($category_description)) {
                $share_desc = $category_description;
            }

            if (!empty($config->sharing_category_title)) {
                $share_title = strtr($config->sharing_category_title, $template_vars);
            }
        } elseif (is_tag()) {
            $tag_con = single_term_title('', false);
            $tag_con = htmlspecialchars($tag_con);
            $template_vars = array(
                '{blog_name}' => get_bloginfo('name'),
                '{blog_sub_name}' => get_bloginfo('description'),
                '{tag_con}' => $tag_con
            );
            $default_share_title = $tag_con . ' | ' . get_bloginfo('name');
            $default_share_desc = "想知道【" . $tag_con . "】的更多内容吗？现在就点我吧。";
            if (!empty($config->sharing_tag_description)) {
                $share_desc = strtr($config->sharing_tag_description, $template_vars);
            }
            if (!empty($config->sharing_tag_title)) {
                $share_title = strtr($config->sharing_tag_title, $template_vars);
            }
        } elseif (is_archive()) {
            $m = get_query_var('m');
            $year = get_query_var('year');
            $monthnum = get_query_var('monthnum');
            $day = get_query_var('day');
            $archive_date = '';

            if (!empty($m)) {
                $my_year = substr($m, 0, 4);
                $my_month = $wp_locale->get_month(substr($m, 4, 2));
                $my_day = intval(substr($m, 6, 2));
                if ($site_lang == "zh") {
                    $archive_date = $my_year . '年' . ($my_month ? $my_month . '月' : '') . ($my_day ? $my_day . '日' : '');
                } else {
                    $archive_date = $my_year . '/' . ($my_month ? $my_month . '/' : '') . ($my_day ? $my_day : '');
                }
            }

            if (!empty($year)) {
                $archive_date = $year . '年';
                if ($site_lang != "zh") {
                    $archive_date = $year;
                }
                if (!empty($monthnum)) {
                    if ($site_lang != "zh") {
                        $archive_date .= '/' . $monthnum;
                    } else {
                        $archive_date .= $monthnum . '月';
                    }
                }

                if (!empty($day)) {
                    if ($site_lang != "zh") {
                        $archive_date .= '/' . zeroise($day, 2);
                    } else {
                        $archive_date .= zeroise($day, 2) . '日';
                    }
                }
            }

            $template_vars = array(
                '{blog_name}' => get_bloginfo('name'),
                '{blog_sub_name}' => get_bloginfo('description'),
                '{archive_date}' => $archive_date
            );
            $default_share_title = $archive_date . ' | ' . get_bloginfo('name');
            $default_share_desc = "想知道" . $archive_date . "都有什么吗？现在就点我吧。";
            if (!empty($config->sharing_archive_description)) {
                $share_desc = strtr($config->sharing_archive_description, $template_vars);
            }
            if (!empty($config->sharing_archive_title)) {
                $share_title = strtr($config->sharing_archive_title, $template_vars);
            }
        } elseif (is_search()) {
            $search = get_query_var('s');
            $search_con = strip_tags($search);
            $template_vars = array(
                '{blog_name}' => get_bloginfo('name'),
                '{blog_sub_name}' => get_bloginfo('description'),
                '{search_con}' => $search_con
            );
            $default_share_title = $search_con . ' | ' . get_bloginfo('name');
            $default_share_desc = "想知道【" . $search_con . "】的更多内容吗？点我开始搜索吧。";
            if (!empty($config->sharing_search_description)) {
                $share_desc = strtr($config->sharing_search_description, $template_vars);
            }
            if (!empty($config->sharing_search_title)) {
                $share_title = strtr($config->sharing_search_title, $template_vars);
            }
        }

        // todo:support custom post type
        // https://codex.wordpress.org/Post_Types#Custom_Post_Types
        
        ?>
        var use_icon_as_share_img = "<?php echo $use_icon_as_share_img ?>";
        var default_img_url='<?php echo $default_img_url ?>';
        var share_img_url='<?php echo $share_img_url ?>';
        var share_desc="<?php echo str_replace("\"","\\\"",$share_desc) ?>";
        var default_share_desc="<?php echo str_replace("\"","\\\"",$default_share_desc) ?>";
        var share_title="<?php echo str_replace("\"","\\\"",$share_title) ?>";
        var default_share_title="<?php echo str_replace("\"","\\\"",$default_share_title) ?>";
        var current_url=location.href.split('#')[0];

        if(share_title==""){
            share_title=jQuery("head title").text();
        }
        if(share_title==""){
            share_title=default_share_title;
        }

        if(use_icon_as_share_img=="1"){
            var icons = jQuery("head link[rel='shortcut icon']");
            if(icons!=null&&icons.length>0){
                share_img_url=jQuery(icons[0]).attr('href');
            }
        }

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
                'updateAppMessageShareData',
                'updateTimelineShareData',
                'onMenuShareWeibo'
            ]
        });

        wx.ready(function () {
            wx.updateTimelineShareData({
                title: share_title,
                link: current_url,
                imgUrl: share_img_url
            });

            wx.updateAppMessageShareData({
                title: share_title,
                desc: share_desc,
                link: current_url,
                imgUrl: share_img_url
            });

            wx.onMenuShareWeibo({
                title: share_title,
                desc: share_desc,
                link: current_url,
                imgUrl: share_img_url
            });
        });
<?php

}

/**
 * 输出引用必须的Js
 */
public static function render_ref_js(){
    wp_enqueue_script('jquery');
    wp_enqueue_script('weixin-js', 'https://res.wx.qq.com/open/js/jweixin-1.6.0.js');
}

/**
 * 输出微信Js
 */
public static function render_js()
{
    $weChat = Bosima_WeChat::getInstance();
    $config = $weChat->getWeChatConfig();
    ?>
            <script>
            jQuery(document).ready(function(){
            <?php
                $ajax_url = admin_url('admin-ajax.php');
            ?>
                var weChatJsSign = '';
                var data={
                    action:'getWeChatJsSign', 
                    cur_url:encodeURIComponent(location.href.split('#')[0])
                }
                jQuery.get('<?php echo $ajax_url; ?>', data, function(response) {
                    weChatJsSign = response;
                    if(weChatJsSign){
                        <?php Bosima_WeChat_Page_Sharing_Page::render_config_js($config); ?>
                    }
                },'json');
            });
            </script>
<?php

}
}
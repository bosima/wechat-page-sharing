<?php

class Bosima_WeChat_Page_Sharing_Page
{
    /**
    * 获取文章中第一张图
    */
    private static function get_post_img_url($post_content) {   
        $first_img = '';   
        
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);

        if(isset($matches[1][0])&&!empty($matches[1][0])){
            $first_img = $matches[1][0];  
        } else{
            $first_img =  get_stylesheet_directory_uri() .'/images/random/article'.rand(1,5).'.jpg';
        }
        
        return $first_img;   
    } 

    public static function render_js(){
        if(is_single()){
            global $post, $posts;  
            $curprotocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
            $cururl = "$curprotocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            echo "<script src=\"https://res.wx.qq.com/open/js/jweixin-1.2.0.js\"></script>";
    ?>
            <script src="<?php echo plugins_url('',__FILE__) ?>/sharing-js.php?refurl=<?php echo urlencode($cururl); ?>"></script>
            <script>
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
            </script>
<?php
        }
    }
}
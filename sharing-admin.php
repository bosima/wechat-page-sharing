<?php
if (!defined('ABSPATH')) {
    exit();
}

require_once 'wechat.php';

class Bosima_WeChat_Page_Sharing_Admin
{
    const NONCENAME='bosima-wechat-appconfig-update';

    public static function init()
    {
        add_action('admin_menu', array('Bosima_WeChat_Page_Sharing_Admin', 'admin_menu'));
        load_plugin_textdomain( 'wechat-page-sharing', false, dirname( plugin_basename( __FILE__ ) ) . '/trans' ); 
    }

    public static function admin_menu()
    {
        add_options_page('微信分享插件配置', '微信分享设置', 'manage_options', 'wechat-appid-config', array('Bosima_WeChat_Page_Sharing_Admin', 'display_page'));
    }

    public static function display_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // variables for the field and option names
        $hidden_field_name = 'wechat_submit_hidden';
        $appid_field_name = 'wechat_appid';
        $appsecrect_field_name = 'wechat_appsecrect';
        $sharing_page_title_field_name='sharing_page_title';
        $sharing_page_description_field_name='sharing_page_description';
        $sharing_tag_title_field_name='sharing_tag_title';
        $sharing_tag_description_field_name='sharing_tag_description';
        $sharing_search_title_field_name='sharing_search_title';
        $sharing_search_description_field_name='sharing_search_description';
        $sharing_archive_title_field_name='sharing_archive_title';
        $sharing_archive_description_field_name='sharing_archive_description';
        $sharing_img_option_field_name='sharing_img_option';
        $sharing_home_title_field_name='sharing_home_title';
        $sharing_home_description_field_name='sharing_home_description';
        $sharing_home_img_useicon_field_name='sharing_home_img_useicon';
        $sharing_single_title_field_name='sharing_single_title';
        $sharing_single_description_field_name='sharing_single_description';
        $sharing_category_title_field_name='sharing_category_title';
        
        $weChat = Bosima_WeChat::getInstance();
        $config = $weChat->getWeChatConfig();
  
        // See if the user has posted us some information
        // If they did, this hidden field will be set to 'Y'
        if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {

            check_admin_referer(self::NONCENAME);

            // Read their posted value
            $config->appId = sanitize_text_field($_POST[$appid_field_name]);
            $config->appSecrect = sanitize_text_field($_POST[$appsecrect_field_name]);
            $config->sharing_page_title=sanitize_text_field($_POST[$sharing_page_title_field_name]);
            $config->sharing_page_description=sanitize_text_field($_POST[$sharing_page_description_field_name]);
            $config->sharing_tag_title=sanitize_text_field($_POST[$sharing_tag_title_field_name]);
            $config->sharing_tag_description=sanitize_text_field($_POST[$sharing_tag_description_field_name]);
            $config->sharing_search_title=sanitize_text_field($_POST[$sharing_search_title_field_name]);
            $config->sharing_search_description=sanitize_text_field($_POST[$sharing_search_description_field_name]);
            $config->sharing_archive_title=sanitize_text_field($_POST[$sharing_archive_title_field_name]);
            $config->sharing_archive_description=sanitize_text_field($_POST[$sharing_archive_description_field_name]);
            $config->sharing_img_option=sanitize_text_field($_POST[$sharing_img_option_field_name]);
            $config->sharing_home_img_useicon=sanitize_text_field($_POST[$sharing_home_img_useicon_field_name]);
            $config->sharing_home_title=sanitize_text_field($_POST[$sharing_home_title_field_name]);
            $config->sharing_home_description=sanitize_text_field($_POST[$sharing_home_description_field_name]);
            $config->sharing_single_title=sanitize_text_field($_POST[$sharing_single_title_field_name]);
            $config->sharing_single_description=sanitize_text_field($_POST[$sharing_single_description_field_name]);
            $config->sharing_category_title=sanitize_text_field($_POST[$sharing_category_title_field_name]);
            
            // Save the posted value in the database
            $weChat->updateAllConfig();

            // Put an settings updated message on the screen
?>
            <div class="updated"><p><strong><?php _e('settings saved.', 'wechat-page-sharing'); ?></strong></p></div>
 <?php

        }

        $wechat_appid = esc_attr($config->appId);
        $wechat_appsecrect = esc_attr($config->appSecrect);
        $sharing_page_title = esc_attr($config->sharing_page_title);
        $sharing_page_description = esc_attr($config->sharing_page_description);
        $sharing_tag_title = esc_attr($config->sharing_tag_title);
        $sharing_tag_description = esc_attr($config->sharing_tag_description);
        $sharing_search_title = esc_attr($config->sharing_search_title);
        $sharing_search_description = esc_attr($config->sharing_search_description);
        $sharing_archive_title = esc_attr($config->sharing_archive_title);
        $sharing_archive_description = esc_attr($config->sharing_archive_description);
        $sharing_img_option = esc_attr($config->sharing_img_option);
        $sharing_home_title = esc_attr($config->sharing_home_title);
        $sharing_home_img_useicon = esc_attr($config->sharing_home_img_useicon);
        $sharing_home_description = esc_attr($config->sharing_home_description);
        $sharing_single_description = esc_attr($config->sharing_single_description);
        $sharing_single_title = esc_attr($config->sharing_single_title);
        $sharing_category_title = esc_attr($config->sharing_category_title);

        $host_name = $_SERVER['SERVER_NAME'];
        $out_ip = Bosima_WeChat::getOutIp();

        echo '<div class="wrap">';
        echo '<h2>'.__('WeChat Page Sharing Plugin Settings', 'wechat-page-sharing').'</h2>'; ?>
            <form name="form1" method="post" action="">

            <?php wp_nonce_field(self::NONCENAME) ?>
            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

            <h3><?php _e('Plugin Instructions', 'wechat-page-sharing'); ?> </h3>
            <p><span style="font-size:14px;font-weight:bold">1.</span> <?php _e('Instructions Setp 1 Content', 'wechat-page-sharing'); ?></p>
            <p><span style="font-size:14px;font-weight:bold">2.</span> <?php _e('Instructions Setp 2 Content', 'wechat-page-sharing'); ?><?php echo $out_ip ?></p>
            <p><span style="font-size:14px;font-weight:bold">3.</span> <?php _e('Instructions Setp 3 Content', 'wechat-page-sharing'); ?><?php echo $host_name ?></p>
            <p><span style="font-size:14px;font-weight:bold">4.</span> <?php _e('Instructions Setp 4 Content', 'wechat-page-sharing'); ?></p>
            <p><span style="font-size:14px;font-weight:bold">5.</span> <?php _e('Instructions Setp 5 Content', 'wechat-page-sharing'); ?></p>
            <p><?php _e('Have Fun', 'wechat-page-sharing'); ?></p>
            <hr />
            
             <h3><?php _e('WeChat Settings', 'wechat-page-sharing'); ?> </h3>
            <p><?php _e('WeChat AppId:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $appid_field_name; ?>" value="<?php echo $wechat_appid; ?>" size="30">
            </p>
            <p><?php _e('WeChat AppSecrect:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $appsecrect_field_name; ?>" value="<?php echo $wechat_appsecrect; ?>" size="40">
            </p>
            <!--<p><?php _e('Server Outbound IP:', 'wechat-page-sharing'); ?>
            <?php
            echo $out_ip;
             ?>
            </p>
            -->
            <hr />
            <h3><?php _e('Template Settings', 'wechat-page-sharing'); ?> </h3>
            <?php _e('Sharing Template Memo', 'wechat-page-sharing'); ?> 
            <p><?php _e('Sharing Home Title Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_home_title_field_name; ?>" value="<?php echo $sharing_home_title; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} 
            </p>
            <p><?php _e('Sharing Home Description Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_home_description_field_name; ?>" value="<?php echo $sharing_home_description; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} 
            </p>
            <p><?php _e('Sharing Home Image Use Site Icon:', 'wechat-page-sharing'); ?> 
            <input type="radio" name="<?php echo $sharing_home_img_useicon_field_name; ?>" value="1" <?php if($sharing_home_img_useicon=="1"){ echo "checked='checked'"; } ?>> <?php _e('Use', 'wechat-page-sharing'); ?> 
            <input type="radio" name="<?php echo $sharing_home_img_useicon_field_name; ?>" value="0" <?php if($sharing_home_img_useicon=="0"){ echo "checked='checked'"; } ?>> <?php _e('Not Use', 'wechat-page-sharing'); ?> 
            </p>
            <p><?php _e('Sharing Category Title Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_category_title_field_name; ?>" value="<?php echo $sharing_category_title; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} {category_name}
            </p>
            <p><?php _e('Sharing Single Title Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_single_title_field_name; ?>" value="<?php echo $sharing_single_title; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} {post_title}
            </p>
            <p><?php _e('Sharing Single Description Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_single_description_field_name; ?>" value="<?php echo $sharing_single_description; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} {post_title} {post_excerpt}
            </p>
            <p><?php _e('Sharing Page Title Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_page_title_field_name; ?>" value="<?php echo $sharing_page_title; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} {post_title}
            </p>
            <p><?php _e('Sharing Page Description Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_page_description_field_name; ?>" value="<?php echo $sharing_page_description; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} {post_title}
            </p>
            <p><?php _e('Sharing Tag Title Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_tag_title_field_name; ?>" value="<?php echo $sharing_tag_title; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} {tag_con}
            </p>
            <p><?php _e('Sharing Tag Description Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_tag_description_field_name; ?>" value="<?php echo $sharing_tag_description; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} {tag_con}
            </p>
            <p><?php _e('Sharing Archive Title Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_archive_title_field_name; ?>" value="<?php echo $sharing_archive_title; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} {archive_date}
            </p>
            <p><?php _e('Sharing Archive Description Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_archive_description_field_name; ?>" value="<?php echo $sharing_archive_description; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} {archive_date}
            </p>
            <p><?php _e('Sharing Search Title Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_search_title_field_name; ?>" value="<?php echo $sharing_search_title; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} {search_con}
            </p>
            <p><?php _e('Sharing Search Description Template:', 'wechat-page-sharing'); ?> 
            <input type="text" name="<?php echo $sharing_search_description_field_name; ?>" value="<?php echo $sharing_search_description; ?>" size="75">
            <?php _e('Valid Variable Label:', 'wechat-page-sharing'); ?> {blog_name} {blog_sub_name} {search_con}
            </p>
            <p><?php _e('Sharing Image Option:', 'wechat-page-sharing'); ?> 
            <input type="radio" name="<?php echo $sharing_img_option_field_name; ?>" value="1" <?php if($sharing_img_option=="1"){ echo "checked='checked'"; } ?>> <?php _e('Post Featued Image', 'wechat-page-sharing'); ?> 
            <input type="radio" name="<?php echo $sharing_img_option_field_name; ?>" value="0" <?php if($sharing_img_option=="0"){ echo "checked='checked'"; } ?>> <?php _e('Post First Image', 'wechat-page-sharing'); ?> 
            <?php _e('Sharing Image Option Memo:', 'wechat-page-sharing'); ?>
            </p>
            <hr />

            <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
            </p>
            </form>
        </div>
<?php

    }
}

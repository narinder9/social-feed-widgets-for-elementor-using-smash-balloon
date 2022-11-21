<?php
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

//get data from instafeed plugin
function sfafe_get_feed_plugin_data()
{
    if (!class_exists('SB_Instagram_Blocks')) {
        return;
    }
   
    $accounts = get_option( 'sb_instagram_settings');
   
    $connected_accounts =  SB_Instagram_Connected_Account::get_all_connected_accounts();
    $user_array = [];
    if (isset($connected_accounts ) && !empty($connected_accounts )) {
        foreach ($connected_accounts  as $key => $value) {
         
            $user_array[] = $value['username'];

        }
    }
 $db_settings = sbi_get_database_settings();
    $users_data = !empty($user_array) ? implode(",", $user_array) : "";
    $atts = array('user' => $users_data, 'sortby' => 'none');
    $feed_settings = new SB_Instagram_Settings($atts, $db_settings,false);
     if (empty($connected_accounts ) && current_user_can( 'manage_options' )) {     
        $admin_url = admin_url() . 'admin.php?page=sbi-settings';
        $error_message='<div  style="display: block;" class="sfafe-error-message">              
                         <p><b>'. __('No connected account.', 'sfafe').'</b>
                         <p><a href="' . $admin_url . '" target="yes"><b>'. __('Please connect your instagram account from plugin settings', 'sfafe').'</b></a></p>
                         </div>';
        return $error_message;
    } 

    $feed_settings->set_feed_type_and_terms();
    $feed_settings->set_transient_name();
    $cache_name = $feed_settings->get_transient_name();
    $config = $feed_settings->get_settings();
    $feed_type = $feed_settings->get_feed_type_and_terms();

    $feeds = new SB_Instagram_Feed($cache_name);
    $feeds->set_cache( $feed_settings->get_cache_time_in_seconds(),$config);
    if ($accounts['sbi_caching_type'] === 'background') {
        $feeds->add_report('background caching used');
        if ($feeds->regular_cache_exists()) {
            $feeds->add_report('setting posts from cache');
            $feeds->set_post_data_from_cache();
        }

        if ($feeds->need_to_start_cron_job()) {
            $feeds->add_report('setting up feed for cron cache');
            $to_cache = array(
                'atts' => $atts,
                'last_requested' => time(),
            );

            $feeds->set_cron_cache($to_cache, $feed_settings->get_cache_time_in_seconds());

            SB_Instagram_Cron_Updater::do_single_feed_cron_update($feed_settings, $to_cache, $atts, false);

            $feeds->set_post_data_from_cache();

        } elseif ($feeds->should_update_last_requested()) {
            $feeds->add_report('updating last requested');
            $to_cache = array(
                'last_requested' => time(),
            );

            $feeds->set_cron_cache($to_cache, $feed_settings->get_cache_time_in_seconds(), $config['backup_cache_enabled']);
        }
    } elseif ($feeds->regular_cache_exists()) {
        $feeds->add_report('page load caching used and regular cache exists');
        $feeds->set_post_data_from_cache();

        if ($feeds->need_posts($config['num']) && $feeds->can_get_more_posts()) {
            while ($feeds->need_posts($config['num']) && $feeds->can_get_more_posts()) {
                $feeds->add_remote_posts($config, $feed_type, $feed_settings->get_connected_accounts_in_feed());
            }
            $feeds->cache_feed_data($feed_settings->get_cache_time_in_seconds(), $config['backup_cache_enabled']);
        }

    } else {
        $feeds->add_report('no feed cache found');

        while ($feeds->need_posts($config['num']) && $feeds->can_get_more_posts()) {
            $feeds->add_remote_posts($config, $feed_type, $feed_settings->get_connected_accounts_in_feed());
        }

        if (!$feeds->should_use_backup()) {
            $feeds->cache_feed_data($feed_settings->get_cache_time_in_seconds(), $config['backup_cache_enabled']);
        }

    }

    if ($feeds->should_use_backup()) {
        $feeds->add_report('trying to use backup');
        $feeds->maybe_set_post_data_from_backup();
        $feeds->maybe_set_header_data_from_backup();
    }

    // header
    if ($feeds->need_header($config, $feed_type)) {
        if ($feeds->should_use_backup() && $config['minnum'] > 0) {
            $feeds->add_report('trying to set header from backup');
            $header_cache_success = $feeds->maybe_set_header_data_from_backup();
        } elseif ($accounts['sbi_caching_type'] === 'background') {
            $feeds->add_report('background header caching used');
            $feeds->set_header_data_from_cache();
        } elseif ($feeds->regular_header_cache_exists()) {
            // set_post_data_from_cache
            $feeds->add_report('page load caching used and regular header cache exists');
            $feeds->set_header_data_from_cache();
        } else {
            $feeds->add_report('no header cache exists');
            $feeds->set_remote_header_data($config, $feed_type, $feed_settings->get_connected_accounts_in_feed());
            $feeds->cache_header_data($feed_settings->get_cache_time_in_seconds(), $config['backup_cache_enabled']);
        }
    } else {
        $feeds->add_report('no header needed');
    }

    if ($config['resizeprocess'] === 'page') {
        $feeds->add_report('resizing images for post set');
        $feed_data = $feeds->get_post_data();
        $feed_data = array_slice($feed_data, 0, $config['num']);

        $feed_set = new SB_Instagram_Post_Set($feed_data, $cache_name);

        $feed_set->maybe_save_update_and_resize_images_for_posts();
    }

    if ($config['disable_js_image_loading'] || $config['imageres'] !== 'auto') {
        global $sb_instagram_posts_manager;
        $feed_data = $feeds->get_post_data();

        if (!$sb_instagram_posts_manager->image_resizing_disabled()) {
            $images_id = array();
            foreach ($feed_data as $id) {
                $images_id[] = SB_Instagram_Parse::get_post_id($id);
            }
            $resized_images = SB_Instagram_Feed::get_resized_images_source_set($images_id, 0, $cache_name);

            $feeds->set_resized_images($resized_images);
        }
    }

    $response = array('post_data' => $feeds->get_post_data(), 'profile_data' => $feeds->get_header_data());

    return $response;

}

function sfafe_user_accounts()
{
   
 $connected_accounts = "";
    if (class_exists('SB_Instagram_Blocks')) {
            $connected_accounts =  SB_Instagram_Connected_Account::get_all_connected_accounts();
    
    }
    $user_array = [];
    if (isset($connected_accounts) && !empty($connected_accounts)) {
        foreach ($connected_accounts as $key => $value) {
            $user_array[$value['username']] = $value['username'];

        }
    }
    return $user_array;
}

function sfafe_recent_dates($element1, $element2)
{
    $datetime1 = strtotime($element1['timestamp']);
    $datetime2 = strtotime($element2['timestamp']);
    return $datetime2 - $datetime1;
}
function sfafe_least_dates($element1, $element2)
{
    $datetime1 = strtotime($element1['timestamp']);
    $datetime2 = strtotime($element2['timestamp']);
    return $datetime1 - $datetime2;
}
/**
** Get  Icon
*/
function sfafe_get_navi_control_icon( $icon) {
if ( false !== strpos( $icon, 'fa-' ) ) {
    return wp_kses('<i class="'. esc_attr($icon ) .'"></i>', [
        'i' => [
            'class' => []
        ]
    ]);
} else {
    return '';
}
}
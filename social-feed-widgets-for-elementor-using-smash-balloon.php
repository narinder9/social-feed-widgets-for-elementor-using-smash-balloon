<?php
/**
 * Plugin Name: Social Feed Widgets For Elementor Using Smash Balloon
 * Description: Social Feed Widget Addon For Elementor create social feed in page and post.
 * Plugin URI:  https://coolplugins.net
 * Version:     1.0.4
 * Author:      Cool Plugins
 * Author URI:  https://coolplugins.net/
 * Text Domain: sfafe
 * Elementor tested up to: 3.8.1
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('SFAFE_VERSION')) {
    return;
}

define('SFAFE_VERSION', '1.0.4');
define('SFAFE_FILE', __FILE__);
define('SFAFE_PATH', plugin_dir_path(SFAFE_FILE));
define('SFAFE_URL', plugin_dir_url(SFAFE_FILE));




/**
 * Class Social_Feed_Addon
 */
final class Social_Feed_Addon
{

    /**
     * Plugin instance.
     *
     * @var Social_Feed_Addon
     * @access private
     */
    private static $instance = null;

    /**
     * Get plugin instance.
     *
     * @return Social_Feed_Addon
     * @static
     */
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Constructor.
     *
     * @access private
     */
    private function __construct()
    {
         register_activation_hook(SFAFE_FILE, array($this, 'sfafe_activate'));    
        //Load the plugin after Elementor (and other plugins) are loaded.
        add_action('plugins_loaded', array($this, 'sfafe_plugins_loaded'));
           

    }

    /**
     * Code you want to run when all other plugins loaded.
     */
    public function sfafe_plugins_loaded()
    {

        // Notice if the Elementor is not active
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', array($this, 'sfafe_fail_to_load'));
            return;
        }
        if (!class_exists('SB_Instagram_Blocks')) {
            add_action('admin_notices', array($this, 'sfafe_feed_fail_to_load'));
        }

        load_plugin_textdomain('sfafe', false, SFAFE_FILE . 'languages');

        // Require the main plugin file
        require_once SFAFE_PATH . '/includes/sfafe-include-widgets.php';
        require_once SFAFE_PATH . '/includes/sfafe-functions.php';
         if( is_admin() ){
             /*** Plugin review notice file */
             require_once SFAFE_PATH . "/admin/feedback/admin-feedback-form.php";

             require_once(SFAFE_PATH. '/admin/sfafe-feedback-notice.php');
             new SFAFE_FeedbackNotice();

             }

    }
    // notice for Smash Balloon Instagram Feed plugin if not active
    public function sfafe_feed_fail_to_load()
    {

        if (current_user_can('activate_plugins')): ?>
			<div class="notice notice-warning is-dismissible">
				<p><?php echo sprintf(__('<a href="%s"  target="_blank" >Smash Balloon Instagram Feed</a>  must be installed and activated for "<strong>Social Feed Widgets For Elementor Using Smash Balloon</strong>" to work'), 'https://wordpress.org/plugins/instagram-feed/
'); ?></p>
			</div>
        <?php endif;

    }

    public function sfafe_fail_to_load()
    {

        if (!is_plugin_active('elementor/elementor.php')): ?>
			<div class="notice notice-warning is-dismissible">
				<p><?php echo sprintf(__('<a href="%s"  target="_blank" >Elementor Page Builder</a>  must be installed and activated for "<strong>Social Feed Widgets For Elementor Using Smash Balloon</strong>" to work'), 'https://wordpress.org/plugins/elementor/'); ?></p>
			</div>
        <?php endif;

    }

    /**
     * Run when activate plugin.
     */
    public static function sfafe_activate()
    {
        update_option("sfafe-v", SFAFE_VERSION);
        update_option("sfafe-type", "FREE");
        update_option("sfafe-installDate", date('Y-m-d h:i:s'));
    }

   
}

function Social_Feed_Addon()
{
    return Social_Feed_Addon::get_instance();
}

$GLOBALS['Social_Feed_Addon'] = Social_Feed_Addon();
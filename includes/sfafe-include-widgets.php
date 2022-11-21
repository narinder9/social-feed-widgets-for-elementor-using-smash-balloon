<?php
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

// Add a custom category for panel widgets

/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
class SFAFE_Widgets
{

    /**
     * Constructor
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function __construct()
    {
        $this->sfafe_add_actions();
    }

    /**
     * Add Actions
     *
     * @since 1.0.0
     *
     * @access private
     */
    private function sfafe_add_actions()
    {
     

        add_action('elementor/widgets/widgets_registered', array($this, 'sfafe_on_widgets_registered'));
        // Add a custom category for panel widgets
        add_action('elementor/elements/categories_registered', array($this, 'sfafe_elementor_widget_categories'));
        add_action('elementor/editor/after_enqueue_styles', [$this, 'sfafe_editor_styles']);
        

    }
  
    public function sfafe_editor_styles()
    {
        wp_enqueue_style('sfafe-editor-styles', SFAFE_URL . 'assets/css/sfafe-editor.css', array());

    }

    public function sfafe_elementor_widget_categories($elements_manager)
    {

        $elements_manager->add_category(
            'sfafe',
            [
                'title' => __('Social Feed Addon', 'sfafe'),
                'icon' => 'fa fa-plug',
            ]
        );

    }

    /**
     * On Widgets Registered
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function sfafe_on_widgets_registered()
    {
        $this->sfafe_widget_includes();
    }

    /**
     * Includes
     *
     * @since 1.0.0
     *
     * @access private
     */
    private function sfafe_widget_includes()
    {
        require_once SFAFE_PATH . 'widgets/sfafe-instagram-widget.php';
    }

}

new SFAFE_Widgets();

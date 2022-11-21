<?php
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Widget_Base;

class SFAFE_Instagram_Widget extends \Elementor\Widget_Base
{

    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        //register script and css files for the plugin

        wp_register_style('sfafe-fancy-css', SFAFE_URL . 'assets/css/jquery.fancybox.css', array(), SFAFE_VERSION, 'all');

        wp_register_style('sfafe-style-css', SFAFE_URL . 'assets/css/sfafe-style.min.css', array(), SFAFE_VERSION, 'all');
        wp_register_style('font-awesome-5-all', ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all.min.css', array(), SFAFE_VERSION, 'all');
        wp_register_script( 'swiper', ELEMENTOR_ASSETS_URL  . 'lib/swiper/swiper.min.js',[ 'elementor-frontend' ],null, true);
        wp_register_script('sfafe-fancy-js', SFAFE_URL . 'assets/js/jquery.fancybox.js', ['elementor-frontend', 'jquery'], null, true);
        wp_register_script('sfafe-script', SFAFE_URL . 'assets/js/sfafe-script.min.js', ['elementor-frontend', 'jquery'], null, true);

    }

    public function get_style_depends()
    {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            return ['sfafe-style-css', 'sfafe-fancy-css'];
        } else {
            return ['sfafe-style-css', 'sfafe-fancy-css', 'font-awesome-5-all'];
        }

    }
    public function get_keywords()
    {
        return ['instagram', 'socialfeed', 'feed', 'social feed addon', 'sfafe', 'instagram gallery', 'instagram photos', 'Instagram widget'];
    }

    public function get_script_depends()
    {
        return ['sfafe-fancy-js', 'sfafe-script','swiper'];
    }
    public function get_name()
    {
        return 'Instagram-Feed-Addon';
    }

    public function get_title()
    {
        return __('Instagram Feed', 'sfafe');
    }

    public function get_icon()
    {
        return 'eicon-instagram-post sfafe-logo-icon';

    }

    public function get_categories()
    {
        return ['sfafe'];
    }

    protected function register_controls()
    {

        $users = sfafe_user_accounts();
        $default = $users;

        $feed = sfafe_get_feed_plugin_data();
        $feed_data = (!empty($feed)) ? $feed : "";
        $data_logo = SFAFE_URL . 'assets/images/insta.png';
        $dp_url = !empty($feed_data) ? $data_logo : "";
        $countfed = !empty($feed_data['post_data']) ? count($feed_data['post_data']) : '';
        $admin_url = admin_url() . 'admin.php?page=sbi-settings';
        $installurl = admin_url() . 'plugin-install.php?tab=plugin-information&plugin=instagram-feed';
        $this->start_controls_section(
            'sfafe_feed_acount',
            [
                'label' => __('Select Feed', 'sfafe-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        if (!class_exists('SB_Instagram_Blocks')) {
            $this->add_control(
                'sfafe_install',
                [
                    'label' => __('Install plugin', 'sfafe-plugin'),
                    'type' => \Elementor\Controls_Manager::RAW_HTML,
                    'show_label' => false,
                    'raw' => '<a href="' . $installurl . '" target="yes">' . __('Click here to install required Plugin', 'sfafe') . '</a>',
                    
                ]
            );

        } else {           

            if (empty($users)) {
                $this->add_control(
                    'sfafe_connect_account',
                    [
                        'label' => __('Connect Account', 'sfafe-plugin'),
                        'type' => \Elementor\Controls_Manager::RAW_HTML,
                        'show_label' => false,
                        'raw' => '<a href="' . $admin_url . '" target="yes">' . __('Please connect your instagram account', 'sfafe') . '</a>',
                        
                    ]
                );
            } else {
             
                $this->add_control(
                    'sfafe_feed_type',
                    [
                        'label' => __('Feed Type', 'sfafe-plugin'),
                        'show_label' => false,
                        'type' => \Elementor\Controls_Manager::RAW_HTML,
                        'raw' => '<span class="sfafe-option-label">' . __('Feed Type', 'sfafe-plugin') . '</span>
                            <select name="sfafe_feed_type" >
                            <option value="user_account">User Account</option>
                            <option value="hashtag" disabled>#Hashtag(Coming SOON..)</option>
                            <option value="tagged" disabled>Tagged(Coming SOON..)</option>
							<option value="mixed" disabled>Mixed(Coming SOON..)</option>
							</select>',
                        'content_classes' => 'sfafe_style_option',

                    ]
                );

                $this->add_control(
                    'sfafe_select_feed',
                    [
                        'label' => __('Select Feed Account', 'sfafe-plugin'),
                        'type' => \Elementor\Controls_Manager::SELECT2,
                        'multiple' => true,
                        'options' => $users,
                        'default' => array(array_shift($default)),
                        'description' => '<a href="' . $admin_url . '" target="yes">Click here to add more accounts</a>',

                    ]
                );
                $this->add_control(
                    'sfafe_load_feed',
                    [
                        'label' => __('Number of feeds to load', 'sfafe-plugin'),
                        'type' => \Elementor\Controls_Manager::NUMBER,
                        'min' => 1,
                        'max' => 1000,
                        'step' => 1,
                        'default' => 50,  
                        'description' => '<span style="color:#93003c;">Update and reload page to this option work</span>',                     

                    ]
                );
            }
        }
        $this->end_controls_section();

        $this->start_controls_section(
            'sfafe_layout_section',
            [
                'label' => __('Feed Layout', 'sfafe-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'sfafe_fedd',
            [
                'label' => __('post', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::HIDDEN,
                'default' => $feed_data,
            ]
        );

        $this->add_control(
            'sfafe_dp_url',
            [
                'label' => __('post', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::HIDDEN,
                'default' => $dp_url,
            ]
        );
        $this->add_control(
            'sfafe_layout',
            [
                'label' => __('Layout', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'grid' => __('Grid', 'sfafe-plugin'),
                    'carousel' => __('Carousel', 'sfafe-plugin'),
                ],
                'default' => 'grid',

            ]
        );
        $this->add_control(
            'sfafe_grid_col',
            [
                'label' => __('Number of column', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '1' => __('1 Column', 'sfafe-plugin'),
                    '2' => __('2 Columns', 'sfafe-plugin'),
                    '3' => __('3 Columns', 'sfafe-plugin'),
                    '4' => __('4 Columns', 'sfafe-plugin'),
                    '5' => __('5 Columns', 'sfafe-plugin'),
                    // '6' => __( '6 Columns', 'sfafe-plugin' ),
                ],
                'default' => 3,
                'selectors' => [
                    '{{WRAPPER}} .sfafe_wraper.sfafe-common.grid .sfafe_gallery ' => 'grid-template-columns:  repeat({{VALUE}}, auto [col-start]) ',
                ],
                'condition' => [
                    'sfafe_layout' => 'grid',
                ],

            ]
        );
        $this->add_control(
            'sfafe_col',
            [
                'label' => __('Number of column', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '1' => __('1 Column', 'sfafe-plugin'),
                    '2' => __('2 Columns', 'sfafe-plugin'),
                    '3' => __('3 Columns', 'sfafe-plugin'),
                    '4' => __('4 Columns', 'sfafe-plugin'),
                    '5' => __('5 Columns', 'sfafe-plugin'),
                    // '6' => __( '6 Columns', 'sfafe-plugin' ),
                ],
                'default' => 3,
                'condition' => [
                    'sfafe_layout' => 'carousel',
                ],

            ]
        );
        		//Horizontal Nav Icon
		$this->add_control(
			'sfafe_control_icon',
			[
				'label' => esc_html__( 'Navigation Icon', 'twea' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'fas fa-chevron-left',
				'options' =>[
					//'fas fa-angle-left' => esc_html__( 'Angle', 'twea' ),
					//'fas fa-angle-double-left' => esc_html__( 'Angle Double', 'twea' ),
					'fas fa-arrow-left' => esc_html__( 'Arrow', 'twea' ),
					'fas fa-arrow-alt-circle-left' => esc_html__( 'Arrow Circle', 'twea' ),
					'far fa-arrow-alt-circle-left' => esc_html__( 'Arrow Circle Alt', 'twea' ),
                    'fas fa-chevron-left' => esc_html__( 'Chevron', 'twea' ),
					/* 'fas fa-long-arrow-alt-left' => esc_html__( 'Long Arrow', 'twea' ),
					
					'fas fa-caret-left' => esc_html__( 'Caret', 'twea' ),
					'fas fa-caret-square-left' => esc_html__( 'Caret Square', 'twea' ),
					'fas fa-hand-point-left' => esc_html__( 'Hand', 'twea' ), */
				],
				'condition'   => [
					'sfafe_layout' => 'carousel',
				]
				// 'separator' => 'before',
			]
		);

    
		//Horizontal Slides Animation Speed
		$this->add_control(
			'sfafe_speed',
			[
				'label' => esc_html__( 'Slide Speed (100 to 10000)', 'twea1' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' =>100,
				'max' =>10000,
				'step' =>100,
				'default' => 1000,
				'condition'   => [
					'sfafe_layout' => 'carousel',
				]
			]
		);

        $this->add_control(
            'sfafe_post',
            [
                'label' => __('Show no. of feeds', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => $countfed,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],

            ]
        );
        $this->add_control(
            'sfafe_sort',
            [
                'label' => __('Sort by', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'recent' => __('Most Recent', 'sfafe-plugin'),
                    'last' => __('Least Recent', 'sfafe-plugin'),
                ],
                'default' => 'recent',

            ]
        );

        $this->add_control(
            'sfafe_autoplay',
            [
                'label' => __('Enable autoplay', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'sfafe-plugin'),
                'label_off' => __('No', 'sfafe-plugin'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'sfafe_layout' => 'carousel',
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'sfafe_general_settings',
            [
                'label' => __('Feed Settings', 'sfafe-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'sfafe_timestamp_hide',
            [
                'label' => __('Show Timestamp', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'sfafe-plugin'),
                'label_off' => __('Hide', 'sfafe-plugin'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'sfafe_like_coment_hide',
            [
                'label' => __('Show Comment Likes', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'sfafe-plugin'),
                'label_off' => __('Hide', 'sfafe-plugin'),
                'description' => __('Only for business profile', 'sfafe-plugin'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'sfafe_caption_hide',
            [
                'label' => __('Show Caption', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'sfafe-plugin'),
                'label_off' => __('Hide', 'sfafe-plugin'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_control(
            'sfafe_caption_length',
            [
                'label' => __('Caption length', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 200,
                'step' => 1,
                'default' => 20,
                'condition' => [
                    'sfafe_caption_hide' => 'yes',
                ],

            ]
        );
        $this->add_control(
            'sfafe_enable_popup',
            [
                'label' => __('Enable popup', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Enable', 'sfafe-plugin'),
                'label_off' => __('Disable', 'sfafe-plugin'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        $this->add_control(
            'sfafe_open_new_tab',
            [
                'label' => __('Open in new Tab', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'sfafe-plugin'),
                'label_off' => __('No', 'sfafe-plugin'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'sfafe_enable_popup!' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'sfafe_enable_pagination',
            [
                'label' => __('Enable Pagination', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Enable', 'sfafe-plugin'),
                'label_off' => __('Disable', 'sfafe-plugin'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'sfafe_layout' => 'carousel',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'sfafe_style',
            [
                'label' => __('Style', 'sfafe-plugin'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'sfafe_image_hading',
            [
                'label' => __('Image', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'sfafe_image_border',
                'label' => __('Border', 'sfafe-plugin'),
                'selector' => '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_child img,
                                {{WRAPPER}} .sfafe_wraper.sfafe-common.carousel .swiper-slide img',
            ]
        );

        $this->add_control(
            'sfafe_border_padding',
            [
                'label' => __('Border Padding', 'sfafe-plugin'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_child img,
                    {{WRAPPER}} .sfafe_wraper.sfafe-common.carousel .swiper-slide img' => 'padding: {{TOP}}{{UNIT}} ;',
                ],
            ]
        );

        $this->add_control(
            'sfafe_border_radius',
            [
                'label' => __('Border Radius', 'sfafe-plugin'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_gallery-item,
                    {{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_gallery-item img,
                    {{WRAPPER}} .sfafe_wraper.sfafe-common .swiper-slide .sfafe-overlay,
                    {{WRAPPER}} .sfafe_wraper.sfafe-common.carousel .swiper-slide,
                    {{WRAPPER}} .sfafe_wraper.sfafe-common.carousel .swiper-slide img' => 'border-radius: {{TOP}}{{UNIT}} ;',
                ],
            ]
        );

        $this->add_control(
            'sfae_img_space',
            [
                'label' => __('Space', 'sfafe-plugin'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_gallery' => 'grid-gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'sfafe_layout' => 'grid',
                ],
            ]
        );

         $this->add_control(
            'sfae_img_size',
            [
                'label' => __('Image Size', 'sfafe-plugin'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 150,
                        'max' => 1000,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
               'description' => '<span style="color:#93003c;">You can made equal size images by this setting</span>', 
                'selectors' => [
                    '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_gallery .sfafe_gallery-item img,
                    {{WRAPPER}} .sfafe_wraper.sfafe-common.carousel .swiper-slide img' => 'height: {{SIZE}}{{UNIT}};',
                ],
               
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'sfafe_box_shadow',
                'label' => __('Box Shadow', 'sfafe-plugin'),
                'selector' => '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_gallery-item,
                                {{WRAPPER}} .sfafe_wraper.sfafe-common.carousel .swiper-slide img',
            ]
        );
                        $this->add_control(
            'sfafe_arrow',
            [
                'label' => __('Arrow', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'   => [
					'sfafe_layout' => 'carousel',
				]
            ]
        );
        $this->add_control(
            'sfafe_arraow_color',
            [
                'label' => __('Color', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sfafe_wraper.sfafe-common  .sfafe-next-btn.swiper-button-next,
                     {{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe-prev-btn.swiper-button-prev' => 'color: {{VALUE}}',
                ],
                'condition'   => [
					'sfafe_layout' => 'carousel',
				]
            ]
        );
        		//Horizontal Slides Animation Speed
		$this->add_control(
			'sfafe_arrow_size',
			[
				'label' => esc_html__( 'Arrow Size', 'twesfafe-plugina1' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				
				],				
				'selectors' => [
					'{{WRAPPER}} .sfafe_wraper.sfafe-common'=> '--arrow-size: {{SIZE}}{{UNIT}};',
				],		
				'condition'   => [
					'sfafe_layout' => 'carousel',
				]
			]
		);
        $this->add_control(
            'sfafe_caption_hading',
            [
                'label' => __('Caption', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'sfafe_link_color',
            [
                'label' => __('Color', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe-caption' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'sfafe_caption_typography',
                'label' => __('Typography', 'sfafe-plugin'),
                'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe-caption',
            ]
        );
        $this->add_control(
            'sfafe_likecoment_hading',
            [
                'label' => __('Likes Comments', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'sfafe_link_bg_color',
            [
                'label' => __('Color', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_child span.sfafe-like-comment span.sfafe-likes-count,
                    {{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_child span.sfafe-like-comment span.sfafe-comment-count' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'sfafe_content_typography',
                'label' => __('Typography', 'sfafe-plugin'),
                'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_child span.sfafe-like-comment span.sfafe-likes-count,
                                {{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_child span.sfafe-like-comment span.sfafe-comment-count',
            ]
        );

        $this->add_control(
            'sfafe_timestapm_hading',
            [
                'label' => __('Time Stamp', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'sfafe_date_bg_color',
            [
                'label' => __('Color', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_child span.sfafe-timestamp' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'sfafe_date_typography',
                'label' => __('Typography', 'sfafe-plugin'),
                'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_child span.sfafe-timestamp',
            ]
        );
        $this->add_control(
            'sfafe_overlay_hading',
            [
                'label' => __('Overlay', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        $this->add_control(
            'sfafe_overlay_bg_color',
            [
                'label' => __('Background color', 'sfafe-plugin'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_parent:hover .sfafe_child span.sfafe-overlay,
                     {{WRAPPER}} .sfafe_wraper.sfafe-common .sfafe_parent:focus .sfafe_child span.sfafe-overlay' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
       
    }

    // for frontend
    protected function render()
    {
        if (!class_exists('SB_Instagram_Blocks')) {
            return;
        }
        $options = get_option( 'sb_instagram_settings');
        $feed = sfafe_get_feed_plugin_data();
        $html = "";
        $connected_accounts =  SB_Instagram_Connected_Account::get_all_connected_accounts();
        if (empty($connected_accounts) && current_user_can( 'manage_options' )) {
            echo $feed;
            
        }
        elseif (empty($connected_accounts)) {
            return;
        } else {
           
            $settings = $this->get_settings_for_display();    
            $get_feed_data= get_option('sfafe_load_feeds');
                          
            if(empty($get_feed_data)){            
            $options[ 'sb_instagram_num' ]=$settings['sfafe_load_feed'];
            $update_feed_load=update_option('sb_instagram_settings', $options);
            update_option('sfafe_load_feeds', $settings['sfafe_load_feed']);           

            }
            if(!empty($get_feed_data) && $get_feed_data!=$settings['sfafe_load_feed']){
            $options[ 'sb_instagram_num' ]=$settings['sfafe_load_feed'];
            $update_feed_load=update_option('sb_instagram_settings', $options);
            update_option('sfafe_load_feeds', $settings['sfafe_load_feed']);          
            }
                 
            //css for dynamic coloumns
            $custom_data = [];
            $inaray = !empty($settings['sfafe_select_feed']) ? $settings['sfafe_select_feed'] : array();
            if(empty($inaray)){
                $html .= '<span class="sfafe-select-account-msg">Please select instagram feed account</span>';
            }
            foreach ($inaray as $feed_key => $feed_value) {

                foreach ($settings['sfafe_fedd']['post_data'] as $post_key => $post_value) {
                    if (in_array($feed_value, $post_value, true)) {
                        $custom_data[] = $post_value;
                    }
                }
            }
            if(isset($settings['sfafe_control_icon'])){
                $control_icon=$settings['sfafe_control_icon'];
                $navi_left_icon=sfafe_get_navi_control_icon($control_icon);
                $right_index=str_replace("left","right",$control_icon);
                $navi_right_icon=sfafe_get_navi_control_icon($right_index);
            }else{
                $navi_left_icon='<i class="fas fa-chevron-left"></i>';
                $navi_right_icon='<i class="fas fa-chevron-right"></i>';
            }
            $sfafe_speed=isset($settings['sfafe_speed'])?$settings['sfafe_speed']:1000;
            $stylecls = ($settings['sfafe_layout'] == "grid") ? 'sfafe_gallery-item ' : 'swiper-slide';
            //  $html.= ($settings['sfafe_layout']=="grid")?'<style>.sfafe_gallery {grid-template-columns:  repeat(' . $settings['sfafe_col'] . ', auto [col-start]);}</style>':"";
            $html .= '<div class="sfafe_wraper sfafe-common ' . $settings['sfafe_layout'] . '">';
            $html .= ($settings['sfafe_layout'] == "grid") ? '<div class="sfafe_gallery " id="sfafe_gallery">' : '<div class="swiper-wrapper" data-column="' . $settings['sfafe_col'] . '" data-autoplay="' . $settings['sfafe_autoplay'] . '" data-speed='.$sfafe_speed.' >';
            $sort = ($settings['sfafe_sort'] == "recent") ? usort($custom_data,'sfafe_recent_dates') : usort($custom_data,'sfafe_least_dates');

            foreach (array_slice($custom_data, 0, $settings['sfafe_post']['size']) as $key => $value) {
                $mediaurl = ($value['media_type'] == "VIDEO") ? $value['thumbnail_url'] : $value['media_url'];
                $caption = isset($value['caption']) ? substr($value['caption'], 0, $settings['sfafe_caption_length']) : '';
                $time_stmp = isset($value['timestamp']) ? substr($value['timestamp'], 0, 10) : '';
                $icon = ($value['media_type'] == "VIDEO") ? '<i class="dashicons dashicons-video-alt3"></i>' : '<i class="dashicons dashicons-format-image"></i>';
                $post_link = isset($value['permalink']) ? $value['permalink'] : '';
                $likes = isset($value['like_count']) ? '<span class="sfafe-likes-count"><i class="far fa-heart"></i> ' . esc_html($value['like_count']) . '</span>' : "";
                $comment = isset($value['comments_count']) ? '<span class="sfafe-comment-count"><i class="far fa-comment"></i> ' . esc_html($value['comments_count']) . '</span>' : "";
                $popup = ($settings['sfafe_enable_popup'] == "yes") ? '<a class="sfafe_fancy" href="' . esc_url($value['media_url']) . '" data-fancybox="gallery" data-caption="' . esc_attr($caption) . '" >' : '<a href="' . esc_url($post_link) . '" target="' . $settings['sfafe_open_new_tab'] . '">';
                $html .= '<div class=" ' . $stylecls . '">' . $popup . '
                         <span class="sfafe_parent">
                            <div class="sfafe_child ">
                                <img src="' . esc_url($mediaurl) . '"  >
                                <span class="sfafe-overlay"></span>';

                $html .= ($settings['sfafe_caption_hide'] == "yes") ? '<span class="sfafe-caption">' . esc_html($caption) . '</span>' : "";
                $html .= ($settings['sfafe_like_coment_hide'] == "yes") ? ' <span class="sfafe-like-comment"> ' . $likes . ' ' . $comment . '</span>' : "";
                $html .= ($settings['sfafe_timestamp_hide'] == "yes") ? ' <span class="sfafe-timestamp"><i class="far fa-clock"></i> ' . esc_html($time_stmp) . '</span>' : "";
                $html .= '</div></span></a>';
                $html .= '</div>';
            }

            $html .= '</div>';
            $html .= ($settings['sfafe_layout'] == "grid") ? "" : '<div class="sfafe-next-btn swiper-button-next">'.$navi_right_icon.'</div><div class="sfafe-prev-btn swiper-button-prev">'.$navi_left_icon.'</div>';
            $html .= ($settings['sfafe_enable_pagination'] == "yes") ? '<div class="sfafe-pagination swiper-pagination"></div>' : "";
            $html .= ' </div>';
        }

        echo $html;
    }

    // for live editor
    protected function content_template()
    {

        if (!class_exists('SB_Instagram_Blocks')) {
            return;
        }

        ?>
        <#
            var html="";
            var custom_data=[],inc=0;
            var inaray=(typeof(settings.sfafe_select_feed) !=="")?settings.sfafe_select_feed:"";
             if(inaray==""){
                html += '<span class="sfafe-select-account-msg">Please select instagram feed account</span>';
            }
            _.each( inaray, function( feed_value, feed_key ) {
            _.each(settings.sfafe_fedd.post_data, function( post_value, post_key ) {
                if(post_value.username==feed_value){
                    custom_data[inc]=post_value;
                    inc++;
                }
            })
            })

            if(settings.sfafe_control_icon!=="undefined"){
                var navi_icon_cls=settings.sfafe_control_icon;
                var navi_left_icon= navi_icon_cls;
                var navi_right_icon=navi_icon_cls.replace("left", "right");
            }else{
                var navi_left_icon='fas fa-chevron-left';
                var navi_right_icon='fas fa-chevron-right';
            }
        var speed=settings.sfafe_speed;
        var stylecls = (settings.sfafe_layout == "grid") ? 'sfafe_gallery-item ' : 'swiper-slide';

        html+= '<div class="sfafe_wraper sfafe-common '+settings.sfafe_layout+'">';
         html+= (settings.sfafe_layout == "grid")?'<div class="sfafe_gallery" id="sfafe_gallery">':'<div class="swiper-wrapper" data-column="'+settings.sfafe_col+'" data-autoplay="'+settings.sfafe_autoplay+'" data-speed="'+speed+'">';
        var sort = (settings.sfafe_sort == "recent") ? custom_data.sort(function(a, b){return b.timestamp - a.timestamp}) : custom_data.sort(function(a, b){return a.timestamp - b.timestamp}).reverse();

        var limitpost=sort.slice(0,settings.sfafe_post.size);

        _.each( limitpost, function( item, index ) {
            var username=item.username;
            var date=item.timestamp;
            var caption=(typeof(item.caption) !=="" &&typeof(item.caption) !=="undefined")?item.caption.slice(0,settings.sfafe_caption_length):"";
            var mediaurl=(item.media_type == "VIDEO") ? item.thumbnail_url : item.media_url;
            var icon = (item.media_type == "VIDEO") ? '<i class="dashicons dashicons-video-alt3"></i>' : '<i class="dashicons dashicons-format-image"></i>';
            var post_link = (typeof(item.permalink) !=="") ? item.permalink : '';
            var likes=(typeof(item.like_count) !=="" &&typeof(item.like_count) !=="undefined")?'<span class="sfafe-likes-count"><i class="far fa-heart"></i> '+item.like_count+'</span>':"";
            var comment = (typeof(item.comments_count) !=="" &&typeof(item.comments_count) !=="undefined") ? '<span class="sfafe-comment-count"><i class="far fa-comment"></i> '+item.comments_count+'</span>' : "";
            var popup = (settings.sfafe_enable_popup == "yes") ? '<a href="'+item.media_url+'" target="blank" data-fancybox="gallery" data-caption="'+caption+'">' : '<a href="'+post_link+'" target="'+settings.sfafe_open_new_tab+'">';

        html+= '<div class="'+stylecls+'">'+popup+'<span class="sfafe_parent"><div class="sfafe_child "><img src="'+mediaurl+'"  class="img-fluid hvrbox-layer_bottom"> <span class="sfafe-overlay"></span>';
        html+= (settings.sfafe_caption_hide=="yes")?'<span class="sfafe-caption">'+caption+'</span>':"";
        html+=(settings.sfafe_like_coment_hide=="yes")?' <span class="sfafe-like-comment"> '+likes+' '+comment+'</span>':"";
        html+= (settings.sfafe_timestamp_hide == "yes") ? ' <span class="sfafe-timestamp"><i class="far fa-clock"></i> '+date.substring(0,10)+' </span>' : "";
        html+= '</div></span></a>';
        html += '</div>';
        });

        html+='</div>';
        html+= (settings.sfafe_layout == "grid") ? "" : '<div class="sfafe-next-btn swiper-button-next"><i class="'+navi_right_icon+'"></i></div><div class="sfafe-prev-btn swiper-button-prev"><i class="'+navi_left_icon+'"></i></div>';
        html+=(settings.sfafe_enable_pagination == "yes") ?'<div class="sfafe-pagination swiper-pagination"></div>':"";
        html+=' </div>';
        print(html);
        #>

        <?php
}



}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new SFAFE_Instagram_Widget());

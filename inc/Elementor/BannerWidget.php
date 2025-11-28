<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Class TagWidget
 * Simple tag/label widget with color and text-size controls
 */
class BannerWidget extends Widget_Base {

    public function get_name() {
        return 'raf-banner-widget';
    }

    public function get_title() {
        return __( 'Banner Widget', 'rafo-elementor-widgets' );
    }

    public function get_icon() {
        return 'eicon-banner';
    }

    public function get_categories() {
        return [ 'basic' ];
    }

     public function get_style_depends(): array {
        return [ 'rafo-ew-banner' ];
    }

    protected function register_controls() {
        // Content
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'rafo-elementor-widgets' ),
            ]
        );

        $this->add_control(
            'tag_field',
            [
                'label' => __( 'ACF Field', 'rafo-elementor-widgets' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'tag_field', 'rafo-elementor-widgets' ),
                'placeholder' => __( 'Insert ACF field name', 'rafo-elementor-widgets' ),
            ]
        );

        $this->add_control(
            'render_context',
            [
                'label' => __( 'Render Context', 'rafo-elementor-widgets' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'listing' => __( 'Listing', 'rafo-elementor-widgets' ),
                    'tour' => __( 'Tour Page', 'rafo-elementor-widgets' ),
                ],
                'separator' => 'before',
            ],
        );

        $this->end_controls_section();

        // Style: Color & Text Size
        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Style', 'rafo-elementor-widgets' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Text color
        $this->add_control(
            'text_color',
            [
                'label' => __( 'Text Color', 'rafo-elementor-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .raf-tag' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Background color
        $this->add_control(
            'background_color',
            [
                'label' => __( 'Background Color', 'rafo-elementor-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .raf-tag' => 'background-color: {{VALUE}};',
                ],
                'separator' => 'after',
            ]
        );

        // Font size (slider)
        $this->add_control(
            'font_size',
            [
                'label' => __( 'Font Size', 'rafo-elementor-widgets' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 8,
                        'max' => 72,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 6,
                    ],
                    'rem' => [
                        'min' => 0.5,
                        'max' => 6,
                    ],
                ],
                'default' => [
                    'size' => 14,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .raf-tag' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $field = isset( $settings['tag_field'] ) ? $settings['tag_field'] : '';
        if(get_field( $field )) {
            $text = get_field( $field );
        } else {
            $text = 'This ACF field does not exist.';
        }

        if('tour' === $settings['render_context']) {
            // Ensure we're in a tour post context
             ?>
                <span class="raf-banner-tour"><?php echo esc_html( $text ); ?></span>
             <?php
        }

        
    }

    // Optional: editor live preview (Elementor)
    protected function content_template() {
        ?>
        <#
        var text = settings.tag_field ? settings.tag_field : '';
        #>
        <span class="raf-tag">{{{ text }}}</span>
        <?php
    }
}
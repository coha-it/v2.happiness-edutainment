<?php
namespace LearnDash\Course_Grid\Gutenberg\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

use LearnDash;
use LearnDash\Course_Grid\Lib\LearnDash_Gutenberg_Block;
use LearnDash\Course_Grid\Utilities;

class LearnDash_Course_Grid_Filter extends LearnDash_Gutenberg_Block
{
    
    /**
     * Object constructor
     */
    public function __construct() {
        $this->shortcode_slug   = 'learndash_course_grid_filter';
        $this->block_slug       = 'ld-course-grid-filter';
        $this->block_attributes = [
            'course_grid_id' => [
                'type' => 'string',
            ],
            'search' => [
                'type' => 'boolean',
            ],
            'taxonomies' => [
                'type' => 'array',
            ],
            'price' => [
                'type' => 'boolean',
            ],
            'price_min' => [
                'type' => 'integer',
            ],
            'price_max' => [
                'type' => 'integer',
            ],
            'preview_show' => [
                'type' => 'boolean',
            ]
        ];

        $this->self_closing = true;

        $this->init();
    }

    /**
     * Render Block
     *
     * This function is called per the register_block_type() function above. This function will output
     * the block rendered content.
     *
     * @param array $attributes Shortcode attrbutes.
     * @return none The output is echoed.
     */
    public function render_block( $attributes = array() ) {
        if ( isset( $attributes['taxonomies'] ) ) {
            $attributes['taxonomies'] = implode( ',', $attributes['taxonomies'] );
        } 

        $attributes = $this->preprocess_block_attributes( $attributes );

        if ( is_user_logged_in() ) {
            $attributes = apply_filters( 'learndash_block_markers_shortcode_atts', $attributes, $this->shortcode_slug, $this->block_slug, '' );
    
            $shortcode_params_str = '';
            foreach ( $attributes as $key => $val ) {
                if ( is_null( $val ) ) {
                    continue;
                }
    
                if ( ! empty( $shortcode_params_str ) ) {
                    $shortcode_params_str .= ' ';
                }
                $shortcode_params_str .= $key . '="' . esc_attr( $val ) . '"';
            }

            $shortcode_params_str = '[' . $this->shortcode_slug . ' ' . $shortcode_params_str . ']';

            $shortcode_out = do_shortcode( $shortcode_params_str );
            
            if ( ( empty( $shortcode_out ) ) ) {
                $shortcode_out = '[' . $this->shortcode_slug . '] placeholder output.';
            }
    
            return $this->render_block_wrap( $shortcode_out, true );
        }

        wp_die();
    }

    /**
     * Called from the LD function learndash_convert_block_markers_shortcode() when parsing the block content.
     *
     * @since 2.0
     *
     * @param array  $attributes The array of attributes parse from the block content.
     * @param string $shortcode_slug This will match the related LD shortcode ld_profile, ld_course_list, etc.
     * @param string $block_slug This is the block token being processed. Normally same as the shortcode but underscore replaced with dash.
     * @param string $content This is the orignal full content being parsed.
     *
     * @return array $attributes.
     */
    public function learndash_block_markers_shortcode_atts_filter( $attributes = array(), $shortcode_slug = '', $block_slug = '', $content = '' ) { 
        if ( $shortcode_slug === $this->shortcode_slug ) {
            if ( isset( $attributes['preview_show'] ) ) {
                unset( $attributes['preview_show'] );
            }

            if ( ! isset( $attributes['taxonomies'] ) ) {
                $attributes['taxonomies'] = '';
            }
        }

        return $attributes;
    }

    /**
     * Called from the LD function convert_block_markers_to_shortcode() when parsing the block content.
     * This function allows hooking into the converted content.
     *
     * @since 2.0
     *
     * @param string $content This is the orignal full content being parsed.
     * @param array  $attributes The array of attributes parse from the block content.
     * @param string $shortcode_slug This will match the related LD shortcode ld_profile, ld_course_list, etc.
     * @param string $block_slug This is the block token being processed. Normally same as the shortcode but underscore replaced with dash.
     *
     * @return string $content.
     */
    public function convert_block_markers_to_shortcode_content_filter( $content = '', $attributes = array(), $shortcode_slug = '', $block_slug = '' ) {
        if ( $shortcode_slug == $this->shortcode_slug ) {
            foreach ( $attributes as $key => $value ) {
                if ( strpos( $content, $key . '=' ) === false ) {
                    $content = str_replace( ']', " {$key}=\"\"]", $content );
                }
            }

        }

        return $content;
    }
}
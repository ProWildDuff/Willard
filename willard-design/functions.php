<?php
// Enable theme support for block styles
add_action('after_setup_theme', 'willard_design_setup');
function willard_design_setup() {
    add_theme_support('wp-block-styles');
}

// CSS embed
if ( ! function_exists( 'willard_design_locale_css' ) ) :
    function willard_design_locale_css( $uri ) {
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) ) {
            $uri = get_template_directory_uri() . '/rtl.css';
        }
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'willard_design_locale_css' );

// Ensure the theme's CSS overrides plugin CSS
if ( ! function_exists( 'willard_design_configurator_css' ) ) :
    function willard_design_configurator_css() {
        // Enqueue the theme's main stylesheet
        // Adjust the version number as needed, e.g., '1.0.0' or use file modification time for automatic cache busting
        wp_enqueue_style( 'willard_design_cfg_separate', get_template_directory_uri() . '/css/willard-design-style.css', array(), filemtime( get_template_directory() . '/css/willard-design-style.css' ) );    
    }
endif;
add_action( 'wp_enqueue_scripts', 'willard_design_configurator_css', 100 );

// Enqueue the back-to-top JavaScript file
if ( ! function_exists( 'willard_design_javascript' ) ) :
    function willard_design_javascript() {
        wp_enqueue_script( 'willard-design-back-to-top', get_template_directory_uri() . '/js/willard-design-back-to-top.js', array(), null, true );
    }
endif;
add_action( 'wp_enqueue_scripts', 'willard_design_javascript' );

// Localize script to pass dynamic data to the JavaScript file
if ( ! function_exists( 'willard_design_localize_script' ) ) :
    function willard_design_localize_script() {
        wp_localize_script( 'willard-design-back-to-top', 'willard_design_vars', array(
            'back_to_top_icon' => esc_url( get_template_directory_uri() . '/assets/images/up-arrow.svg' )
        ));
    }
endif;
add_action( 'wp_enqueue_scripts', 'willard_design_localize_script' );

// Register pattern categories
if ( ! function_exists( 'willard_design_pattern_categories' ) ) :
    function willard_design_pattern_categories() {
        register_block_pattern_category(
            'willard_design_page',
            array(
                'label'       => _x( 'Pages', 'Block pattern category', 'willard-design' ),
                'description' => __( 'A collection of full page layouts.', 'willard-design' ),
            )
        );
    }
endif;

// Register block patterns from JSON files
add_action('init', 'willard_design_register_block_patterns');
function willard_design_register_block_patterns() {
    $pattern_files = glob(get_stylesheet_directory() . '/patterns/*.json');

    foreach ($pattern_files as $pattern_file) {
        $pattern_data = json_decode(file_get_contents($pattern_file), true);

        if (isset($pattern_data['title'], $pattern_data['content'])) {
            register_block_pattern(
                'willard_design/' . basename($pattern_file, '.json'),
                array(
                    'title'       => $pattern_data['title'],
                    'description' => isset($pattern_data['description']) ? $pattern_data['description'] : '',
                    'content'     => $pattern_data['content'],
                )
            );
        }
    }
}
?>
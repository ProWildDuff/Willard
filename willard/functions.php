<?php
// Enable theme support for block styles
add_action('after_setup_theme', 'willard_setup');
function willard_setup() {
    add_theme_support('wp-block-styles');
}

// Template quick edit
function willard_add_template_quick_edit($column_name, $post_type) {
    if ($post_type != 'page') return;
    ?>
    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
            <label>
                <span class="title"><?php _e( 'Template', 'willard' ); ?></span>
                <span class="input-text-wrap">
                    <select name="page_template">
                        <option value=""><?php _e( 'Default Template', 'willard' ); ?></option>
                        <?php page_template_dropdown(); ?>
                    </select>
                </span>
            </label>
        </div>
    </fieldset>
    <?php
}
add_action('quick_edit_custom_box', 'willard_add_template_quick_edit', 10, 2);

// Save the template from Quick Edit
function willard_save_template_quick_edit($post_id) {
    if (!isset($_POST['page_template'])) return;
    $template = $_POST['page_template'];
    update_post_meta($post_id, '_wp_page_template', $template);
}
add_action('save_post', 'willard_save_template_quick_edit');

// CSS embed
if ( ! function_exists( 'willard_locale_css' ) ) :
    function willard_locale_css( $uri ) {
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) ) {
            $uri = get_template_directory_uri() . '/rtl.css';
        }
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'willard_locale_css' );

// Ensure the theme's CSS overrides plugin CSS
if ( ! function_exists( 'willard_configurator_css' ) ) :
    function willard_configurator_css() {
        // Enqueue the theme's main stylesheet
        // Adjust the version number as needed, e.g., '1.0.0' or use file modification time for automatic cache busting
        wp_enqueue_style( 'willard_cfg_separate', get_template_directory_uri() . '/css/willard-style.css', array(), filemtime( get_template_directory() . '/css/willard-style.css' ) );    }
endif;
add_action( 'wp_enqueue_scripts', 'willard_configurator_css', 100 );


// END ENQUEUE PARENT ACTION

// Enqueue the back-to-top JavaScript file
if ( ! function_exists( 'willard_javascript' ) ) :
    function willard_javascript() {
        wp_enqueue_script( 'willard-back-to-top', get_template_directory_uri() . '/js/willard-back-to-top.js', array(), null, true );
    }
endif;
add_action( 'wp_enqueue_scripts', 'willard_javascript' );

// Localize script to pass dynamic data to the JavaScript file
if ( ! function_exists( 'willard_localize_script' ) ) :
    function willard_localize_script() {
        wp_localize_script( 'willard-back-to-top', 'willard_vars', array(
            'back_to_top_icon' => esc_url( get_template_directory_uri() . '/assets/images/up-arrow-thumb.png' )
        ));
    }
endif;
add_action( 'wp_enqueue_scripts', 'willard_localize_script' );


/**
 * Register pattern categories.
 */

if ( ! function_exists( 'twentytwentyfour_pattern_categories' ) ) :
	/**
	 * Register pattern categories
	 *
	 * @since Willard 1.0
	 * @return void
	 */
	function willard_pattern_categories() {

		register_block_pattern_category(
			'willard_page',
			array(
				'label'       => _x( 'Pages', 'Block pattern category', 'willard' ),
				'description' => __( 'A collection of full page layouts.', 'willard' ),
			)
		);
	}
endif;

// Register block patterns from JSON files
add_action('init', 'willard_register_block_patterns');
function willard_register_block_patterns() {
    $pattern_files = glob(get_stylesheet_directory() . '/patterns/*.json');

    foreach ($pattern_files as $pattern_file) {
        $pattern_data = json_decode(file_get_contents($pattern_file), true);

        if (isset($pattern_data['title'], $pattern_data['content'])) {
            register_block_pattern(
                'willard/' . basename($pattern_file, '.json'),
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
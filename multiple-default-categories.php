<?php
/*
Plugin Name: Multiple Default Categories
Plugin URI: http://www.mediasuite.co.nz
Description: Allows multiple default categories to be selected for new posts.
Version: 0.1
Author: The Media Suite
Author URI: http://www.mediasuite.co.nz
License: MIT
*/

//Set default categories on newly created posts
function set_default_categories( $post_id ) {
    //Newly created post
    if ( get_post_status( $post_id) === 'auto-draft' ) {
        $options = array_keys(get_option( 'default_categories_settings' ));
        wp_set_object_terms( $post_id, $options, 'category');
    }
}

add_action( 'wp_insert_post', 'set_default_categories' );

//Add a setting to store category ids in
add_action('admin_init', 'default_categories_register_settings');
function default_categories_register_settings(){
    register_setting('default_categories_settings', 'default_categories_settings');
}


//Add a menu to select defaults
function default_categories_menu() {
    add_options_page( 'Default Category Options', 'Multiple Default Categories', 'manage_options', 'multiple-default-categories', 'default_categories_options' );
}

function default_categories_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo '<div class="wrap">';
    screen_icon();
    echo '<h2>Multiple Default Categories</h2>';
    echo '<form method="post" action="options.php">';
    settings_fields( 'default_categories_settings' );
    do_settings_sections( __FILE__ );

    $options = get_option( 'default_categories_settings' );

    $categories = get_categories(array(
        'hide_empty' => 0
    ));

    echo '<p>Select categories to default new posts to</p>';

    foreach( $categories as $category ) {
        $checked = isset($options[$category->term_id]) ? 'checked="checked"' : '';
        echo '<label>';
        echo '<input name="default_categories_settings['.$category->term_id.']" type="checkbox" value="'.$category->term_id.'" '. $checked .'/>&nbsp;'.$category->name;
        echo '</label><br/>';
    }

    submit_button();
    echo '</form>';
    echo '</div>';
}

add_action( 'admin_menu', 'default_categories_menu' );
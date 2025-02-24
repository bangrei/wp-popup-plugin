<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Custom Post Type for Pop-Up.
 */
class PopUp_CPT {
  /**
   * Holds the class instance.
   *
   * @var PopUp_CPT
   */
  private static $instance = null;

  /**
   * Singleton pattern to get the instance.
   *
   * @return PopUp_CPT
   */
  public static function get_instance() {
    if ( null === self::$instance ) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Register the custom post type.
   */
  public function register() {
    $args = [
      'labels' => [
        'name' => 'Pop-Ups',
        'singular_name' => 'Pop-Up',
      ],
      'public' => true,
      'has_archive' => true,
      'supports' => [ 'title', 'editor', 'custom-fields' ],
      'show_in_rest' => true, // Enable Gutenberg and REST API support
      'menu_icon' => 'dashicons-lightbulb',
    ];
    register_post_type( 'popup', $args );
  }

  public function check_user_permission() {
    if ( ! is_user_logged_in() ) {
      return new WP_Error( 'unauthorized', 'You must be logged in to access this data.', [ 'status' => 401 ] );
    }
    return true;
  }


  public function __construct() {
    add_action( 'add_meta_boxes', [ $this, 'add_popup_meta_box' ] );
    add_action( 'save_post', [ $this, 'save_popup_meta_box_data' ] );
  }

  public function add_popup_meta_box() {
    add_meta_box( 'popup_details', 'Pop-Up Details', [ $this, 'popup_meta_box_callback' ], 'popup', 'normal', 'high' );
  }

  public function popup_meta_box_callback( $post ) {
    $title = get_post_meta( $post->ID, '_popup_title', true );
    $description = get_post_meta( $post->ID, '_popup_description', true );
    $selected_page = get_post_meta( $post->ID, '_popup_page', true );

    $args = [
        'post_type'   => 'page',    // Only pages
        'post_status' => 'publish', // Only published pages
        'numberposts' => -1,
    ];
    $pages = get_pages($args);
    ?>
      <div style="width: 100%; display:flex; flex-direction:column; gap:16px;">
        <div style="width: 100%; display:flex; flex-direction:column; gap:8px;">
          <label for="popup_title">Title</label>
          <input type="text" id="popup_title" name="popup_title" value="<?php echo esc_attr( $title ); ?>" />
        </div>
        <div style="width: 100%; display:flex; flex-direction:column; gap:8px;">
          <label for="popup_description">Description</label>
          <textarea id="popup_description" name="popup_description"><?php echo esc_textarea( $description ); ?></textarea>
        </div>
        <div style="width: 100%; display:flex; flex-direction:column; gap:8px;">
          <label for="popup_page">Page</label>
          <select id="popup_page" name="popup_page">
            <option value="">-- Select --</option>
            <?php 
            foreach ( $pages as $page ) {
              echo '<option value="' . esc_attr( $page->ID ) . '" ' . selected( $selected_page, $page->ID, false ) . '>' . esc_html( $page->post_title ) . '</option>';
            }
            ?>
          </select>
        </div>
      </div>
    <?php
  }

  public function save_popup_meta_box_data( $post_id ) {
    if ( isset( $_POST['popup_title'] ) ) {
      update_post_meta( $post_id, '_popup_title', sanitize_text_field( $_POST['popup_title'] ) );
    }
    if ( isset( $_POST['popup_description'] ) ) {
      update_post_meta( $post_id, '_popup_description', sanitize_textarea_field( $_POST['popup_description'] ) );
    }
    if ( isset( $_POST['popup_page'] ) ) {
      update_post_meta( $post_id, '_popup_page', sanitize_text_field( $_POST['popup_page'] ) );
    }
  }

}

PopUp_CPT::get_instance();

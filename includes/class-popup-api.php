<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * PopUp API Endpoints.
 */
class PopUp_API {
  /**
   * Holds the class instance.
   *
   * @var PopUp_API
   */
  private static $instance = null;

  /**
   * Singleton pattern to get the instance.
   *
   * @return PopUp_API
   */
  public static function get_instance() {
    if ( null === self::$instance ) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  /**
   * Register API endpoints.
   */
  public function register_endpoints() {
    register_rest_route( 
      'artistudio/v1', 
      '/popup/(?P<slug>[a-zA-Z0-9-_]+)', 
      [
        'methods' => 'GET',
        'callback' => [ $this, 'get_popup_data' ],
      ' permission_callback' => [ $this, 'check_user_permission' ],
      ]
    );
  }
  /**
   * Callback for fetching the pop-up content.
   */
  public function get_popup_data( WP_REST_Request $request ) {
    $slug = $request->get_param('slug');
    if($slug){
      $post = get_page_by_path($slug, OBJECT, 'page');
      if (!$post) {
        return new WP_REST_Response('Page_not_found', 'Page not found', [ 'status' => 404 ]);
      }
      $popup_query = new WP_Query( [
        'post_type' => 'popup',
        'posts_per_page' => 1,
        'meta_key' => '_popup_page',
        'meta_value' => $post->ID,
      ]);
      if ( $popup_query->have_posts() ) {
        $popup = $popup_query->posts[0];
        return [
          'title' => get_post_meta( $popup->ID, '_popup_title', true ),
          'description' => get_post_meta( $popup->ID, '_popup_description', true ),
        ];
      }
    }

    return new WP_Error( 'no_popup', 'No pop-up found', [ 'status' => 404 ] );
  }
  /**
   * Check if user is logged in.
   */
  public function check_user_permission() {
    $current_user = wp_get_current_user();
    return !empty($current_user);
  }
}

PopUp_API::get_instance();

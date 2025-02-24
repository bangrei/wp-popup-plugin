<?php
/**
 * Plugin Name:     Wp Popup Plugin
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     wp-popup-plugin
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wp_Popup_Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Main plugin class
 */
final class WP_PopUp_Plugin {

  /**
   * Holds the class instance
   *
   * @var WP_PopUp_Plugin
   */
  private static $instance = null;

  /**
   * Plugin directory path
   *
   * @var string
   */
  private $plugin_path;

  /**
   * Singleton instance.
   *
   * @return WP_PopUp_Plugin
   */
  public static function get_instance() {
    if ( null === self::$instance ) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Constructor.
   */
  private function __construct() {
    $this->plugin_path = plugin_dir_path( __FILE__ );
    $this->includes();
    $this->setup_hooks();
  }

  /**
   * Includes the necessary files.
   */
  private function includes() {
    require_once $this->plugin_path . 'includes/class-popup-cpt.php';
    require_once $this->plugin_path . 'includes/class-popup-api.php';
  }

  /**
   * Setup hooks.
   */
  private function setup_hooks() {
    add_action( 'init', [ $this, 'register_popup_cpt' ] );
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles_scripts' ] );
    add_action( 'rest_api_init', [ $this, 'register_api_endpoints' ] );
  }

  /**
   * Register custom post type for the popup.
   */
  public function register_popup_cpt() {
    PopUp_CPT::get_instance()->register();
  }

  /**
   * Enqueue styles and scripts.
   */
  public function enqueue_styles_scripts() {
    wp_enqueue_style( 'popup-plugin-styles', plugin_dir_url( __FILE__ ) . 'assets/css/popup.css' );
    wp_enqueue_script( 'vue-js', 'https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js', [], null, true );
    wp_enqueue_script( 'popup-plugin-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/popup.js', ['vue-js'], null, true );

    // Add the Vue app container to the footer of the page
    add_action( 'wp_footer', function() {
      echo '<div id="popup-app"></div>';
    });
  }

  /**
   * Register custom API endpoints.
   */
  public function register_api_endpoints() {
    PopUp_API::get_instance()->register_endpoints();
  }

}

WP_PopUp_Plugin::get_instance();


/*
function wp_popup_plugin_enqueue_scripts() {
  wp_enqueue_style('popup-style', plugin_dir_url(__FILE__) . 'assets/css/popup.css');
  wp_enqueue_script('popup-script', plugin_dir_url(__FILE__) . 'assets/js/popup.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'wp_popup_plugin_enqueue_scripts');

function wp_popup_plugin_display_popup() {
  ?>
  <div id="wp-popup" class="wp-popup">
    <div class="wp-popup-content">
      <span class="wp-popup-close">&times;</span>
      <h2>Special Offer!</h2>
      <p>Get 20% off on your first purchase. Sign up now!</p>
    </div>
  </div>
  <?php
}
add_action('wp_footer', 'wp_popup_plugin_display_popup');
*/

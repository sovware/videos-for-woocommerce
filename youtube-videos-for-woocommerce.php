<?php
/**
 * Plugin Name: Youtube Videos For WooCommerce
 * Plugin URI: https://wpwax.com/
 * Description: WooCommerce product related youtube videos.
 * Version: 0.1.0
 * Author: wpWax
 * Author URI: https://wpwax.com
 * Text Domain: youtube-videos-for-woocommerce
 * Domain Path: /languages/
 * Requires at least: 5.5
 * Requires PHP: 7.0
 *
 * @package WC_Youtube_Videos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class WC_Youtube_Videos {

	protected $version = '1.0';

	protected static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->init();
	}

	public function init() {
		add_action( 'woocommerce_init', array( $this, 'setup_hooks' ) );
	}

	public function setup_hooks() {
		add_filter( 'woocommerce_product_tabs', array( $this, 'add_product_tab' ) );
		add_filter( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function add_product_tab( $tabs ) {
		$tabs['youtube_videos'] = array(
			'title' 	=> __( 'Videos', 'woocommerce' ),
			'priority' 	=> 50,
			'callback' 	=> array( $this, 'render_product_tab' )
		);
	
		return $tabs;
	}

	public function render_product_tab() {
		global $product;

		// print_r( $this->get_videos( $product->get_title() ) );
		?>
		<div class="ytwc-grid">
			<div class="ytwc-grid__item">
				<iframe loading="lazy" width="560" height="315" src="https://www.youtube.com/embed/WrhjbdWDpTk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			<div class="ytwc-grid__item">
				<iframe loading="lazy" width="560" height="315" src="https://www.youtube.com/embed/WrhjbdWDpTk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			<div class="ytwc-grid__item">
				<iframe loading="lazy" width="560" height="315" src="https://www.youtube.com/embed/WrhjbdWDpTk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			<div class="ytwc-grid__item">
				<iframe loading="lazy" width="560" height="315" src="https://www.youtube.com/embed/WrhjbdWDpTk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			<div class="ytwc-grid__item">
				<iframe loading="lazy" width="560" height="315" src="https://www.youtube.com/embed/WrhjbdWDpTk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			<div class="ytwc-grid__item">
				<iframe loading="lazy" width="560" height="315" src="https://www.youtube.com/embed/WrhjbdWDpTk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			<div class="ytwc-grid__item">
				<iframe loading="lazy" width="560" height="315" src="https://www.youtube.com/embed/WrhjbdWDpTk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			<div class="ytwc-grid__item">
				<iframe loading="lazy" width="560" height="315" src="https://www.youtube.com/embed/WrhjbdWDpTk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
		</div>
		<?php
	}

	public function enqueue_scripts() {
		if ( is_product() && $this->has_videos() ) {
			wp_enqueue_style(
				'ytwc-style',
				plugin_dir_url( __FILE__ ) . 'assets/css/style.css',
				null,
				$this->version
			);
		}	
	}

	public function has_videos() {
		return true;
	}

	public function get_videos( $query_string ) {
		$api_key = 'AIzaSyDX2n54ulhfNRFu7gr7-JSSK_2t01bs5Qo';
		$api_url = 'https://www.googleapis.com/youtube/v3/search';
		$api_url = add_query_arg(
			array(
				'key' => $api_key,
				'q'   => $query_string,
			),
			$api_url
		);

		$response = wp_remote_get( $api_url );
		$response = wp_remote_retrieve_body( $response );

		return $response;
	}
}

function wc_youtube_videos() {
	return WC_Youtube_Videos::instance();
}

wc_youtube_videos();

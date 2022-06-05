<?php
/**
 * Plugin Name: Youtube Videos For WooCommerce
 * Plugin URI: https://wpwax.com/
 * Description: WooCommerce product related youtube videos.
 * Version: 1.0.0
 * Author: wpWax
 * Author URI: https://wpwax.com
 * Text Domain: youtube-videos-for-woocommerce
 * Domain Path: /languages/
 * Requires at least: 5.5
 * Tested up to: 6.0
 * Requires PHP: 7.0
 * WC requires at least: 3
 * WC tested up to: 6.5
 *
 * @package WC_Youtube_Videos
 */
namespace wpWax;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Product Related Youtube Videos class.
 */
final class WC_Youtube_Videos {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	protected $version = '1.0';

	/**
	 * Instace of WC_Youtube_Videos.
	 *
	 * @var WC_Youtube_Videos
	 */
	protected static $instance = null;

	/**
	 * Singalaton instance.
	 *
	 * @return WC_Youtube_Videos
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'woocommerce_init', array( $this, 'setup_hooks' ) );
	}

	/**
	 * Setup plugin hooks.
	 *
	 * @return void
	 */
	public function setup_hooks() {
		add_action( 'save_post', array( $this, 'cache_videos' ) );
		add_filter( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'woocommerce_product_tabs', array( $this, 'add_product_tab' ) );

		add_filter( 'woocommerce_get_sections_products', array( $this, 'add_settings_section' ), 999 );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings_fields' ), 10, 2 );

		// Add link to settings page.
		add_filter( 'plugin_action_links', array( $this, 'add_plugin_action_links' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( $this, 'add_plugin_action_links' ), 10, 2 );
	}

	public function add_plugin_action_links( $links, $file ) {
		// Return normal links if not BuddyPress.
		if ( plugin_basename( __FILE__ ) != $file ) {
			return $links;
		}

		$url = add_query_arg( array(
			'page'    => 'wc-settings',
			'tab'     => 'products',
			'section' => 'ytwc',
		), self_admin_url( 'admin.php' ) );

		if ( empty( get_option( 'ytwc_youtube_api_key' ) ) ) {
			$link_text = esc_html__( 'Set API Key', 'youtube-videos-for-woocommerce' );
		} else {
			$link_text = esc_html__( 'Settings', 'youtube-videos-for-woocommerce' );
		}

		// Add a few links to the existing links array.
		return array_merge( $links, array(
			'settings' => '<a href="' . esc_url( $url ) . '">' . $link_text . '</a>',
		) );
	}

	public function add_settings_section( $sections ) {
		$sections['ytwc'] = esc_html__( 'Youtube Vidoes', 'youtube-videos-for-woocommerce' );
		return $sections;
	}

	public function add_settings_fields( $fields, $section_id ) {
		if ( $section_id === 'ytwc' ) {
			$fields['ytwc_section_start'] = array(
				'name'     => esc_html__( 'Youtube Videos Settings', 'youtube-videos-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'ytwc_section_start'
			);
			$fields['ytwc_youtube_api_key'] = array(
				'name' => esc_html__( 'API Key', 'youtube-videos-for-woocommerce' ),
				'type' => 'text',
				'desc' => sprintf(
					__( 'You have to put your API key here. %1$sClick here to get your API key%1$s', 'youtube-videos-for-woocommerce' ),
					'<a href="https://developers.google.com/youtube/v3/getting-started" rel="noopener" target="_blank">',
					'</a>',
				),
				'id'       => 'ytwc_youtube_api_key',
				'autoload' => 'no',
			);
			$fields['ytwc_section_end'] = array(
				'type' => 'sectionend',
				'id'   => 'wc_settings_tab_demo_section_end'
			);
		}

		return $fields;
	}

	/**
	 * Register custom product tab.
	 *
	 * @param  array $tabs
	 *
	 * @return array Modified array with the custom tab.
	 */
	public function add_product_tab( $tabs ) {
		$tabs['ytwc'] = array(
			'title' 	=> esc_html__( 'Videos', 'youtube-videos-for-woocommerce' ),
			'priority' 	=> 50,
			'callback' 	=> array( $this, 'render_product_tab' )
		);
	
		return $tabs;
	}

	/**
	 * Render product custom tab.
	 *
	 * @return void
	 */
	public function render_product_tab() {
		global $product;

		$videos = $this->get_videos( $product->get_id() );

		if ( $videos ) :
		?>
		<div class="ytwc-grid">
			<?php
			foreach ( $videos as $video ) :
				$url = 'https://www.youtube.com/embed/' . $video['id']['videoId'];
				?>
			<div class="ytwc-grid__item">
				<iframe loading="lazy" width="560" height="315" data-src="<?php echo esc_url( $url ); ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>
			<?php endforeach; ?>
		</div>
		<script>
			var ytwc = document.querySelector('#tab-title-ytwc > a');
			if (ytwc) {
				ytwc.addEventListener('click', function(event) {
					event.preventDefault();
					if (event.target.classList.contains('ytwc-loaded')) {
						return false;
					}
					
					var iframes = document.querySelectorAll('.ytwc-grid__item > iframe');
					if (iframes) {
						iframes.forEach(function(iframe) {
							iframe.setAttribute('src', iframe.getAttribute('data-src'));
						});
						event.target.classList.add('ytwc-loaded');
					}
				});
			}
		</script>
		<?php
		endif;
	}

	/**
	 * Enqueue assets.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( is_product() ) {
			wp_enqueue_style(
				'ytwc-style',
				plugin_dir_url( __FILE__ ) . 'assets/css/style.css',
				null,
				$this->version
			);
		}	
	}

	public function query_videos( $query_string ) {
		$api_key = get_option( 'ytwc_youtube_api_key' );
		$api_url = 'https://www.googleapis.com/youtube/v3/search';
		$api_url = add_query_arg(
			array(
				'key'             => $api_key,
				'q'               => $query_string,
				'maxResults'      => 4,
				'type'            => 'video',
				'videoDuration'   => 'short',
				'videoEmbeddable' => true,
			),
			$api_url
		);

		$response = wp_remote_get( esc_url_raw( $api_url ) );
		$response = wp_remote_retrieve_body( $response );

		return ( $response ? json_decode( $response, true ) : array() );
	}

	public function get_videos( $product_id = 0 ) {
		$data = $this->get_videos_data( $product_id );

		return ( ! empty( $data['items'] ) ? $data['items'] : array() );
	}

	public function get_videos_data( $product_id = 0 ) {
		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return array();
		}

		$videos = get_post_meta( $product_id, '_ytwc_cache', true );

		if ( empty( $videos ) ) {
			$videos = $this->query_videos( strip_tags( $product->get_title() ) );

			if ( ! empty( $videos ) ) {
				update_post_meta( $product_id, '_ytwc_cache', $videos );

				$videos = get_post_meta( $product_id, '_ytwc_cache', true );
			}
		}

		return $videos;
	}

	public function cache_videos( $post_id ) {
		// Autosaving, bail.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( get_post_type( $post_id ) !== 'product' ) {
			return;
		}

		$product = wc_get_product( $post_id );
		if ( ! $product ) {
			return;
		}

		delete_post_meta( $post_id, '_ytwc_cache' );
		$this->get_videos( $post_id );
	}
}

function wc_youtube_videos() {
	return WC_Youtube_Videos::instance();
}

wc_youtube_videos();

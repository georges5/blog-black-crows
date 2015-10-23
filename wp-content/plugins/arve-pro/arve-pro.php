<?php
/**
 * @link              https://nextgenthemes.com
 * @since             1.0.0
 * @package           Advanced_Responsive_Video_Embedder_Pro
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Responsive Video Embedder Pro Addon
 * Plugin URI:        https://nextgenthemes.com/plugins/advanced-responsive-video-embedder/
 * Description:       Lazyload, Lightbox and HTML5 experimental features for ARVE.
 * Version:           1.4.0
 * Author:            Nicolas Jonas
 * Author URI:        http://nico.onl
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       arve-pro
 * Domain Path:       /languages
 */

add_action( 'plugins_loaded', 'arve_pro_init' );

function arve_pro_init() {

	$arve_pro = new Advanced_Responsive_Video_Embedder_Pro;
	$arve_pro->init();
}

class Advanced_Responsive_Video_Embedder_Pro {

	protected $plugin_slug;
	protected $version;

	protected $item_name;
	protected $remote_api_url;
	protected $transient_name;

	protected $options = array();

	public function init() {

		if( ! class_exists( 'Advanced_Responsive_Video_Embedder_Shared' ) ) {
			return;
		}

		if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			// load our custom updater
			include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
		}

		$this->plugin_slug    = 'arve-pro';
		$this->version        = '1.4.0';
		$this->item_name      = 'Advanced Responsive Video Embedder Pro';
		$this->remote_api_url = 'https://nextgenthemes.com';
		$this->transient_name = sanitize_key( $this->item_name . '-license-status' );

		$this->set_options();

		add_action( 'admin_init', array( $this, 'edd_sl_plugin_updater' ), 0 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_action( 'admin_notices', array( $this, 'notices' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), 0 );

		add_filter( 'arve_admin_pro_message', array( $this, 'filter_admin_pro_message' ) );
		add_filter( 'arve_modes',             array( $this, 'filter_modes' ) );
		add_filter( 'arve_maxwidth',          array( $this, 'filter_maxwidth' ), 10, 3 );
		add_filter( 'arve_output',            array( $this, 'filter_output' ), 10, 2 );
		add_filter( 'arve_thumbnail',         array( $this, 'filter_thumbnail' ), 10, 2 );
	}

	public function filter_admin_pro_message( $message ) {

		$message = __( 'Thanks so much for buying the pro addon! Please check out the <a href="https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/">plugins page</a> for updated links to the documentation and more.', 'arve_pro_addon' );
		$message = "<p><big>$message</big></p>";

		return $message;
	}

	public function get_options_defaults() {

		return array(
			'fakethumb'             => true,
			'key'                   => '',
			'key_status'            => null,
			'thumbnail_fallback'    => '',
			'lazyload_maxwidth'     => 400,
			'transient_expire_time' => DAY_IN_SECONDS,
			'grow'                  => true,
		);
	}

	public function set_options() {

		$this->options = wp_parse_args( get_option( 'arve_options_pro', array() ), $this->get_options_defaults() );
	}

	public function register_styles() {

		wp_register_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'arve-pro.css', array(), $this->version, 'all' );
	}

	public function register_scripts() {

		wp_register_script( 'lity',                          plugin_dir_url( __FILE__ ) . 'lity.min.js',          array( 'jquery' ),               '1.3.0',        true );
		wp_register_script( 'screenfull',                    plugin_dir_url( __FILE__ ) . 'screenfull.min.js',    array(),                         '2.0.0',        true );
		wp_register_script( "{$this->plugin_slug}-lazyload", plugin_dir_url( __FILE__ ) . 'arve-pro-lazyload.js', array( 'screenfull', 'jquery' ), $this->version, true );
		wp_register_script( "{$this->plugin_slug}-lightbox", plugin_dir_url( __FILE__ ) . 'arve-pro-lightbox.js', array( 'lity', 'jquery' ),       $this->version, true );
	}

	public function filter_output( $output, $args ) {

		global $_arve_public;

		$iframe = ''; # we dont want the bool value from ARVE everything is iframe here

		if( !$this->is_pro_mode( $args['mode'] ) ) {

			return $output;
		}

		if( 'valid' !== $this->options['key_status'] ) {

			return new WP_Error( 'invalid_license', sprintf(
				__( 'Invalid <a href="%s">ARVE Pro</a> License.', $this->plugin_slug ),
				esc_url( 'https://nextgenthemes.com/downloads/advanced-responsive-video-embedder-pro' )
			) );
		}

		$fakethumb = (bool) $this->options['fakethumb'];

		if ( ! $args['properties'][ $args['provider'] ]['wmode_transparent'] || filter_var( $args['thumbnail'], FILTER_VALIDATE_URL ) ) {
			$fakethumb = false;
		}

		wp_enqueue_style( $this->plugin_slug );

		if ( in_array( $args['mode'], array( 'lazyload-lightbox', 'link-lightbox' ) ) ) {

			wp_enqueue_script( "{$this->plugin_slug}-lightbox" );

			$hidden_embed = $_arve_public->create_iframe( array (
				'provider'   => $args['provider'],
				#'src'        => $args['url_autoplay_no'],
				'data-src'   => $args['url_autoplay_yes'],
				'class'      => 'arve-inner'
			) );

			$hidden_embed = sprintf(
				'<span id="lity-%s" class="lity-hide arve-embed-container" style="width: 1200px; max-width: 100%%; padding-bottom: %g;">%s</span>',
				# Width Option?
				urlencode( $args['id'] ),
				$args['aspect_ratio'] . '%',
				$hidden_embed
			);

			$output .= $hidden_embed;

		} else {

			wp_enqueue_script( "{$this->plugin_slug}-lazyload" );
		}

		if ( 'lazyload-fullscreen' === $args['mode'] ) {

			$args['url_autoplay_yes'] = $this->disable_fullscreen( $args['provider'], $args['url_autoplay_yes'] );
			$args['url_autoplay_no']  = $this->disable_fullscreen( $args['provider'], $args['url_autoplay_no'] );
		}

		if ( 'link-lightbox' === $args['mode'] ) {

			if ( empty( $args['link_text'] ) ) {

				$args['link_text'] = 'open video';
			}

			$output .= sprintf(
				'<a href="#lity-%s" class="arve-lightbox-link" data-lity>%s</a>',
				urlencode( $args['id'] ),
				esc_attr( $args['link_text'] )
			);

		} else {

			if ( 'lazyload' === $args['mode'] || 'lazyload-lightbox' === $args['mode'] ) {

				$iframe = $_arve_public->create_iframe( array (
					'provider'       => $args['provider'],
					'src'            => ( ! $args['thumbnail'] && $fakethumb ) ? $args['url_autoplay_no'] : false,
					'data-src'       => $args['url_autoplay_yes'],
					'class'          => ( $args['thumbnail'] ) ? 'arve-inner arve-hidden' : 'arve-inner'
				) );
			}

			switch ( $args['grow'] ) {
				case '1':
				case 'true':
				case 'yes':
					$args['grow'] = true;
					break;
				case '0':
				case 'false':
				case 'no':
					$args['grow'] = false;
					break;
				default:
					$args['grow'] = (bool) $this->options['grow'];
					break;
			}

			$btn_class  = 'arve-inner arve-btn arve-btn-start';
			$btn_class .= ( ! $args['thumbnail'] && $fakethumb ) ? '' : ' arve-btn-play-bg';
			$btn_class .= ( 'lazyload-lightbox' == $args['mode'] ) ? ' iframe' : '';

			$btn = sprintf(
				'<button %s class="%s" type="button"><span class="arve-rectangle"></span></button>',
				( 'lazyload-lightbox' == $args['mode'] ) ? sprintf( 'href="#lity-%s" data-lity', urlencode( $args['id'] ) ) : '',
				#( 'lazyload-lightbox' == $mode ) ? sprintf( 'href="%s"', $_arve_public->esc_url( $args['provider'], $args['url_autoplay_yes'] ) ) : '',
				esc_attr( $btn_class )
			);

			$output .= $_arve_public->wrappers( $iframe . $btn, $args );
		}

		return $output;
	}

	public function disable_fullscreen( $provider, $url ) {

		switch ( $provider ) {

			case 'YYYyoutube':
				$url = add_query_arg( 'fs', 0, $url );
				break;
		}

		return $url;
	}

	public function filter_maxwidth( $maxwidth, $align, $mode ) {

		if( $maxwidth < 100 && 0 === strpos( $mode, 'lazyload' ) ) {

			$maxwidth = $this->options['lazyload_maxwidth'];
		}

		return (int) $maxwidth;
	}

	public function get_pro_modes() {

		return array(
			'lazyload'            => __( 'Lazyload', 'arve_pro_addon' ),
			'lazyload-lightbox'   => __( 'Lazyload -> Lightbox', 'arve_pro_addon' ),
			'link-lightbox'       => __( 'Link -> Lightbox', 'arve_pro_addon' ),
			'lazyload-fullscreen' => __( 'Lazyload -> Fullscreen (experimental)', 'arve_pro_addon' ),
			'lazyload-fixed'      => __( 'Lazyload -> Fixed (experimental)', 'arve_pro_addon' ),
		);
	}

	public function is_pro_mode( $mode ) {

		$pro_modes = array_flip( $this->get_pro_modes() );

		return ( in_array( $mode, $pro_modes ) ) ? true : false;
	}

	public function filter_modes( $modes ) {

		return array_merge( $modes, $this->get_pro_modes() );
	}

	public function register_settings() {

		global $_arve_admin;

		$title = __( 'Pro Options', $this->plugin_slug );

		//Pro version
		add_settings_section(
			'pro_section',
			sprintf( '<span class="arve-settings-section" id="arve-settings-section-pro" title="%s"></span>%s', esc_attr( $title ), esc_html( $title ) ),
			null,
			'advanced-responsive-video-embedder'
		);

		add_settings_field(
			'arve_options_pro[key]',
			__( 'License Key', $this->plugin_slug ),
			array( $this, 'input_key' ),
			'advanced-responsive-video-embedder',
			'pro_section',
			array(
				'label_for'   => 'arve_options_pro[key]',
				'value'       => $this->options['key'],
				'class'       => 'regular-text',
			)
		);

		add_settings_field(
			'arve_options_pro[lazyload_maxwidth]',
			__( 'Lazyload Maximal Width', $this->plugin_slug ),
			array( $_arve_admin, 'input_field' ),
			'advanced-responsive-video-embedder',
			'pro_section',
			array(
				'label_for'   => 'arve_options_pro[lazyload_maxwidth]',
				'value'       => $this->options['lazyload_maxwidth'],
				'suffix'      => 'px',
				'class'       => 'small-text',
				'description' => __( 'Optional, Must be 100+ to work. If not set your preview images will be the maximum size of the container they are in. You can decide if you want to small clickable thumbnails as previews or fullsize images. (Some video providers may not provide thumbnails in the desired resolution)', $this->plugin_slug )
			)
		);

		add_settings_field(
			'arve_options_pro[thumbnail_fallback]',
			__( 'Thumbnail Fallback', $this->plugin_slug ),
			array( $_arve_admin, 'input_field' ),
			'advanced-responsive-video-embedder',
			'pro_section',
			array(
				'label_for'   => 'arve_options_pro[thumbnail_fallback]',
				'value'       => $this->options['thumbnail_fallback'],
				'suffix'      => '',
				'class'       => 'large-text',
				'description' => __( 'URL of image to be used when no thumbnail is submited via shortcode or when no thumbnail can be catched from the provider', $this->plugin_slug ),
			)
		);

		// add_settings_field( $id, $title, $callback, $page, $section, $args );
		add_settings_field(
			'arve_options_pro[fakethumb]',
			__( 'Fake Thumbnail', $this->plugin_slug ),
			array( $_arve_admin, 'yes_no_select' ),
			'advanced-responsive-video-embedder',
			'pro_section',
			array(
				'label_for'   => 'arve_options_pro[fakethumb]',
				'value'       => $this->options['fakethumb'],
				'description' => __( 'Loads the actual Videoplayer as "background image" to for thumbnails to emulate the feature Youtube, Dailymotion, and Bliptv have.', $this->plugin_slug )
			)
		);

		// add_settings_field( $id, $title, $callback, $page, $section, $args );
		add_settings_field(
			'arve_options_pro[grow]',
			__( 'Grow Thumbnail on Click', $this->plugin_slug ),
			array( $_arve_admin, 'yes_no_select' ),
			'advanced-responsive-video-embedder',
			'pro_section',
			array(
				'label_for'   => 'arve_options_pro[grow]',
				'value'       => $this->options['grow'],
				'description' => __( 'Grow the thumbnail on click by default? (Lazyload Mode)', $this->plugin_slug )
			)
		);

		// add_settings_section( $id, $title, $callback, $page )
		add_settings_field(
			'arve_options_pro[transient_expire_time]',
			__( 'Transient Expire Time', $this->plugin_slug ),
			array( $_arve_admin, 'input_field' ),
			'advanced-responsive-video-embedder',
			'pro_section',
			array(
				'label_for'   => 'arve_options_pro[transient_expire_time]',
				'value'       => $this->options['transient_expire_time'],
				'suffix'      => 'seconds',
				'class'       => 'medium-text',
				'description' => __('This plugin uses WordPress transients to cache video thumbnail URLS that greatly speeds up Page loading. The maximum of seconds to keep the URLS before refreshing. For example: hour - 3600, day - 86400, week - 604800.', $this->plugin_slug )
			)
		);

		add_settings_field(
			'arve_options_pro[reset]',
			null,
			array( $_arve_admin, 'submit_reset' ),
			'advanced-responsive-video-embedder',
			'pro_section',
			array(
				'reset_name' => 'arve_options_pro[reset]',
			)
		);

		register_setting( 'arve-settings-group', 'arve_options_pro', array( $this, 'validate_options' ) );
	}

	public function input_key( $args ) {

		printf(
			'<input id="%1$s" name="%1$s" type="text" value="%2$s" class="%3$s">',
			esc_attr( $args['label_for'] ),
			esc_attr( $args['value'] ),
			esc_attr( $args['class'] )
		);

		if( !empty( $args['value'] ) ) {

			if( 'valid' === $this->options['key_status'] ) {

				// submit_button( $text, $type, $name, $wrap, $other_attributes
				submit_button( __('Deactivate License', $this->plugin_slug ), 'secondary', 'arve_options_pro[deactivate_license]', false );

			} else {

				submit_button( __('Activate License', $this->plugin_slug ), 'primary', 'arve_options_pro[activate_license]', false );
			}

			printf( '<p>%s %s</p>', __( 'Status:', $this->plugin_slug ), $this->options['key_status'] );
		}
	}

	public function validate_options( $input ) {

		//* Storing the Options Section as a empty array will cause the plugin to use defaults
		if( isset( $input['reset'] ) ) {
			return array();
		}

		$output['fakethumb']          = (bool) $input['fakethumb'];
		$output['grow']               = (bool) $input['grow'];
		$output['thumbnail_fallback'] = esc_url_raw( $input['thumbnail_fallback'] );

		if( (int) $input['lazyload_maxwidth'] > 100 ) {
			$output['lazyload_maxwidth'] = (int) $input['lazyload_maxwidth'];
		} else {
			$output['lazyload_maxwidth'] = '';
		}

		if( (int) $input['transient_expire_time'] >= 1 ) {
			$output['transient_expire_time'] = (int) $input['transient_expire_time'];
		}

		$output['key'] = sanitize_text_field( $input['key'] );

		if( ( $output['key'] !== $this->options['key'] ) || isset( $input['activate_license'] ) ) {

			$output['key_status'] = $this->api_action( 'activate', $output['key'] );

		} elseif ( isset( $input['deactivate_license'] ) ) {

			$output['key_status'] = $this->api_action( 'deactivate', $output['key'] );

		} else {

			$output['key_status'] = $this->options['key_status'];
		}

		//* Store only the options in the database that are different from the defaults.
		return array_diff_assoc( $output, $this->get_options_defaults() );
	}

	public function filter_thumbnail( $thumbnail, $args ) {

		extract( $args );

		if( !$this->is_pro_mode( $mode ) ) {

			return $thumbnail;
		}

		//* If we have a custom thumbnail from the shortcode, we use it
		if( !empty( $thumbnail ) ) {

			if( is_numeric( $thumbnail ) ) {

				$image_attributes = wp_get_attachment_image_src( $thumbnail, 'full' ); // returns an array

				if( $image_attributes ) {
					return $image_attributes[0];
				}

			} elseif( !filter_var( $thumbnail, FILTER_VALIDATE_URL ) === false ) {

				return $thumbnail;
			}
		}

		$transient_name = "arve_{$args['provider']}_{$id}";

		if( get_transient( $transient_name ) ) {
			return get_transient( $transient_name );
		}

		$result = false;

		$error_message_pattern = __( 'Error retrieving video information from the URL <a href="%s">%s</a> using <code>wp_remote_get()</code>. If opening that URL in your web browser returns anything else than an error page, the problem may be related to your web server and might be something your host administrator can solve.<br />Details: %s', $this->plugin_slug );

		switch ( $args['provider'] ) {

			case 'alugha':

				$request = 'https://api.alugha.com/v1/videos/' . $id;
				$response = wp_remote_get( $request, array( 'sslverify' => false ) );

				if( is_wp_error( $response ) ) {

					$result = new WP_Error( $args['provider'] . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );

				} elseif ( $response['response']['code'] == 404 ) {

					$result = new WP_Error( $args['provider'] . '_thumbnail_retrieval', __( 'The endpoint located at <a href="' . $request . '">' . $request . '</a> returned a 404 error.<br />Details: ' . $response['response']['message'] ) );

				} elseif ( $response['response']['code'] == 403 ) {

					$result = new WP_Error( $args['provider'] . '_thumbnail_retrieval', __( 'The endpoint located at <a href="' . $request . '">' . $request . '</a> returned a 403 error.<br />This can occur when a video has embedding disabled or restricted to certain domains. Try entering API credentials in the provider settings.' ) );

				} else {

					$result = json_decode( $response['body'] );
					$result = $result->background;

					$result = str_replace( 'http://', 'https://', $result );
				}

				break;

			case 'youtube':

				//* Because for youtube playlists the ID consists of 123456&list=123456789 so we extract just the video id here
				preg_match( '/[0-9a-z_\-]+/i', $id, $found );
				$id = $found[0];

				$maxres = 'https://img.youtube.com/vi/' . $id . '/maxresdefault.jpg';

				$response = wp_remote_head( $maxres, array( 'sslverify' => false ) );

				if ( ! is_wp_error( $response ) && $response['response']['code'] == '200' ) {
					$result = $maxres;
				} else {
					$result = 'https://img.youtube.com/vi/' . $id . '/0.jpg';
				}
				break;

			case 'vimeo':

				$request = "https://vimeo.com/api/oembed.json?url=http%3A//vimeo.com/$id";
				$response = wp_remote_get( $request, array( 'sslverify' => false ) );

				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $args['provider'] . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} elseif ( $response['response']['code'] == 404 ) {
					$result = new WP_Error( 'vimeo_thumbnail_retrieval', __( 'The Vimeo endpoint located at <a href="' . $request . '">' . $request . '</a> returned a 404 error.<br />Details: ' . $response['response']['message'] ) );
				} elseif ( $response['response']['code'] == 403 ) {
					$result = new WP_Error( 'vimeo_thumbnail_retrieval', __( 'The Vimeo endpoint located at <a href="' . $request . '">' . $request . '</a> returned a 403 error.<br />This can occur when a video has embedding disabled or restricted to certain domains. Try entering API credentials in the provider settings.' ) );
				} else {
					$result = json_decode( $response['body'] );
					$result = $result->thumbnail_url;

					$result = str_replace( 'http://', 'https://', $result );
				}

				break;

			case 'blip':
			case 'bliptv':

				#if ( $blip_xml = simplexml_load_file( "http://blip.tv/players/episode/$id?skin=rss" ) ) {
				#	$blip_result = $blip_xml->xpath( "/rss/channel/item/media:thumbnail/@url" );
				#	$thumbnail = (string) $blip_result[0]['url'];
				#} else {
				#	return $this->error( __( 'Could not get Blip.tv thumbnail', $this->plugin_slug ) );
				#}
				#break;

				$request = "http://blip.tv/players/episode/$id?skin=rss";

				if ( $blip_xml = simplexml_load_file( $request ) ) {
					$blip_result = $blip_xml->xpath( "/rss/channel/item/media:thumbnail/@url" );
					$result = (string) $blip_result[0]['url'];
				} else {
					$result = new WP_Error( 'arve_get_blip_thumb', sprintf(
						__( 'Could not get Blip.tv thumbnail from <a href="%s">%s</a>.<br>Details: %s', $this->plugin_slug ),
						esc_url( $request ),
						esc_html( $request ),
						$blip_xml
					) );
				}
				break;

			case 'collegehumor':

				$request = "http://www.collegehumor.com/oembed.json?url=http%3A%2F%2Fwww.collegehumor.com%2Fvideo%2F$id";
				$response = wp_remote_get( $request, array( 'sslverify' => false ) );
				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $args['provider'] . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} else {
					$result = json_decode( $response['body'] );
					$result = $result->thumbnail_url;
				}
				break;

			case 'dailymotion':

				$result = 'https://www.dailymotion.com/thumbnail/video/' . $id;
				break;

			//* TODO Check of there are always 720p thumbnails
			case 'dailymotionlist':

				$request = 'https://api.dailymotion.com/playlist/' . $id . '?fields=thumbnail_720_url';

				$response = wp_remote_get( $request, array( 'sslverify' => false ) );

				#d($response);

				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $args['provider'] . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} else {
					$result = json_decode( $response['body'] );
					$result = $result->thumbnail_720_url;

					$result = str_replace( 'http://', 'https://', $result );
				}

				break;

			case 'funnyordie':

				$request = "http://www.funnyordie.com/oembed.json?url=http%3A%2F%2Fwww.funnyordie.com%2Fvideos%2F$id";
				$response = wp_remote_get( $request, array( 'sslverify' => false ) );
				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $args['provider'] . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} else {
					$result = json_decode( $response['body'] );
					$result = $result->thumbnail_url;
				}
				break;

			//* TODO
			case 'TODOmetacafe':

				show($id);

				$request = "http://www.metacafe.com/api/item/$id/";
				$response = wp_remote_get( $request, array( 'sslverify' => false ) );
				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $args['provider'] . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} else {
					$xml = new SimpleXMLElement( $response['body'] );
					$result = $xml->xpath( "/rss/channel/item/media:thumbnail/@url" );
					$result = (string) $result[0]['url'];

					show($xml);
				}
				break;

			case 'mpora':

				$result = 'http://ugc4.mporatrons.com/thumbs/' . $id . '_640x360_0000.jpg';
				break;

			case 'twitch':

				$tw = explode( '/', $id );

				if ( isset( $tw[1] ) && isset( $tw[2] ) && is_numeric( $tw[2] ) ) {

					if ( 'c' === $tw[1] ) {
						$request = 'https://api.twitch.tv/kraken/videos/c' . $tw[2];
					} elseif ( 'b' === $tw[1] ) {
						$request = 'https://api.twitch.tv/kraken/videos/a' . $tw[2];
					} elseif ( 'v' === $tw[1] ) {
						$request = 'https://api.twitch.tv/kraken/videos/v' . $tw[2];
					}
				}
				else {
					$request = 'https://api.twitch.tv/kraken/channels/' . $id;
					$banner = true;
				}

				$response = wp_remote_get( $request, array( 'sslverify' => false ) );

				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $args['provider'] . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} else {
					$result = json_decode( $response['body'] );

					if ( isset( $banner ) )
						$result = $result->video_banner;
					else
						$result = $result->preview;

					$result = str_replace( 'http://', 'https://', $result );
				}
				break;
		}

		if ( empty( $result ) ) {
			$result = false;
		}

		if ( ! empty( $result ) && ! is_wp_error( $result ) ) {
			set_transient( $transient_name, $result, $this->options['transient_expire_time'] );
		}

		//* If there is no thumnail beeing pulled from a provider
		if ( false === $result ) {
			$result = (string) $this->options['thumbnail_fallback'];
		}

		return $result;
	}

	function edd_sl_plugin_updater() {

		if ( 'valid' !== $this->options['key_status'] ) {
			return;
		}

		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater(
			$this->remote_api_url,
			__FILE__,
			array(
				'version' 	=> $this->version,
				'license' 	=> $this->options['key'],
				'item_name' => $this->item_name,
				'author' 	=> 'Nicolas Jonas'
			)
		);
	}

	public function api_action( $action, $key ) {

		if ( !in_array( $action, array( 'activate', 'deactivate', 'check' ) ) ) {
			wp_die( 'invalid action' );
		}

		// Data to send to the API
		$api_params = array(
			'edd_action' => $action . '_license',
			'license'    => sanitize_text_field( $key ),
			'item_name'  => urlencode( $this->item_name ),
			'url'        => $this->remote_api_url,
		);

		$response = wp_remote_post(
            add_query_arg( $api_params, $this->remote_api_url ), array( 'timeout' => 15, 'sslverify' => false )
        );

		// Make sure there are no errors
		if ( is_wp_error( $response ) ) {
			return $response->get_error_message();
		}

		// Tell WordPress to look for updates
		set_site_transient( 'update_plugins', null );

		// Decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if( ! (bool) $license_data->success ) {
			set_transient( 'arve_license_error', $license_data, 1000 );

			if( empty( $license_data->error ) ) {
				return var_export($license_data, true);
			} else {
				return $license_data->error;
			}
		} else {
			delete_transient( 'arve_license_error' );
			return $license_data->license;
		}
	}

	/**
	 * Admin notices for errors
	 *
	 * @access  public
	 * @return  void
	 */
	public function notices() {

		$license_error = get_transient( 'arve_license_error' );

		if( false === $license_error ) {
			return;
		}

		if( ! empty( $license_error->error ) ) {

			switch( $license_error->error ) {

				case 'item_name_mismatch' :

					$message = __( 'This license does not belong to the product you have entered it for.', 'arve-pro' );
					break;

				case 'no_activations_left' :

					$message = __( 'This license does not have any activations left', 'arve-pro' );
					break;

				case 'expired' :

					$message = __( 'This license key is expired. Please renew it.', 'arve-pro' );
					break;

				default :

					$message = sprintf( __( 'There was a problem activating your license key, please try again or contact support. Error code: %s', 'arve-pro' ), $license_error->error );
					break;
			}
		}

		if( ! empty( $message ) ) {

			echo '<div class="error">';
				echo '<p>' . $message . '</p>';
			echo '</div>';

		}

		delete_transient( 'edd_license_error' );

	}
}

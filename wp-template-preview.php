<?php
/**
 * Plugin Name: WP Template Preview
 * Plugin URI: https://obiPlabon.im/wp-template-preview
 * Description: Display custom page or post template preview image to improve UX.
 * Version: 0.0.7
 * Author: obiPlabon
 * Author URI: https://obiPlabon.im
 *
 * Text Domain: wp-template-preview
 * Domain Path: /lang
 *
 * @package WP_Template_Preview
 * @category Core
 * @author obiPlabon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Template_Preview' ) ) :
	
	/**
	 * WP_Template_Preview class defination
	 */
	class WP_Template_Preview {

		/**
		 * Custom tempaltes image preview header map
		 *
		 * @var array
		 */
		protected $header = array();

		/**
		 * Constructor
		 * 
		 * Setup initialization hooks
		 */
		public function __construct() {
			add_action( 'load-post.php', array( $this, 'init' ) );
			add_action( 'load-post-new.php', array( $this, 'init' ) );
		}

		/**
		 * Initialize all the stuffs
		 *
		 * @return void
		 */
		public function init() {
			$this->header = array(
				'PreviewImage' => apply_filters( 'wp_template_preview_header', 'Preview Image' ),
			);

			add_action( 'page_attributes_meta_box_template', array( $this, 'render_frame' ), 10, 2 );
			add_action( 'admin_head', array( $this, 'enqueue_style' ) );
			add_action( 'admin_footer', array( $this, 'enqueue_script' ) );
		}

		/**
		 * Enqueue styles to admin head
		 *
		 * @return void
		 */
		public function enqueue_style() {
			?>
			<style>
				#wp-template-preview {
					margin-top: .5em;
				}
				#wp-template-preview > img {
					max-width: 100%;
					height: auto;
				}
			</style>
			<?php
		}

		/**
		 * Enqueue scripts to admin footer
		 *
		 * @return void
		 */
		public function enqueue_script() {
			?>
			<script>
				jQuery( function( $ ) {
					var $pageTemplate     = $( '#page_template' ),
						$wpTemplatePrview = $( '#wp-template-preview' ),
						$previewImage     = $wpTemplatePrview.find( 'img' ),
						$deletableEmptyP  = $( '#wp-template-preview' ).next(),
						templatePreviews  = $wpTemplatePrview.data( 'template-images' );

					if ( $deletableEmptyP.is( 'p' ) && 0 === $deletableEmptyP.text().length ) {
						$deletableEmptyP.remove();
					}

					$pageTemplate.after( $wpTemplatePrview.detach() );

					$pageTemplate.on( 'change.wpTemplatePreview', function() {
						var template = $( this ).val();
						if ( $.isPlainObject( templatePreviews ) && typeof templatePreviews[ template ] !== undefined ) {
							$previewImage.attr( 'src', templatePreviews[ template ] );
						}
						
						if ( ! $previewImage.prop( 'src' ) ) {
							$wpTemplatePrview.addClass( 'hidden' );
						} else {
							$wpTemplatePrview.removeClass( 'hidden' );
						}
					} );

					$pageTemplate.trigger( 'change.wpTemplatePreview' );
				} );
			</script>
			<?php
		}

		/**
		 * Render preview image frame
		 * 
		 * Render already selected template preview image
		 * and create a frame to load template preivew image.
		 *
		 * @param string $template
		 * @param object $post WP_Post object
		 * @return void
		 */
		public function render_frame( $template, $post ) {
			$images = $this->get_images( $post );
			$images['default'] = ''; // Prevents breaking

			if ( empty( $images ) || ! isset( $images[ $template ] ) ) {
				return;
			}
			?>
			<div id="wp-template-preview" data-template-images="<?php echo esc_js( wp_json_encode( $images ) ); ?>">
				<img src="<?php echo esc_url( $images[ $template ] ); ?>" alt="<?php esc_attr( $template ); ?>">
			</div>
			<?php
		}
		
		/**
		 * Get the list of all preview images
		 * 
		 * Get all preivew images of the current post type
		 *
		 * @param object $post WP_Post object
		 * @return array Images URI with template name
		 */
		protected function get_images( $post ) {
			$images = array();
			foreach ( get_page_templates( $post ) as $template_name => $template_file ) {
				$data = $this->get_header_data( $template_file );

				if ( empty( $data['PreviewImage'] ) ) {
					continue;
				}

				if ( file_exists( $this->get_path() . $data['PreviewImage'] ) ) {
					$images[ $template_file ] =  $this->get_uri() . $data['PreviewImage'];
				}
			}
			return $images;
		}

		/**
		 * Get the list of all preview links
		 * 
		 * Get all preivew links for all templates for current post
		 *
		 * @param object $post WP_Post object
		 * @return array URL
		 * @author oneTarek
		 */
		protected function get_preview_links( $post ) {
			$links = array();
			$preview_link = get_preview_post_link( $post );
			$templates = get_page_templates( $post );
			foreach ( $templates as $template_name => $template_file ) {
				$links[$template_file] = add_query_arg( array("template"=>$template_file), $preview_link );
			}
			return $links;
		}
		
		/**
		 * Get template preview image header data
		 *
		 * @param string $template_file Template file name
		 * @return array Header data
		 */
		protected function get_header_data( $template_file ) {
			return get_file_data( get_template_directory() . DIRECTORY_SEPARATOR . $template_file, $this->header );
		}

		/**
		 * Returns template directory path
		 *
		 * @return string Template directory path
		 */
		protected function get_uri() {
			return get_template_directory_uri() . DIRECTORY_SEPARATOR;
		}

		/**
		 * Returns template directory URI
		 *
		 * @return string Template URI
		 */
		protected function get_path() {
			return get_template_directory() . DIRECTORY_SEPARATOR;
		}
	}

	new WP_Template_Preview();
	
endif;

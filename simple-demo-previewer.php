<?php
/**
 * Plugin Name: Simple Demo Previewer
 * Plugin URI: https://github.com/rsmith4321/simple-demo-previewer
 * Description: Automatically generates a beautiful, full-screen, responsive iframe previewer and a central hub page to show off your website themes.
 * Version: 1.9.2
 * Author: Shoreline Web Designs
 * Author URI: https://shorelinewebdesigns.com/
 * Text Domain: simple-demo-previewer
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// 1. Reusable function to create the Hub Page (Updated to remove deprecated get_page_by_title)
function sdp_create_hub_page() {
	$page_title = 'Example Sites';
	
	$query = new WP_Query( array(
		'post_type'              => 'page',
		'title'                  => $page_title,
		'post_status'            => 'all',
		'posts_per_page'         => 1,
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
	) );
	
	if ( empty( $query->posts ) ) {
		$page_id = wp_insert_post( array(
			'post_title'   => $page_title,
			'post_content' => "\n[demo_sites_hub]\n",
			'post_status'  => 'publish',
			'post_type'    => 'page',
		) );
		return $page_id; 
	}
	return false; 
}

// 2. Activation Hook
register_activation_hook( __FILE__, 'sdp_plugin_activation' );
function sdp_plugin_activation() {
	sdp_create_hub_page();
}

// 3. Register the "Demo Sites" Custom Post Type
add_action( 'init', 'sdp_register_cpt' );
function sdp_register_cpt() {
	register_post_type( 'demo_site', array(
		'labels' => array(
			'name'          => __( 'Demo Sites', 'simple-demo-previewer' ),
			'singular_name' => __( 'Demo Site', 'simple-demo-previewer' ),
			'add_new_item'  => __( 'Add New Demo Site', 'simple-demo-previewer' ),
			'edit_item'     => __( 'Edit Demo Site', 'simple-demo-previewer' ),
			'all_items'     => __( 'All Demo Sites', 'simple-demo-previewer' ),
		),
		'public'      => true,
		'has_archive' => true,
		'supports'    => array( 'title', 'thumbnail', 'page-attributes' ),
		'menu_icon'   => 'dashicons-desktop',
		'rewrite'     => array( 'slug' => 'demo' ),
	));
}

// 4. Create the Admin Submenu for the Dashboard/Setup
add_action( 'admin_menu', 'sdp_add_admin_menu' );
function sdp_add_admin_menu() {
	add_submenu_page(
		'edit.php?post_type=demo_site',
		__( 'Getting Started', 'simple-demo-previewer' ),
		__( 'Getting Started', 'simple-demo-previewer' ),
		'manage_options',
		'sdp-hub-setup',
		'sdp_hub_setup_page_html'
	);
}

// 5. Output the Admin Settings & Instructions Page HTML
function sdp_hub_setup_page_html() {
	if ( ! current_user_can( 'manage_options' ) ) return;
	
	// Handle Hub Generation
	if ( isset( $_POST['sdp_generate_hub'] ) && check_admin_referer( 'sdp_generate_hub_action', 'sdp_generate_hub_nonce' ) ) {
		$result = sdp_create_hub_page();
		if ( $result ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Success! The "Example Sites" hub page has been generated.', 'simple-demo-previewer' ) . ' <a href="' . esc_url( get_edit_post_link( $result ) ) . '">' . esc_html__( 'View Page', 'simple-demo-previewer' ) . '</a></p></div>';
		} else {
			echo '<div class="notice notice-info is-dismissible"><p>' . esc_html__( 'The "Example Sites" page already exists. No new page was created. Check your Pages menu or your Trash.', 'simple-demo-previewer' ) . '</p></div>';
		}
	}

	// Handle Plugin Settings Save (Added strict isset, unslash, and sanitization checks)
	if ( isset( $_POST['sdp_save_settings'] ) && check_admin_referer( 'sdp_save_settings_action', 'sdp_save_settings_nonce' ) ) {
		$disable_seo = isset( $_POST['sdp_disable_seo'] ) ? 'yes' : 'no';
		update_option( 'sdp_disable_seo', $disable_seo );
		
		if ( isset( $_POST['sdp_topbar_bg'] ) ) {
			update_option( 'sdp_topbar_bg', sanitize_hex_color( wp_unslash( $_POST['sdp_topbar_bg'] ) ) );
		}
		if ( isset( $_POST['sdp_topbar_text'] ) ) {
			update_option( 'sdp_topbar_text', sanitize_hex_color( wp_unslash( $_POST['sdp_topbar_text'] ) ) );
		}
		if ( isset( $_POST['sdp_button_bg'] ) ) {
			update_option( 'sdp_button_bg', sanitize_hex_color( wp_unslash( $_POST['sdp_button_bg'] ) ) );
		}
		if ( isset( $_POST['sdp_button_text'] ) ) {
			update_option( 'sdp_button_text', sanitize_hex_color( wp_unslash( $_POST['sdp_button_text'] ) ) );
		}
		
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved successfully!', 'simple-demo-previewer' ) . '</p></div>';
	}
	
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Simple Demo Previewer - Getting Started', 'simple-demo-previewer' ); ?></h1>
		<p class="about-description"><?php esc_html_e( 'Welcome to Simple Demo Previewer! Follow the steps below to set up your professional theme directory and full-screen preview app.', 'simple-demo-previewer' ); ?></p>
		
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				
				<div id="post-body-content">
					<div class="postbox">
						<h2 class="hndle" style="padding: 15px;"><span><?php esc_html_e( '📖 How to Create Your Demo Directory', 'simple-demo-previewer' ); ?></span></h2>
						<div class="inside" style="padding: 0 15px 15px;">
							
							<h3><?php esc_html_e( '1. Add Your Demo Sites', 'simple-demo-previewer' ); ?></h3>
							<p><?php esc_html_e( 'Navigate to ', 'simple-demo-previewer' ); ?><strong><?php esc_html_e( 'Demo Sites > Add New Demo Site', 'simple-demo-previewer' ); ?></strong>.</p>
							<ul style="list-style-type: disc; margin-left: 20px;">
								<li><strong><?php esc_html_e( 'Title:', 'simple-demo-previewer' ); ?></strong> <?php esc_html_e( 'Enter the name of your template or project.', 'simple-demo-previewer' ); ?></li>
								<li><strong><?php esc_html_e( 'URL:', 'simple-demo-previewer' ); ?></strong> <?php esc_html_e( 'Scroll down to the "Demo Site Settings" box and paste the live URL of the site you want to display in the previewer.', 'simple-demo-previewer' ); ?></li>
							</ul>

							<hr style="margin: 20px 0;">

							<h3><?php esc_html_e( '2. Add Beautiful Thumbnails', 'simple-demo-previewer' ); ?></h3>
							<p><?php esc_html_e( 'To make your directory grid look professional, upload a screenshot of your site layout.', 'simple-demo-previewer' ); ?></p>
							<ul style="list-style-type: disc; margin-left: 20px;">
								<li><?php esc_html_e( 'While editing a Demo Site, look in the right-hand sidebar for the ', 'simple-demo-previewer' ); ?><strong><?php esc_html_e( 'Featured Image', 'simple-demo-previewer' ); ?></strong> <?php esc_html_e( 'box.', 'simple-demo-previewer' ); ?></li>
								<li><?php esc_html_e( 'Upload a high-quality screenshot. The plugin will automatically crop and optimize it to a perfect 16:9 ratio for your grid cards.', 'simple-demo-previewer' ); ?></li>
							</ul>

							<hr style="margin: 20px 0;">

							<h3><?php esc_html_e( '3. View Your Hub Page', 'simple-demo-previewer' ); ?></h3>
							<p><?php esc_html_e( 'The plugin automatically generated a page called ', 'simple-demo-previewer' ); ?><strong><?php esc_html_e( '"Example Sites"', 'simple-demo-previewer' ); ?></strong> <?php esc_html_e( 'upon activation. You can find this in your standard WordPress ', 'simple-demo-previewer' ); ?><strong><?php esc_html_e( 'Pages', 'simple-demo-previewer' ); ?></strong> <?php esc_html_e( 'menu.', 'simple-demo-previewer' ); ?></p>
							<p><?php esc_html_e( 'If you wish to display your directory on a different page, simply paste this shortcode anywhere on your site:', 'simple-demo-previewer' ); ?></p>
							<p><code style="font-size: 16px; padding: 5px 10px; display: inline-block;">[demo_sites_hub]</code></p>
							
							<hr style="margin: 20px 0;">
							
							<h3><?php esc_html_e( '4. Custom Sorting (Menu Order)', 'simple-demo-previewer' ); ?></h3>
							<p><?php esc_html_e( 'To change the order of your sites, look for the "Post Attributes" box on the right-hand sidebar when editing a Demo Site.', 'simple-demo-previewer' ); ?></p>
							<ul style="list-style-type: disc; margin-left: 20px;">
								<li><?php esc_html_e( 'Lower numbers (like 0 or 1) appear first.', 'simple-demo-previewer' ); ?></li>
								<li><?php esc_html_e( 'For drag-and-drop sorting, we recommend the free "Simple Custom Post Order" plugin.', 'simple-demo-previewer' ); ?></li>
							</ul>

							<hr style="margin: 20px 0;">

							<h3><?php esc_html_e( '5. SEO Best Practices', 'simple-demo-previewer' ); ?></h3>
							<p><?php esc_html_e( 'By default, this plugin hides the SEO meta boxes on Demo Sites and adds a "noindex" tag. Why?', 'simple-demo-previewer' ); ?></p>
							<ul style="list-style-type: disc; margin-left: 20px;">
								<li><?php esc_html_e( 'Since the previewer loads your actual websites inside an iframe, allowing search engines to index these preview pages can cause ', 'simple-demo-previewer' ); ?><strong><?php esc_html_e( 'duplicate content', 'simple-demo-previewer' ); ?></strong><?php esc_html_e( ' penalties against your main sites.', 'simple-demo-previewer' ); ?></li>
								<li><?php esc_html_e( 'Keeping them hidden ensures all SEO value stays where it belongs: on your actual live sites. You can toggle this off in the Plugin Settings if needed.', 'simple-demo-previewer' ); ?></li>
							</ul>
						</div>
					</div>
				</div>

				<div id="postbox-container-1" class="postbox-container">
					
					<div class="postbox">
						<h2 class="hndle" style="padding: 15px;"><span><?php esc_html_e( '⚙️ Plugin Settings', 'simple-demo-previewer' ); ?></span></h2>
						<div class="inside" style="padding: 0 15px 15px;">
							<form method="post" action="">
								<?php wp_nonce_field( 'sdp_save_settings_action', 'sdp_save_settings_nonce' ); ?>
								<input type="hidden" name="sdp_save_settings" value="1">
								
								<p style="margin-top:0;"><strong><?php esc_html_e( 'SEO Preferences', 'simple-demo-previewer' ); ?></strong></p>
								<label style="display: block; margin-bottom: 20px;">
									<input type="checkbox" name="sdp_disable_seo" value="yes" <?php checked( get_option( 'sdp_disable_seo', 'yes' ), 'yes' ); ?> />
									<?php esc_html_e( 'Disable SEO plugins and No Index Demo Pages', 'simple-demo-previewer' ); ?>
								</label>

								<hr style="margin: 20px 0;">
								
								<p style="margin-top:0;"><strong><?php esc_html_e( 'Previewer Top Bar', 'simple-demo-previewer' ); ?></strong></p>
								<div style="display: flex; align-items: center; margin-bottom: 10px;">
									<input type="color" id="sdp_topbar_bg" name="sdp_topbar_bg" value="<?php echo esc_attr( get_option( 'sdp_topbar_bg', '#ffffff' ) ); ?>" style="margin-right: 10px;" />
									<label for="sdp_topbar_bg"><?php esc_html_e( 'Background Color', 'simple-demo-previewer' ); ?></label>
								</div>
								<div style="display: flex; align-items: center; margin-bottom: 20px;">
									<input type="color" id="sdp_topbar_text" name="sdp_topbar_text" value="<?php echo esc_attr( get_option( 'sdp_topbar_text', '#333333' ) ); ?>" style="margin-right: 10px;" />
									<label for="sdp_topbar_text"><?php esc_html_e( 'Icon & Text Color', 'simple-demo-previewer' ); ?></label>
								</div>

								<p><strong><?php esc_html_e( 'Grid Buttons', 'simple-demo-previewer' ); ?></strong></p>
								<div style="display: flex; align-items: center; margin-bottom: 10px;">
									<input type="color" id="sdp_button_bg" name="sdp_button_bg" value="<?php echo esc_attr( get_option( 'sdp_button_bg', '#2563eb' ) ); ?>" style="margin-right: 10px;" />
									<label for="sdp_button_bg"><?php esc_html_e( 'Button Color', 'simple-demo-previewer' ); ?></label>
								</div>
								<div style="display: flex; align-items: center; margin-bottom: 20px;">
									<input type="color" id="sdp_button_text" name="sdp_button_text" value="<?php echo esc_attr( get_option( 'sdp_button_text', '#ffffff' ) ); ?>" style="margin-right: 10px;" />
									<label for="sdp_button_text"><?php esc_html_e( 'Button Text', 'simple-demo-previewer' ); ?></label>
								</div>

								<?php submit_button( __( 'Save Settings', 'simple-demo-previewer' ), 'primary', 'submit', false ); ?>
							</form>
						</div>
					</div>

					<div class="postbox">
						<h2 class="hndle" style="padding: 15px;"><span><?php esc_html_e( '🔄 Page Setup Tool', 'simple-demo-previewer' ); ?></span></h2>
						<div class="inside" style="padding: 0 15px 15px;">
							<p><?php esc_html_e( 'Did you accidentally delete your "Example Sites" directory page? Click the button below to instantly regenerate it with the correct shortcode.', 'simple-demo-previewer' ); ?></p>
							<form method="post" action="">
								<?php wp_nonce_field( 'sdp_generate_hub_action', 'sdp_generate_hub_nonce' ); ?>
								<input type="hidden" name="sdp_generate_hub" value="1">
								<?php submit_button( __( 'Generate "Example Sites" Page', 'simple-demo-previewer' ), 'secondary' ); ?>
							</form>
						</div>
					</div>

					<div class="postbox">
						<h2 class="hndle" style="padding: 15px;"><span><?php esc_html_e( '👋 About the Developer', 'simple-demo-previewer' ); ?></span></h2>
						<div class="inside" style="padding: 0 15px 15px;">
							<p><?php esc_html_e( 'Simple Demo Previewer is proudly built and maintained by the team at Shoreline Web Designs. We specialize in high-performance web solutions and premium designs.', 'simple-demo-previewer' ); ?></p>
							<p>
								<a href="https://shorelinewebdesigns.com/" target="_blank" class="button button-secondary" style="width: 100%; text-align: center;">
									<?php esc_html_e( 'Visit Shoreline Web Designs', 'simple-demo-previewer' ); ?>
								</a>
							</p>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>
	<?php
}

// 6. Create the Custom Field (Meta Box) for the URL
add_action( 'add_meta_boxes', 'sdp_add_meta_box' );
function sdp_add_meta_box() {
	add_meta_box( 'sdp_url_meta', __( 'Demo Site Settings', 'simple-demo-previewer' ), 'sdp_meta_box_html', 'demo_site', 'normal', 'high' );
}

function sdp_meta_box_html( $post ) {
	$url = get_post_meta( $post->ID, '_sdp_demo_url', true );
	wp_nonce_field( 'sdp_save_meta', 'sdp_meta_nonce' );
	?>
	<p>
		<label for="sdp_demo_url"><strong><?php esc_html_e( 'Demo Website URL:', 'simple-demo-previewer' ); ?></strong></label><br>
		<input type="url" id="sdp_demo_url" name="sdp_demo_url" value="<?php echo esc_url( $url ); ?>" style="width:100%; max-width: 600px; margin-top:5px;" placeholder="https://example.com/my-demo" required />
		<br><small><?php esc_html_e( 'Enter the full URL of the site you want to display in the previewer.', 'simple-demo-previewer' ); ?></small>
	</p>
	<?php
}

// 7. Save the Custom Field Data (Updated with wp_unslash and sanitize_text_field)
add_action( 'save_post', 'sdp_save_meta_box' );
function sdp_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['sdp_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sdp_meta_nonce'] ) ), 'sdp_save_meta' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['sdp_demo_url'] ) ) {
		update_post_meta( $post_id, '_sdp_demo_url', esc_url_raw( wp_unslash( $_POST['sdp_demo_url'] ) ) );
	}
}

// 8. Override the Page Template to show the Fullscreen Iframe
add_action( 'template_redirect', 'sdp_render_preview_page' );
function sdp_render_preview_page() {
	if ( is_singular( 'demo_site' ) ) {
		$post_id = get_the_ID();
		$title = get_the_title();
		$demo_url = get_post_meta( $post_id, '_sdp_demo_url', true );
		
		if ( empty( $demo_url ) ) { $demo_url = home_url(); }
		$close_url = home_url( '/example-sites/' );

		$all_demos = get_posts( array( 'post_type' => 'demo_site', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
		$site_name = get_bloginfo( 'name' );

		$topbar_bg = get_option( 'sdp_topbar_bg', '#ffffff' );
		$topbar_text = get_option( 'sdp_topbar_text', '#333333' );
		$disable_seo = get_option( 'sdp_disable_seo', 'yes' );

		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			
			<?php // Dynamically inject the NoIndex tag based on user settings ?>
			<?php if ( $disable_seo === 'yes' ) : ?>
				<meta name="robots" content="noindex, nofollow" />
			<?php endif; ?>

			<title><?php echo esc_html( $title . ' - ' . $site_name ); ?></title>
			<style>
				body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; background: #e5e7eb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
				#stp-previewer-wrapper { display: flex; flex-direction: column; height: 100vh; width: 100vw; }
				.stp-topbar { height: 60px; background: <?php echo esc_attr( $topbar_bg ); ?>; display: flex; justify-content: space-between; align-items: center; padding: 0 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); color: <?php echo esc_attr( $topbar_text ); ?>; flex-shrink: 0; }
				.stp-demo-dropdown { padding: 8px 12px; font-size: 15px; font-weight: 600; color: #374151; background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; outline: none; transition: border-color 0.2s, box-shadow 0.2s; max-width: 250px; }
				.stp-demo-dropdown:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
				.stp-device-toggles { display: flex; gap: 15px; }
				.stp-btn { background: none; border: none; cursor: pointer; color: <?php echo esc_attr( $topbar_text ); ?>; opacity: 0.5; padding: 5px; transition: opacity 0.2s; }
				.stp-btn:hover, .stp-btn.active { opacity: 1; }
				.stp-close a { color: <?php echo esc_attr( $topbar_text ); ?>; opacity: 0.7; text-decoration: none; display: flex; align-items: center; transition: opacity 0.2s; }
				.stp-close a:hover { opacity: 1; color: #ef4444; }
				.stp-iframe-container { flex-grow: 1; display: flex; justify-content: center; align-items: flex-start; overflow: hidden; }
				#stp-iframe { width: 100%; height: 100%; background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: none; }
			</style>
		</head>
		<body>
			<div id="stp-previewer-wrapper">
				<div class="stp-topbar">
					<div class="stp-title">
						<select id="stp-demo-selector" class="stp-demo-dropdown" aria-label="<?php esc_attr_e( 'Select a Demo Site', 'simple-demo-previewer' ); ?>">
							<?php foreach ( $all_demos as $demo ) : ?>
								<option value="<?php echo esc_url( get_permalink( $demo->ID ) ); ?>" <?php selected( $post_id, $demo->ID ); ?>>
									<?php echo esc_html( $demo->post_title ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					
					<div class="stp-device-toggles">
						<button class="stp-btn active" data-width="100%" aria-label="<?php esc_attr_e( 'Desktop View', 'simple-demo-previewer' ); ?>" title="<?php esc_attr_e( 'Desktop View', 'simple-demo-previewer' ); ?>">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
						</button>
						<button class="stp-btn" data-width="768px" aria-label="<?php esc_attr_e( 'Tablet View', 'simple-demo-previewer' ); ?>" title="<?php esc_attr_e( 'Tablet View', 'simple-demo-previewer' ); ?>">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
						</button>
						<button class="stp-btn" data-width="375px" aria-label="<?php esc_attr_e( 'Mobile View', 'simple-demo-previewer' ); ?>" title="<?php esc_attr_e( 'Mobile View', 'simple-demo-previewer' ); ?>">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
						</button>
					</div>

					<div class="stp-close">
						<a href="<?php echo esc_url( $close_url ); ?>" title="<?php esc_attr_e( 'Close Preview', 'simple-demo-previewer' ); ?>" aria-label="<?php esc_attr_e( 'Close Preview', 'simple-demo-previewer' ); ?>">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
						</a>
					</div>
				</div>

				<div class="stp-iframe-container">
					<iframe id="stp-iframe" src="<?php echo esc_url( $demo_url ); ?>" title="<?php echo esc_attr( $title ); ?>"></iframe>
				</div>
			</div>

			<script>
				document.addEventListener('DOMContentLoaded', function() {
					const buttons = document.querySelectorAll('.stp-btn');
					const iframe = document.getElementById('stp-iframe');

					buttons.forEach(button => {
						button.addEventListener('click', function() {
							buttons.forEach(btn => btn.classList.remove('active'));
							this.classList.add('active');
							iframe.style.width = this.getAttribute('data-width');
						});
					});

					const demoSelector = document.getElementById('stp-demo-selector');
					if (demoSelector) {
						demoSelector.addEventListener('change', function() {
							window.location.href = this.value; 
						});
					}
				});
			</script>
		</body>
		</html>
		<?php
		exit;
	}
}

// 9. Clean up Admin Interface (Conditionally Remove Heavy SEO Meta Boxes)
add_action( 'add_meta_boxes', 'sdp_remove_seo_meta_boxes', 99 );
function sdp_remove_seo_meta_boxes() {
	if ( get_option( 'sdp_disable_seo', 'yes' ) === 'yes' ) {
		// Remove Yoast SEO
		remove_meta_box( 'wpseo_meta', 'demo_site', 'normal' );
		// Remove All in One SEO (AIOSEO)
		remove_meta_box( 'aiosp', 'demo_site', 'normal' );
	}
}

// Disable Rank Math safely (Conditionally)
add_filter( 'rank_math/excluded_post_types', function( $post_types ) {
	if ( get_option( 'sdp_disable_seo', 'yes' ) === 'yes' ) {
		$post_types['demo_site'] = 'demo_site';
	}
	return $post_types;
});

// 10. Create the beautiful "Hub" Shortcode with Featured Images & Dynamic Colors
add_shortcode( 'demo_sites_hub', 'sdp_hub_shortcode' );
function sdp_hub_shortcode() {
	$demos = get_posts( array(
		'post_type'      => 'demo_site',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'menu_order title',
		'order'          => 'ASC'
	));

	if ( empty( $demos ) ) {
		return '<p>' . esc_html__( 'You have not published any demo sites yet.', 'simple-demo-previewer' ) . '</p>';
	}

	$button_bg = get_option( 'sdp_button_bg', '#2563eb' );
	$button_text = get_option( 'sdp_button_text', '#ffffff' );

	ob_start();
	?>
	<div class="sdp-hub-wrapper" style="max-width: 1200px; margin: 0 auto; padding: 20px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;">
		<div class="sdp-hub-header" style="text-align: center; margin-bottom: 50px;">
			<h2 style="margin-bottom: 20px; font-size: 28px; color: #1f2937;"><?php esc_html_e( 'Select a demo site below', 'simple-demo-previewer' ); ?></h2>
			<select class="sdp-hub-dropdown" onchange="if(this.value) window.location.href=this.value;" style="padding: 12px 20px; font-size: 16px; font-weight: 500; color: #374151; background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 8px; cursor: pointer; outline: none; transition: all 0.2s; max-width: 350px; width: 100%; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
				<option value=""><?php esc_html_e( '-- Choose a Design --', 'simple-demo-previewer' ); ?></option>
				<?php foreach ( $demos as $demo ) : ?>
					<option value="<?php echo esc_url( get_permalink( $demo->ID ) ); ?>">
						<?php echo esc_html( $demo->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="sdp-demo-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
			<?php foreach ( $demos as $demo ) : ?>
				<div class="sdp-demo-card">
					
					<?php if ( has_post_thumbnail( $demo->ID ) ) : ?>
						<div class="sdp-demo-thumbnail">
							<a href="<?php echo esc_url( get_permalink( $demo->ID ) ); ?>">
								<?php echo get_the_post_thumbnail( $demo->ID, 'medium_large', array( 'alt' => esc_attr( $demo->post_title ) ) ); ?>
							</a>
						</div>
					<?php endif; ?>

					<h3 class="sdp-demo-title"><?php echo esc_html( $demo->post_title ); ?></h3>
					
					<a href="<?php echo esc_url( get_permalink( $demo->ID ) ); ?>" class="sdp-demo-btn">
						<?php esc_html_e( 'Launch Preview', 'simple-demo-previewer' ); ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<style>
		.sdp-hub-dropdown:hover { border-color: #9ca3af; }
		.sdp-hub-dropdown:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
		.sdp-demo-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: transform 0.2s ease, box-shadow 0.2s ease; display: flex; flex-direction: column; justify-content: space-between; min-height: 200px; }
		.sdp-demo-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border-color: #cbd5e1; }
		.sdp-demo-thumbnail { margin-bottom: 20px; overflow: hidden; border-radius: 8px; border: 1px solid #f3f4f6; }
		.sdp-demo-thumbnail img { width: 100%; height: auto; aspect-ratio: 16/9; object-fit: cover; display: block; transition: transform 0.3s ease; }
		.sdp-demo-card:hover .sdp-demo-thumbnail img { transform: scale(1.05); }
		.sdp-demo-title { margin: 0 0 20px 0; font-size: 20px; color: #1f2937; font-weight: 600; }
		.sdp-demo-btn { display: inline-block; background: <?php echo esc_attr( $button_bg ); ?> !important; color: <?php echo esc_attr( $button_text ); ?> !important; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 500; transition: filter 0.2s ease, transform 0.1s ease; }
		.sdp-demo-btn:hover { filter: brightness(0.85); color: <?php echo esc_attr( $button_text ); ?>; }
		.sdp-demo-btn:active { transform: scale(0.98); }
	</style>
	<?php
	return ob_get_clean();
}

// 11. Add Settings Link directly to the Plugins Page
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'sdp_add_settings_link' );
function sdp_add_settings_link( $links ) {
	$settings_link = '<a href="' . admin_url( 'edit.php?post_type=demo_site&page=sdp-hub-setup' ) . '">' . __( 'Settings', 'simple-demo-previewer' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

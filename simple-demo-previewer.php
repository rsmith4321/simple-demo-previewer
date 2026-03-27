<?php
/**
 * Plugin Name: Simple Demo Previewer
 * Plugin URI: https://github.com/rsmith4321/simple-demo-previewer
 * Description: Automatically generates a beautiful, full-screen, responsive iframe previewer and a central hub page to show off your website themes. Includes drag-and-drop ordering.
 * Version: 2.1
 * Author: Shoreline Web Designs
 * Author URI: https://shorelinewebdesigns.com/
 * Text Domain: simple-demo-previewer
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// -----------------------------------------------------------------------------
// 1. REUSABLE FUNCTION TO CREATE THE HUB PAGE
// -----------------------------------------------------------------------------
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

// -----------------------------------------------------------------------------
// 2. ACTIVATION HOOK
// -----------------------------------------------------------------------------
register_activation_hook( __FILE__, 'sdp_plugin_activation' );
function sdp_plugin_activation() {
	sdp_create_hub_page();
}

// -----------------------------------------------------------------------------
// 3. REGISTER THE "DEMO SITES" CUSTOM POST TYPE
// -----------------------------------------------------------------------------
add_action( 'init', 'sdp_register_cpt' );
function sdp_register_cpt() {
	register_post_type( 'demo_site', array(
		'labels' => array(
			'name'                  => __( 'Demo Sites', 'simple-demo-previewer' ),
			'singular_name'         => __( 'Demo Site', 'simple-demo-previewer' ),
			'add_new_item'          => __( 'Add New Demo Site', 'simple-demo-previewer' ),
			'edit_item'             => __( 'Edit Demo Site', 'simple-demo-previewer' ),
			'all_items'             => __( 'All Demo Sites', 'simple-demo-previewer' ),
			'featured_image'        => __( 'Website Thumbnail', 'simple-demo-previewer' ),
			'set_featured_image'    => __( 'Set Website Thumbnail', 'simple-demo-previewer' ),
			'remove_featured_image' => __( 'Remove Website Thumbnail', 'simple-demo-previewer' ),
			'use_featured_image'    => __( 'Use as Website Thumbnail', 'simple-demo-previewer' ),
		),
		'public'      => true,
		'has_archive' => true,
		'supports'    => array( 'title', 'thumbnail', 'page-attributes' ),
		'menu_icon'   => 'dashicons-desktop',
		'rewrite'     => array( 'slug' => 'examples' ),
	));
}
// -----------------------------------------------------------------------------
// 3.5 REGISTER DEMO CATEGORIES (TAXONOMY)
// -----------------------------------------------------------------------------
add_action( 'init', 'sdp_register_taxonomy' );
function sdp_register_taxonomy() {
	register_taxonomy( 'demo_type', array( 'demo_site' ), array(
		'labels' => array(
			'name'              => __( 'Categories', 'simple-demo-previewer' ),
			'singular_name'     => __( 'Category', 'simple-demo-previewer' ),
			'menu_name'         => __( 'Categories', 'simple-demo-previewer' ),
			'add_new_item'      => __( 'Add New Category', 'simple-demo-previewer' ),
		),
		'hierarchical'      => true, // Acts like standard WordPress categories (checkboxes)
		'show_ui'           => true,
		'show_admin_column' => true, // Adds a helpful column in your wp-admin list!
		'rewrite'           => array( 'slug' => 'demo-category' ),
	));
}
// -----------------------------------------------------------------------------
// 3.6 ADD CUSTOM "ORDER" FIELD TO CATEGORIES
// -----------------------------------------------------------------------------

// 1. Add field to "Add New Category" screen
add_action( 'demo_type_add_form_fields', 'sdp_add_category_order_field' );
function sdp_add_category_order_field() {
	?>
	<div class="form-field">
		<label for="term_order"><?php esc_html_e( 'Display Order', 'simple-demo-previewer' ); ?></label>
		<input type="number" name="term_order" id="term_order" value="0" />
		<p class="description"><?php esc_html_e( 'Enter a number to sort this category in the preview dropdown (e.g., 1 for first, 2 for second).', 'simple-demo-previewer' ); ?></p>
	</div>
	<?php
}

// 2. Add field to "Edit Category" screen
add_action( 'demo_type_edit_form_fields', 'sdp_edit_category_order_field' );
function sdp_edit_category_order_field( $term ) {
	$order = get_term_meta( $term->term_id, 'term_order', true );
	if ( $order === '' ) { $order = 0; }
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_order"><?php esc_html_e( 'Display Order', 'simple-demo-previewer' ); ?></label></th>
		<td>
			<input type="number" name="term_order" id="term_order" value="<?php echo esc_attr( $order ); ?>" />
			<p class="description"><?php esc_html_e( 'Enter a number to sort this category in the preview dropdown (e.g., 1 for first, 2 for second).', 'simple-demo-previewer' ); ?></p>
		</td>
	</tr>
	<?php
}

// 3. Save the custom field data
add_action( 'saved_demo_type', 'sdp_save_category_order' );
add_action( 'created_demo_type', 'sdp_save_category_order' );
function sdp_save_category_order( $term_id ) {
	if ( isset( $_POST['term_order'] ) ) {
		update_term_meta( $term_id, 'term_order', absint( $_POST['term_order'] ) );
	}
}

// 4. Add "Order" column to the Categories list table
add_filter( 'manage_edit-demo_type_columns', 'sdp_add_category_order_column' );
function sdp_add_category_order_column( $columns ) {
	$columns['term_order'] = __( 'Order', 'simple-demo-previewer' );
	return $columns;
}

add_filter( 'manage_demo_type_custom_column', 'sdp_fill_category_order_column', 10, 3 );
function sdp_fill_category_order_column( $content, $column_name, $term_id ) {
	if ( $column_name === 'term_order' ) {
		$order = get_term_meta( $term_id, 'term_order', true );
		$content = ( $order !== '' ) ? esc_html( $order ) : '0';
	}
	return $content;
}

// -----------------------------------------------------------------------------
// 4. CREATE THE ADMIN SUBMENU
// -----------------------------------------------------------------------------
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

// -----------------------------------------------------------------------------
// 5. ADMIN SETTINGS & INSTRUCTIONS PAGE HTML
// -----------------------------------------------------------------------------
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

	// Handle Plugin Settings Save
	if ( isset( $_POST['sdp_save_settings'] ) && check_admin_referer( 'sdp_save_settings_action', 'sdp_save_settings_nonce' ) ) {
		$disable_seo = isset( $_POST['sdp_disable_seo'] ) ? 'yes' : 'no';
		update_option( 'sdp_disable_seo', $disable_seo );
		
		if ( isset( $_POST['sdp_topbar_bg'] ) ) update_option( 'sdp_topbar_bg', sanitize_hex_color( wp_unslash( $_POST['sdp_topbar_bg'] ) ) );
		if ( isset( $_POST['sdp_topbar_text'] ) ) update_option( 'sdp_topbar_text', sanitize_hex_color( wp_unslash( $_POST['sdp_topbar_text'] ) ) );
		if ( isset( $_POST['sdp_button_bg'] ) ) update_option( 'sdp_button_bg', sanitize_hex_color( wp_unslash( $_POST['sdp_button_bg'] ) ) );
		if ( isset( $_POST['sdp_button_text'] ) ) update_option( 'sdp_button_text', sanitize_hex_color( wp_unslash( $_POST['sdp_button_text'] ) ) );
		
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
							
							<h3><?php esc_html_e( '1. Add & Categorize Your Demo Sites', 'simple-demo-previewer' ); ?></h3>
							<p><?php esc_html_e( 'Navigate to ', 'simple-demo-previewer' ); ?><strong><?php esc_html_e( 'Demo Sites > Add New Demo Site', 'simple-demo-previewer' ); ?></strong>.</p>
							<ul style="list-style-type: disc; margin-left: 20px;">
								<li><strong><?php esc_html_e( 'Title:', 'simple-demo-previewer' ); ?></strong> <?php esc_html_e( 'Enter the name of your template or project.', 'simple-demo-previewer' ); ?></li>
								<li><strong><?php esc_html_e( 'URL:', 'simple-demo-previewer' ); ?></strong> <?php esc_html_e( 'Scroll down to the "Demo Site Settings" box and paste the live URL of the site you want to display in the previewer.', 'simple-demo-previewer' ); ?></li>
								<li><strong><?php esc_html_e( 'Category:', 'simple-demo-previewer' ); ?></strong> <?php esc_html_e( 'Use the Categories box on the right sidebar to separate your "Real Sites" from your "Templates".', 'simple-demo-previewer' ); ?></li>
							</ul>

							<hr style="margin: 20px 0;">

							<h3><?php esc_html_e( '2. Add Beautiful Thumbnails', 'simple-demo-previewer' ); ?></h3>
							<p><?php esc_html_e( 'To make your directory grid look professional, upload a screenshot of your site layout.', 'simple-demo-previewer' ); ?></p>
							<ul style="list-style-type: disc; margin-left: 20px;">
								<li><?php esc_html_e( 'While editing a Demo Site, look right below the URL setting for the ', 'simple-demo-previewer' ); ?><strong><?php esc_html_e( 'Website Thumbnail', 'simple-demo-previewer' ); ?></strong> <?php esc_html_e( 'box.', 'simple-demo-previewer' ); ?></li>
								<li><?php esc_html_e( 'Upload a high-quality image in the website thumbnail section. The plugin will automatically crop and optimize it to a perfect 16:9 ratio for your grid cards.', 'simple-demo-previewer' ); ?></li>
							</ul>

							<hr style="margin: 20px 0;">

							<h3><?php esc_html_e( '3. Display Your Hub Pages', 'simple-demo-previewer' ); ?></h3>
							<p><?php esc_html_e( 'The plugin automatically generated a page called ', 'simple-demo-previewer' ); ?><strong><?php esc_html_e( '"Example Sites"', 'simple-demo-previewer' ); ?></strong> <?php esc_html_e( 'upon activation. You can find this in your standard WordPress Pages menu.', 'simple-demo-previewer' ); ?></p>
							
							<p><strong><?php esc_html_e( 'Show All Sites:', 'simple-demo-previewer' ); ?></strong><br>
							<?php esc_html_e( 'To display your entire directory, use the standard shortcode:', 'simple-demo-previewer' ); ?></p>
							<p><code style="font-size: 16px; padding: 5px 10px; display: inline-block;">[demo_sites_hub]</code></p>
							
							<p style="margin-top: 15px;"><strong><?php esc_html_e( 'Filter By Category:', 'simple-demo-previewer' ); ?></strong><br>
							<?php esc_html_e( 'To separate your grids, you can use the shortcode with a category slug. For example, to show only your "Real Sites" category:', 'simple-demo-previewer' ); ?></p>
							<p><code style="font-size: 16px; padding: 5px 10px; display: inline-block;">[demo_sites_hub category="real-sites"]</code></p>
							
							<hr style="margin: 20px 0;">
							
							<h3><?php esc_html_e( '4. Custom Sorting (Drag and Drop)', 'simple-demo-previewer' ); ?></h3>
							<p><?php esc_html_e( 'To change the order of your sites, simply go to your "All Demo Sites" page, click on any row, and drag it up or down. The order saves automatically and matches your live grid exactly.', 'simple-demo-previewer' ); ?></p>

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
// -----------------------------------------------------------------------------
// 6. RE-POSITION THE THUMBNAIL (FEATURED IMAGE) META BOX
// -----------------------------------------------------------------------------
add_action( 'do_meta_boxes', 'sdp_move_thumbnail_meta_box' );
function sdp_move_thumbnail_meta_box() {
	// Remove it from the right sidebar
	remove_meta_box( 'postimagediv', 'demo_site', 'side' );
	// Add it to the main content area (normal) below the URL settings
	add_meta_box( 'postimagediv', __( 'Website Thumbnail', 'simple-demo-previewer' ), 'post_thumbnail_meta_box', 'demo_site', 'normal', 'default' );
}

// -----------------------------------------------------------------------------
// 7. CREATE THE CUSTOM FIELD (META BOX) FOR THE URL
// -----------------------------------------------------------------------------
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
	</p>
	<?php
}

// -----------------------------------------------------------------------------
// 8. SAVE THE CUSTOM FIELD DATA
// -----------------------------------------------------------------------------
add_action( 'save_post', 'sdp_save_meta_box' );
function sdp_save_meta_box( $post_id ) {
	if ( ! isset( $_POST['sdp_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sdp_meta_nonce'] ) ), 'sdp_save_meta' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['sdp_demo_url'] ) ) {
		update_post_meta( $post_id, '_sdp_demo_url', esc_url_raw( wp_unslash( $_POST['sdp_demo_url'] ) ) );
	}
}

// -----------------------------------------------------------------------------
// 9. OVERRIDE THE PAGE TEMPLATE TO SHOW THE FULLSCREEN IFRAME
// -----------------------------------------------------------------------------
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

		// 1. Get all categories and sort them by our custom "Display Order" number
		$terms = get_terms( array( 'taxonomy' => 'demo_type', 'hide_empty' => false ) );
		$ordered_terms = array();
		
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$order = get_term_meta( $term->term_id, 'term_order', true );
				$ordered_terms[] = array(
					'name'  => $term->name,
					'order' => $order !== '' ? (int) $order : 999 // Unordered categories go to the bottom
				);
			}
			// Sort the array by the order number
			usort( $ordered_terms, function($a, $b) {
				return $a['order'] <=> $b['order'];
			});
		}

		// 2. Prepare our empty groups in the correct order
		$grouped_demos = array();
		foreach ( $ordered_terms as $t ) {
			$grouped_demos[ $t['name'] ] = array();
		}
		$grouped_demos['Other'] = array(); // Fallback for uncategorized sites

		// 3. Loop through all sites and drop them into their assigned group
		foreach ( $all_demos as $demo ) {
			$demo_terms = get_the_terms( $demo->ID, 'demo_type' );
			if ( $demo_terms && ! is_wp_error( $demo_terms ) ) {
				$group_name = $demo_terms[0]->name; // Grab the first category assigned to the site
				if ( isset( $grouped_demos[ $group_name ] ) ) {
					$grouped_demos[ $group_name ][] = $demo;
				} else {
					$grouped_demos['Other'][] = $demo;
				}
			} else {
				$grouped_demos['Other'][] = $demo;
			}
		}

		// 4. Remove any groups that don't have sites in them to keep the dropdown clean
		foreach ( $grouped_demos as $key => $group ) {
			if ( empty( $group ) ) {
				unset( $grouped_demos[$key] );
			}
		}

		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<?php if ( $disable_seo === 'yes' ) : ?>
				<meta name="robots" content="noindex, nofollow" />
			<?php endif; ?>
			<title><?php echo esc_html( $title . ' - ' . $site_name ); ?></title>
			<style>
				body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; background: #e5e7eb; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
				#stp-previewer-wrapper { display: flex; flex-direction: column; height: 100vh; width: 100vw; }
				.stp-topbar { height: 60px; background: <?php echo esc_attr( $topbar_bg ); ?>; display: flex; justify-content: space-between; align-items: center; padding: 0 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); color: <?php echo esc_attr( $topbar_text ); ?>; flex-shrink: 0; }
				.stp-demo-dropdown { padding: 8px 12px; font-size: 15px; font-weight: 600; color: #374151; background-color: #f9fafb; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer; max-width: 250px; }
				.stp-demo-dropdown optgroup { font-weight: 700; color: #111827; }
				.stp-demo-dropdown option { font-weight: normal; color: #374151; }
				.stp-device-toggles { display: flex; gap: 15px; }
				.stp-btn { background: none; border: none; cursor: pointer; color: <?php echo esc_attr( $topbar_text ); ?>; opacity: 0.5; padding: 5px; transition: opacity 0.2s; }
				.stp-btn:hover, .stp-btn.active { opacity: 1; }
				.stp-close a { color: <?php echo esc_attr( $topbar_text ); ?>; opacity: 0.7; display: flex; align-items: center; transition: opacity 0.2s; }
				.stp-close a:hover { opacity: 1; color: #ef4444; }
				.stp-iframe-container { flex-grow: 1; display: flex; justify-content: center; align-items: flex-start; overflow: hidden; }
				#stp-iframe { width: 100%; height: 100%; background: #fff; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: none; }
			</style>
		</head>
		<body>
			<div id="stp-previewer-wrapper">
				<div class="stp-topbar">
					<div class="stp-title">
						<select id="stp-demo-selector" class="stp-demo-dropdown">
							<?php foreach ( $grouped_demos as $group_name => $demos ) : ?>
								<optgroup label="<?php echo esc_attr( $group_name ); ?>">
									<?php foreach ( $demos as $demo ) : ?>
										<option value="<?php echo esc_url( get_permalink( $demo->ID ) ); ?>" <?php selected( $post_id, $demo->ID ); ?>>
											<?php echo esc_html( $demo->post_title ); ?>
										</option>
									<?php endforeach; ?>
								</optgroup>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="stp-device-toggles">
						<button class="stp-btn active" data-width="100%"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg></button>
						<button class="stp-btn" data-width="768px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg></button>
						<button class="stp-btn" data-width="375px"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg></button>
					</div>
					<div class="stp-close">
						<a href="<?php echo esc_url( $close_url ); ?>"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></a>
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
						demoSelector.addEventListener('change', function() { window.location.href = this.value; });
					}
				});
			</script>
		</body>
		</html>
		<?php
		exit;
	}
}
// -----------------------------------------------------------------------------
// 10. CLEAN UP ADMIN INTERFACE (CONDITIONALLY REMOVE HEAVY SEO META BOXES)
// -----------------------------------------------------------------------------
add_action( 'add_meta_boxes', 'sdp_remove_seo_meta_boxes', 99 );
function sdp_remove_seo_meta_boxes() {
	if ( get_option( 'sdp_disable_seo', 'yes' ) === 'yes' ) {
		remove_meta_box( 'wpseo_meta', 'demo_site', 'normal' ); // Yoast
		remove_meta_box( 'aiosp', 'demo_site', 'normal' ); // AIOSEO
	}
}

add_filter( 'rank_math/excluded_post_types', function( $post_types ) {
	if ( get_option( 'sdp_disable_seo', 'yes' ) === 'yes' ) {
		$post_types['demo_site'] = 'demo_site';
	}
	return $post_types;
});

// -----------------------------------------------------------------------------
// 11. CREATE THE BEAUTIFUL "HUB" SHORTCODE (UPDATED FOR CATEGORIES)
// -----------------------------------------------------------------------------
add_shortcode( 'demo_sites_hub', 'sdp_hub_shortcode' );
function sdp_hub_shortcode( $atts ) {
	// Allow filtering by category slug via shortcode attribute
	$atts = shortcode_atts( array(
		'category' => '', 
	), $atts, 'demo_sites_hub' );

	$query_args = array(
		'post_type'      => 'demo_site',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'menu_order title',
		'order'          => 'ASC'
	);

	// If a category is specified in the shortcode, filter the query
	if ( ! empty( $atts['category'] ) ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'demo_type',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $atts['category'] ),
			),
		);
	}

	$demos = get_posts( $query_args );

	if ( empty( $demos ) ) {
		return '<p>' . esc_html__( 'No demo sites found for this category.', 'simple-demo-previewer' ) . '</p>';
	}

	$button_bg = get_option( 'sdp_button_bg', '#2563eb' );
	$button_text = get_option( 'sdp_button_text', '#ffffff' );

	ob_start();
	?>
	<div class="sdp-hub-wrapper" style="max-width: 1200px; margin: 0 auto; padding: 20px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
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
					<a href="<?php echo esc_url( get_permalink( $demo->ID ) ); ?>" class="sdp-demo-btn"><?php esc_html_e( 'Launch Preview', 'simple-demo-previewer' ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<style>
		.sdp-demo-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; text-align: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: transform 0.2s ease, box-shadow 0.2s ease; display: flex; flex-direction: column; justify-content: space-between; min-height: 200px; }
		.sdp-demo-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border-color: #cbd5e1; }
		.sdp-demo-thumbnail { margin-bottom: 20px; overflow: hidden; border-radius: 8px; }
		.sdp-demo-thumbnail img { width: 100%; height: auto; aspect-ratio: 16/9; object-fit: cover; display: block; transition: transform 0.3s ease; }
		.sdp-demo-card:hover .sdp-demo-thumbnail img { transform: scale(1.05); }
		.sdp-demo-title { margin: 0 0 20px 0; font-size: 20px; color: #1f2937; font-weight: 600; }
		.sdp-demo-btn { display: inline-block; background: <?php echo esc_attr( $button_bg ); ?> !important; color: <?php echo esc_attr( $button_text ); ?> !important; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 500; transition: filter 0.2s; }
		.sdp-demo-btn:hover { filter: brightness(0.85); color: <?php echo esc_attr( $button_text ); ?>; }
	</style>
	<?php
	return ob_get_clean();
}

// -----------------------------------------------------------------------------
// 12. ADD SETTINGS LINK TO PLUGINS PAGE
// -----------------------------------------------------------------------------
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'sdp_add_settings_link' );
function sdp_add_settings_link( $links ) {
	$settings_link = '<a href="' . admin_url( 'edit.php?post_type=demo_site&page=sdp-hub-setup' ) . '">' . __( 'Settings', 'simple-demo-previewer' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

// -----------------------------------------------------------------------------
// 13. AUTO-ASSIGN MENU ORDER TO NEW POSTS (SAFELY)
// -----------------------------------------------------------------------------
add_filter( 'wp_insert_post_data', 'sdp_auto_set_menu_order_on_create', 10, 2 );
function sdp_auto_set_menu_order_on_create( $data, $postarr ) {
	if ( $data['post_type'] === 'demo_site' && $data['menu_order'] == 0 ) {
		$is_new_post = false;

		if ( ! empty( $postarr['ID'] ) ) {
			$existing_post = get_post( $postarr['ID'] );
			if ( $existing_post && $existing_post->post_status === 'auto-draft' && $data['post_status'] !== 'auto-draft' ) {
				$is_new_post = true;
			}
		} else {
			$is_new_post = true;
		}

		if ( $is_new_post ) {
			global $wpdb;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$max_order = $wpdb->get_var( "SELECT MAX(menu_order) FROM {$wpdb->posts} WHERE post_type = 'demo_site' AND post_status NOT IN ('auto-draft', 'trash')" );
			$data['menu_order'] = $max_order ? (int) $max_order + 1 : 1;
		}
	}
	return $data;
}

// -----------------------------------------------------------------------------
// 14. ADD & POPULATE THE "ORDER" COLUMN IN THE ADMIN LIST
// -----------------------------------------------------------------------------
add_filter( 'manage_demo_site_posts_columns', 'sdp_add_order_column' );
function sdp_add_order_column( $columns ) {
	$new_columns = array();
	foreach ( $columns as $key => $title ) {
		$new_columns[$key] = $title;
		if ( $key === 'title' ) {
			$new_columns['menu_order'] = __( 'Order', 'simple-demo-previewer' );
		}
	}
	return $new_columns;
}

add_action( 'manage_demo_site_posts_custom_column', 'sdp_fill_order_column', 10, 2 );
function sdp_fill_order_column( $column, $post_id ) {
	if ( $column === 'menu_order' ) {
		echo esc_html( get_post( $post_id )->menu_order );
	}
}

add_filter( 'manage_edit-demo_site_sortable_columns', 'sdp_make_order_column_sortable' );
function sdp_make_order_column_sortable( $columns ) {
	$columns['menu_order'] = 'menu_order';
	return $columns;
}

// -----------------------------------------------------------------------------
// 15. DEFAULT THE ADMIN LIST TO SORT BY MENU ORDER
// -----------------------------------------------------------------------------
add_action( 'pre_get_posts', 'sdp_default_admin_sort_order' );
function sdp_default_admin_sort_order( $query ) {
	if ( is_admin() && $query->is_main_query() && $query->get( 'post_type' ) === 'demo_site' ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['orderby'] ) ) {
			$query->set( 'orderby', 'menu_order title' );
			$query->set( 'order', 'ASC' );
		}
	}
}

// -----------------------------------------------------------------------------
// 16. ENABLE DRAG-AND-DROP SORTING
// -----------------------------------------------------------------------------
add_action( 'admin_enqueue_scripts', 'sdp_enqueue_sortable_script' );
function sdp_enqueue_sortable_script( $hook ) {
	global $post_type;
	if ( $hook === 'edit.php' && $post_type === 'demo_site' ) {
		wp_enqueue_script( 'jquery-ui-sortable' );
	}
}

add_action( 'admin_footer-edit.php', 'sdp_sortable_js' );
function sdp_sortable_js() {
	global $post_type;
	if ( $post_type !== 'demo_site' ) return;
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('table.wp-list-table #the-list').sortable({
			items: 'tr',
			cursor: 'move',
			axis: 'y',
			helper: function(e, tr) {
				var $originals = tr.children();
				var $helper = tr.clone();
				$helper.children().each(function(index) {
					$(this).width($originals.eq(index).width());
				});
				return $helper;
			},
			update: function(e, ui) {
				var postIDs = $(this).sortable('toArray', { attribute: 'id' });
				var sortedIDs = postIDs.map(function(id) { return id.replace('post-', ''); });

				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'sdp_update_post_order',
						order: sortedIDs,
						security: '<?php echo esc_html( wp_create_nonce("sdp_sort_nonce") ); ?>'
					},
					success: function() {
						$('#the-list tr').css('transition', 'background-color 0.5s').css('background-color', '#f0f6fc');
						setTimeout(function(){ $('#the-list tr').css('background-color', 'transparent'); }, 500);
					}
				});
			}
		});
	});
	</script>
	<style>
		table.wp-list-table #the-list tr { cursor: grab; }
		table.wp-list-table #the-list tr:active { cursor: grabbing; }
		.ui-sortable-helper { box-shadow: 0 5px 15px rgba(0,0,0,0.15); background: #fff !important; display: table; }
	</style>
	<?php
}

add_action( 'wp_ajax_sdp_update_post_order', 'sdp_save_drag_drop_order' );
function sdp_save_drag_drop_order() {
	check_ajax_referer( 'sdp_sort_nonce', 'security' );
	if ( ! current_user_can( 'edit_posts' ) ) wp_die( 'Permission denied' );

	if ( isset( $_POST['order'] ) && is_array( $_POST['order'] ) ) {
		
		// Sanitize the array of IDs by ensuring they are all unslashed positive integers
		$sanitized_order = array_map( 'absint', wp_unslash( $_POST['order'] ) );
		
		global $wpdb;
		foreach ( $sanitized_order as $index => $post_id ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->update( 
				$wpdb->posts, 
				array( 'menu_order' => $index + 1 ), 
				array( 'ID' => $post_id ),
				array( '%d' ),
				array( '%d' )
			);
			clean_post_cache( $post_id );
		}
	}
	wp_send_json_success();
}

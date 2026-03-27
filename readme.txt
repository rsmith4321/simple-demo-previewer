=== Simple Demo Previewer ===
Contributors: rsmith4321, Shoreline Web Designs
Tags: demo previewer, theme directory, iframe preview, portfolio, showcase
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 2.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically generates a beautiful, full-screen, responsive iframe previewer and a central hub page to show off your website themes.

== Description ==

Simple Demo Previewer is the ultimate tool for web designers, agencies, and theme developers who want to showcase their work professionally. It automatically generates a beautiful, full-screen, responsive iframe previewer so clients can test your sites on desktop, tablet, and mobile views.

The plugin also generates a beautiful, centralized "Hub" grid page to display all your available demos in one place.

**Key Features:**
* **Categorize Your Demos:** Organize your portfolio by separating client work from starting templates using the built-in custom taxonomy.
* **Smart Dropdown Grouping:** The previewer automatically groups your sites by category, making it easy for clients to navigate your different offerings.
* **Full-Screen Previewer:** Clients can view your sites with a sleek top bar featuring device toggles (Desktop, Tablet, Mobile).
* **Central Hub Page:** Automatically creates a gorgeous grid directory of all your demo sites, which can be filtered by category via shortcode.
* **Drag-and-Drop Sorting:** Easily reorder how your sites appear on the frontend grid by simply dragging and dropping rows in the WordPress admin area.
* **Smart SEO Protection:** Prevents duplicate content penalties by hiding SEO meta boxes on demo pages and automatically applying `noindex` tags to the iframe wrappers.
* **Customizable Colors:** Easily match the previewer top bar and grid buttons to your own brand colors.
* **Foolproof UI:** Designed with non-technical users in mind, featuring clear instructions and logical setup flows.

== Installation ==

1. Upload the `simple-demo-previewer` folder to the `/wp-content/plugins/` directory, or install it directly through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Upon activation, a new "Example Sites" page will be automatically generated for you.
4. Navigate to **Demo Sites > Getting Started** in your WordPress dashboard to configure your brand colors and view the setup instructions.
5. Go to **Demo Sites > Add New Demo Site** to start adding and categorizing your projects!

== Frequently Asked Questions ==

= How can I separate my Real Sites from Templates? =
You can use the built-in "Categories" taxonomy. Create your categories (e.g., "Real Sites", "Templates"), assign your demo sites to them, and use the shortcode attribute to display specific grids on your pages: `[demo_sites_hub category="real-sites"]`.

= How do I group sites in the previewer dropdown? =
The plugin automatically groups your sites by category in the top-bar dropdown. You can even control the order of these groups by setting a custom "Display Order" number when creating or editing your categories!

= How do I change the order of my demo sites? =
It's incredibly easy! Go to **Demo Sites > All Demo Sites**. Simply click and drag any row up or down to change its position. The new order will automatically save and instantly update your live Hub page grid.

= I accidentally deleted the Hub page, what do I do? =
Don't worry! Go to **Demo Sites > Getting Started** and scroll down to the "Page Setup Tool". Click the button to instantly regenerate the page with the correct shortcode. You can also manually create a page and paste the `[demo_sites_hub]` shortcode.

= Why does it say "Disable SEO plugins" in the settings? =
Because your demo pages load your actual live websites inside an iframe, allowing Google to index these preview pages can cause "duplicate content" penalties against your main sites. The plugin safely hides these preview pages from search engines so all your SEO value stays on your main websites where it belongs.

== Screenshots ==

1. The responsive full-screen iframe previewer.
2. The automatic Hub directory grid.
3. The intuitive Getting Started and Settings dashboard.
4. Simple drag-and-drop admin sorting.

== Changelog ==

= 2.1.0 =
* **Major Feature:** Introduced a Custom Taxonomy (Categories) to easily organize and separate different types of demo sites (e.g., Real Sites vs. Templates).
* **Feature:** Added a "Display Order" numerical field to Categories, allowing you to easily control the exact sorting order of your category groups.
* **Feature:** The full-screen previewer dropdown now automatically groups your demo sites by category using `<optgroup>` tags and sorts them based on your custom Display Order.
* **Feature:** Enhanced the `[demo_sites_hub]` shortcode to support category filtering via slug (e.g., `[demo_sites_hub category="templates"]`).
* **Update:** Updated the "Getting Started" dashboard with new instructions for categorizing sites and using shortcode filters.

= 2.0.0 =
* **Major Feature:** Added intuitive drag-and-drop sorting to the All Demo Sites admin list. Changes save automatically via AJAX.
* **Feature:** The WordPress admin list now visually matches the exact order of your frontend grid.
* **Feature:** Added a dedicated "Order" column to the admin list for better visibility.
* **UI Improvement:** Renamed the confusing "Featured Image" sidebar box to "Website Thumbnail" for a better user experience.
* **UI Improvement:** Relocated the Website Thumbnail uploader to the main content column directly below the URL setting so users don't miss it.
* **Fix:** Added automatic object cache flushing (Redis/Memcached) upon drag-and-drop reordering to ensure the live site updates instantly.
* **Fix:** Rewrote the background auto-numbering logic to safely use the `wp_insert_post_data` filter, completely eliminating the risk of database infinite loops.
* **Update:** Restored and expanded the "Getting Started" onboarding instructions to reflect the new drag-and-drop and thumbnail changes.

= 1.9.2 =
* Initial stable release and UI refinements.

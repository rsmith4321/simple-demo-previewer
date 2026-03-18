=== Simple Demo Previewer ===
Contributors: rsmith4321
Donate link: https://shorelinewebdesigns.com/
Tags: theme preview, demo, portfolio, showcase, iframe
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.9.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically generates a beautiful, full-screen, responsive iframe previewer and a central hub page to show off your website themes.

== Description ==

**The Story Behind the Plugin**
When I was looking for a way to showcase my web design portfolio and templates, I hit a wall. Every "demo bar" or "theme previewer" plugin I found in the repository was either years out of date, weighed down by bloated code, or completely broke when viewed on a mobile device. I just wanted a clean, modern, and fully responsive way to let my clients preview my work—exactly like the big WordPress theme agencies do. Since I couldn't find one that met my standards, I decided to build it myself!

**What It Does**
Simple Demo Previewer is the ultimate tool for web design agencies, theme developers, and freelancers who want to showcase their portfolio or template designs in a professional, interactive environment.

Upon activation, the plugin automatically generates a beautiful "Example Sites" directory grid. When a user clicks to view a site, it launches a custom, full-screen iframe app completely separate from your main theme's header and footer. 

The previewer includes dynamic device-toggle icons so your potential clients can instantly see how your designs look on Desktop, Tablet, and Mobile devices!

= Features =
* **Automated Setup:** Automatically creates your directory Hub page upon activation.
* **Custom Post Type:** Easily manage your demo URLs and featured images from a clean WordPress menu.
* **Responsive Device Toggles:** Let users preview your sites on Desktop, Tablet, and Mobile instantly.
* **Dynamic Dropdown Navigation:** Users can switch between your different demo sites without ever leaving the preview app.
* **Custom Branding:** Easily change the background colors, button colors, and text colors of the app from the settings menu to match your brand.
* **SEO Protection:** Automatically adds a "noindex" tag to your preview pages and safely disables extra meta boxes (like Yoast and Rank Math) on your demo posts to prevent duplicate content penalties.

== Installation ==

1. Upload the `simple-demo-previewer` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. A new page called "Example Sites" will automatically be created for you.
4. Navigate to **Demo Sites > Add New Demo Site** in your dashboard to start adding your templates!

== Frequently Asked Questions ==

= Why did it create an "Example Sites" page? =
This is your Hub page! It uses the `[demo_sites_hub]` shortcode to display a beautiful grid of all your templates. If you accidentally delete it, you can regenerate it anytime by going to **Demo Sites > Getting Started**.

= Why aren't my images showing up in the directory grid? =
Make sure you upload a "Featured Image" when creating or editing a Demo Site. The plugin will automatically optimize it into a perfect 16:9 thumbnail for your grid.

= My previewer is showing a blank white screen? =
Some websites (like Google or Facebook) use security settings to block their sites from being loaded inside iframes. Simple Demo Previewer works perfectly for your own domains, subdomains, and standard WordPress installations where you control the environment.

== Changelog ==

= 1.9.2 =
* Addressed Plugin Check strict validation standards and updated WP core compatibility.

= 1.9.1 =
* Initial release. Includes automated hub generation, custom color settings, settings dashboard, and SEO protection features.
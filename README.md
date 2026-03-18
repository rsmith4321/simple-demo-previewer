# Simple Demo Previewer

Automatically generates a beautiful, full-screen, responsive iframe previewer and a central hub page to show off your website themes.

**Requires WordPress:** 6.0 or higher  
**Tested up to:** 6.9  
**Requires PHP:** 7.4 or higher  
**License:** GPLv2 or later  

---

## 📖 The Story Behind the Plugin
When I was looking for a way to showcase my web design portfolio and templates, I hit a wall. Every "demo bar" or "theme previewer" plugin I found in the repository was either years out of date, weighed down by bloated code, or completely broke when viewed on a mobile device. I just wanted a clean, modern, and fully responsive way to let my clients preview my work—exactly like the big WordPress theme agencies do. Since I couldn't find one that met my standards, I decided to build it myself!

## 🚀 What It Does
Simple Demo Previewer is the ultimate tool for web design agencies, theme developers, and freelancers who want to showcase their portfolio or template designs in a professional, interactive environment.

Upon activation, the plugin automatically generates a beautiful "Example Sites" directory grid. When a user clicks to view a site, it launches a custom, full-screen iframe app completely separate from your main theme's header and footer. 

The previewer includes dynamic device-toggle icons so your potential clients can instantly see how your designs look on Desktop, Tablet, and Mobile devices!

## ✨ Features
* **Automated Setup:** Automatically creates your directory Hub page upon activation.
* **Custom Post Type:** Easily manage your demo URLs and featured images from a clean WordPress menu.
* **Responsive Device Toggles:** Let users preview your sites on Desktop, Tablet, and Mobile instantly.
* **Dynamic Dropdown Navigation:** Users can switch between your different demo sites without ever leaving the preview app.
* **Custom Branding:** Easily change the background colors, button colors, and text colors of the app from the settings menu to match your brand.
* **SEO Protection:** Automatically adds a "noindex" tag to your preview pages and safely disables extra meta boxes (like Yoast and Rank Math) on your demo posts to prevent duplicate content penalties.

---

## 📥 How to Download & Install

You can easily install this plugin directly from our official releases:

1. On the right side of this page, look for the **Releases** section and click on the latest version.
2. Under the **Assets** dropdown, click on **`simple-demo-previewer.zip`** to download the plugin to your computer.
3. Log in to your WordPress dashboard.
4. Navigate to **Plugins > Add New Plugin** and click the **Upload Plugin** button at the top.
5. Choose the `.zip` file you just downloaded and click **Install Now**.
6. Click **Activate Plugin**. 
7. A new page called "Example Sites" will automatically be created for you. Navigate to **Demo Sites > Add New Demo Site** in your dashboard to start adding your templates!

---

## ❓ Frequently Asked Questions

**Why did it create an "Example Sites" page?** This is your Hub page! It uses the `[demo_sites_hub]` shortcode to display a beautiful grid of all your templates. If you accidentally delete it, you can regenerate it anytime by going to **Demo Sites > Getting Started**.

**Why aren't my images showing up in the directory grid?** Make sure you upload a "Featured Image" when creating or editing a Demo Site. The plugin will automatically optimize it into a perfect 16:9 thumbnail for your grid.

**My previewer is showing a blank white screen?** Some websites (like Google or Facebook) use security settings to block their sites from being loaded inside iframes. Simple Demo Previewer works perfectly for your own domains, subdomains, and standard WordPress installations where you control the environment.

---

## 👨‍💻 About the Developer
Simple Demo Previewer is proudly built and maintained by the team at **[Shoreline Web Designs](https://shorelinewebdesigns.com/)**. We specialize in high-performance web solutions, custom WordPress development, and premium SEO strategies for businesses in Myrtle Beach and beyond.

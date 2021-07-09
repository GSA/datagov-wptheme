Roots Theme
Roots is a WordPress starter theme based on HTML5 Boilerplate & Bootstrap that will help you make better themes.

Source: https://github.com/roots/roots
Home Page: http://roots.io/
Twitter: @retlehs
Newsletter: Subscribe
Forum: http://discourse.roots.io/
Installation
Clone the git repo - git clone git://github.com/roots/roots.git - or download it and then rename the directory to the name of your theme or website. Install Grunt, and then install the dependencies for Roots contained in package.json by running the following from the Roots theme directory:

npm install
Reference the theme activation documentation to understand everything that happens once you activate Roots.

Theme Development
After you've installed Grunt and ran npm install from the theme root, use grunt watch to watch for updates to your LESS and JS files and Grunt will automatically re-build as you write your code.

Configuration
Edit lib/config.php to enable or disable support for various theme functions and to define constants that are used throughout the theme.

Edit lib/init.php to setup custom navigation menus and post thumbnail sizes.

Documentation
Roots Docs
Roots 101 — A guide to installing Roots, the files and theme organization
Theme Wrapper — Learn all about the theme wrapper
Build Script — A look into the Roots build script powered by Grunt
Roots Sidebar — Understand how to display or hide the sidebar in Roots
Features
Organized file and template structure
HTML5 Boilerplate's markup along with ARIA roles and microformat
Bootstrap
Grunt build script
Theme activation
Theme wrapper
Root relative URLs
Clean URLs with a plugin (no more /wp-content/)
All static theme assets are rewritten to the website root (/assets/*)
Cleaner HTML output of navigation menus
Cleaner output of wp_head and enqueued scripts/styles
Nice search (/search/query/)
Image captions use <figure> and <figcaption>
Example vCard widget
Posts use the hNews microformat
Multilingual ready (Brazilian Portuguese, Bulgarian, Catalan, Danish, Dutch, English, Finnish, French, German, Hungarian, Indonesian, Italian, Korean, Macedonian, Norwegian, Polish, Russian, Simplified Chinese, Spanish, Swedish, Traditional Chinese, Turkish, Vietnamese, Serbian)
Contributing
Everyone is welcome to help contribute and improve this project. There are several ways you can contribute:

Reporting issues (please read issue guidelines)
Suggesting new features
Writing or refactoring code
Fixing issues
Replying to questions on the forum
Support
Use the Roots Discourse to ask questions and get support.

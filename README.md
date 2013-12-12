Next.Data.gov
==============
We built [Next.Data.gov](http://next.data.gov) using new tools. This project tapped into two very active open source projects: [WordPress](http://wordpress.org) and [CKAN](http://ckan.org). The content and community sections of Next.Data.gov are powered by WordPress. The data catalog is powered by CKAN. 

This repository contains the HTML and WordPress template files for the content and community sections. The repository for the CKAN extensions will be published shortly.

##HTML

This directory contains HTML, CSS, and JavaScript that embodies the design for Next.Data.gov.

##themes/wordpress

This directory contains the WordPress template files.

```
- assets
- images
- import
- js
- stylesheets
category-search.php
category.php
footer.php
functions.php
header.php
index.php
navigation.php
primary-search.php
single-post.php
style.css
```

This template requires the following plugins:

```
- Advanced Custom Fields
- Category Sticky Post
- Link Manager
- WP No Category Base
```

## Plugins

* Advanced Custom Fields - http://wordpress.org/plugins/advanced-custom-fields/
* Better WP Security - http://wordpress.org/plugins/better-wp-security/
* Category Sticky Post - http://wordpress.org/plugins/category-sticky-post/
* Configure SMTP - http://wordpress.org/plugins/configure-smtp/
* Google Analyticator - http://wordpress.org/plugins/google-analyticator/
* Link Manager - http://wordpress.org/plugins/link-manager/
* Restrict Categories - http://wordpress.org/plugins/restrict-categories/
* W3 Total Cache - http://wordpress.org/plugins/w3-total-cache/
* Wordpress Importer - http://wordpress.org/plugins/wordpress-importer/
* WP No Category Base - http://wordpress.org/plugins/no-category-base-wpml/
* Custom Post Type UI - http://wordpress.org/plugins/custom-post-type-ui/
* Custom Content Type Manager - http://wordpress.org/plugins/custom-content-type-manager/
* Q and A FAQ + Knowledge Base - http://wordpress.org/plugins/q-and-a/
* Custom Contact Form - http://wordpress.org/plugins/custom-contact-forms/
* PHP Code for posts - http://wordpress.org/plugins/php-code-for-posts/
* Broken Link Checker - http://wordpress.org/plugins/broken-link-checker/
* Interactive World Map(paid plugin) - http://cmoreira.net/interactive-world-maps-demo/ 
* Redirection - http://wordpress.org/plugins/redirection/
* Custom Permalink - http://wordpress.org/plugins/custom-permalinks/
* Date and Time Picker - http://wordpress.org/plugins/acf-field-date-time-picker/
* Advanced Custom Fields Location Field - https://github.com/elliotcondon/acf-location-field/
* Import Blogroll With Categories - http://wordpress.org/plugins/import-blogroll-with-categories/
* Widget Logic - http://wordpress.org/plugins/widget-logic/ 
* bbPress - http://wordpress.org/plugins/bbpress/
* Posts in Page - http://wordpress.org/plugins/posts-in-page/
* Subscribe2 - http://wordpress.org/plugins/subscribe2/
* Upload Scanner - http://wordpress.org/plugins/upload-scanner/
* WP Crontrol - http://wordpress.org/support/view/plugin-reviews/wp-crontrol
* SI CAPTCHA Anti-Spam - http://wordpress.org/plugins/si-captcha-for-wordpress/
* Sticky Posts In Category - http://wordpress.org/plugins/sticky-posts-in-category/
* Twitter Widget Pro - http://wordpress.org/plugins/twitter-widget-pro/
* External Links - http://wordpress.org/plugins/sem-external-links/
* WP Popup Plugin - http://wordpress.org/plugins/m-wp-popup/
#### Locally hosted plugins
* SAML 2.0 Single Sign-On - http://wordpress.org/plugins/saml-20-single-sign-on/
* Custom Post View Generator - http://wordpress.org/plugins/custom-post-view-generator/
* Metric Count	
* Datagov Custom Post Types
* ArcGis Map Validation

## Exportables
This folder contains advanced custom fields definitions in xml format that can be imported manually via acf plugin.

##Contributing

In the spirit of free software, everyone is encouraged to help improve this project.

Here are some ways you can contribute:

- by using alpha, beta, and prerelease versions
- by reporting bugs
- by suggesting new features
- by translating to a new language
- by writing or editing documentation
- by writing specifications
- by writing code (**no patch is too small**: fix typos, add comments, clean up inconsistent whitespace)
- by refactoring code
- by closing issues
- by reviewing patches

When you are ready, submit a [pull request](https://github.com/GSA/datagov-design/pulls).

##Submitting an Issue

We use the [GitHub issue tracker](https://github.com/GSA/datagov-design/issues) to track bugs and features. Before submitting a bug report or feature request, check to make sure it hasn't already been submitted. You can indicate support for an existing issue by voting it up. When submitting a bug report, please include a Gist that includes a stack trace and/or any details that may be necessary to reproduce the bug.

##License

This project constitutes a work of the United States Government and is not subject to domestic copyright protection under 17 USC § 105.

The project utilizes code licensed under the terms of the GNU General Public License and therefore is licensed under GPL v2 or later.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

Visit http://www.gnu.org/licenses/ to learn more about the GNU General Public License.

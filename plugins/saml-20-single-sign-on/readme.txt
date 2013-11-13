=== SAML 2.0 Single Sign-On ===
Contributors: ktbartholomew
Tags: sso, saml, single sign-on, simplesamlphp, onelogin, ssocircle
Requires at least: 3.3
Tested up to: 3.6
Stable tag: 0.9.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SAML 2.0 Single Sign-On allows you to use a SAML 2.0-compliant Identity Provider for Single Sign-On to your blog.

== Description ==

SAML 2.0 Single Sign-On allows you to use any SAML 2.0-compliant Identity Provider for Single Sign-On to your blog or network of blogs.  The plugin will replace the standard WordPress login screen and can automatically redirect login/logout requests to your SSO portal. Group membership from the Identity Provider (such as Active Directory) can be used to determine what privileges the user will have on your blog, such as Administrator, Editor, or Subscriber. This plugin uses a modified version of the SimpleSAMLPHP library for all SAML assertions, and can be configured exclusively from the WordPress Admin menu.

== Installation ==

1. Upload `samlauth.zip` to the `/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the "Identity Provider" and "Service Provider" sections of the plugin in the Settings > Single Sign-On menu.
4. Enable the plugin to do authentication on the "General" section of the plugin.

== Frequently asked questions ==

= What does this plugin do with my passwords? =

Because of the way SAML SSO systems work, this plugin is never aware of your password. When activated, you will always enter your password into your company's SSO portal website, which will then pass an authentication token--not a real password--to the WordPress site.

= Do I really need an SSL certificate to use this plugin? =

You may have noticed the fields that ask you to upload an SSL certificate and private key. This is only necessary if you want users to initiate their login from your website, that is, by visiting the `/wp-admin` URL on your site. Logins that originate from the SSO portal will work fine without this certificate. Because exchanging the certificate with your Identity Provider is part of the initial setup process, it is not necessary to have a publicly-signed (paid for) certificate. You can generate a self-signed certificate for free and use that.

= Can I have some users use single sign-on and others use the standard WordPress login method? =

This is not currently possible. You should make sure that all necessary administrators have SSO-ready user accounts before enabling the plugin.

== Changelog ==

= 0.9.1 =
* The plugin is feature-complete until v1.0. All updates between 0.9.0 and 1.0 will be strictly bugfixes or improvements.
* Fewer warnings and errors when not all IdP attributes are specified.
* If a user's group membership changes at the IdP, their WordPress role will be changed accordingly at next login.

= 0.9.0 =
* Added nonces and basic type-checking to admin pages for improved security.
* Quick access to common attributes used by popular IdP's including ADFS, OneLogin, and SimpleSAMLPHP
* Extensive internal code improvements to improve maintainability

= 0.8.9 =
* Status check lets you know when everything appears to be configured correctly.
* Fixed an issue that prevented users from logging out if a Single Logout service was not specified.
* Fixed an issue that caused SP settings to get out of sync when importing IdP settings from metadata.

= 0.8.8 =
* IdP info can now be automatically loaded from a metadata URL.
* Signing certificate can be automatically generated if you don't know how (or don't want to do it yourself). The generated certificate can be downloaded so you can share it with your IdP.


= 0.8.7 =
* Uploading a certificate and private key is now optional, which makes IdP-initiated testing much simpler.
* Folders and config files are created if they don't already exist, which fixes many issues with various screens being blank.

= 0.8.6 =
* Moved configuration files from plugins directory to a subdirectory in uploads to ensure the plugin doesn't break itself when updated.


== Upgrade Notice ==

= 0.8.6 =
This update will delete your certificates and IdP info (Hint: Back up before upgrading), but this is the last time it will ever happen! This update fixes that problem going forward.
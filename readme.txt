=== EW Enginemailer ===
Contributors: eWallz
Donate link: https://www.ewallzsolutions.com/
Tags: email, mail, smtp, api, enginemailer, phpmailer
Requires at least: 6.8
Tested up to: 6.8
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Route all WordPress emails via Enginemailer API or SMTP, with fallback and delivery logging. Modern, modular, and easy to use.

== Description ==

[EW Enginemailer](https://www.ewallzsolutions.com/) allows you to send all WordPress emails through the Enginemailer API for reliable delivery, with optional SMTP backup. Includes delivery logging, test email, and a clean admin UI.

=== Features ===

* Send all WordPress emails via Enginemailer API (with API key)
* SMTP Backup: fallback to SMTP if API fails or is not configured
* Delivery log: see the latest 20 email delivery attempts (status, method, recipient, response)
* Test Email tab for easy diagnostics
* Debug Info tab with server and delivery info
* Modular codebase for easy extension
* Option to delete all plugin data on uninstall

=== Settings Overview ===

* **General**: From Email, From Name, Force From Name, Force From Address, Disable SSL Verify, Delete on Uninstall
* **API Settings**: Enter your Enginemailer API key
* **SMTP Backup**: Enable/disable, SMTP Host, Auth, Username, Password, Encryption, Port
* **Test Email**: Send a test email to verify configuration
* **Debug Info**: View server info and the latest 20 delivery log entries

=== Installation ===

1. Go to the Add New plugins screen in your WordPress Dashboard
2. Click the upload tab
3. Browse for the plugin file (ew-enginemailer.zip) on your computer
4. Click "Install Now" and then hit the activate button

== Frequently Asked Questions ==

= What happens if the API fails? =
If SMTP Backup is enabled, the plugin will automatically try to send via SMTP. If both fail, WordPress will use its default mail function.

= Can I see if my emails are delivered? =
Yes, the Debug Info tab shows the latest 20 delivery attempts, including status, method, recipient, and any error response.

== Screenshots ==

1. EW Enginemailer Settings
2. Test Email Tab
3. Debug Info Tab

== Upgrade Notice ==

= 1.0.0 =
* Initial release as EW Enginemailer, with API, SMTP fallback, and delivery log.

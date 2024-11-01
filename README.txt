===Tipalti Payee Iframe===
Contributors: tipalti
Tags: tipalti, payee, iframe
Requires at least: 5.6
Tested up to: 6.0
Stable tag: 1.0.5
Requires PHP: 7.3
License: GPLv2 or Later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin allows Tipalti payers to embed payee portal IFRAME onto their Wordpress site so that payees can add and view their information.

== Description ==
This plugin allows Tipalti payers to embed payee portal IFRAME onto their Wordpress site so that payees can add and view their information. The portal elements can be embedded into any post or page and website owners can essentially define their own menu structure. They can easily make the IFRAME experience a part of the rest of their Wordpress site. The plugin also directly links Wordpress Users with Payee IDs so users can securely log into Wordpress and be associated with a Payee ID. Tipalti Payee Iframe also encrypts the masterkey before it is saved together with the rest of the input to the database.

== Setup Instructions ==
###### Connecting to the Tipalti Payor Account
After you have downloaded and activated the plugin, go to **Settings** on the menu panel and click on **Tipalti Payee Iframe**.

Enter both the **Masterkey** and **Payer Name** provided by Tipalti. Check **Sandbox** to connect to the sandbox environment. Leave it unchecked to connect to the production environment.

###### Linking Wordpress Users as Payees
To link Wordpress Users as payees, go to **Edit User Profile** and check **Allow Tipalti Payee IFRAME** to grant the user access to the IFRAME.

If the user is an existing payee with a payee id, enter the payee id to sync the account already set up with Tipalti. For a new payee, the plugin will automatically generate a payee id.

###### Embedding Payee IFRAMES
The IFRAME can be embedded to any page or post with a series of shortcodes. Simply begin with *[tipalti-payee-iframe]* and specify the type of iframe to embed. Be sure to embed each shortcode into its own shortcode block on Wordpress.

###### Shortcodes Examples
Examples:
*[tipalti-payee-iframe type=account-settings]*
*[tipalti-payee-iframe type=invoice-history]*
*[tipalti-payee-iframe type=payment-history]*
*[tipalti-payee-iframe type=submit-invoice]*

== Screenshots ==
1. Connecting to the Tipalti Payor Account

![tipalti-payor-account](/assets/images/tipalti-payor-account.png)

2. Linking Wordpress Users as Payees

![linking-wordpress-users](/assets/images/linking-users.png)

3. Embedding Payee IFRAMEs

![embedding-iframes](/assets/images/embedding-iframes.gif)

== Recommendations and Considerations ==
- The IFRAME used in the plugin uses the default styling from Tipalti. This styling cannot be changed at this time. The IFRAME is not responsive.
- The IFRAME default width is 1020px, we recommend the page width to be at least 1020px.
- We recommend your Wordpress site use some form of 2FA for payee logins.
- We recommend that payees have a limited role on the site. The plugin does not require the user to be assigned with any role.

== Changelog ==

= 1.0.4 =
* Fixed Bug : masterkey not saved correctly when options are updated.
* Changed : requires re-entering of masterkey when options changes.

= 1.0.5 =
* Changed : improved, no longer requires re-entering of masterkey when plugins and/or settings are updated
* Added : option to specify preferred skin
* Changed : improved payee id validation and error messages 
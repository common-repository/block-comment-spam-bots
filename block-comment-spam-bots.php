<?php

/**
Plugin Name:       Block Comment Spam Bots
Contributors: rhellewellgmailcom
Donate link: https://cellarweb.com/
Author URI: https://www.cellarweb.com
Plugin URI: https://www.cellarweb.com/wordpress-plugins/
Description:       Blocks spam bots from directly posting to wp-post-comments.php file.
Version:           2.62
Requires at least: 4.9
Tested up to: 6.3 
Requires PHP:      5.4
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// So we can use the version number on the plugin info page
define("BCSB_VERSION_NUMBER", "2.62 (4 Apr 2022)");
// checkmark icon
define("BCSB_CHECKMARK", plugin_dir_url(__FILE__) . "/assets/icons8-check-mark-48.png");

// ============================================================================
// this is the function that adds the hidden field to the comment form
//      $fake_guid is new in 3.1
// ----------------------------------------------------------------------------
function bcsb_add_hidden_field($fields) {
	$fake_guid = wp_generate_uuid4();
	$fake_guid = str_ireplace("9", "!", $fake_guid);
	$fake_guid = str_ireplace("8", "$", $fake_guid);
	$fake_guid = str_ireplace("7", "@", $fake_guid);
	$fake_guid = str_ireplace("6", "#", $fake_guid);
	$fake_guid = str_ireplace("5", "%", $fake_guid);
	$fake_guid = str_ireplace("4", "^", $fake_guid);
	$fake_guid = str_ireplace("3", "&", $fake_guid);
	$fake_guid = str_ireplace("2", "(", $fake_guid);
	$fake_guid = str_ireplace("1", ")", $fake_guid);
	echo '<input id="bcsb_hidden_guid" name="bcsb_hidden_guid" type="text" value="' . $fake_guid . '" style="display:none;"    />';
	// add the JS to the footer area to change hidden field to footer area
		add_action('wp_footer', 'bcsb_change_guid', 20);

	return;
}

// this gets the hidden field into the form via the 'hook'
// need both in case user is/is-not logged in  bug fix version 1.7)
add_action('comment_form_logged_in_after', 'bcsb_add_hidden_field');
add_action('comment_form_after_fields', 'bcsb_add_hidden_field');

// ============================================================================
// hide submit button for the delay to prevent quick 'comment pasters' from submitting too soon
// ----------------------------------------------------------------------------
add_action('wp_head', 'bcsb_hide_submit', 100);
function bcsb_hide_submit() {
	?>
<style>
#submit {
    display:none;
}
</style>
<?php
return;
}

// ============================================================================
// check if the hidden field is there when submitted, and that it is a WP-valid UUID value
// ----------------------------------------------------------------------------
function bcsb_verify_hidden_field($commentdata) {

	// if user can moderate comments, then are probably in the admin/comments screen, so force the GUID into the POST instead of modifying the quick-comment form
	if (current_user_can('moderate_comments')) {
		$_POST['bcsb_hidden_guid'] = wp_generate_uuid4(); // forces the guid on admin/comment replies without needing to add a hidden field to the dropdown form.
		return $commentdata;
	}
	if ((!isset($_POST['bcsb_hidden_guid']))  or (!wp_is_uuid($_POST['bcsb_hidden_guid'], 4))) {
		wp_die(__('Error: Go away spammer!'));
	}
	return $commentdata;
}

add_filter('preprocess_comment', 'bcsb_verify_hidden_field');

// ============================================================================
// Save the comment meta data along with comment
// ----------------------------------------------------------------------------
function bcsb_save_comment_meta_data($comment_id) {
	if ((isset($_POST['bcsb_hidden_guid'])) && ($_POST['bcsb_hidden_guid'] != '')) {
		$bcsb_hidden = wp_filter_nohtml_kses($_POST['bcsb_hidden_guid']);
		/*    add_comment_meta($comment_id, 'bcsb_hidden', $bcsb_hidden);  */
		// updates the comment meta if exists; adds if not exists
		update_comment_meta($comment_id, 'bcsb_hidden', $bcsb_hidden);
	}
	return;}

add_action('comment_post', 'bcsb_save_comment_meta_data');

// ============================================================================
// show the content on individual edit pages
// ----------------------------------------------------------------------------
add_action('add_meta_boxes_comment', 'bcsb_comment_add_meta_box');
function bcsb_comment_add_meta_box() {
	add_meta_box('bcsb_hidden_title', __('Block Comment Spam Bots - plugin from CellarWeb.com'), 'bcsb_comment_meta_box_callback', 'comment', 'normal', 'high');
}

// ============================================================================
// get the guid from the comment meta
// ----------------------------------------------------------------------------
function bcsb_comment_meta_box_callback($comment) {
	$bcsp_hidden = get_comment_meta($comment->comment_ID, 'bcsb_hidden', true);
	wp_nonce_field('bcsb_hidden', 'bcsb_hidden_guid', false);
	?>
    <p>
        <label for="bcsb_hidden_guid"><?php _e('Unique Comment ID (read-only)');?></label>;
        <input type="text" name="bcsb_hidden_guid" value="<?php echo esc_attr($bcsp_hidden); ?>" class="widefat" readonly />
    </p>
    <?php
}

// ============================================================================
// save changes from the admin
// ----------------------------------------------------------------------------
add_action('edit_comment', 'bcsb_comment_edit_bcsb');
function bcsb_comment_edit_bcsb($comment_id) {
	if (!isset($_POST['bcsb_hidden_guid']) || !wp_verify_nonce($_POST['bcsb_hidden_guid'], 'bcsb_hidden')) {return;}
	if (isset($_POST['bcsb_hidden_guid'])) {
		update_comment_meta($comment_id, 'bcsb_hidden', esc_attr($_POST['bcsb_hidden_guid']));
	}
}

// ============================================================================
// how data in the comment list table i
// ----------------------------------------------------------------------------
add_action('load_edit_comments.php', 'bcsb_load');
function bcsb_load() {
	$screen = get_current_screen();

	add_filter("manage_{$screen->id}_columns", 'manage_comments_custom_column');
}

// ============================================================================
// show column heading
// guidance from https://whtly.com/2011/07/27/adding-custom-columns-to-the-wordpress-comments-admin-page/
// ----------------------------------------------------------------------------
add_filter('manage_edit-comments_columns', 'bcsb_comment_columns');
function bcsb_comment_columns($columns) {
	$columns['bcsb_hidden'] = __('Comment Verified');
	return $columns;
}

// ============================================================================
// show column data
// ----------------------------------------------------------------------------
add_filter('manage_comments_custom_column', 'bcsb_comment_column', 10, 2);
function bcsb_comment_column($column, $comment_ID) {
	$bcsb_data = get_comment_meta($comment_ID, 'bcsb_hidden');
	// 2nd condition required to ensure data only displayed on our added columns, not added columns from other plugins
	if (($bcsb_data[0]) AND ('bcsb_hidden' == $column)) {
		echo '<img src="' . BCSB_CHECKMARK . '" width="24" height="24" alt="" title="' . $bcsb_data[0] . '" >';
		//   echo $bcsb_data[0] ;
	}
}

// ============================================================================
// Add settings link on plugin page
// ----------------------------------------------------------------------------
function bcsb_settings_link($links) {
	$settings_link = '<a href="options-general.php?page=bcsb_settings" title="Block Comment Spam Bots">Block Comment Spam Bots Info/Usage</a>';
	array_unshift($links, $settings_link);
	return $links;
}

// ============================================================================
// link to the settings page
// ----------------------------------------------------------------------------
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'bcsb_settings_link');

// ============================================================================
//  build the class for all of this
// ----------------------------------------------------------------------------
class bcsb_Settings_Page {

// start your engines!
	public function __construct() {
		add_action('admin_menu', array($this, 'bcsb_add_plugin_page'));
	}

// add options page
	public function bcsb_add_plugin_page() {
// This page will be under "Settings"
		add_options_page('Block Comment Spam Bots Info/Usage', 'Block Comment Spam Bots Info/Usage', 'manage_options', 'bcsb_settings', array($this, 'bcsb_create_admin_page'));
	}

// options page callback
	public function bcsb_create_admin_page() {
		// Set class property
		$this->options = get_option('bcsb_options');
		?>
 <!--
<div class = 'bcsb_header'>
  <h1 align="center" >Block Comment Spam Bots</h1>
	<h3 align='center'>from <a href="https://www.cellarweb.com" target="_blank" title="CellarWeb.com">CellarWeb.com</a></h3>-->
<div align='center' class = 'bcsb_header'>
	 <img src="<?php echo plugin_dir_url(__FILE__); ?>assets/banner-1000x200.jpg" width="95%"  alt="" class='bcsb_shadow'>
</div>
    <p align='center'>Version: <?php echo BCSB_VERSION_NUMBER; ?></p>
  <!--	<span class='bcsb_logo_image'><img src="<?php echo plugin_dir_url(__FILE__); ?>assets/cellarweb-logo-2022.jpg"  width="20%" class="bcsb_shadow" ></span>  -->
<!--</div>    -->
<div >
    <div class="bcsb_options">
        <?php bcsb_info_top();?>
    </div>
    <div class='bcsb_sidebar'>
        <?php bcsb_sidebar();?>
    </div>
</div>
<!-- not sure why this one is needed ... -->
<div class="bcsb_footer">
    <?php bcsb_footer();?>
</div>
<?php }

// print the Section text
	public function bcsb_print_section_info() {
		print '<h3><strong>Information about Block Comment Spam Bots from CellarWeb.com</strong></h3>';
	}
}
// end of the class stuff
// ============================================================================

// ============================================================================
// if on the admin pages, set up the settings page
// ----------------------------------------------------------------------------

if (is_admin()) { // display info on the admin pages  only
	$my_settings_page = new bcsb_Settings_Page();
	// ----------------------------------------------------------------------------
	// supporting functions
	// ----------------------------------------------------------------------------
	//  display the top info part of the settings page
	// ----------------------------------------------------------------------------
	function bcsb_info_top() {
		$latest_cpt  = get_posts("post_type=post&numberposts=1");
		$post_id     = $latest_cpt[0]->ID;
		$add_text    = "(random code = " . substr(wp_generate_uuid4(), 3, 5) . ') ';
		$image_url   = plugin_dir_url(__FILE__);
		$bot_command = 'curl --data curl --data "author=Mr. SpamBot&email=spambot@example.com&comment=Hello from a spam bot! ' . $add_text . '&comment_post_ID=' . $post_id . '" ' . site_url() . '/wp-comments-post.php';
		?>
<h1 align='center'>Block Comment Spam Immediately!</h1>
<!--<p>Professional spammers use programs to automate their spamming. The 'Block Comment Spam Bots' (BCSB) plugin counter-targets their process, nipping it in the bud.</p>  -->
<p><b> A simple to use plugin that immediately stops automated spam.</b> Install and forget, and any automated spam targeting your native Word Press comments is immediately terminated, and never gets written to your WOP database. Automated spam will never show up in your spam queue. Ever.
</p>
<p>As no legitimate user will use the professional spammers automated process which relies on CURL and WET commands, real users will never notice the BCSB plugin at work. There are no CAPTCHAS for your visitors to interact with - or to be irritated by.</p>

<p>On the admin side, there are no blacklists, special keys (like Askimet), or overloaded spam queues. And comment spam won't impact your database - they are just tossed into the 'bit bucket'.</p>

<p><b>Install the plugin and that's it. Invisible, to you and your visitors.</b> The only change you will notice is in your admin area. The list of new comments now has a green check next to them. That way you know that comment was made on your website and was not bypassed by hacking spammers connecting directly to your server.</p>

<p>All that remains is comments made by real people, and while real people can spam, it takes them time and effort. The amount of spam from real people is a lot more manageable than the tsunami from automated spammers, saving you time to concentrate on the important things in life, like your readers, and making connections. </p>

We've tested it on multiple websites and it wipes out automated spam completely. If it doesn't on your site, please let us know.

<h2 align='center'>Geeky Things</h2>
<h4 align='center'><i>If you want to know how it works - and how to test it.</i></h4>
<p><b>Comment spam is irritating.</b> And easy for the bots to add to your site. All the bot needs to do is to send the comment field content directly to the site, bypassing the comment form. </p>
<p>Bypassing the comment form by posting directly (via CURL or WGET commands), is quite easy. Just send the post ID number, and the bot's fake name and email, and the spammy content. Boom! Comment spam is on your site!</p>
<p>You could block all comments, but maybe you like comments on your site. You could try modifying the comment form, or adding hidden fields, or captchas, but the bot doesn't use the comment form, so that's not effective.</p>
<p>You need to block the direct posting of comments. And that's what this plugin does. Quickly and easily.</p>
<p>We add a hidden field to the comment form with a random value. Besides, and then we change that field's value after a short delay. When the comment is processed, we add a check for that changed hidden field name and value. A 'normal' comment, entered via the form, will have those values. So the comment is processed normally.</p>
<p>But a bot, since they are bypassing the form, will not have the proper hidden value. So the plugin just throws that comment into the bit-bucket. And, just for fun, we give them a polite error message. (I suppose we could redirect them somewhere, but this is a 'kinder and gentler' age...)</p>
<p>There is also a short delay until the 'submit' button is displayed. </p>
<p><b>The result - no bot-submitted comment spam! </b>And no spam cluttering up your comments database table. You'll just see this message on your Admin Dashboard:</p>
<div align='center'><img src="<?php echo $image_url; ?>assets/there-is-nothing-in-your-spam-queue.jpg" width="395" height="36" alt="" style="border: thin solid #CCC;"  class=' bcsb_shadow'></div>
<h3><b>But wait! There's more!!</b></h3>
<p>Some bot spammers will go the extra step to automate filling out the actual form. So we catch them by using a bit of JavaScript to change the value of the hidden field to a random value. The bot spammer won't see the hidden field, so their bot comment is blocked.</p>
<p>But we go one step further - we change the hidden field after a delay. The automated bot can't see that change, so we catch them.</p>
<p>And so you can see valid comments, we add a column to the Admin, Comments list screen. You'll see an green checkmark in that column for every form-submitted value. But you won't see the bot-submitted comments. They are never processed. You'll always see a '0 spam comments' on your Comments screen. Huzzah !!</p>
<h3><b>Do you want to test the automated comment spam process?</b></h3>
<p>Here's the command to run on your command line (the old "DOS prompt"). Copy/paste this onto the command line of your system with this plugin activate, and then look at the Admin, Comments screen. Your bot-submitted comment is blocked.</p>
<p>Here's the CURL command that a bot spammer could use, and that you can use to test this plugin. Note that we aren't telling anything secret; the spammers already know this. We added a random number at the end to allow you to test multiple times. Click inside the box to copy the command into your clipboard. Then paste it into the Command prompt and press Enter key. (Get the command prompt with the "Windows" key and type in "Command" and press Enter.)</p>
<div  id='bot_command' class='bcsb_box_yellow bcsb_shadow' readonly  onclick="copyTextFromElement('bot_command');"><?php echo $bot_command; ?></div>
<p>And the result of blocking direct access to the wp-comments-post.php file should be something similar to this, where you can see the 'polite bug-off' message we use (along with some other HTML code):</p>
<div align="center"><img src="<?php echo $image_url; ?>/assets/curl-result.jpg" height="47" alt="" class=' bcsb_shadow'></div>
<p>You might also see something similar to this, which indicates an <i>.htaccess</i> directive might be blocking the attempt:</p>
<div align="center"><img src="<?php echo $image_url; ?>/assets/document-has-moved.jpg" height="47" alt="" class=' bcsb_shadow'></div>
<p>Try the CURL command with this plugin disabled. The comment will not be blocked - it will show up in the site's Admin, Comments screen. If you want to try it again, refresh this screen to get a new CURL command with a new random value (so you don't see a 'duplicate comment' message).</p>
<h3 align='center' class='bcsb_back_yellow'><i>With our plugin enabled, there will be no more Comment spam on your site! </i></h3>
<hr />
<p><strong>Tell us how the Block Comment Spam Bots plugin works for you - leave a <a href="https://wordpress.org/plugins/block-comment-spam-bots/#reviews" title="Block Comment Spam Bots Reviews" target="_blank" >review or rating</a> on our plugin page.&nbsp;&nbsp;&nbsp;<a href="https://wordpress.org/support/plugin/block-comment-spam-bots/" title="Help or Questions" target="_blank">Get Help or Ask Questions here</a>.</strong></p>
<p><b>Plus - we have a way to block Contact form spam.</b> Just visit our <a href="https://www.FormSpammerTrap.com" target="_blank">https://www.FormSpammerTrap.com</a> site. It's fully free. Check it out!</p>
<hr>
<script>
function copyTextFromElement(elementID) {
    let element = document.getElementById(elementID); //select the element
    let elementText = element.textContent; //get the text content from the element
    copyText(elementText); //use the copyText function below
}
function copyText(text) {
    navigator.clipboard.writeText(text);
        alert("Copied the command into clipboard:\n\n " + text + "\n\nPaste it into the Comment (DOS) prompt.");
}
</script>
<?php
return;}

// ============================================================================
	// display the copyright info part of the admin  page
	// ----------------------------------------------------------------------------
	function bcsb_info_bottom() {
		// print copyright with current year, never needs updating
		$xstartyear    = "2016";
		$xname         = "Rick Hellewell";
		$xcompanylink1 = ' <a href="http://CellarWeb.com" title="CellarWeb" >CellarWeb.com</a>';
		echo '<hr><div style="background-color:#9FE8FF;padding-left:15px;padding:10px 0 10px 0;margin:15px 0 15px 0;">
<p align="center">Copyright &copy; ' . $xstartyear . '  - ' . date("Y") . ' by ' . $xname . ' and ' . $xcompanylink1;
		echo ' , All Rights Reserved. Released under GPL2 license.</p></div><hr>';
		return;
	}

	// end  copyright ---------------------------------------------------------

	// ----------------------------------------------------------------------------
	// ``end of admin area
	//here's the closing bracket for the is_admin thing
}

// ============================================================================
// add the css to the settings page
// ----------------------------------------------------------------------------
function bcsb_init() {
	wp_register_style('bcsb_namespace', plugins_url('/css/settings.css', __FILE__), array(), time());
	wp_enqueue_style('bcsb_namespace'); // gets the above css file in the proper spot
}

add_action('init', 'bcsb_init');

// ============================================================================
//  settings page sidebar content
// ----------------------------------------------------------------------------
function bcsb_sidebar() {
	?>
    <h3 align="center">But wait, there's more!</h3>
    <p>There's our plugin that will automatically add your <strong>Amazon Affiliate code</strong> to any Amazon links - even links entered in comments by others!&nbsp;&nbsp;&nbsp;Check out our nifty <a href="https://wordpress.org/plugins/amazolinkenator/" target="_blank">AmazoLinkenator</a>! It will probably increase your Amazon Affiliate revenue!</p>
    <p>We've got a <a href="https://wordpress.org/plugins/simple-gdpr/" target="_blank"><strong>Simple GDPR</strong></a> plugin that displays a GDPR banner for the user to acknowledge. And it creates a generic Privacy page, and will put that Privacy Page link at the bottom of all pages.</p>
    <p>How about our <strong><a href="https://wordpress.org/plugins/url-smasher/" target="_blank">URL Smasher</a></strong> which automatically shortens URLs in pages/posts/comments?</p>
    <hr />
    <p><strong>To reduce and prevent spam</strong>, check out:</p>
    <p><a href="https://wordpress.org/plugins/formspammertrap-for-comments/" target="_blank"><strong>FormSpammerTrap for Comments</strong></a>: reduces spam without captchas, silly questions, or hidden fields - which don't always work. </p>
    <p><a href="https://wordpress.org/plugins/formspammertrap-for-contact-form-7/" target="_blank"><strong>FormSpammerTrap for Contact Form 7</strong></a>: reduces spam when you use Contact Form 7 forms. All you do is add a little shortcode to the contact form.</p>
    <hr />
    <p>For <strong>multisites</strong>, we've got:

    <ul>
        <li><strong><a href="https://wordpress.org/plugins/multisite-comment-display/" target="_blank">Multisite Comment Display</a></strong> to show all comments from all subsites.</li>
        <li><strong><a href="https://wordpress.org/plugins/multisite-post-reader/" target="_blank">Multisite Post Reader</a></strong> to show all posts from all subsites.</li>
        <li><strong><a href="https://wordpress.org/plugins/multisite-media-display/" target="_blank">Multisite Media Display</a></strong> shows all media from all subsites with a simple shortcode. You can click on an item to edit that item. </li>
    </ul>
    </p>
    <hr />
    <p><strong>They are all free and fully featured!</strong></p>
    <hr />
    <p>I don't drink coffee, but if you are inclined to donate any amount because you like my WordPress plugins, go right ahead! I'll grab a nice hot chocolate, and maybe a blueberry muffin. Thanks!</p>
    <div align="center">
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="SKSN99LR67WS6">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
    </div>
    <hr />
    <p><strong>Privacy Notice</strong>: This plugin does not store or use any personal information or cookies.</p>
<!--</div> -->
<?php
bscb_cellarweb_logo();
	return;
}

// ============================================================================
// show the logo in the sidebar
// ----------------------------------------------------------------------------
function bscb_cellarweb_logo() {
	?>
 <p align="center"><a href="https://www.cellarweb.com" target="_blank" title="CellarWeb.com site"><img src="<?php echo plugin_dir_url(__FILE__); ?>assets/cellarweb-logo-2022.jpg"  width="90%" class="bcsb_shadow" ></a></p>
 <?php
return;
}

// ============================================================================
// show the footer
// ----------------------------------------------------------------------------
function bcsb_footer() {
	?>
<p align="center"><strong>Copyright &copy; 2016- <?php echo date('Y'); ?> by Rick Hellewell and <a href="http://CellarWeb.com" title="CellarWeb" >CellarWeb.com</a> , All Rights Reserved. Released under GPL2 license. <a href="http://cellarweb.com/contact-us/" target="_blank" title="Contact Us">Contact us page</a>.</strong></p>
<?php
return;
}

// ============================================================================
// script that changes the hidden field's value, and displays the submit button
//      after a delay   -
// the  add_action('wp_footer', 'bcsb_change_guid', 20);   is added if the hidden field
//		added - to prevent JS code on non-comment pages  (version 2.6)
// --------------------------------------------------------------------------

function bcsb_change_guid() {

	?>
<script>
                setTimeout(function()
                    {
                        document.getElementById("bcsb_hidden_guid").value='<?php echo wp_generate_uuid4(); ?>';
                        document.getElementById("submit").style.display = "block";


                    }
                    ,8000);
</script>
<?php

	return;
}

// END
// ============================================================================
// ============================================================================

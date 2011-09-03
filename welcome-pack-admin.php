<?php
/**
 * The idea of using the meta boxes came from Joost de Valk (http://yoast.com).
 * The implementation of the above is credited to http://www.code-styling.de/english/how-to-use-wordpress-metaboxes-at-own-plugins.
 * Thanks to both!
 *
 * @package Welcome Pack
 * @subpackage Administration
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

/**
 * Setting screens and options
 *
 * @since 3.0
 */
class DP_Welcome_Pack_Admin {
	/**
	 * Constructor.
	 *
	 * @since 3.0
	 */
	public function __construct() {
		$this->setup_menu();
	}

	/**
	 * Set up the admin menu
	 *
	 * @since 3.0
	 */
	protected function setup_menu() {
		add_options_page( __( 'Welcome Pack', 'dpw' ), __( 'Welcome Pack', 'dpw' ), 'manage_options', 'welcome-pack', array( $this, 'admin_page' ) );
		add_action( 'load-settings_page_welcome-pack', array( $this, 'init' ) );
	}

	/**
	 * Initialise common elements for all pages of the admin screen.
	 *
	 * @since 3.0
	 */
	public function init() {
		if ( !empty( $_GET['tab'] ) ) {
			if ( 'support' == $_GET['tab'] )
				$tab = 'support';
			elseif ( 'emails' == $_GET['tab'] )
				$tab = 'emails';

		}	else {
			$tab = 'settings';
		}

		// How many columns does this page have by default?
		add_screen_option( 'layout_columns', array( 'max' => 2 ) );

		// Support tab
		if ( 'support' == $tab )
			add_meta_box( 'dpw-helpushelpyou', __( 'Help Us Help You', 'dpw' ), array( $this, 'helpushelpyou'), 'settings_page_welcome-pack', 'side', 'high' );
		else
			add_meta_box( 'dpw-likethis', __( 'Love Welcome Pack?', 'dpw' ), array( $this, 'like_this_plugin' ), 'settings_page_welcome-pack', 'side', 'default' );

		// All tabs
		add_meta_box( 'dpw-paypal', __( 'Give Kudos', 'dpw' ), array( $this, 'paypal' ), 'settings_page_welcome-pack', 'side', 'default' );
		add_meta_box( 'dpw-latest', __( 'Latest News', 'dpw' ), array( $this, 'metabox_latest_news' ), 'settings_page_welcome-pack', 'side', 'default' );

		// Javascripts for meta box
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'dashboard' );
		?>

			<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
			  {parsetags: 'explicit'}
			</script>
			<script type="text/javascript">gapi.plusone.go();</script>

		<?php
	}

	/**
	 * Outputs admin page HTML
	 *
	 * @global int $screen_layout_columns Number of columns shown on this admin page
	 * @since 3.0
	 */
	public function admin_page() {
		global $screen_layout_columns;

		if ( !empty( $_GET['tab'] ) && 'support' == $_GET['tab'] )
			$tab = 'support';
		else
			$tab = 'settings';

		$updated  = $this->maybe_save();
		$url      = network_admin_url( 'options-general.php?page=welcome-pack' );
		$settings = DP_Welcome_Pack::get_settings();
	?>

		<style type="text/css">
		#dpw-helpushelpyou ul {
			list-style: disc;
			padding-left: 2em;
		}
		#dpw-likethis #___plusone_0,
		#dpw-likethis .fb {
			max-width: 49% !important;
			width: 49% !important;
		}
		#dpw-likethis .fb {
			height: 20px;
		}
		#dpw-paypal .inside {
			text-align: center;
		}
		#dpw_contact_form,
		#dpw_contact_form .button-primary {
			margin-top: 2em;
		}
		#dpw_contact_form textarea,
		#dpw_contact_form input[type="text"]  {
			width: 100%;
		}
		.dpw_friends,
		.dpw_groups,
		.dpw_startpage,
		.dpw_welcomemsg {
			margin-right: 2em;
		}
		</style>

		<div class="wrap">
			<?php screen_icon( 'options-general' ); ?>

			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_attr( $url ); ?>"                       class="nav-tab <?php if ( 'settings' == $tab )  : ?>nav-tab-active<?php endif; ?>"><?php _e( 'Welcome Pack', 'dpw' );     ?></a>
				<a href="<?php echo esc_attr( $url . '&amp;tab=support' ); ?>"  class="nav-tab <?php if ( 'support'  == $tab  ) : ?>nav-tab-active<?php endif; ?>"><?php _e( 'Get Support', 'dpw' ); ?></a>
			</h2>

			<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
				<div id="side-info-column" class="inner-sidebar">
					<?php do_meta_boxes( 'settings_page_welcome-pack', 'side', $settings ); ?>
				</div>

				<div id="post-body" class="has-sidebar">
					<div id="post-body-content" class="has-sidebar-content">
						<?php
						if ( 'support' == $tab )
							$this->admin_page_support();
						else
							$this->admin_page_settings( $settings, $updated );
						?>
					</div><!-- #post-body-content -->
				</div><!-- #post-body -->

			</div><!-- #poststuff -->
		</div><!-- .wrap -->

	<?php
	}

	/**
	 * Support tab content for the admin page.
	 * Also handles contact form submission.
	 *
	 * @since 3.0
	 */
	protected function admin_page_support() {
		// Email contact form
		if ( !empty( $_POST['contact_body'] ) && !empty( $_POST['contact_type'] ) && !empty( $_POST['contact_email'] ) ) {
			$body  = force_balance_tags( wp_filter_kses( stripslashes( $_POST['contact_body'] ) ) );
			$type  = force_balance_tags( wp_filter_kses( stripslashes( $_POST['contact_type'] ) ) );
			$email = sanitize_email( force_balance_tags( wp_filter_kses( stripslashes( $_POST['contact_email'] ) ) ) );

			if ( $body && $type && $email && is_email( $email ) )
				$email_sent = wp_mail( array( 'paul@byotos.com', $email ), "Welcome Pack support request: " . $type, $body );
		}
	?>

		<p><?php printf( __( "Have you found a bug or do you have a great idea for the next release? Please make a report on <a href='%s'>BuddyPress.org</a>, or use the form below to get in contact. We're listening.", 'dpw' ), 'http://buddypress.org/community/groups/welcome-pack/forum/' ); ?></p>	

		<?php if ( isset( $email_sent ) ) : ?>
			<div class="welcomepack updated below-h2">
				<p><?php _e( "Thanks, we've received your message and have emailed you a copy for your records. We'll be in touch soon!", 'dpw' ); ?></p>
			</div>
		<?php endif; ?>

		<form id="dpw_contact_form" name="contact_form" method="post" action="<?php echo admin_url( 'options-general.php?page=welcome-pack&amp;tab=support' ); ?>">
			<p><?php _e( "What type of request do you have?", 'dpw' ); ?></p>
			<select name="contact_type">
				<option value="bug" selected="selected"><?php _e( "Bug report", 'dpw' ); ?></option>
				<option value="idea"><?php _e( "Idea", 'dpw' ); ?></option>
				<option value="suggestion"><?php _e( "Other support request", 'dpw' ); ?></option>
			</select>

			<p><?php _e( "How can we help?", 'dpw' ); ?></p>
			<textarea id="contact_body" name="contact_body"></textarea>

			<p><?php _e( "What's your email address?", 'dpw' ); ?></p>
			<input type="text" name="contact_email" />
			<br />

			<input type="submit" class="button-primary" value="<?php _e( 'Send', 'dpw' ); ?>" />
		</form>
	<?php
	}

	/**
	 * Main tab's content for the admin page
	 *
	 * @param array $settings Plugin settings (from DB)
	 * @param bool $updated Have settings been updated on the previous page submission?
	 * @since 3.0
	 */
	protected function admin_page_settings( $settings, $updated ) {
	?>
		<?php if ( $updated ) : ?>
			<div id="message" class="updated below-h2"><p><?php _e( 'Your preferences have been updated.', 'dpw' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo admin_url( 'options-general.php?page=welcome-pack' ); ?>" id="dpw-form">
			<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
			<?php wp_nonce_field( 'dpw-admin', 'dpw-admin-nonce', false ); ?>

			<p><?php _e( 'When a user registers on your site, Welcome Pack lets you automatically send them a friend or group invitation, a Welcome Message and can redirect them to a Start Page.', 'dpw' ); ?></p>

			<h4><?php _e( 'Friends', 'dpw' ); ?></h4>
			<p><?php _e( "Invite the new user to become friends with certain members. It's a great way of teaching people how the friend acceptance process works on your site, and how they can use friendships to filter activity streams.", 'dpw' ); ?></p>
			<label><?php _e( 'On', 'dpw' ); ?> <input type="radio" name="dpw_friendstoggle" class="dpw_friends" value="on" <?php checked( $settings['dpw_friendstoggle'] ); ?>/></label>
			<label><?php _e( 'Off', 'dpw' ); ?> <input type="radio" name="dpw_friendstoggle" class="dpw_friends" value="off" <?php checked( $settings['dpw_friendstoggle'], false ); ?>/></label>

			<h4><?php _e( 'Groups', 'dpw' ); ?></h4>
			<p><?php _e( "Ask the new user if they'd like to join a group. You could use this to invite all new users on your site to join a support group, to keep all of your frequently asked questions in the same place.", 'dpw' ); ?></p>
			<label><?php _e( 'On', 'dpw' ); ?> <input type="radio" name="dpw_groupstoggle" class="dpw_groups" value="on" <?php checked( $settings['dpw_groupstoggle'] ); ?>/></label>
			<label><?php _e( 'Off', 'dpw' ); ?> <input type="radio" name="dpw_groupstoggle" class="dpw_groups" value="off" <?php checked( $settings['dpw_groupstoggle'], false ); ?>/></label>

			<h4><?php _e( 'Start Page', 'dpw' ); ?></h4>
			<p><?php _e( "When the new user logs into your site for the very first time, use Start Page to redirect them anywhere you'd like. This complements the Welcome Message fantastically; create a page or blog post which showcases the features of your site.", 'dpw' ); ?></p>
			<label><?php _e( 'On', 'dpw' ); ?> <input type="radio" name="dpw_startpagetoggle" class="dpw_startpage" value="on" <?php checked( $settings['dpw_startpagetoggle'] ); ?>/></label>
			<label><?php _e( 'Off', 'dpw' ); ?> <input type="radio" name="dpw_startpagetoggle" class="dpw_startpage" value="off" <?php checked( $settings['dpw_startpagetoggle'], false ); ?>/></label>

			<h4><?php _e( 'Welcome Message', 'dpw' ); ?></h4>
			<p><?php _e( "Send the newly-registered user a private message; use this to welcome people to your site and help them get started.", 'dpw' ); ?></p>
			<label><?php _e( 'On', 'dpw' ); ?> <input type="radio" name="dpw_welcomemsgtoggle" class="dpw_welcomemsg" value="on" <?php checked( $settings['dpw_welcomemsgtoggle'] ); ?>/></label>
			<label><?php _e( 'Off', 'dpw' ); ?> <input type="radio" name="dpw_welcomemsgtoggle" class="dpw_welcomemsg" value="off" <?php checked( $settings['dpw_welcomemsgtoggle'], false ); ?>/></label>

			<p><input type="submit" class="button-primary" value="<?php _e( 'Update Settings', 'dpw' ); ?>" /></p>
		</form>

	<?php
	}

	/**
	 * Check for and handle form submission.
	 *
	 * @return bool Have settings been updated?
	 * @since 1.1
	 * @static
	 */
	protected static function maybe_save() {
		$settings = $existing_settings = DP_Welcome_Pack::get_settings();
		$updated  = false;

		if ( !empty( $_POST['dpw_friendstoggle'] ) ) {
			if ( 'on' == $_POST['dpw_friendstoggle'] )
				$settings['dpw_friendstoggle'] = true;
			else
				$settings['dpw_friendstoggle'] = false;
		}

		if ( !empty( $_POST['dpw_groupstoggle'] ) ) {
			if ( 'on' == $_POST['dpw_groupstoggle'] )
				$settings['dpw_groupstoggle'] = true;
			else
				$settings['dpw_groupstoggle'] = false;
		}

		if ( !empty( $_POST['dpw_startpagetoggle'] ) ) {
			if ( 'on' == $_POST['dpw_startpagetoggle'] )
				$settings['dpw_startpagetoggle'] = true;
			else
				$settings['dpw_startpagetoggle'] = false;
		}

		if ( !empty( $_POST['dpw_welcomemsgtoggle'] ) ) {
			if ( 'on' == $_POST['dpw_welcomemsgtoggle'] )
				$settings['dpw_welcomemsgtoggle'] = true;
			else
				$settings['dpw_welcomemsgtoggle'] = false;
		}

		if ( $settings != $existing_settings ) {
			check_admin_referer( 'dpw-admin', 'dpw-admin-nonce' );
			update_site_option( 'welcomepack', $settings );
			$updated = true;
		}

		return $updated;
	}

	/**
	 * Latest news metabox
	 *
	 * @param array $settings Plugin settings (from DB)
	 * @since 3.0
	 */
	public function metabox_latest_news( $settings) {
		$rss = fetch_feed( 'http://feeds.feedburner.com/BYOTOS' );
		if ( !is_wp_error( $rss ) ) {
			$content = '<ul>';
			$items = $rss->get_items( 0, $rss->get_item_quantity( 3 ) );

			foreach ( $items as $item )
				$content .= '<li><p><a href="' . esc_url( $item->get_permalink(), null, 'display' ) . '">' . apply_filters( 'dpw_admin_metabox_latest_news', stripslashes( $item->get_title() ) ) . '</a></p></li>';

			echo $content;

		} else {
			echo '<ul><li class="rss">' . __( 'No news; check back later.', 'dpw' ) . '</li></ul>';
		}
	}

	/**
	 * "Help Me Help You" metabox
	 *
	 * @global wpdb $wpdb WordPress database object
	 * @global string $wp_version WordPress version number
	 * @global WP_Rewrite $wp_rewrite WordPress Rewrite object for creating pretty URLs
	 * @global object $wp_rewrite
	 * @param array $settings Plugin settings (from DB)
	 * @since 3.0
	 */
	public function helpushelpyou( $settings ) {
		global $wpdb, $wp_rewrite, $wp_version;

		$active_plugins = array();
		$all_plugins    = apply_filters( 'all_plugins', get_plugins() );

		foreach ( $all_plugins as $filename => $plugin ) {
			if ( 'Welcome Pack' != $plugin['Name'] && 'BuddyPress' != $plugin['Name'] && is_plugin_active( $filename ) )
				$active_plugins[] = $plugin['Name'] . ': ' . $plugin['Version'];
		}
		natcasesort( $active_plugins );

		if ( !$active_plugins )
			$active_plugins[] = __( 'No other plugins are active', 'dpw' );

		if ( is_multisite() ) {
			if ( is_subdomain_install() )
				$is_multisite = __( 'subdomain', 'dpw' );
			else
				$is_multisite = __( 'subdirectory', 'dpw' );

		} else {
			$is_multisite = __( 'no', 'dpw' );
		}

		if ( 1 == constant( 'BP_ROOT_BLOG' ) )
			$is_bp_root_blog = __( 'standard', 'dpw' );
		else
			$is_bp_root_blog = __( 'non-standard', 'dpw' );

		$is_bp_default_child_theme = __( 'no', 'dpw' );
		$theme = current_theme_info();

		if ( 'BuddyPress Default' == $theme->parent_theme )
			$is_bp_default_child_theme = __( 'yes', 'dpw' );

		if ( 'BuddyPress Default' == $theme->name )
			$is_bp_default_child_theme = __( 'n/a', 'dpw' );

	  if ( empty( $wp_rewrite->permalink_structure ) )
			$custom_permalinks = __( 'default', 'dpw' );
		else
			if ( strpos( $wp_rewrite->permalink_structure, 'index.php' ) )
				$custom_permalinks = __( 'almost', 'dpw' );
			else
				$custom_permalinks = __( 'custom', 'dpw' );
	?>

		<p><?php _e( "If you have trouble, a little information about your site goes a long way.", 'dpw' ); ?></p>

		<h4><?php _e( 'Versions', 'dpw' ); ?></h4>
		<ul>
			<li><?php printf( __( 'Welcome Pack: %s', 'dpw' ), WELCOME_PACK_VERSION ); ?></li>
			<li><?php printf( __( 'BP_ROOT_BLOG: %s', 'dpw' ), $is_bp_root_blog ); ?></li>
			<li><?php printf( __( 'BuddyPress: %s', 'dpw' ), BP_VERSION ); ?></li>
			<li><?php printf( __( 'MySQL: %s', 'dpw' ), $wpdb->db_version() ); ?></li>
			<li><?php printf( __( 'Permalinks: %s', 'dpw' ), $custom_permalinks ); ?></li>
			<li><?php printf( __( 'PHP: %s', 'dpw' ), phpversion() ); ?></li>
			<li><?php printf( __( 'WordPress: %s', 'dpw' ), $wp_version ); ?></li>
			<li><?php printf( __( 'WordPress multisite: %s', 'dpw' ), $is_multisite ); ?></li>
		</ul>

		<h4><?php _e( 'Theme', 'dpw' ); ?></h4>
		<ul>
			<li><?php printf( __( 'BP-Default child theme: %s', 'dpw' ), $is_bp_default_child_theme ); ?></li>
			<li><?php printf( __( 'Current theme: %s', 'dpw' ), $theme->name ); ?></li>
		</ul>

		<h4><?php _e( 'Active Plugins', 'dpw' ); ?></h4>
		<ul>
			<?php foreach ( $active_plugins as $plugin ) : ?>
				<li><?php echo esc_html( $plugin ); ?></li>
			<?php endforeach; ?>
		</ul>

	<?php
	}

	/**
	 * Social media sharing metabox
	 *
	 * @param array $settings Plugin settings (from DB)
	 * @since 3.0
	 */
	public function like_this_plugin( $settings ) {
	?>

		<p><?php _e( 'Why not do any or all of the following:', 'dpw' ); ?></p>
		<ul>
			<li><p><a href="http://wordpress.org/extend/plugins/welcome-pack/"><?php _e( 'Give it a five star rating on WordPress.org.', 'dpw' ); ?></a></p></li>
			<li><p><a href="http://buddypress.org/community/groups/welcome-pack/reviews/"><?php _e( 'Write a review on BuddyPress.org.', 'dpw' ); ?></a></p></li>
			<li><p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=P3K7Z7NHWZ5CL&amp;lc=GB&amp;item_name=B%2eY%2eO%2eT%2eO%2eS%20%2d%20BuddyPress%20plugins&amp;currency_code=GBP&amp;bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted"><?php _e( 'Fund development.', 'dpw' ); ?></a></p></li>
			<li>
				<g:plusone size="medium" href="http://wordpress.org/extend/plugins/welcome-pack/"></g:plusone>
				<iframe class="fb" allowTransparency="true" frameborder="0" scrolling="no" src="http://www.facebook.com/plugins/like.php?href=http://wordpress.org/extend/plugins/welcome-pack/&amp;send=false&amp;layout=button_count&amp;width=90&amp;show_faces=false&amp;action=recommend&amp;colorscheme=light&amp;font=arial"></iframe>
			</li>
		</ul>

	<?php
	}

	/**
	 * Paypal donate button metabox
	 *
	 * @param array $settings Plugin settings (from DB)
	 * @since 3.0
	 */ 
	public function paypal( $settings ) {
	?>

		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
			<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="<?php esc_attr_e( 'PayPal', 'dpw' ); ?>">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1" />
		</form>

	<?php
	}
}
new DP_Welcome_Pack_Admin();








/**
 * Produces the main admin page (/options-general.php?page=welcome-pack)
 *
 * @global int $screen_layout_columns Number of columns to display
 * @see dpw_add_admin_menu()
 * @since 2.0
 */
function dpw_admin_screen() {
	global $screen_layout_columns;

	$settings = get_site_option( 'welcomepack' );
?>
<div id="bp-admin">
	<div id="dpw-admin-metaboxes-general" class="wrap">

		<div id="bp-admin-header">
			<h3><?php _e( 'BuddyPress', 'dpw' ); ?></h3>
			<h4><?php _e( 'Welcome Pack', 'dpw' ); ?></h4>
		</div>

		<div id="bp-admin-nav">
			<ol>
				<li <?php echo 'class="current"' ?>><a href="<?php echo admin_url( 'options-general.php?page=welcome-pack' ); ?>"><?php _e( 'Friends, Groups <span class="ampersand">&amp;</span> Welcome Message', 'dpw' ); ?></a></li>
				<li><a href="<?php echo admin_url( 'edit.php?post_type=dpw_email' ); ?>"><?php _e( 'Emails', 'dpw' ); ?></a></li>
			</ol>
		</div>

		<?php if ( isset( $_GET['updated'] ) ) : ?>
			<div id="message" class="updated">
				<p><?php _e( 'Your Welcome Pack settings have been saved.', 'dpw' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="dpw-spacer">
			<p><?php _e( 'When a user registers on your site, Welcome Pack lets you automatically send them a friend or group invitation, a Welcome Message and can redirect them to a Start Page.', 'dpw' ); ?></p>
		</div>

		<form method="post" action="options.php" id="welcomepack">
			<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
			<?php settings_fields( 'dpw-settings-group' ); ?>

			<div id="poststuff" class="metabox-holder<?php echo ( 2 == $screen_layout_columns ) ? ' has-right-sidebar' : '' ?>">
				<div id="side-info-column" class="inner-sidebar">
					<?php do_meta_boxes( 'settings_page_welcome-pack', 'side', $settings ); ?>
				</div>

				<div id="post-body" class="has-sidebar">
					<div id="post-body-content" class="has-sidebar-content">
						<?php do_meta_boxes( 'settings_page_welcome-pack', 'normal', $settings ); ?>
					</div>

					<p><input type="submit" class="button-primary" value="<?php _e( 'Save Welcome Pack Settings', 'dpw' ); ?>" /></p>
				</div>
			</div><!-- #poststuff -->
		</form>

	</div><!-- #dpw-admin-metaboxes-general -->
</div><!-- #bp-admin -->
<?php
}

/* TODO: need to figure out how to dynamically set bottom-margin = 0 of the last div.setting-group */
function dpw_admin_screen_configurationbox( $settings ) {
	global $bp, $wpdb;

	$defaults = array(
		'friends'           => array(),
		'friendstoggle'     => false,

		'groups'            => array(),
		'groupstoggle'      => false,

		'startpage'         => '',
		'startpagetoggle'   => false,

		'welcomemsg'        => '',
		'welcomemsgsender'  => 0,
		'welcomemsgsubject' => '',
		'welcomemsgtoggle'  => false
	);

	$r = wp_parse_args( $settings, $defaults );
	extract( $r );

	$members = array();
	if ( bp_is_active( 'friends' ) || bp_is_active( 'messages' ) ) {
		if ( is_multisite() )
			$column = "spam";
		else
			$column = "user_status";

		$members = $wpdb->get_results( $wpdb->prepare( "SELECT ID, display_name FROM $wpdb->users WHERE $column = 0 ORDER BY display_name ASC" ) );
	}

	if ( bp_is_active( 'groups' ) )
		$groups = $wpdb->get_results( $wpdb->prepare( "SELECT id, name FROM {$bp->groups->table_name} ORDER BY name ASC" ) );
?>
	<?php if ( bp_is_active( 'friends' ) ) : ?>
		<div class="setting setting-group setting-friends <?php if ( !$friendstoggle ) echo 'initially-hidden' ?>">
			<div class="settingname">
				<p><?php _e( 'Invite the new user to become friends with these people:', 'dpw' ); ?></p>
			</div>
			<div class="settingvalue">
				<select multiple="multiple" name="welcomepack[friends][]" style="overflow-y: hidden">
				<?php foreach ( (array)$members as $member ) : ?>
					<option value="<?php echo esc_attr( apply_filters( 'bp_get_member_user_id', $member->ID ) ); ?>"<?php foreach ( (array)$friends as $id ) { if ( $member->ID == $id ) echo " selected='selected'"; } ?>><?php echo apply_filters( 'bp_core_get_user_displayname', $member->display_name, $member->ID ); ?></option>
				<?php endforeach; ?>
				</select>
			</div>
			<div style="clear: left"></div>
		</div>
	<?php endif ?>

	<?php if ( bp_is_active( 'groups' ) ) : ?>
		<div class="setting setting-group setting-groups <?php if ( !$groupstoggle ) echo 'initially-hidden' ?>">
			<div class="settingname">
				<p><?php _e( "Ask the new user if they'd like to join these groups:", 'dpw' ); ?></p>
			</div>
			<div class="settingvalue">
				<select multiple="multiple" name="welcomepack[groups][]">
				<?php foreach( (array)$groups as $group ) : ?>
					<option value="<?php echo esc_attr( apply_filters( 'bp_get_group_id', $group->id ) ); ?>"<?php foreach ( (array)$groups as $id ) { if ( $group->id == $id ) echo " selected='selected'"; } ?>><?php echo apply_filters( 'bp_get_group_name', $group->name ); ?></option>
				<?php endforeach; ?>
				</select>
			</div>
			<div style="clear: left"></div>
		</div>
	<?php endif ?>

	<div class="setting-group setting-startpage <?php if ( !$startpagetoggle ) echo 'initially-hidden' ?>">
		<div class="setting wide">
			<div class="settingname">
				<p><?php _e( "When the new user logs into your site for the very first time, redirect them to this URL:", 'dpw' ); ?></p>
			</div>
			<div class="settingvalue">
				<input type="url" name="welcomepack[startpage]" value="<?php echo esc_attr( apply_filters( 'dpw_admin_settings_startpage', $startpage ) ); ?>" />
			</div>
			<div style="clear: left"></div>
		</div>
	</div>

	<?php if ( bp_is_active( 'messages' ) ) : ?>
		<div class="setting-welcomemsg setting-group <?php if ( !$welcomemsgtoggle ) echo 'initially-hidden' ?>">
			<div class="setting wide">
				<div class="settingname">
					<p><?php _e( 'Send the new user a Welcome Message&hellip;', 'dpw' ); ?></p>
				</div>
				<div class="settingvalue">
					<textarea name="welcomepack[welcomemsg]"><?php echo apply_filters( 'dpw_admin_settings_welcomemsg', $welcomemsg ); ?></textarea>
				</div>
				<div style="clear: left"></div>
			</div>

			<div class="setting">
				<div class="settingname">
					<p><?php _e( '&hellip;with this subject:', 'dpw' ); ?></p>
				</div>
				<div class="settingvalue">
					<input type="text" name="welcomepack[welcomemsgsubject]" value="<?php echo esc_attr( apply_filters( 'dpw_admin_settings_welcomemsg_subject', $welcomemsgsubject ) ); ?>" />
				</div>
				<div style="clear: left"></div>
			</div>

			<div class="setting">
				<div class="settingname">
					<p><?php _e( '&hellip;from this user:', 'dpw' ); ?></p>
				</div>
				<div class="settingvalue">
					<select name="welcomepack[welcomemsgsender]">
					<?php foreach ( (array)$members as $member ) : ?>
						<option value="<?php echo esc_attr( apply_filters( 'bp_get_member_user_id', $member->ID ) ); ?>"<?php if ( $welcomemsgsender && $member->ID == $welcomemsgsender ) echo " selected='selected'"; ?>><?php echo apply_filters( 'bp_core_get_user_displayname', $member->display_name, $member->ID ); ?></option>
					<?php endforeach; ?>
					</select>
				</div>
				<div style="clear: left"></div>
			</div>
		</div>
	<?php endif;
}
?>
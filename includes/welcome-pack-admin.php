<?php
/*
The idea of using the meta boxes came from Joost de Valk (http://yoast.com), your one stop-shop for a wide range of WordPress plugins and SEO advice.
The implementation of the above is credited to http://www.code-styling.de/english/how-to-use-wordpress-metaboxes-at-own-plugins.

Big thanks to both!
*/

//TODO: go through and pass URLs within i18n function calls by using sprintf?
function dpw_admin_screen_on_load() {
	add_meta_box( 'dpw-admin-metaboxes-sidebox-1', __( 'Like this plugin?', 'dpw' ), 'dpw_admin_screen_socialmedia', 'buddypress_page_welcome-pack', 'side', 'core' );
	add_meta_box( 'dpw-admin-metaboxes-sidebox-2', __( 'Need support?', 'dpw' ), 'dpw_admin_screen_support', 'buddypress_page_welcome-pack', 'side', 'core' );
	add_meta_box( 'dpw-admin-metaboxes-sidebox-3', __( 'Latest news from the author', 'dpw' ), 'dpw_admin_screen_news', 'buddypress_page_welcome-pack', 'side', 'core' );
	add_meta_box( 'dpw-admin-metaboxes-settingsbox', __( 'Settings', 'dpw' ), 'dpw_admin_screen_settingsbox', 'buddypress_page_welcome-pack', 'normal', 'core' );
	add_meta_box( 'dpw-admin-metaboxes-configurationbox', __( 'Configuration', 'dpw' ), 'dpw_admin_screen_configurationbox', 'buddypress_page_welcome-pack', 'normal', 'core' );

	/* Emails tab */
	add_meta_box( 'dpw-admin-metaboxes-sidebox-1', __( 'Like this plugin?', 'dpw' ), 'dpw_admin_screen_socialmedia', 'buddypress_page_welcome-pack-emails', 'side', 'core' );
	add_meta_box( 'dpw-admin-metaboxes-sidebox-2', __( 'Need support?', 'dpw' ), 'dpw_admin_screen_support', 'buddypress_page_welcome-pack-emails', 'side', 'core' );
	add_meta_box( 'dpw-admin-metaboxes-sidebox-3', __( 'Latest news from the author', 'dpw' ), 'dpw_admin_screen_news', 'buddypress_page_welcome-pack-emails', 'side', 'core' );
	add_meta_box( 'dpw-admin-metaboxes-emailsbox', __( 'Emails', 'dpw' ), 'dpw_admin_screen_emailsbox', 'buddypress_page_welcome-pack-emails', 'normal', 'core' );

	/* Help panel */
	$help = '<p>' . __( 'If you are changing a setting that allows text entry, you can use the following placeholder tags which will be automatically replaced when a private message or an email is being sent:', 'dpw' ) . '</p>';
	$help .= '<dl>';
	$help .= "<dt>USERNAME</dt><dd>" . __( "Replaced with the person's username.", 'dpw' ) . "</dd>";
	$help .= "<dt>NICKNAME</dt><dd>" . __( "Replaced with the person's name from their user profile.", 'dpw' ) . "</dd>";
	$help .= "<dt>USER_URL</dt><dd>" . __( "Replaced with the URL of their user profile.", 'dpw' ) . "</dd>";
	$help .= '</dl><br />';
	$help .= '<p>' . __( "The default behaviour for Friends and Groups is for invitations to be sent. If you would prefer to suppress those invitations and have them automatically accepted on the user's behalf, set <code>define( 'WELCOME_PACK_AUTOACCEPT_INVITATIONS', true );</code> in wp-config.php.", 'dpw' ) .'</p><br />';

	add_contextual_help( 'buddypress_page_welcome-pack', $help );
	add_filter( 'default_contextual_help', 'dpw_admin_screen_contextual_help' );
}

/* WP Help panel (the "Help" dropdown in the top-right of the page) */
function dpw_admin_screen_contextual_help( $default_text ) {
	return '<a href="http://wordpress.org/tags/welcome-pack" target="_blank">' . __( 'Support Forums', 'dpw' ) . '</a>';
}

// Tells WP that we support two columns
function dpw_admin_screen_layout_columns( $columns, $screen ) {
	if ( 'buddypress_page_welcome-pack' == $screen )
		$columns['buddypress_page_welcome-pack'] = 2;

	return $columns;
}
add_filter( 'screen_layout_columns', 'dpw_admin_screen_layout_columns', 10, 2 );

// Add "Settings" link on plugins menu
function dpw_admin_add_action_link( $links, $file ) {
	if ( 'welcome-pack/loader.php' != $file )
		return $links;

	$settings_link = '<a href="' . admin_url( 'admin.php?page=welcome-pack' ) . '">' . __( 'Settings', 'dpw' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'dpw_admin_add_action_link', 10, 2 );

function dpw_admin_register_settings() {
	register_setting( 'dpw-settings-group', 'welcomepack', 'dpw_admin_validate' );
}

function dpw_admin_screen_socialmedia( $settings ) {
?>
<p><?php _e( 'Why not do any or all of the following:', 'dpw' ) ?></p>
<ul>
	<li><p><?php _e( 'Tell your friends!', 'dpw' ) ?></a></p></li>
	<li><p><a href="http://wordpress.org/extend/plugins/welcome-pack/"><?php _e( 'Give it a good rating on WordPress.org', 'dpw' ) ?></a>.</p></li>
	<li><p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=P3K7Z7NHWZ5CL&amp;lc=GB&amp;item_name=B%2eY%2eO%2eT%2eO%2eS%20%2d%20BuddyPress%20plugins&amp;currency_code=GBP&amp;bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted"><?php _e( 'Thank me by donating towards future development', 'dpw' ) ?></a>.</p></li>
</ul>
<p><?php _e( 'Or share on one of these social networks:', 'dpw' ) ?></p>
<ul class="menu">
	<li><a target="_new" href="http://twitter.com/home?status=Check%20out%20Welcome%20Pack%20for%20http://wordpress.org/extend/plugins/welcome-pack/"><img src="<?php echo plugins_url( '/images/twitter_32.png', __FILE__ ) ?>" alt="<?php _e( 'Twitter', 'dpw' ) ?>" /></a></li>
	<li><a target="_new" href="http://www.facebook.com/sharer.php?u=http://wordpress.org/extend/plugins/welcome-pack/"><img src="<?php echo plugins_url( '/images/facebook_32.png', __FILE__ ) ?>" alt="<?php _e( 'Facebook', 'dpw' ) ?>" /></a></li>
	<li><a target="_new" href="http://del.icio.us/post?url=http://wordpress.org/extend/plugins/welcome-pack/&amp;title=When%20a%20user%20registers%20on%20your%20BuddyPress-powered%20site,%20you%20may%20want%20to%20automatically%20send%20them%20a%20friend%20or%20group%20invitation,%20or%20a%20welcome%20message.%20Welcome%20Pack%20lets%20you%20do%20that."><img src="<?php echo plugins_url( '/images/delicious_32.png', __FILE__ ) ?>" alt="<?php _e( 'Delicious - social bookmarking', 'dpw' ) ?>" /></a></li>
	<li><a target="_new" href="http://www.stumbleupon.com/submit?url=http://wordpress.org/extend/plugins/welcome-pack/&amp;title=When%20a%20user%20registers%20on%20your%20BuddyPress-powered%20site,%20you%20may%20want%20to%20automatically%20send%20them%20a%20friend%20or%20group%20invitation,%20or%20a%20welcome%20message.%20Welcome%20Pack%20lets%20you%20do%20that."><img src="<?php echo plugins_url( '/images/stumbleupon_32.png', __FILE__ ) ?>" alt="<?php _e( 'Stumble Upon', 'dpw' ) ?>" /></a></li>
</ul>
<?php
}

function dpw_admin_screen_news( $settings ) {
	if ( $rss = fetch_feed( 'http://feeds.feedburner.com/BYOTOS' ) ) {
		$content = '<ul>';
		$items = $rss->get_items( 0, $rss->get_item_quantity( 3 ) );

		foreach ( $items as $item )
			$content .= '<li><p><a target="_new" href="' . clean_url( $item->get_permalink(), null, 'display' ) . '">' . apply_filters( 'dpw_admin_rss_feed', $item->get_title() ) . '</a></p></li>';

		$content .= '<li class="rss"><p><a target="_new" href="http://feeds.feedburner.com/BYOTOS">' . __( 'Subscribe with RSS', 'dpw' ) . '</a></p></li></ul>';
		echo $content;
	} else {
		echo '<ul><li>' . __( 'No news!', 'dpw' ) . '</li></ul>';
	}
}

function dpw_admin_screen_support( $settings ) {
?>
<p><?php _e( "If you need help and support using this plugin, or ideas for new features, please visit the ", 'dpw' ) ?><a href="http://wordpress.org/tags/welcome-pack" target="_new"><?php _e( 'Support Forums', 'dpw' ) ?></a>.</p>
<?php
}

function dpw_admin_screen_emailsbox( $settings ) {
	$emails = dpw_get_default_email_data();
	wp_nonce_field( 'dpw-emails', '_ajax_nonce_dpw_emails' );
?>
	<select id="emailpicker">
		<?php for ( $i=0; $i<count($emails); $i++ ) : ?>
		<option value="<?php echo $i ?>"><?php echo $emails[$i]['name'] ?></option>
		<?php endfor; ?>
	</select>

	<div id="email">
 	</div>
<?php
}

/* TODO: need to figure out how to dynamically set bottom-margin = 0 of the last div.setting-group */
function dpw_admin_screen_configurationbox( $settings ) {
?>
<?php if ( function_exists( 'friends_install' ) && bp_has_members( 'type=alphabetical&populate_extras=false&per_page=10000' ) ) : ?>	
	<div class="setting setting-group setting-friends <?php if ( !$settings["friendstoggle"] ) echo 'initially-hidden' ?>">
		<div class="settingname">
			<p><?php _e( 'Invite the new user to become friends with these people:', 'dpw' ) ?></p>
		</div>
		<div class="settingvalue">
			<?php dpw_admin_settings_friends( $settings ) ?>
		</div>
		<div style="clear: left"></div>
	</div>
<?php endif ?>

<?php if ( function_exists( 'groups_install' ) && bp_has_groups( 'type=alphabetically&populate_extras=false&per_page=10000' ) ) : ?>
	<div class="setting setting-group setting-groups <?php if ( !$settings["groupstoggle"] ) echo 'initially-hidden' ?>">
		<div class="settingname">
			<p><?php _e( 'Ask the new user if they\'d like to join these groups:', 'dpw' ) ?></p>
		</div>
		<div class="settingvalue">
			<?php dpw_admin_settings_groups( $settings ) ?>
		</div>
		<div style="clear: left"></div>
	</div>
<?php endif ?>

<?php if ( function_exists( 'messages_install' ) && bp_has_members( 'type=alphabetical&populate_extras=false&per_page=10000' ) ) : ?>
	<div class="setting-welcomemsg setting-group <?php if ( !$settings["welcomemsgtoggle"] ) echo 'initially-hidden' ?>">
		<div class="setting wide">
			<div class="settingname">
				<p><?php _e( 'Send the new user a welcome message&hellip;', 'dpw' ) ?></p>
			</div>
			<div class="settingvalue">
				<?php dpw_admin_settings_welcomemsg( $settings ) ?>
			</div>
			<div style="clear: left"></div>
		</div>

		<div class="setting">
			<div class="settingname">
				<p><?php _e( '&hellip;with this subject:', 'dpw' ) ?></p>
			</div>
			<div class="settingvalue">
				<?php dpw_admin_settings_welcomemsg_subject( $settings ) ?>
			</div>
			<div style="clear: left"></div>
		</div>

		<div class="setting">
			<div class="settingname">
				<p><?php _e( '&hellip;from this user:', 'dpw' ) ?></p>
			</div>
			<div class="settingvalue">
				<?php dpw_admin_settings_welcomemsg_sender( $settings ) ?>
			</div>
			<div style="clear: left"></div>
		</div>
	</div>
<?php endif;
}

function dpw_admin_screen_settingsbox( $settings ) {
?>
<div class="component">
	<h5><?php _e( "Friends", 'dpw' ) ?>
		<div class="radio">
			<?php dpw_admin_settings_toggle( 'friends', $settings ) ?>
		</div>
	</h5>

	<p><?php _e( "Invite the new user to become friends with the selected members. It's a great way of teaching people how the friend acceptance process works on your website, and how they can use friendships to filter activity streams.", 'dpw' ) ?></p>
</div>

<div class="component">
	<h5><?php _e( "Groups", 'dpw' ) ?>
		<div class="radio">
			<?php dpw_admin_settings_toggle( 'groups', $settings ) ?>
		</div>
	</h5>

	<p><?php _e( "Ask the new user if they'd like to join a group. You could use this to invite all new users on your site to join a support group, so that all of your frequently asked questions are kept in the same place.", 'dpw' ) ?></p>
</div>

<div class="component">
	<h5><?php _e( "Welcome Message", 'dpw' ) ?>
		<div class="radio">
			<?php dpw_admin_settings_toggle( 'welcomemsg', $settings ) ?>
		</div>
	</h5>
	<p><?php _e( "Send the newly-registered user a private message; use this to welcome people to your site and help them get started.", 'dpw' ) ?></p>
</div>
<?php
}

// TODO: per_page http://trac.buddypress.org/ticket/1991
function dpw_admin_settings_friends( $settings ) {
?>
	<select multiple="multiple" name="welcomepack[friends][]" style="overflow-y: hidden">
	<?php while ( bp_members() ) : bp_the_member(); ?>
		<option value="<?php bp_member_user_id() ?>"<?php foreach ( $settings['friends'] as $id ) { if ( bp_get_member_user_id() == $id ) echo " selected='selected'"; } ?>><?php bp_member_name() ?></option>
	<?php endwhile; ?>
	</select>
<?php
}

function dpw_admin_settings_groups( $settings ) {
?>
	<select multiple="multiple" name="welcomepack[groups][]">
	<?php while ( bp_groups() ) : bp_the_group(); ?>
		<option value="<?php bp_group_id() ?>"<?php foreach ( $settings['groups'] as $id ) { if ( bp_get_group_id() == $id ) echo " selected='selected'"; } ?>><?php bp_group_name() ?></option>
	<?php endwhile; ?>
	</select>
<?php
}

function dpw_admin_settings_welcomemsg( $settings ) {
?>
	<textarea name="welcomepack[welcomemsg]"><?php echo apply_filters( 'dpw_admin_settings_welcomemsg', $settings['welcomemsg'] ) ?></textarea>
<?php
}

function dpw_admin_settings_welcomemsg_subject( $settings ) {
?>
	<input type="text" name="welcomepack[welcomemsgsubject]" value="<?php echo apply_filters( 'dpw_admin_settings_welcomemsg_subject', $settings['welcomemsgsubject'] ) ?>" />
<?php
}

function dpw_admin_settings_welcomemsg_sender( $settings ) {
?>
	<select name="welcomepack[welcomemsgsender]">
	<?php while ( bp_members() ) : bp_the_member(); ?>
		<option value="<?php bp_member_user_id() ?>"<?php if ( bp_get_member_user_id() == $settings['welcomemsgsender'] ) echo " selected='selected'"; ?>><?php bp_member_name() ?></option>
	<?php endwhile; ?>
	</select>
<?php
}

function dpw_admin_settings_toggle( $name, $settings ) {
	$checked = $settings["{$name}toggle"];
?>
	<input type="radio" class="<?php echo $name ?>" name="welcomepack[<?php echo $name ?>toggle]" value="1" <?php if ( $checked ) echo 'checked="checked" ' ?>/> <?php _e( 'Enabled', 'buddypress' ) ?> &nbsp;
	<input type="radio" class="<?php echo $name ?>" name="welcomepack[<?php echo $name ?>toggle]" value="0" <?php if ( !$checked ) echo 'checked="checked" ' ?>/> <?php _e( 'Disabled', 'buddypress' ) ?>
<?php
}

function dpw_admin_validate( $input ) {
	$current_settings = maybe_unserialize( get_site_option( 'welcomepack' ) );

	if ( is_string( $input ) )  // wpmu-edit.php
		return get_site_option( 'welcomepack' );

	if ( isset( $input['friends'] ) )
		foreach ( $input['friends'] as $friend_id )
			$friend_id = apply_filters( 'dpw_admin_validate_friend_id', $friend_id );

	if ( isset( $input['groups'] ) )
		foreach ( $input['groups'] as $group_id )
			$group_id = apply_filters( 'dpw_admin_validate_group_id', $group_id );

	if ( isset( $input['welcomemsg'] ) )
		$input['welcomemsg'] = apply_filters( 'dpw_admin_settings_welcomemsg', $input['welcomemsg'] );

	if ( isset( $input['welcomemsgsubject'] ) )
		$input['welcomemsgsubject'] = apply_filters( 'dpw_admin_settings_welcomemsg_subject', $input['welcomemsgsubject'] );

	if ( isset( $input['welcomemsgsender'] ) )
		$input['welcomemsgsender'] = apply_filters( 'dpw_admin_validate_friend_id', $input['welcomemsgsender'] );
 
	if ( isset( $input['groupstoggle'] ) )
		$input['groupstoggle'] = ( $input['groupstoggle'] ) ? true : false;

	if ( isset( $input['friendstoggle'] ) )
		$input['friendstoggle'] = ( $input['friendstoggle'] ) ? true : false;

	if ( isset( $input['welcomemsgtoggle'] ) )
		$input['welcomemsgtoggle'] = ( $input['welcomemsgtoggle'] ) ? true : false;

	return serialize( wp_parse_args( $input, $current_settings ) );
}


/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

function dpw_admin_screen() {
	global $screen_layout_columns;

	$settings = maybe_unserialize( get_site_option( 'welcomepack' ) );

	$is_email_tab = false;
	if ( isset( $_GET['tab'] ) && 'emails' == $_GET['tab'] )
		$is_email_tab = true;
?>
<div id="bp-admin">
<div id="dpw-admin-metaboxes-general" class="wrap">

	<div id="bp-admin-header">
		<h3><?php _e( 'BuddyPress', 'dpw' ) ?></h3>
		<h4><?php _e( 'Welcome Pack', 'dpw' ) ?></h4>
	</div>

	<div id="bp-admin-nav">
		<ol>
			<li <?php if ( !$is_email_tab ) echo 'class="current"' ?>><a href="<?php echo site_url('wp-admin/admin.php?page=welcome-pack', 'admin') ?>"><?php _e( 'Friends, Groups <span class="ampersand">&amp;</span> Welcome Message', 'dpw' ) ?></a></li>
			<li <?php if ( $is_email_tab ) echo 'class="current"' ?>><a href="<?php echo site_url('wp-admin/admin.php?page=welcome-pack&amp;tab=emails', 'admin') ?>"><?php _e( 'Emails', 'dpw' ) ?></a></li>
		</ol>
	</div>

	<div class="dpw-spacer">
		<p><?php _e( 'When a user registers on your site, you may want to automatically send them a friend or group invitation, or a welcome message. This plugin lets you do that.', 'dpw' ); //TODO: update this to include emails + copy into readme ?></p>
	</div>

	<form method="post" action="options.php" id="welcomepack">
		<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
		<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ) ?>
		<?php settings_fields( 'dpw-settings-group' ) ?>

		<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
			<div id="side-info-column" class="inner-sidebar">
				<?php
				if ( $is_email_tab )
					do_meta_boxes( 'buddypress_page_welcome-pack-emails', 'side', $settings );
				else
					do_meta_boxes( 'buddypress_page_welcome-pack', 'side', $settings );
				?>
			</div>
			<div id="post-body" class="has-sidebar">
				<div id="post-body-content" class="has-sidebar-content">
					<?php
					if ( $is_email_tab )
						do_meta_boxes( 'buddypress_page_welcome-pack-emails', 'normal', $settings );
					else
						do_meta_boxes( 'buddypress_page_welcome-pack', 'normal', $settings );
					?>
				</div>

				<p><input type="submit" class="button-primary" value="<?php _e( 'Save Welcome Pack Settings', 'dpw' ) ?>" /></p>
			</div>
			<br class="clear"/>
		</div>
	</form>

</div><!-- #dpw-admin-metaboxes-general -->
</div><!-- #bp-admin -->
<?php
}
?>
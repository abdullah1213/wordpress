<?php
/**
 * Credits administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

$title = __( 'Credits' );
$parent_file = 'index.php';

add_action( 'admin_head', 'add_css' );
function add_css() { ?>
<style type="text/css">
h3.wp-people-group, h3.wp-props-group { clear: both; }
ul.wp-people-group { margin-bottom: 50px; }
li.wp-person { float: left; height: 100px; width: 240px; margin-right: 20px; }
li.wp-person img.gravatar { float: left; margin-right: 10px; margin-bottom: 10px; width: 60px; height: 60px }
li.wp-person a.web { font-size: 16px; text-decoration: none; }
</style>
<?php }

function wp_credits() {
	global $wp_version;
	$locale = get_locale();

	$results = get_site_transient( 'wordpress_credits' );

	if ( false === $results ) {
		$response = wp_remote_get( "http://api.wordpress.org/core/credits/1.0/?version=$wp_version&locale=$locale" );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
			return false;
	
		$results = unserialize( wp_remote_retrieve_body( $response ) );

		if ( ! $results )
			return false;

		set_site_transient( 'wordpress_credits', $results, 604800 ); // One week.
	}

	return $results;
}

function _wp_credits_add_profile_link( &$display_name, $username, $prefix ) {
	$display_name = '<a href="' . esc_url( $prefix . $username ) . '">' . esc_html( $display_name ) . '</a>';
}

include( './admin-header.php' );
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e( 'WordPress Credits' ); ?></h2>

<?php

$results = wp_credits();

if ( ! $results ) {
	echo '<p>' . sprintf( __( 'WordPress is created by a <a href="%1$s">worldwide team</a> of passionate individuals. <a href="%2$s">Get involved in WordPress</a>.' ),
		'http://wordpress.org/about/',
		'http://codex.wordpress.org/Contributing_to_WordPress' ) . '</p>';
	include( './admin-footer.php' );
	exit;
}

echo '<p>' . __( "WordPress is created by a worldwide team of passionate individuals. We couldn't possibly list them all, but here some of the most influential people currently involved with the project:" ) . "<p>\n";

$gravatar = is_ssl() ? 'https://secure.gravatar.com/avatar/' : 'http://0.gravatar.com/avatar/';

foreach ( $results['people'] as $group_slug => $members ) {
	echo '<h3 class="wp-people-group">' . translate( $results['groups'][ $group_slug ] ) . "</h3>\n";
	echo '<ul class="wp-people-group" id="wp-people-group-' . $group_slug . '">' . "\n";
	shuffle( $members ); // We were going to sort by ability to pronounce "hierarchical," but that wouldn't be fair to Matt.
	foreach ( $members as $member_slug => $member ) {
		echo '<li class="wp-person" id="wp-person-' . $member_slug . '"><img src="' . $gravatar . $member[3] . '?s=60" class="gravatar" /><a class="web" href="' . $results['data']['profile_prefix'] . $member[2] . '">' . $member[0] . '</a><br /><span class="title">' . translate( $member[1] ) . "</span></li>\n";
	}
	echo "</ul>\n";
}

if ( isset( $results['props'] ) ) {
	echo '<h3 class="wp-props-group">' . sprintf( translate( $results['groups']['props'] ), $results['data']['version'] ) . "</h3>\n\n";
	array_walk( $results['props'], '_wp_credits_add_profile_link', $results['data']['profile_prefix'] );
	shuffle( $results['props'] );
	echo wp_sprintf( '%l.', $results['props'] );
}

?>
<p class="clear"><?php printf( __( 'Want to see your name in lights on this page? <a href="%s">Get involved in WordPress</a>.' ), 'http://codex.wordpress.org/Contributing_to_WordPress' ); ?></p>

</div>
<?php

include( './admin-footer.php' );

return;

__( 'Project Leaders' );
__( 'Extended Core Team' );
__( 'Recent Rockstars' );
__( 'Core Contributors to WordPress %s' );
__( 'Cofounder, Project Lead' );
__( 'Lead Developer' );
__( 'UI/UX and Community Lead' );
__( 'Developer, Core Committer' );
__( 'Developer' );
__( 'Designer' );
__( 'XML-RPC Developer' );
__( 'Internationalization' );

?>
<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

function itcp_delete_site_data(): void {
	delete_option( 'itcp_role_changes' );
}

function itcp_delete_network_data(): void {
	foreach ( get_sites( array( 'fields' => 'ids', 'number' => 0 ) ) as $itcp_site_id ) {
		switch_to_blog( (int) $itcp_site_id );
		itcp_delete_site_data();
		restore_current_blog();
	}
}

if ( is_multisite() ) {
	itcp_delete_network_data();
} else {
	itcp_delete_site_data();
}

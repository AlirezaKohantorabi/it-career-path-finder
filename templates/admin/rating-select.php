<?php

use ItCareerPathFinder\Data\RoleSanitizer;

defined( 'ABSPATH' ) || exit;

$itcp_selected = (int) $view['value'];
?>
<select id="<?php echo esc_attr( $view['id'] ); ?>" name="<?php echo esc_attr( $view['name'] ); ?>">
	<?php for ( $itcp_level = RoleSanitizer::RATING_MIN; $itcp_level <= RoleSanitizer::RATING_MAX; $itcp_level++ ) : ?>
		<option value="<?php echo esc_attr( (string) $itcp_level ); ?>" <?php selected( $itcp_selected, $itcp_level ); ?>>
			<?php echo esc_html( number_format_i18n( $itcp_level ) . ' — ' . ( $view['legend'][ (string) $itcp_level ] ?? '' ) ); ?>
		</option>
	<?php endfor; ?>
</select>

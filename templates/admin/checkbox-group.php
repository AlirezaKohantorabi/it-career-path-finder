<?php

defined( 'ABSPATH' ) || exit;

$itcp_selected = (array) $view['selected'];
?>
<fieldset class="itcp-checkbox-group">
	<legend class="screen-reader-text"><?php echo esc_html( $view['legend'] ); ?></legend>
	<?php foreach ( $view['options'] as $itcp_option ) : ?>
		<?php $itcp_value = (string) ( $itcp_option['value'] ?? '' ); ?>
		<label class="itcp-checkbox">
			<input type="checkbox" name="<?php echo esc_attr( $view['name'] ); ?>[]" value="<?php echo esc_attr( $itcp_value ); ?>" <?php checked( in_array( $itcp_value, $itcp_selected, true ) ); ?>>
			<span><?php echo esc_html( (string) ( $itcp_option['label'] ?? $itcp_value ) ); ?></span>
		</label>
	<?php endforeach; ?>
</fieldset>

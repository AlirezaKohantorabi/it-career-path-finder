<?php

defined( 'ABSPATH' ) || exit;

$itcp_text     = $view['text'];
$itcp_instance = (int) $view['instance'];
?>
<div class="itcp-app itcp-scope" id="<?php echo esc_attr( 'itcp-app-' . $itcp_instance ); ?>" dir="rtl" lang="fa-IR" data-itcp-app data-itcp-instance="<?php echo esc_attr( (string) $itcp_instance ); ?>">
	<div class="itcp-shell" data-itcp="shell">
		<div class="itcp-head">
			<div class="itcp-head-top">
				<span class="itcp-chip-h" data-itcp="phase"></span>
				<span class="itcp-chip-h itcp-chip-time">
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
					<span data-itcp="time"></span>
				</span>
				<button type="button" class="itcp-restart" data-itcp="restart" aria-label="<?php echo esc_attr( $itcp_text['restart'] ?? '' ); ?>" title="<?php echo esc_attr( $itcp_text['restart'] ?? '' ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
					<span class="itcp-xs-hide"><?php echo esc_html( $itcp_text['restart'] ?? '' ); ?></span>
				</button>
			</div>
			<h2 class="itcp-title"><?php echo esc_html( $itcp_text['title'] ?? '' ); ?></h2>
			<?php if ( '' !== ( $itcp_text['subtitle'] ?? '' ) ) : ?>
				<p class="itcp-sub"><?php echo esc_html( $itcp_text['subtitle'] ); ?></p>
			<?php endif; ?>
			<div class="itcp-meta">
				<span data-itcp="step" aria-live="polite"></span>
				<span class="itcp-meta-left">
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 2h12M6 22h12M7 2c0 5 10 5 10 10S7 17 7 22M17 2c0 5-10 5-10 10s10 5 10 10"/></svg>
					<span data-itcp="left"></span>
				</span>
			</div>
			<div class="itcp-track" role="progressbar" aria-label="<?php echo esc_attr( $itcp_text['progress_label'] ?? '' ); ?>" aria-valuemin="0" aria-valuemax="1" aria-valuenow="0" data-itcp="bar">
				<div class="itcp-fill" data-itcp="fill"></div>
			</div>
		</div>
		<nav class="itcp-dots" data-itcp="dots" aria-label="<?php echo esc_attr( $itcp_text['dots_label'] ?? '' ); ?>"></nav>
		<form class="itcp-form" data-itcp="form" novalidate>
			<div class="itcp-body" data-itcp="question">
				<p class="itcp-loading" data-itcp="loading"><?php echo esc_html( $itcp_text['loading'] ?? '' ); ?></p>
				<p class="itcp-loading" data-itcp="data-error" hidden><?php echo esc_html( $itcp_text['data_error'] ?? '' ); ?></p>
				<noscript><p class="itcp-loading"><?php echo esc_html( $itcp_text['noscript'] ?? '' ); ?></p></noscript>
			</div>
			<div class="itcp-foot">
				<button type="button" class="itcp-btn itcp-btn-s" data-itcp="previous" disabled>
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 6l6 6-6 6"/></svg>
					<span><?php echo esc_html( $itcp_text['previous'] ?? '' ); ?></span>
				</button>
				<button type="button" class="itcp-btn itcp-btn-p" data-itcp="next">
					<span data-itcp="next-label"><?php echo esc_html( $itcp_text['next'] ?? '' ); ?></span>
					<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6"/></svg>
				</button>
			</div>
		</form>
		<div class="itcp-res" data-itcp="results"></div>
	</div>
	<div class="itcp-toast" data-itcp="toast" role="status" aria-live="polite"></div>
</div>

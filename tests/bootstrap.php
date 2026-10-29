<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'ITCP_TESTS_ROOT', dirname( __DIR__ ) . '/' );
define( 'ITCP_PLUGIN_DIR', ITCP_TESTS_ROOT );
define( 'ITCP_PLUGIN_URL', 'http://example.test/wp-content/plugins/it-career-path-finder/' );
define( 'ITCP_VERSION', '0.0.0-test' );

require_once __DIR__ . '/Support/WordPressStubs.php';
require_once __DIR__ . '/Support/TestCase.php';
require_once ITCP_TESTS_ROOT . 'src/Autoloader.php';

ItCareerPathFinder\Autoloader::register( ITCP_TESTS_ROOT . 'src/' );

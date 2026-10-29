<?php
/**
 * Plugin Name:       IT Career Path Finder
 * Plugin URI:        https://github.com/AlirezaKohantorabi/it-career-path-finder
 * Description:       دستیار انتخاب مسیر شغلی IT در ایران. با شورت‌کد [it_career_path_finder] در هر برگه یا نوشته‌ای قرارش بدهید.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Khosro Azimi, Alireza Kohantorabi
 * Author URI:        https://khosro-azimi.ir
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       it-career-path-finder
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'ITCP_VERSION', '1.0.0' );
define( 'ITCP_PLUGIN_FILE', __FILE__ );
define( 'ITCP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ITCP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once ITCP_PLUGIN_DIR . 'src/Autoloader.php';

ItCareerPathFinder\Autoloader::register( ITCP_PLUGIN_DIR . 'src/' );
ItCareerPathFinder\Plugin::boot();

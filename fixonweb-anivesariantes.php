<?php
/**
 * Plugin Name:     FIXONWEB - Anivesariantes
 * Plugin URI:      https://github.com/fixonweb/fixonweb-anivesariantes
 * Description:     Cadastrar uma lista de pessoas e poder exibir quem faz aniversário no mês atual. - Plugin WordPress
 * Author:          FIXONWEB
 * Author URI:      https://github.com/fixonweb
 * Text Domain:     fix158949
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Fix158949
 */

defined( 'ABSPATH' ) or die();

require 'plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/fixonweb/fixonweb-anivesariantes',
	__FILE__, 
	'fixonweb-anivesariantes/fixonweb-anivesariantes'
);

add_action('wp_enqueue_scripts', "fix158949_enqueue_scripts");
function fix158949_enqueue_scripts(){
    wp_enqueue_script( 'jquery-validate-min', plugin_dir_url( __FILE__ ) . '/js/jquery.validate.min.js', array( 'jquery' )  );
}

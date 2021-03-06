<?php
/**
 * Putting WordPress into Hyperdrive.
 *
 * @package     Hyperdrive
 * @author      Josh Habdas
 * @since       1.0.0
 * @license     GPL-3.0
 *
 * Plugin Name:     Hyperdrive
 * Plugin URI:      https://github.com/comfusion/hyperdrive
 * Description:     The fastest way to load pages in WordPress.
 * Author:          Josh Habdas
 * Author URI:      https://habd.as
 * Text Domain:     hyperdrive
 * Version:         1.0.0-beta.3
 * License:         GPL-3.0
 *
 * Hyperdrive. The fastest way to load pages in WordPress.
 * Copyright (C) 2017  Josh Habdas and contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 */

namespace hyperdrive;

defined( 'ABSPATH' ) or die( 'Now you are going to die! BAM!' );

/**
 * Defines Hyperdrive version output to source.
 *
 * @since 1.0.0
 * @var HYPERDRIVE_VERSION Semantic program version.
 */
const HYPERDRIVE_VERSION = '1.0.0-beta.3';

/**
 * Engages Hyperdrive.
 *
 * Engages Hyperdrive while printing scripts or data
 * in the head tag on the front end.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', __NAMESPACE__ . '\engage' );

/**
 * Calibrates Hyperdrive thrusters.
 *
 * Creates an associative array containing structured data required
 * for Fetch Injection. Also dequeues enqueued scripts so WordPress
 * doesn't load them. Data structure is assumed by functions using
 * and used by this method.
 *
 * @since Hyperdrive 1.0.0
 * @return Associative array containing thruster calibration data.
 *
 * Example structured data ("Calibration data"):
 *
 *    array(
 *      string "jquery-scrollto",
 *      string "/assets/js/jquery.scrollTo.js?ver=2.1.2",
 *      array(
 *        string "jquery",
 *        string ""
 *        array(
 *          array(
 *            string "jquery-core",
 *            string "/wp-includes/js/jquery/jquery.js?ver=1.12.4",
 *            array(0)
 *          )
 *          array(
 *            string "jquery-migrate",
 *            string "/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.4.1",
 *            array(0)
 *          )
 *        )
 *      )
 *    );
 */
function calibrate_thrusters() {
	$calibration_data = [];
	$scripts = get_enqueued_scripts();
	foreach ( $scripts as $script ) {
		if ( empty( $script->extra['conditional'] ) ) {
			// It's a good thing you were wearing that helmet.
			$calibration_data[] = array(
			$script->handle,
			get_src_for_handle( $script->handle ),
			get_dependency_data( $script->deps ),
			);
			// Not in here, mister! This is a Mercedes!
			wp_dequeue_script( $script->handle );
		}
	}
	return $calibration_data;
}

/**
 * Generates antimatter particles.
 *
 * Translates thruster calibration data into an antimatter
 * particle array and dedupes it while respecting sort order.
 *
 * @since Hyperdrive 1.0.0
 *
 * @param array   $calibration_data Thurster calibration settings.
 * @param boolean $recursing True when generating subparticles.
 * @return A list of scripts for use in Fetch Injection.
 */
function generate_antimatter( $calibration_data, $recursing = false ) {
	$particle_array = [];
	foreach ( $calibration_data as $idx => $data ) {
		$handle = $data[0];
		$url = $data[1];
		$particle_array[] = "{$url}";
		$subparticles = $data[2];
		if ( $subparticles ) {
			$particle_array[] = generate_antimatter( $subparticles, true );
		}
	}
	// Remove numeric array keys.
	array_multisort( $particle_array );
	// Remove duplicate values.
	$particle_array = array_map(
		'unserialize', array_unique(
			array_map( 'serialize', $particle_array )
		)
	);
	return $particle_array;
}

/**
 * Converts antimatter particles into dark matter.
 *
 * Takes an antimatter particle array transforms it into something
 * Fetch Inject understands, making FTL a future possibility.
 *
 * @since Hyperdrive 1.0.0
 * @link https://github.com/jhabdas/fetch-inject
 *
 * @todo Consolidate dedupe logic with `generate_antimatter`.
 *
 * @param array $antimatter_particles Partical array.
 * @return A string containing a fully-assembled inline script.
 */
function fold_spacetime( $antimatter_particles ) {
	$injectors = $particle_array = []; // @codingStandardsIgnoreLine
	$fetch_inject_string = '';

	/**
	 * Create ordered array of JSON encoded strings for Fetch Injection.
	 *
	 * @param array $array Multidimensional array of antimatter particles.
	 * @param array $accumulator Accumulates particles during recursion.
	 * @param array $injectors JSON-encoded strings for Fetch Injection.
	 * @param array $particle_array I'm not sure why this is here. See @todo above.
	 * @param array $injection_json JSON-encoded representation of $accumulator.
	 */
	function walk_recursive( $array, $accumulator, &$injectors, &$particle_array, &$injection_json = '' ) {
		$accumulator = [];
		array_walk( $array, function( $item ) use ( &$accumulator, &$injectors, &$particle_array, &$injection_json ) {
			if ( ! empty( $item ) ) {
				if ( is_array( $item ) ) {
					walk_recursive( $item, $accumulator, $injectors, $particle_array, $injection_json );
				} else {
					if ( ! in_multi_array( $item, $particle_array ) ) {
						$accumulator[] = $particle_array[] = $item; // @codingStandardsIgnoreLine
					}
				}
			}
		});

		if ( ! empty( $accumulator ) ) {
			  $injection_json = json_encode( $accumulator, JSON_UNESCAPED_SLASHES );
			  $injectors[] = $injection_json;
		}
	}
	walk_recursive( $antimatter_particles, false, $injectors, $particle_array );

	/**
	 * Assemble Fetch Inject string using ordered array.
	 */
	$first_element = reset( $injectors );
	$last_element = end( $injectors );
	foreach ( $injectors as $idx => $injector ) {
		if ( $injector === $first_element ) {
			$fetch_inject_string = "fetchInject($injector)";
		} elseif ( $injector === $last_element ) {
			$fetch_inject_string = "fetchInject($injector, $fetch_inject_string)";
		} else {
			$array_with_empty_string = array( '' ); // Like WordPress core jquery handle.
			if ( ! (json_decode( $injector ) === $array_with_empty_string) ) {
				$fetch_inject_string = "fetchInject($injector, $fetch_inject_string)";
			}
		}
	}

	$hyperdrive_ver = HYPERDRIVE_VERSION;
	return <<<EOD
/*!
 * Hyperdrive v$hyperdrive_ver
 * Copyright (c) 2017 Josh Habdas
 * @license GPL-3.0
 */
(function () {
  if (!window.fetch) return;
  /**
   * Fetch Inject v1.6.11
   * Copyright (c) 2017 Josh Habdas
   * @licence ISC
   */
  var fetchInject=function(){"use strict";const e=function(e,t,n,r,o,i,c){i=t.createElement(n),c=t.getElementsByTagName(n)[0],i.appendChild(t.createTextNode(r.text)),i.onload=o(r),c?c.parentNode.insertBefore(i,c):t.head.appendChild(i)},t=function(t,n){if(!t||!Array.isArray(t))return Promise.reject(new Error("`inputs` must be an array"));if(n&&!(n instanceof Promise))return Promise.reject(new Error("`promise` must be a promise"));const r=[],o=n?[].concat(n):[],i=[];return t.forEach(e=>o.push(window.fetch(e).then(e=>{return[e.clone().text(),e.blob()]}).then(e=>{return Promise.all(e).then(e=>{r.push({text:e[0],blob:e[1]})})}))),Promise.all(o).then(()=>{return r.forEach(t=>{i.push({then:n=>{"text/css"===t.blob.type?e(window,document,"style",t,n):e(window,document,"script",t,n)}})}),Promise.all(i)})};return t}();
  $fetch_inject_string;
})();
EOD;
}

/**
 * Enter hyperspace.
 *
 * Echos an inline script into the document.
 *
 * @since Hyperdrive 1.0.0
 * @param string $dark_energy An inline script to asynchronously
 *     fetch previously enqueued page resources.
 */
function enter_hyperspace( $dark_energy ) {
	echo "<script>{$dark_energy}</script>";
}

/**
 * Main function engages the hyperdrive.
 *
 * @since Hyperdrive 1.0.0
 *
 * @todo return void (requires PHP 7.1).
 */
function engage() {
	$calibration_data = calibrate_thrusters();
	$antimatter_particles = generate_antimatter( $calibration_data );
	$dark_energy = fold_spacetime( $antimatter_particles );
	enter_hyperspace( $dark_energy );
}

/**
 * Gets dependency data recursively.
 *
 * @since Hyperdrive 1.0.0
 *
 * @param array(string) $handles An array of handles.
 * @return array(array) Dependency data matching expected structure.
 */
function get_dependency_data( $handles ) {
	$dependency_data = [];
	foreach ( $handles as $idx => $handle ) {
		$source_url = get_src_for_handle( $handle );
		if ( $source_url ) {
			$dependency_data[] = array(
			$handle,
			$source_url,
			array(), // Maintain thrust.
			);
		}
		$deps = get_deps_for_handle( $handle );
		if ( count( $deps ) > 0 ) {
			$dependency_data[] = array(
			$handle,
			'', // Maintain thrust.
			get_dependency_data( $deps ),
			);
		}
	}
	return $dependency_data;
}

/**
 * Gets scripts registered and enqueued.
 *
 * @since Hyperdrive 1.0.0
 * @return array(_WP_Dependency) A list of enqueued dependencies.
 */
function get_enqueued_scripts() {
	$wp_scripts = wp_scripts();
	foreach ( $wp_scripts->queue as $handle ) {
		$enqueued_scripts[] = $wp_scripts->registered[ $handle ];
	}
	return $enqueued_scripts;
}

/**
 * Gets a script dependency for a handle.
 *
 * @since Hyperdrive 1.0.0
 *
 * @param string $handle The handle.
 * @return _WP_Dependency associated with input handle.
 */
function get_dep_for_handle( $handle ) {
	$wp_scripts = wp_scripts();
	return $wp_scripts->registered[ $handle ];
}

/**
 * Gets the source URL given a script handle.
 *
 * @since Hyperdrive 1.0.0
 *
 * @param string $handle The handle.
 * @return URL associated with handle, or empty string.
 */
function get_src_for_handle( $handle ) {
	$dep = get_dep_for_handle( $handle );
	$suffix = ( $dep->src && $dep->ver )
	? "?ver={$dep->ver}"
	: '';
	return "{$dep->src}{$suffix}";
}

/**
 * Gets all dependencies for a given handle.
 *
 * @since Hyperdrive 1.0.0
 *
 * @param string $handle The handle.
 * @return array(string) List of handles for dependencies of `$handle`.
 */
function get_deps_for_handle( $handle ) {
	$dep = get_dep_for_handle( $handle );
	return $dep->deps;
}

/**
 * Checks if a value exists in a multidimensional array.
 *
 * @since Hyperdrive 1.0.0
 *
 * @todo Eliminate multiple return statements.
 *
 * @param string/array $needle The value(s) to search for.
 * @param array        $haystack The array to search.
 * @return boolean True if found, false otherwise.
 */
function in_multi_array( $needle, $haystack ) {
	foreach ( $haystack as $item ) {
		if ( is_array( $item ) && in_multi_array( $needle, $item ) ) {
			return true;
		} elseif ( $item == $needle ) {
			return true;
		}
	}
	return false;
}

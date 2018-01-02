<?php
/**
 * Main plugin's file.
 *
 * @package twitter-egosearching
 */

/**
 * Plugin Name: twitter-egosearching
 * Plugin URI: https://okawa.routecompass.net
 * Description: Twitter垢持ってない人向けのエゴサーチプラグイン
 * Version: 1.0
 * Author: Masaya Okawa
 * Author URI: https://okawa.routecompass.net
 * License: GPLv2 or later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

require __DIR__ . '/class-egosearch-setting.php';

if ( is_admin() ) {
	new Egosearch_Setting();
	new Twitter_Egosearch();
}

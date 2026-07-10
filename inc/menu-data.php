<?php
/**
 * Kenzy Kreates menu — single source of truth.
 *
 * Rendered by front-page.php (featured items) and template-menu.php (full menu).
 * To change offerings, prices, or availability, edit THIS file only.
 *
 * Item fields:
 *   name     (string)  Display name.
 *   price    (string)  Display price, e.g. '$2.50'.
 *   per      (string)  Unit, e.g. 'each'.
 *   note     (string)  Optional, e.g. '3 count'.
 *   image    (string)  Optional stock image stem in assets/images/stock/ (no extension).
 *                      Items with an image appear in the front-page featured grid.
 *   sold_out (bool)    Optional. true shows a "Sold out" badge and hides ordering.
 *
 * Category fields:
 *   title, tagline, items, dozen (optional display price for a dozen, e.g. '$30.00').
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(

	'cookies' => array(
		'title'   => 'Cookies',
		'tagline' => 'Freshly baked cookies, perfect for any sweet tooth.',
		'dozen'   => '$30.00',
		'items'   => array(
			array(
				'name'  => 'Chocolate Chip',
				'price' => '$2.50',
				'per'   => 'each',
				'image' => 'chocolate-chip-cookies',
			),
			array(
				'name'  => 'Sugar Cookies (decorated)',
				'price' => '$3.00',
				'per'   => 'each',
				'image' => 'decorated-sugar-cookies',
			),
			array(
				'name'  => 'Oatmeal Raisin',
				'price' => '$2.50',
				'per'   => 'each',
			),
			array(
				'name'  => 'Peanut Butter',
				'price' => '$2.50',
				'per'   => 'each',
			),
			array(
				'name'  => 'Snickerdoodle',
				'price' => '$2.50',
				'per'   => 'each',
			),
		),
	),

	'cake-pops' => array(
		'title'   => 'Cake Pops',
		'tagline' => 'Available in a variety of flavors and decorated to match your theme.',
		'dozen'   => '$28.00',
		'items'   => array(
			array(
				'name'  => 'Classic Vanilla Bean',
				'price' => '$3.00',
				'per'   => 'each',
				'image' => 'cake-pops',
			),
			array(
				'name'  => 'Rich Chocolate Fudge',
				'price' => '$3.00',
				'per'   => 'each',
			),
			array(
				'name'  => 'Red Velvet Delight',
				'price' => '$3.00',
				'per'   => 'each',
			),
			array(
				'name'  => 'Lemon Zest',
				'price' => '$3.00',
				'per'   => 'each',
			),
			array(
				'name'  => 'Confetti',
				'price' => '$3.00',
				'per'   => 'each',
			),
		),
	),

	'brownies' => array(
		'title'   => 'Brownies',
		'tagline' => 'Fudgy and decadent brownies, a classic favorite.',
		'dozen'   => '$30.00',
		'items'   => array(
			array(
				'name'  => 'Classic Fudgy Brownie',
				'price' => '$3.50',
				'per'   => 'each',
				'image' => 'brownies',
			),
			array(
				'name'  => 'Walnut Brownie',
				'price' => '$3.75',
				'per'   => 'each',
			),
			array(
				'name'  => 'Caramel Drizzle Brownie',
				'price' => '$3.50',
				'per'   => 'each',
			),
			array(
				'name'  => 'Mint Chocolate',
				'price' => '$3.75',
				'per'   => 'each',
			),
		),
	),

	'extras' => array(
		'title'   => 'Extras & Chocolate Covered Delights',
		'tagline' => 'Add a little something extra to your order!',
		'items'   => array(
			array(
				'name'  => 'Rice Krispy Treats',
				'price' => '$2.50',
				'per'   => 'each',
			),
			array(
				'name'  => 'Chocolate Covered Strawberries',
				'price' => '$4.00',
				'per'   => 'each',
				'note'  => '3 count',
				'image' => 'chocolate-covered-strawberries',
			),
			array(
				'name'  => 'Chocolate Covered Pretzels',
				'price' => '$3.00',
				'per'   => 'each',
				'note'  => '3 count',
				'image' => 'chocolate-covered-pretzels',
			),
			array(
				'name'  => 'Chocolate Covered Marshmallows',
				'price' => '$2.50',
				'per'   => 'each',
				'note'  => '2 count',
			),
			array(
				'name'  => 'Chocolate Covered Cherries',
				'price' => '$3.50',
				'per'   => 'each',
				'note'  => '3 count',
			),
			array(
				'name'  => 'Chocolate Covered Bananas',
				'price' => '$4.00',
				'per'   => 'each',
				'note'  => '1 count',
			),
		),
	),

	'mini-desserts' => array(
		'title'   => 'Mini Desserts',
		'tagline' => 'Bite-sized portions of pure joy.',
		'items'   => array(
			array(
				'name'  => 'Mini Cheesecake Bites',
				'price' => '$3.00',
				'per'   => 'each',
				'image' => 'mini-cheesecakes',
			),
			array(
				'name'  => 'Mini Waffle Bites',
				'price' => '$2.50',
				'per'   => 'each',
			),
		),
	),

);

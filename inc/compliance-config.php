<?php
/**
 * Kenzy Kreates business + compliance configuration — single source of truth.
 *
 * Florida cottage food operation (F.S. 500.80). The disclosure wording below is
 * the exact statutory statement and must not be reworded. Florida permits
 * internet and mail-order sales with in-person or in-state carrier delivery;
 * wholesale is prohibited, and shipping outside Florida is not permitted under
 * federal law.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(

	// Brand + contact.
	'business_name' => 'Kenzy Kreates',
	'tagline'       => 'Baked Treats & Sweets',
	'owners'        => 'Adam & McKenzie',
	'phone'         => '727-906-5594',
	'phone_href'    => 'tel:+17279065594',
	'email'         => 'kenzykreatesbakes@gmail.com',

	// OWNER INPUT NEEDED: exact city / pickup area wording from Adam & McKenzie.
	'service_area'  => 'our local Florida community',

	// Fulfillment: 'pickup' (default), local delivery arranged at confirmation.
	// Florida also allows in-state shipping via USPS/commercial carrier if the
	// owners ever want it. NEVER enable out-of-state shipping.
	'fulfillment'   => 'pickup',

	// Exact required statement per F.S. 500.80 — do not reword.
	'disclosure'    => "Made in a cottage food operation that is not subject to Florida's food safety regulations.",

	// Cautious general allergen statement shown with the menu and FAQ.
	'allergen_note' => 'Our treats are made in a home kitchen that may handle common food allergens such as wheat, eggs, dairy, peanuts, and tree nuts. Please tell us about any allergies when you order.',

	// Custom order deposit policy shown on the inquiry form.
	'deposit_note'  => 'Custom orders are confirmed with a 50% deposit once we agree on the details and price.',

	// OWNER INPUT NEEDED: social media profile URLs.
	'social'        => array(
		// 'instagram' => 'https://instagram.com/...',
		// 'facebook'  => 'https://facebook.com/...',
	),

);

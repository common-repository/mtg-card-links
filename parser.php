<?php

	require_once( 'constants.inc.php' );
	global $wpdb;

// Quit right away if some data is missing
	if( !isset( $_POST['t'] ) || !isset( $_POST['nonce'] ) || !isset( $_POST['p'] ) )
		return;

	if( get_magic_quotes_gpc() ) {
		foreach( $_POST as $Key => $Value ) {
			$_POST[$Key] = stripslashes($Value);
		}
	}
	
	$base_path = $_POST['p'];
	$text_to_parse = $_POST['t'];
	$nonce = $_POST['nonce'];

	if( strlen( $base_path ) > 0 ) {
		if( $base_path[strlen( $base_path ) - 1] != '/' )
			$base_path .= '/';
	} else {
		$base_path = '/';
	}

// Include database layer if it not already loaded (it should not be) and because we need to verify the nonce
	if( !isset( $wpdb ) || !function_exists( 'wp_verify_nonce' ) ) {
		include_once( $base_path . 'wp-load.php' );
		include_once( $base_path . 'wp-includes/wp-db.php' );
	}

	if( !wp_verify_nonce($_POST['nonce'], MTGCARDREF_NONCE_NAME ) )
		return;

// Get all card names from the database
	$query = "SELECT card_name FROM " . $wpdb->prefix . MTGCARDREF_TABLE_NAME .
		" WHERE card_name IS NOT NULL AND " .
 		" card_name NOT IN ('?', '', 'B', 'D', 'M', 'Cunning', 'Flash', 'Thirst', 'Web', 'Opportunity', 'Dismiss', 'Foil', 'Curiosity', 'Dismantle', 'Prohibit', 'Justice', 'Onslaught', 'Vengeance', 'Rootwalla', 'Clear', 'Sustenance', 'Weakness', 'Forget', 'Flood', 'Simulacrum', 'Avalanche', 'Blight', 'Exile', 'Castle', 'Lance', 'Orgg', 'Squelch', 'Feedback', 'Darkness', 'Scrap', 'Carrion', 'Glory', 'Seeker', 'Riptide', 'Shapeshifter', 'Mulch', 'Tek', 'Choke', 'Void', 'Reclamation', 'Revelation', 'Opt', 'Tangle', 'Scour', 'Lunge', 'Rally', 'Lure', 'Extract', 'Fissure', 'Formation', 'Roots', 'Nightmare', 'Flare', 'Momentum', 'Recall', 'Accelerate', 'Flight', 'Channel', 'Sacrifice', 'Maro', 'Restraint', 'Bind', 'Complicate', 'Anger', 'Greed', 'Blizzard', 'Fling', 'Dominate', 'Jump', 'Wonder', 'Slay', 'Fear', 'Shock', 'Rust', 'Blaze', 'Havoc', 'Torment', 'Reparations', 'Concentrate', 'Balance', 'Singe', 'Equilibrium', 'Encroach', 'Reclaim', 'Blessing', 'Aggression', 'Fighting Chance', 'Shackles', 'Zap', 'Predict', 'Inspiration', 'Reset', 'Brand', 'Rout', 'Sunder', 'Smash', 'Disrupt', 'Nourish', 'Awakening', 'Breach', 'Heal', 'Cleansing', 'Absorb', 'Gamble', 'Unhinge', 'Warning', 'Browse', 'Reality Anchor', 'Conspiracy', 'Mountain', 'Time Spiral', 'Planar Chaos', 'Swamp', 'Island', 'Plains', 'Forest', 'Shelter', 'Dredge', 'Sky Swallower', 'Omen', 'Inquisition', 'Revenant', 'Genesis') " .
		" ORDER BY CHAR_LENGTH(card_name) DESC";

	$card_names = $wpdb->get_results( $query );
	$partner_code = get_option( MTGCARDREF_PLUGIN_NAME . '_partner_code' );
	$partner_text = ( $partner_code && $partner_code != '' ) ? "&partner=$partner_code" : '';

	foreach( $card_names as $card ) {
		$text_length = strlen($text_to_parse);
		$position = stripos($text_to_parse, $card->card_name);

		while( $position !== false ) {
			$url_ready_card_name = urlencode( $card->card_name );
			$name_length = strlen( $card->card_name );
			
			if( $position + $name_length - 1 + 4 > $text_length ) {
				$url_ready_card_name = urlencode( $card->card_name );
				$text_to_parse = substr_replace( $text_to_parse, MTGCARDREF_BASE_URL . $url_ready_card_name . '" rel="' . MTGCARDREF_GETTER_URL . $url_ready_card_name . '">' . $card->card_name . '</a>', $position, $name_length );
			// For the next pass	
				$position = false;
			} elseif( substr($text_to_parse, $position + $name_length - 1, 4) != '</a>' ) {
				$boundary_start = ( $position > 0 ) ? $position - 1 : 0;
				$boundary_stop = ($position + $name_length + 1 < strlen($text_to_parse)) ? $position + $name_length + 1 : $position;
				$text_to_check = substr($text_to_parse, $boundary_start, ($boundary_stop - $boundary_start));

				if( preg_match( '/([^a-zA-Z0-9\-_+\"]' . $card->card_name . '(s|[^a-zA-Z0-9\-_+\"]))/i', $text_to_check ) )
					$text_to_parse = substr_replace( $text_to_parse, MTGCARDREF_BASE_URL . $url_ready_card_name . $partner_text . '" rel="' . MTGCARDREF_GETTER_URL . $url_ready_card_name . '">' . $card->card_name . '</a>', $position, $name_length );
				
				$position = stripos( $text_to_parse, $card->card_name, $position + strlen( MTGCARDREF_BASE_URL . $url_ready_card_name . $partner_text . '" rel="' . MTGCARDREF_GETTER_URL . $url_ready_card_name . '">' . $card->card_name . '</a>' ) );
			}
		}
	}

	echo $text_to_parse;

/*
				if( $card->card_name == 'Ow' ) {
					echo "Found this: " . substr($text_to_parse, $position + $name_length, 4) . '<br/>';
					echo "Checking: $text_to_check<br/>";
					echo "pattern: " . '/([^a-zA-Z0-9\-_+\"]' . $card->card_name . '(s|[^a-zA-Z0-9\-_+\"]))/i<br/>';
					echo preg_match( '/([^a-zA-Z0-9\-_+\"]' . $card->card_name . '(s|[^a-zA-Z0-9\-_+\"]))/i', $text_to_check );
				}
*/

?>
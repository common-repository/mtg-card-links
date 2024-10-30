<?php

	define( 'MTGCARDREF_VERSION', '1.0.0' );
	define( 'MTGCARDREF_PLUGIN_NAME', 'mtgcardref' );
	define( 'MTGCARDREF_TABLE_NAME', MTGCARDREF_PLUGIN_NAME . '_card_names' );
	define( 'MTGCARDREF_DIRECTORY', dirname(__FILE__) . '/' );
	define( 'MTGCARDREF_WEB_DIRECTORY', dirname($_SERVER['PHP_SELF']) . '/' );
	define( 'MTGCARDREF_TINYMCE_PLUGIN_DIRECTORY', 'wp-includes/js/tinymce/plugins/' . MTGCARDREF_PLUGIN_NAME . '/' );
	define( 'MTGCARDREF_OPTION_NAME', MTGCARDREF_PLUGIN_NAME . '_version' );
	define( 'MTGCARDREF_NONCE_NAME', 'mtgcardref_nonce' );
	define( 'MTGCARDREF_BASE_URL', '<a class="mtgcardref_rollover" href="http://store.tcgplayer.com/Products.aspx?GameName=Magic&Name=' );
	define( 'MTGCARDREF_GETTER_URL', MTGCARDREF_WEB_DIRECTORY . 'getter.php?n=' );

?>
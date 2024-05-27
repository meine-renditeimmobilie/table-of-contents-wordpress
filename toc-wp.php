function mri_slug_from_string( $slug ) {

	# wp_strip_all_tags didn't suffice
	$slug = strip_tags($slug);
	// Clean up things like &amp;
	$slug = html_entity_decode($slug);

	$slug = strtolower( str_replace( array( 'ä', 'ö', 'ü', 'ß' ), array( 'ae', 'oe', 'ue', 'ss' ), $slug ) );

	$slug = preg_replace( "/[^A-Za-z0-9]/", '-', $slug );
	$slug = str_replace( ' ', '-', $slug ); 
  $slug = str_replace( array( '------', '-----', '----', '---', '--'), '-', $slug ); 
	$slug = trim( $slug, '-' );
  return $slug;
}

$mri_onpagenav = '';

add_action( 'save_post', 'mri_onpagenav_save', 10, 2 );
function mri_onpagenav_save( $post_id, $post ) {

	global $mri_onpagenav;
	
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( 'post' !== $post->post_type ) {
		return; 
	}
	
	$content = apply_filters( 'the_content', $post->post_content );
	
	// preg_replace_callback not really needed here, had to split the generation of the On-Page-Nav and the changing of the post content into two steps, kept the original preg_replace_callback used for the content changing part, instead of using e.g. preg_match_all for the Nav creation
	$new_content = preg_replace_callback( '/<section(.*?)>(.*?)<h2(.*?)>(.*?)<\/h2>/ms', function( $match ) {
	    
		global $mri_onpagenav;
		$slug = mri_slug_from_string( $match[4] );
		
	  $mri_onpagenav .= '<li><a href="#' . $slug . '">' . $match[4] . '</a></li>';
	    
		return '<section' . $match[1] . ' id="' . $slug . '">' . $match[2] . '<h2' . $match[3] . '>' . $match[4] . '</h2>';
	}, $content );
	
	update_post_meta( $post_id, "_mrionpagenav", $mri_onpagenav );
}

add_filter( 'the_content', 'mri_filter_the_content_in_the_main_loop', 1 );
function mri_filter_the_content_in_the_main_loop( $content ) {

	// Check if we're inside the main loop in a single Post.
	if ( is_single() && in_the_loop() && is_main_query() ) {
	
		$new_content = preg_replace_callback( '/<section(.*?)>(.*?)<h2(.*?)>(.*?)<\/h2>/ms', function( $match ) {

			$slug = mri_slug_from_string( $match[4] );
			return '<section' . $match[1] . ' id="' . $slug . '">' . $match[2] . '<h2' . $match[3] . '>' . $match[4] . '</h2>';
			
		}, $content );	
	
		return $new_content;
	}
	return $content;
}


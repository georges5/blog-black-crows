<?php  add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/css/ie.css' );
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/css/ie7.css' );

}
/* widget dans le header */
if (function_exists('register_sidebar')) {
register_sidebar(array(
'name' => 'Sidebar Widgets',
'id' => 'sidebar-widgets',
'description' => 'Widget Area',
'before_widget' => '<div id="one" class="two">',
'after_widget' => '</div>',
'before_title' => '<h2>',
'after_title' => '</h2>'
)); }

/* Fonction pour masquer la barre d'administration aux utilisateurs*/
    function admin_bar($content) {
     
        return ( current_user_can("administrator") ) ? $content : false;     
    }
    add_filter( 'show_admin_bar' , 'admin_bar');
	
if ( ! function_exists( 'twentysixteen_entry_meta' ) ) :	
	/**
 * Prints HTML with meta information for the categories, tags.
 *
 * Create your own twentysixteen_entry_meta() to override in a child theme.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_entry_meta() {
	if ( 'post' == get_post_type() ) {
		$author_avatar_size = apply_filters( 'twentysixteen_author_avatar_size', 49 );
		printf( '<span class="byline"><span class="author vcard">%1$s<span class="screen-reader-text">%2$s </span> <a class="url fn n" href="%3$s">%4$s</a></span></span>',
			esc_html_x( 'Author', 'Used before post author name.', 'twentysixteen' ),
			('test'),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
	}

	if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
		twentysixteen_entry_date();
	}

	$format = get_post_format();
	if ( current_theme_supports( 'post-formats', $format ) ) {
		printf( '<span class="entry-format">%1$s<a href="%2$s">%3$s</a></span>',
			sprintf( '<span class="screen-reader-text">%s </span>', esc_html_x( 'Format', 'Used before post format.', 'twentysixteen' ) ),
			esc_url( get_post_format_link( $format ) ),
			esc_html( get_post_format_string( $format ) )
		);
	}

	if ( 'post' == get_post_type() ) {
		twentysixteen_entry_taxonomies();
	}

	if ( ! is_singular() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentysixteen' ), get_the_title() ) );
		echo '</span>';
	}
}
endif;


if ( ! function_exists( 'twentysixteen_excerpt_more' ) && ! is_admin() ) :
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 *
 * Create your own twentysixteen_excerpt_more() function to override in a child theme.
 *
 * @since Twenty Sixteen 1.0
 *
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function twentysixteen_excerpt_more() {
	$link = sprintf( '<a href="%1$s" class="more-link">%2$s</a>',
		esc_url( get_permalink( get_the_ID() ) ),
		/* translators: %s: Name of current post */
		sprintf( __( 'more %s', 'twentysixteen' ), '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>' )
	);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'twentysixteen_excerpt_more' );
endif;
<?php
if ( ! function_exists( 'bootstrap_content_nav' ) ) :
function bootstrap_content_nav($nav_id, $nav_class) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<div class="pagination <?php echo $nav_class; ?>" id="<?php echo $nav_id; ?>">
			<?php
				$prev_link = get_previous_posts_link(__('&larr; Newer Posts', 'wpbootstrap'));
				if($prev_link == '') {
					$prev_class = 'class="disabled"';
					$prev_link = '<a href="#" onclick="return false;">' . __('&larr; Newer Posts', 'wpbootstrap') . '</a>';
				}

				$next_link = get_next_posts_link(__('Older Posts &rarr;', 'wpbootstrap'));
				if($next_link == '') {
					$next_class = 'class="disabled"';
					$next_link = '<a href="#" onclick="return false;">' . __('Older Posts &rarr;', 'wpbootstrap') . '</a>';
				}
			?>
			<ul>
				<li <?php echo $prev_class ?>><?php echo $prev_link ?></li>
				<?php for($i=0; $i < $wp_query->max_num_pages; $i++) : ?>
					<?php 
					$page = $wp_query -> query_vars['paged'] <= 1 ? 0 : $wp_query -> query_vars['paged'] - 1;
					$class = $page == $i ? 'class="active"' : ''; 
					?>
					<li <?php echo $class; ?>><a href="<?php echo home_url('/'); ?>?paged=<?php echo $i+1; ?>"><?php echo $i+1; ?></a></li>
				<?php endfor; ?>
				<li <?php echo $next_class ?>><?php echo $next_link ?></li>
			</ul>
		</div>
	<?php endif;
}
endif;



/*
 * Print the <title> tag based on what is being viewed.
 */
if ( ! function_exists( 'bootstrap_title' ) ) :
function bootstrap_title() {
	global $page, $paged;
	wp_title( '|', true, 'right' );
	// Add the blog name.
	bloginfo( 'name' );
	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'wpbootstrap' ), max( $paged, $page ) );
}
endif;

/*
 * Print the <meta description> of the web page regarding the context
 */
if ( ! function_exists( 'bootstrap_description' ) ) :
function bootstrap_description() {
	global $post, $wp_query;
	if ( is_404() ) {
		$description = __('404 page not found: fish is gone, try again', 'wpbootstrap');
	} else if ( is_search() && '' != $wp_query->found_posts ) {
		$description = __('No result found: try again!', 'wpbootstrap');
	} else if ( is_home() || is_front_page() ) {
		$description = get_bloginfo( 'description', 'display' );
	} else if ( '' !== $post->post_excerpt ) { 
		$description = strip_tags( $post->post_excerpt );
	} else if ( is_category() ) {
		$description = wptexturize( category_description() );
	} else if ( is_tag() ) {
		$description = wptexturize( tag_description() );
	} else if ( is_author() ) {
		$description = wptexturize( get_the_author_meta( 'description' ) );
	} else { 
		$description = wp_html_excerpt( $post->post_content, 200 );
	}
	//Prevent shortcode to appear "as is"
	$description = preg_replace('#\[(.+)\]#','', $description);
	return $description;
}
endif;
/*
 * Return the section heading (title and description) regarding the context.
 */
if ( ! function_exists( 'bootstrap_section_heading' ) ) :
function bootstrap_section_heading() {
	global $post;
	$category_description = wptexturize( category_description() );
	$tag_description = wptexturize( tag_description() );
	$author_description = wptexturize( get_the_author_meta( 'description' ) );
	$section = array(
		'section_title' => '',
		'section_description' => ''
	);
	
	if(is_author()) {
		$section['section_title'] = sprintf( esc_attr__( 'Archives author for: %s', 'wpbootstrap' ), get_the_author() );
		
		if ( ! empty( $author_description ) ) {
			$section['section_description'] = $author_description;
		} else {
			$section['section_description'] = sprintf( __( 'Sorry, there is no description for author %s. If it\'s you, feel free to write a consistent description. It is a gook way to promote yourself.', 'wpbootstrap' ), '<mark>' . get_the_author() . '</mark>' );
		}
	} elseif(is_date() ) {
		if ( is_day() ) {
			$section['section_title'] = __( 'Daily Archives:', 'wpbootstrap' );
			$section['section_description'] = get_the_date();
		}
		elseif ( is_month() ) {
			$section['section_title'] = __( 'Monthly Archives:', 'wpbootstrap' );
			$section['section_description'] = get_the_date('F Y');
		}
		elseif ( is_year() ) {
			$section['section_title'] = __( 'Yearly Archives:', 'wpbootstrap' );
			$section['section_description'] = get_the_date('Y');
		}
		else {
			$section['section_title'] = __( 'Blog Archives', 'wpbootstrap' );
			$section['section_description'] = __( 'Blog Archives description', 'wpbootstrap' );
		}
	} 
	else if ( is_search() ) {
		$section['section_title'] = __('Search results for:', 'wpbootstrap' );
		$section['section_description'] = sprintf( __( '%s', 'wpbootstrap' ), '<mark>' . get_search_query() . '</mark>' );

	}
	else if ( is_404() ) {
		$section['section_title'] = __( 'Hi! This is somewhat embarrassing, isn&rsquo;t it?', 'wpbootstrap' );
		$section['section_description'] = __( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching, or one of the links below, can help.', 'wpbootstrap' );
	}
	return $section;
}
endif;

/*
 * Print the post meta in the post's header
 */
if ( ! function_exists( 'bootstrap_posted_on' ) ) :
function bootstrap_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'wpbootstrap' ),
		'meta-prep meta-prep-author',
		sprintf( '<time title="%1$s published at %2$s" class="%3$s" datetime="%4$s" pubdate>%5$s</time>',
			'[ ' . get_permalink() . ' ]',
			esc_attr( get_the_time() ),
			'entry-date',
			get_the_date('c'),
			get_the_date()
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'wpbootstrap' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;

/*
 * Print the post meta in the post's footer
 */
if ( ! function_exists( 'bootstrap_posted_in' ) ) :
function bootstrap_posted_in() {
	// Retrieves tag list of current post, separated by commas.
	$tag_list = get_the_tag_list( '', ', ' );
	if ( $tag_list ) {
		$posted_in = __( 'Posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark nofollow">permalink</a>. ', 'wpbootstrap' );
	} elseif ( is_object_in_taxonomy( get_post_type(), 'category' ) ) {
		$posted_in = __( 'Posted in %1$s. Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark tag nofollow">permalink</a>. ', 'wpbootstrap' );
	} else {
		$posted_in = __( 'Bookmark the <a href="%3$s" title="Permalink to %4$s" rel="bookmark nofollow">permalink</a>. ', 'wpbootstrap' );
	}
	// Prints the string, replacing the placeholders.
	printf(
		$posted_in,
		get_the_category_list( ', ' ),
		$tag_list,
		get_permalink(),
		the_title_attribute( 'echo=0' )
	);
	// Link for comments
	if ( comments_open() ) {
		comments_popup_link( __( 'No comments yet', 'wpbootstrap' ), __( '1 comment', 'wpbootstrap' ), __( '% comments', 'wpbootstrap' ), 'comments-link', __( 'Comments are off for this post', 'wpbootstrap' ) );
		_e( '<span class="meta-sep"> | </span>', 'wpbootstrap' );
	}
	// Edit post if user is logged in and allowed
	edit_post_link( __( '(Edit this post)', 'wpbootstrap' ) );
}
endif;

if(!function_exists( 'bootstrap_favicons')) :
    function bootstrap_favicons() {
        $dir = get_stylesheet_directory_uri();
        $favicon = $dir . '/img/icons/favicon.ico';
        $ifavico = $dir . '/img/icons/apple-touch-icon.png';

        return sprintf(
            "%s\n%s\n",
            sprintf('<link rel="shortcut icon" href="%s" />', $favicon),
            sprintf('<link rel="apple-touch-icon" href="%s" />', $ifavico)
        );
    }
endif;

/**
 * Print extra meta tags into <head>
 * Signup on Google Webmaster Tools : https://www.google.com/webmasters/tools/
 * Signup on Alexa : http://www.alexa.com/siteowners/claim
 *
 * Don't forget to fill the "content" attributes, 
 * or duplicate this function in your Child theme functions.php file
 */
if (!function_exists( 'bootstrap_extra_head')) :
function bootstrap_extra_head() {
    global $theme_config;
    
	return sprintf(
        "%s\n%s\n",
        (isset($theme_config['google-site-verification']) ? sprintf('<meta name="google-site-verification" content="%s" />', $theme_config['google-site-verification']) : ''),
        '<link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml" />'
    );
}
endif;

?>
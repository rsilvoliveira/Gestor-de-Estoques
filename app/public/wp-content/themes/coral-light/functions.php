<?php
/**
 * coral-light functions and definitions
 *
 * @package coral-light
 */

/* Launch the Hybrid Core framework. 
require_once( trailingslashit( get_template_directory() ) . 'hybrid-core/hybrid.php' );
new Hybrid(); */
 
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

if ( ! function_exists( 'coral_light_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function coral_light_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on coral-light, use a find and replace
	 * to change 'coral-light' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'coral-light', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	// Gutenberg editor
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support('editor-styles');
	/*
	 * This theme styles the visual editor to resemble the theme style
	 */	
	add_editor_style( 'css/editor-style.css' );
	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );
	/*
	 * Enable support for custom logo.
	 */
	add_theme_support( 'custom-logo', array( 'flex-height' => true, 'flex-width' => true, ) );
	set_post_thumbnail_size( 210, 210 );
	add_image_size( 'coral-light-medium-large-2x', 1536 );
	add_image_size( 'coral-light-large-2x', 1960 );
	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'coral-light' ),
		'social' => __( 'Social Links Menu', 'coral-light' ),
	) );
	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'audio',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'coral_light_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
	
	// Woocommerce 
    add_theme_support( 'woocommerce' );

	add_theme_support( 'customize-selective-refresh-widgets' );	

}
endif; // coral_light_setup
add_action( 'after_setup_theme', 'coral_light_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function coral_light_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'coral-light' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer', 'coral-light' ),
		'id'            => 'footer-copyright',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'coral_light_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function coral_light_scripts() {

	$logoheight = absint(get_theme_mod('coral_light_logoheight_setting', '100'));
	$title_font = wp_kses(get_theme_mod('title_font_setting', 'Default font'), array(), array());
	$tagline_font = wp_kses(get_theme_mod('tagline_font_setting', 'Default font'), array(), array());
	$body_font = wp_kses(get_theme_mod('body_font_setting', 'Default font'), array(), array());
	$heading_font = wp_kses(get_theme_mod('heading_font_setting', 'Default font'), array(), array());
	
	$search_offset = intval(get_theme_mod('coral_light_searchoffset_setting', '40'));
	$social_offset = intval(get_theme_mod('coral_light_socialoffset_setting', '43'));
	$menu_offset = intval(get_theme_mod('coral_light_menuoffset_setting', '15'));
	$title_offset = intval(get_theme_mod('coral_light_titleoffset_setting', '25'));
	$tagline_offset = intval(get_theme_mod('coral_light_taglineoffset_setting', '-5'));
	$title_fontsize = absint(get_theme_mod('coral_light_titlesize_setting', '38'));	
	$tagline_fontsize = absint(get_theme_mod('coral_light_taglinesize_setting', '15'));
	$title_color = '#' . get_theme_mod('title_color_setting', '000000');
	$title_color = ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $title_color )) ? $title_color : '#000000';
	$tagline_color = '#' . get_theme_mod('tagline_color_setting', '000000');
	$tagline_color = ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $tagline_color )) ? $tagline_color : '#000000';		
	$body_fontsize = absint(get_theme_mod('body_fontsize_setting', '14'));
	
	$slider_effect = wp_kses(get_theme_mod('slider_effect_setting','fade'), array(), array());
	$slider_animspeed = absint(get_theme_mod('slide_animspeed_setting','500'));	
	$slider_pausetime = absint(get_theme_mod('slide_pausetime_setting','5000'));
	$slider_array = array(
		'effect' => $slider_effect,
		'animspeed' => $slider_animspeed,
		'pausetime' => $slider_pausetime
	);	
	
	$css = "";
	if ("Default font" != $body_font && "0" != $body_font) {
		$css .= "body, button, input, select, textarea {	font-family: {$body_font};}
		";
	}
	if ("Default font" != $heading_font && "0" != $heading_font) {
		$css .= "h1, h2, h3, h4, h5, h6 { font-family: {$heading_font};}
		";
	}
	if ("Default font" != $title_font && "0" != $title_font) {
		$css .= "h1.site-title, h3.site-title { font-family: {$title_font};}
		";
	}
	if ("Default font" != $tagline_font && "0" != $tagline_font) {
		$css .= "h2.site-description, h4.site-description { font-family: {$tagline_font};}
		";
	}

	$css .= "
		body, button, input, select, textarea {	font-size: {$body_fontsize}px;}
		h1.site-title, h3.site-title {
			margin-top: {$title_offset}px; 
			font-size: {$title_fontsize}px; 
		}
		h1.site-title a,
		h1.site-title a:visited,
		h1.site-title a:hover,
		h1.site-title a:active,
		h1.site-title a:focus,
		h3.site-title a,
		h3.site-title a:visited,
		h3.site-title a:hover,
		h3.site-title a:active,
		h3.site-title a:focus {
			color: {$title_color} !important;
		}
		
		h2.site-description, h4.site-description {
			margin-top: {$tagline_offset}px;
			font-size: {$tagline_fontsize}px;
			color: {$tagline_color};
		}
		.custom-logo {max-height: {$logoheight}px;}		
		@media screen and (min-width: 768px) {
			.main-navigation {margin-top: {$menu_offset}px;}
			#search1 {margin-top: {$search_offset}px;}
			#social1 {margin-top: {$social_offset}px;}
		}
	";

	if (!is_rtl()) {
		wp_enqueue_style( 'coral-light-style', get_stylesheet_directory_uri() . '/style.css' );
	} else {
		wp_enqueue_style( 'coral-light-style', get_stylesheet_directory_uri() . '/style-rtl.css' );
	}	
	wp_add_inline_style( 'coral-light-style', $css );
	
	wp_enqueue_script( 'navigation', get_template_directory_uri() . '/js/jquery.smartmenus.min.js', array('jquery'), '0.9.7', true );

	wp_enqueue_script( 'slider', get_template_directory_uri() . '/js/jquery.nivo.slider.pack.js', array('jquery'), '3.2' );

	wp_enqueue_script( 'skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	wp_enqueue_script( 'coral-light-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20160430', true );

	wp_localize_script( 'coral-light-script', 'nivoSliderParams', $slider_array );	
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'coral_light_scripts' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Create the required classes for the logo
 */
function coral_light_logo_class() {
	$logowidth = absint(get_theme_mod('coral_light_logowidth_setting', '35'));
	$class=" grid-". $logowidth ." tablet-grid-". $logowidth ." mobile-grid-100";
	echo $class;
}	
function coral_light_social_class() {
	$searchwidth = absint(get_theme_mod('coral_light_searchwidth_setting', '40'));
	$showsearch = absint(get_theme_mod('coral_light_showsearch_setting', '1'));
	if ( '0' == $showsearch ) $searchwidth = 0;
	$socialwidth= 100 - $searchwidth;
	if ( 0 == $socialwidth ) {
		$class=" hide-on-desktop hide-on-tablet hide-on-mobile";
	} else {
		if (34 == $socialwidth || 67 == $socialwidth) $socialwidth = $socialwidth -1;
		$class=" grid-". $socialwidth ." tablet-grid-". $socialwidth ." mobile-grid-100";
	}
	echo $class;
}	
function coral_light_search_class() {
	$searchwidth = absint(get_theme_mod('coral_light_searchwidth_setting', '40'));
	$showsearch = absint(get_theme_mod('coral_light_showsearch_setting', '1'));
	if ( '0' == $showsearch ) $searchwidth = 0;
	if ( 0 == $searchwidth ) {
		$class=" hide-on-desktop hide-on-tablet hide-on-mobile";
	} else {	
		$class=" grid-". $searchwidth ." tablet-grid-". $searchwidth ." mobile-grid-100";
	}
	echo $class;
}	
/**
 * Create the required classes for the header widget area
 */
function coral_light_header_right_class() {
	$logowidth = absint(get_theme_mod('coral_light_logowidth_setting', '35'));
	$areawidth = 100 - $logowidth;
	if ( 0 != $areawidth) {
		if (34 == $areawidth || 67 == $areawidth) $areawidth = $areawidth -1;
		$class=" grid-". $areawidth ." tablet-grid-". $areawidth ." mobile-grid-100";
	} else {
		$class=" hide-on-desktop hide-on-tablet hide-on-mobile";
	}
	echo $class;
}	

/**
 * Create the required classes for the site columns
 */
function coral_light_column_class($column) {

		$sidebarwidth = absint(get_theme_mod('coral_light_sidebarwidth_setting', '30'));
		$contentwidth = 100 - $sidebarwidth;
		if (34 == $contentwidth || 67 == $contentwidth) $contentwidth = $contentwidth -1;
		switch ($column) {
			case "content":
				$class=" grid-". $contentwidth ." tablet-grid-". $contentwidth ." mobile-grid-100 push-". $sidebarwidth ." tablet-push-". $sidebarwidth;
				break;
			case "sidebar1":
				$class=" grid-". $sidebarwidth ." tablet-grid-". $sidebarwidth ." mobile-grid-100 pull-". $contentwidth ." tablet-pull-". $contentwidth;
				break;
		}	
		echo $class;
}

/**
 * Create the required classes for the footer copyright widget
 */
function coral_light_copyright_class() {
	$class=" grid-70 tablet-grid-70 mobile-grid-100";
	echo $class;
}	
/**
 * Create the required classes for the footer link
 */
function coral_light_footer_link_class() {
	$class=" grid-30 tablet-grid-30 mobile-grid-100";
	echo $class;
}	

// Nivoslider
if ( ! function_exists( "coral_light_nivoslider" )) :
function coral_light_nivoslider() {

  $front_page = ( '1' == get_theme_mod('front_page_setting','')) ? '1' : '';
  $allpages = ( '1' == get_theme_mod('allpages','1')) ? '1' : '';
  $posts_page = ( '1' == get_theme_mod('posts_page_setting','')) ? '1' : '';
  $ids = get_theme_mod('post_id_setting','-999999');
  $arrids = explode(',', $ids);
  foreach($arrids as $key => $val) {
    $arrids[$key] = intval($val);
  }

  if (($front_page && is_front_page()) || $allpages || ($posts_page && is_home()) || is_single($arrids) || is_page($arrids)) {
  echo '<div id="myslideshow" class="myslideshow grid-container">
			<div class="slider-wrapper theme-default grid-100 tablet-grid-100 mobile-grid-100">
			<div id="slider" class="nivoSlider">';
			for ($i=1;$i<5;$i++) { 
				if ( 1 == $i ) {
					$def_image = get_template_directory_uri() . '/images/balloons-grey.jpg';
					$slider_image = get_theme_mod('slider_image'.$i, $def_image);
				} elseif ( 2 == $i ) {
					$def_image = get_template_directory_uri() . '/images/balloons.jpg';
					$slider_image = get_theme_mod('slider_image'.$i, $def_image);
				} else { 
					$slider_image = get_theme_mod('slider_image'.$i, '');
				}
				
				$slide_title = get_theme_mod('slide_title'.$i,'');
				if ($slider_image) {
					if ($slide_title) { 
					echo '<img src="'. esc_url($slider_image) .'" alt="" title="#htmlcaption'.$i.'">';
					} else {
					echo '<img src="'. esc_url($slider_image) .'" alt="">';
					}
				}
			} 
		echo '</div>';
			for ($i=1;$i<5;$i++) { 
				if ( 1 == $i ) {
					$def_image = get_template_directory_uri() . '/images/balloons-grey.jpg';
					$slider_image = get_theme_mod('slider_image'.$i, $def_image);
				} elseif ( 2 == $i ) {
					$def_image = get_template_directory_uri() . '/images/balloons.jpg';
					$slider_image = get_theme_mod('slider_image'.$i, $def_image);
				} else { 
					$slider_image = get_theme_mod('slider_image'.$i, '');
				}
				$slide_link = get_theme_mod('slide_link'.$i,'#');
				$slide_title = get_theme_mod('slide_title'.$i,'');
				if ($slider_image && $slide_title) {
					echo '<div id="htmlcaption'.$i.'" class="nivo-html-caption">
							<a href="'.esc_url($slide_link).'">'.esc_html($slide_title).'</a>.
						 </div>'; 
				}
			} 		
	echo '</div>
		</div>';
  }
}
add_action( 'coral_light_slider', 'coral_light_nivoslider' );
endif;

/* Change attachment page image size*/
if ( ! function_exists( "coral_light_prepend_attachment" )) :
function coral_light_prepend_attachment($p) {
return wp_get_attachment_link(0, 'large', false);
}
add_filter('prepend_attachment', 'coral_light_prepend_attachment');
endif;

/* Fallback for wp_nav_menu */
function coral_light_wp_page_menu_mine( $args = array() ) {
	$defaults = array('sort_column' => 'menu_order, post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
	$args = wp_parse_args( $args, $defaults );

	/**
	 * Filter the arguments used to generate a page-based menu.
	 *
	 * @since 2.7.0
	 *
	 * @see wp_page_menu()
	 *
	 * @param array $args An array of page menu arguments.
	 */
	$args = apply_filters( 'wp_page_menu_args', $args );

	$menu = '';

	$list_args = $args;

	// Show Home in the menu
	if ( ! empty($args['show_home']) ) {
		if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			$text = __('Home', 'coral-light');
		else
			$text = $args['show_home'];
		$class = '';
		if ( is_front_page() && !is_paged() )
			$class = 'class="current_page_item"';
		$menu .= '<li ' . $class . '><a href="' . home_url( '/' ) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
		// If the front page is a page, add it to the exclude list
		if (get_option('show_on_front') == 'page') {
			if ( !empty( $list_args['exclude'] ) ) {
				$list_args['exclude'] .= ',';
			} else {
				$list_args['exclude'] = '';
			}
			$list_args['exclude'] .= get_option('page_on_front');
		}
	}

	$list_args['echo'] = false;
	$list_args['title_li'] = '';
	$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );

	if ( $menu ) {
		if (!is_rtl()) {
			$menu = '<ul id="main-menu" class="sm sm-clean collapsed">' . $menu . '</ul>';
		} else {
			$menu = '<ul id="main-menu" class="sm sm-rtl sm-clean collapsed">' . $menu . '</ul>';
		}	
	}
	$menu = '<div class="' . esc_attr($args['menu_class']) . '">' . $menu . "</div>\n";

	/**
	 * Filter the HTML output of a page-based menu.
	 *
	 * @since 2.7.0
	 *
	 * @see wp_page_menu()
	 *
	 * @param string $menu The HTML output.
	 * @param array  $args An array of arguments.
	 */
	$menu = apply_filters( 'wp_page_menu', $menu, $args );
	if ( $args['echo'] )
		echo $menu;
	else
		return $menu;
}
/* Woocommerce support */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_before_main_content', 'coral_light_theme_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'coral_light_theme_wrapper_end', 10);

function coral_light_theme_wrapper_start() {
  echo '<div id="primary" class="content-area egrid'; coral_light_column_class('content'); echo '">
		<main id="main" class="site-main" role="main">';
}

function coral_light_theme_wrapper_end() {
  echo '</main>	</div>';
}

// Woocommerce breadcrumbs removal
add_action( 'init', 'coral_light_remove_wc_breadcrumbs' );
function coral_light_remove_wc_breadcrumbs() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}
// Modification of max_srcset_image_width
if ( ! function_exists( "coral_light_max_srcset_image_width" )) :
function coral_light_max_srcset_image_width( $max_width, $size_array ) {
    $width = $size_array[0];
 
    if ( $width > 800 ) {
        $max_width = 1960;
    }
 
    return $max_width;
}
add_filter( 'max_srcset_image_width', 'coral_light_max_srcset_image_width', 10, 2 );
endif;
/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images
 *
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function coral_light_content_image_sizes_attr( $sizes, $size ) {
	$width = $size[0];

	if ( 980 > $width ) {
		$sizes = '(max-width: ' . $width . 'px) 100vw, ' . $width . 'px';
	} else {
		$sizes = '(max-width: 980px) 100vw, 980px';
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'coral_light_content_image_sizes_attr', 10 , 2 );

// make your custom sizes selectable from your WordPress admin
add_filter( 'image_size_names_choose', 'coral_light_my_custom_sizes' );
 
function coral_light_my_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'medium_large' => __( 'Medium-Large', 'coral-light' ),
    ) );
}

/**
 * Display icons in social links menu.
 *
 * @param  string  $item_output The menu item output.
 * @param  WP_Post $item        Menu item object.
 * @param  int     $depth       Depth of the menu.
 * @param  array   $args        wp_nav_menu() arguments.
 * @return string  $item_output The menu item output with social icon.
 */
function coral_light_nav_menu_social_icons( $item_output, $item, $depth, $args ) {
	// Get supported social icons.
	$social_icons = coral_light_social_links_icons();

	// Change icon inside social links menu if there is supported URL.
	if ( 'social' === $args->theme_location ) {
		foreach ( $social_icons as $attr => $value ) {
			if ( false !== strpos( $item_output, $attr ) ) {
				$iconclass = esc_attr( $value );
				$item_output = str_replace( $args->link_after, '</span>' . '<i class="'. $iconclass .'"></i>', $item_output );
			}
		}
	}

	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'coral_light_nav_menu_social_icons', 10, 4 );

/**
 * Returns an array of supported social links (URL and icon class).
 *
 * @return array $social_links_icons
 */
function coral_light_social_links_icons() {
	// Supported social links icons.
	$social_links_icons = array(
		'behance.net'     => 'fa fa-behance',
		'codepen.io'      => 'fa fa-codepen',
		'del.icio.us'     => 'fa fa-delicious',
		'deviantart.com'  => 'fa fa-deviantart',
		'digg.com'        => 'fa fa-digg',
		'dribbble.com'    => 'fa fa-dribbble',
		'dropbox.com'     => 'fa fa-dropbox',
		'facebook.com'    => 'fa fa-facebook',
		'feed'    		  => 'fa fa-rss',
		'flickr.com'      => 'fa fa-flickr',
		'foursquare.com'  => 'fa fa-foursquare',
		'plus.google.com' => 'fa fa-google-plus',
		'github.com'      => 'fa fa-github',
		'instagram.com'   => 'fa fa-instagram',
		'linkedin.com'    => 'fa fa-linkedin',
		'mailto:'         => 'fa fa-envelope-o',
		'medium.com'      => 'fa fa-medium',
		'pinterest.com'   => 'fa fa-pinterest-p',
		'getpocket.com'   => 'fa fa-get-pocket',
		'reddit.com'      => 'fa fa-reddit-alien',
		'skype.com'       => 'fa fa-skype',
		'skype:'          => 'fa fa-skype',
		'slideshare.net'  => 'fa fa-slideshare',
		'snapchat.com'    => 'fa fa-snapchat-ghost',
		'soundcloud.com'  => 'fa fa-soundcloud',
		'spotify.com'     => 'fa fa-spotify',
		'stumbleupon.com' => 'fa fa-stumbleupon',
		'tumblr.com'      => 'fa fa-tumblr',
		'twitch.tv'       => 'fa fa-twitch',
		'twitter.com'     => 'fa fa-twitter',
		'vimeo.com'       => 'fa fa-vimeo',
		'vine.co'         => 'fa fa-vine',
		'vk.com'          => 'fa fa-vk',
		'wordpress.org'   => 'fa fa-wordpress',
		'wordpress.com'   => 'fa fa-wordpress',
		'xing.com'   	  => 'fa fa-xing',
		'yelp.com'        => 'fa fa-yelp',
		'youtube.com'     => 'fa fa-youtube',
	);

	/**
	 * Filter social links icons.
	 *
	 * @param array $social_links_icons
	 */
	return apply_filters( 'coral_light_social_links_icons', $social_links_icons );
}
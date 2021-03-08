<?php
/**
 * coral-light Theme Customizer
 *
 * @package coral-light
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */

 //--------- Sanitize
function coral_light_sanitize_yesno($setting){ if ( 0==$setting || 1==$setting ) return $setting; return 1;}
function coral_light_sanitize_voffset($setting){ if (is_numeric($setting) && $setting>=0) return $setting; return 25;}
function coral_light_sanitize_voffset2($setting){ if (is_numeric($setting)) return $setting; return 0;}
function coral_light_sanitize_logoheight($setting){ if (is_numeric($setting) && $setting>=40 && $setting<=300) return $setting; return 100;}
function coral_light_sanitize_size($setting){ if (is_numeric($setting) && $setting>=0) return $setting; return 0;}
function coral_light_sanitize_typography( $input ) {
		$valid = array( 	"Default font" => "Default font",
							"Arial, Helvetica, sans-serif" => "Arial, Helvetica, sans-serif",
							"'Arial Black', Gadget, sans-serif" => "'Arial Black', Gadget, sans-serif",
							"'Helvetica Neue', Helvetica, Arial, sans-serif" => "'Helvetica Neue', Helvetica, Arial, sans-serif",
							"'Comic Sans MS', cursive, sans-serif" => "'Comic Sans MS', cursive, sans-serif",
							"Impact, Charcoal, sans-serif" => "Impact, Charcoal, sans-serif",
							"'Lucida Sans Unicode', 'Lucida Grande', sans-serif" => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
							"Tahoma, Geneva, sans-serif" => "Tahoma, Geneva, sans-serif",
							"'Trebuchet MS', Helvetica, sans-serif" => "'Trebuchet MS', Helvetica, sans-serif",
							"Verdana, Geneva, sans-serif" => "Verdana, Geneva, sans-serif",
							"Georgia, serif" => "Georgia, serif",
							"'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
							"'Times New Roman', Times, serif" => "'Times New Roman', Times, serif",
							"'Courier New', Courier, monospace" => "'Courier New', Courier, monospace",
							"'Lucida Console', Monaco, monospace" => "'Lucida Console', Monaco, monospace"
		);
 
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return "Default font";
    }
}
function coral_light_sanitize_pausetime($setting){ if (is_numeric($setting) && $setting>=0) return $setting; return 5000;}
function coral_light_sanitize_animspeed($setting){ if (is_numeric($setting) && $setting>=0) return $setting; return 500;}
function coral_light_sanitize_effect( $input ) {
    $valid = array(
				'fade' => 'fade',
				'fold' => 'fold',
				'random' => 'random',
				'sliceDown' => 'sliceDown',
				'sliceDownLeft' => 'sliceDownLeft',
				'sliceDownLeft' => 'sliceDownLeft',
				'sliceUp' => 'sliceUp',
				'sliceUpLeft' => 'sliceUpLeft',
				'sliceUpDown' => 'sliceUpDown',
				'sliceUpDownLeft' => 'sliceUpDownLeft',
				'slideInRight' => 'slideInRight',
				'slideInLeft' => 'slideInLeft',
				'boxRandom' => 'boxRandom',
				'boxRain' => 'boxRain',
				'boxRainReverse' => 'boxRainReverse',
				'boxRainGrow' => 'boxRainGrow',
				'boxRainGrowReverse' => 'boxRainGrowReverse',
    );
 
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return '';
    }
}
function coral_light_sanitize_checkbox( $input ) {
		if ( $input == '1' ) {
			return '1';
		} else {
			return '';
		}
}
function coral_light_sanitize_radio( $input ) {
    $valid = array(
        '1' => __( 'Yes', 'coral-light' ),
		'0' => __( 'No, I want to display my own images', 'coral-light' ),
    );
 
    if ( array_key_exists( $input, $valid ) ) {
        return $input;
    } else {
        return '';
    }
}


//---------- Controls
if ( class_exists( 'WP_Customize_Control' ) ) {
	class Coral_Light_Textarea_Control extends WP_Customize_Control {
	    public $type = 'textarea';

	    public function render_content() {
	        ?>
	        <label>
	        <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
	        <textarea rows="5" class="custom-textarea" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
	        </label>
	        <?php
	    }
	}

    class Coral_Light_Text_Description_Control extends WP_Customize_Control {
        public $description;

	    public function render_content() {
			?>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <input type="text" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
            </label>
            <p class="description more-top"><?php echo ( $this->description ); ?></p>
			<?php
        }
    }
}
// function coral_light_customize_controls_print_styles() {
// 	wp_enqueue_style( 'coral_light_customizer_css', get_template_directory_uri() . '/css/customizer.css' );
// }
// add_action( 'customize_controls_print_styles', 'coral_light_customize_controls_print_styles' );

function coral_light_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->selective_refresh->add_partial( 'blogname', array(
		'selector' => '.site-title a',
		'render_callback' => 'coral_light_customize_partial_blogname',
	) );
	$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
		'selector' => '.site-description',
		'render_callback' => 'coral_light_customize_partial_blogdescription',
	) );

// Site title section panel ------------------------------------------------------
		$wp_customize->add_section( 'title_tagline', array(
			'title' => __( 'Logo, Site Title, Tagline, Site icon', 'coral-light' ),
			'priority' => 20,
		) );
		$choices =  array(
			'10' => '10%',
			'15' => '15%',
			'20' => '20%',
			'25' => '25%',
			'30' => '30%',
			'33' => '33%',
			'35' => '35%',
			'40' => '40%',
			'45' => '45%',
			'50' => '50%',
		);
		$wp_customize->add_setting( 'coral_light_logowidth_setting', array(
			'default' => '35',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'coral_light_logowidth_control', array(
			'label' => __( 'Max. width of the logo image or logo text (site title and tagline):', 'coral-light' ),
			'description' => __( 'This also affects the place width of the social icons and the search form.', 'coral-light' ),
			'section' => 'title_tagline',
			'settings' => 'coral_light_logowidth_setting',
			'priority' => 8,
			'type' => 'select',
			'choices' => $choices,
		) );	
		$logoharr =  array( 40 => '40px');
		for ($i = 41; $i <= 300; $i++) {
			$logoharr[$i]=$i."px";
		}
		$wp_customize->add_setting( 'coral_light_logoheight_setting' , array(
			'default'           => 100,
            'sanitize_callback' => 'coral_light_sanitize_logoheight',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( 'coral_light_logoheight_control', array(
			'label' 			=> __( 'Max. height of the logo image', 'coral-light' ),
			'section' 			=> 'title_tagline',
			'settings' 			=> 'coral_light_logoheight_setting',
			'priority' 			=> 9,
			'type' => 'select',
			'choices' => $logoharr,
		) );	
		$wp_customize->add_setting( 'blogname', array(
			'default'    => get_option( 'blogname' ),
			'type'       => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( 'blogname', array(
			'label'      => __( 'Site Title', 'coral-light' ),
			'description' => __( 'This is displayed only if you do not upload an image logo', 'coral-light' ),
			'section'    => 'title_tagline',
			'priority' => 10,
		) );

		$wp_customize->add_setting( 'blogdescription', array(
			'default'    => get_option( 'blogdescription' ),
			'type'       => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( 'blogdescription', array(
			'label'      => __( 'Tagline', 'coral-light' ),
			'description' => __( 'This is displayed only if you do not upload an image logo', 'coral-light' ),
			'section'    => 'title_tagline',
			'priority' => 20,
		) );

		$vposarr =  array( -100 => '-100px');
		for ($i = -99; $i <= 100; $i++) {
			$vposarr[$i]=$i."px";
		}
		$wp_customize->add_setting( 'coral_light_titleoffset_setting' , array(
			'default'           => 25,
            'sanitize_callback' => 'coral_light_sanitize_voffset2',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( 'coral_light_titleoffset__control', array(
			'label' 			=> __( 'Vertical position (margin-top) of the site title', 'coral-light' ),
			'section' 			=> 'title_tagline',
			'settings' 			=> 'coral_light_titleoffset_setting',
			'priority' 			=> 58,
			'type' => 'select',
			'choices' => $vposarr,
		) );	
		$wp_customize->add_setting( 'coral_light_taglineoffset_setting' , array(
			'default'           => -5,
            'sanitize_callback' => 'coral_light_sanitize_voffset2',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( 'coral_light_taglineoffset__control', array(
			'label' 			=> __( 'Vertical position (margin-top) of the tagline', 'coral-light' ),
			'section' 			=> 'title_tagline',
			'settings' 			=> 'coral_light_taglineoffset_setting',
			'priority' 			=> 59,
			'type' => 'select',
			'choices' => $vposarr,
		) );	

// Layout section panel ------------------------------------------------------
		$wp_customize->add_section( 'coral_light_layout_section', array(
			'title' => __( 'Layout', 'coral-light' ),
			'priority' => 27,
		) );

		$wp_customize->add_setting( 'coral_light_socialoffset_setting' , array(
			'default'           => '43',
            'sanitize_callback' => 'coral_light_sanitize_voffset2',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( 'coral_light_socialoffset_control', array(
			'label' 			=> __( 'Vertical position (margin-top) of the social icons', 'coral-light' ),
			'section' 			=> 'coral_light_layout_section',
			'settings' 			=> 'coral_light_socialoffset_setting',
			'priority' 			=> 39,
			'type' => 'select',
			'choices' => $vposarr,
		) );	
		$wp_customize->add_setting( 'coral_light_showsearch_setting', array(
			'default' => '1',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'coral_light_sanitize_yesno',
		) );

		$wp_customize->add_control( 'coral_light_showsearch_control', array(
			'label' => __( 'Display search form?', 'coral-light' ),
			'section' => 'coral_light_layout_section',
			'settings' => 'coral_light_showsearch_setting',
			'priority' => 40,
			'type' => 'select',
			'choices' => array(
				'1' => __( 'Yes', 'coral-light' ),
				'0' => __( 'No', 'coral-light' ),
			),
		) );
		$choices2 =  array(
			'10' => '10%',
			'15' => '15%',
			'20' => '20%',
			'25' => '25%',
			'30' => '30%',
			'33' => '33%',
			'35' => '35%',
			'40' => '40%',
			'45' => '45%',
			'50' => '50%',
			'55' => '55%',
			'60' => '60%',
			'65' => '65%',
			'66' => '66%',
			'70' => '70%',
			'75' => '75%',
			'80' => '80%',
			'85' => '85%',
			'90' => '90%',
			'95' => '95%',
			'100' => '100%',
		);		
		$wp_customize->add_setting( 'coral_light_searchwidth_setting', array(
			'default' => '40',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'coral_light_searchwidth_control', array(
			'label' => __( 'Width of the search form:', 'coral-light' ),
			'description' => __( 'Percentage of the place on the side of the logo text. (Here the right side is 100%, but leave enough place for the social icons!)', 'coral-light' ),
			'section' => 'coral_light_layout_section',
			'settings' => 'coral_light_searchwidth_setting',
			'priority' => 42,
			'type' => 'select',
			'choices' => $choices2,
		) );
		$wp_customize->add_setting( 'coral_light_searchoffset_setting' , array(
			'default'           => '40',
            'sanitize_callback' => 'coral_light_sanitize_voffset2',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( 'coral_light_searchoffset_control', array(
			'label' 			=> __( 'Vertical position (margin-top) of the search box', 'coral-light' ),
			'section' 			=> 'coral_light_layout_section',
			'settings' 			=> 'coral_light_searchoffset_setting',
			'priority' 			=> 44,
			'type' => 'select',
			'choices' => $vposarr,
		) );	
		$wp_customize->add_setting( 'coral_light_menuoffset_setting' , array(
			'default'           => '15',
            'sanitize_callback' => 'coral_light_sanitize_voffset2',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( 'coral_light_menuoffset_control', array(
			'label' 			=> __( 'Vertical position (margin-top) of the topmenu', 'coral-light' ),
			'section' 			=> 'coral_light_layout_section',
			'settings' 			=> 'coral_light_menuoffset_setting',
			'priority' 			=> 45,
			'type' => 'select',
			'choices' => $vposarr,
		) );
		$choices1 =  array(
			'10' => '10%',
			'15' => '15%',
			'20' => '20%',
			'25' => '25%',
			'30' => '30%',
			'33' => '33%',
			'35' => '35%',
			'40' => '40%',
			'45' => '45%',
			'50' => '50%',
			'55' => '55%',
			'60' => '60%',
			'65' => '65%',
			'66' => '66%',
			'70' => '70%',
		);
		$wp_customize->add_setting( 'coral_light_sidebarwidth_setting', array(
			'default' => '30',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'coral_light_sidebarwidth_control', array(
			'label' => __( 'Sidebar width:', 'coral-light' ),
			'section' => 'coral_light_layout_section',
			'settings' => 'coral_light_sidebarwidth_setting',
			'priority' => 46,
			'type' => 'select',
			'choices' => $choices1,
		) );		

// Typography
		$wp_customize->add_section( 'coral_light_typography_section', array(
			'title' 			=> __( 'Typography', 'coral-light' ),
			'priority'			=> 32,
			'description' => __( 'Here you can set up the typography with basic web safe fonts', 'coral-light' ),
		) );
		$typoarr = array( 	"Default font" => "Default font",
							"Arial, Helvetica, sans-serif" => "Arial, Helvetica, sans-serif",
							"'Arial Black', Gadget, sans-serif" => "'Arial Black', Gadget, sans-serif",
							"'Helvetica Neue', Helvetica, Arial, sans-serif" => "'Helvetica Neue', Helvetica, Arial, sans-serif",
							"'Comic Sans MS', cursive, sans-serif" => "'Comic Sans MS', cursive, sans-serif",
							"Impact, Charcoal, sans-serif" => "Impact, Charcoal, sans-serif",
							"'Lucida Sans Unicode', 'Lucida Grande', sans-serif" => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif",
							"Tahoma, Geneva, sans-serif" => "Tahoma, Geneva, sans-serif",
							"'Trebuchet MS', Helvetica, sans-serif" => "'Trebuchet MS', Helvetica, sans-serif",
							"Verdana, Geneva, sans-serif" => "Verdana, Geneva, sans-serif",
							"Georgia, serif" => "Georgia, serif",
							"'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif",
							"'Times New Roman', Times, serif" => "'Times New Roman', Times, serif",
							"'Courier New', Courier, monospace" => "'Courier New', Courier, monospace",
							"'Lucida Console', Monaco, monospace" => "'Lucida Console', Monaco, monospace"
		);
		$wp_customize->add_setting( 'title_font_setting', array(
			'default'        => 'Default font',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'coral_light_sanitize_typography',
		) );
		$wp_customize->add_control( 'title_font_control', array(
			'label'   			=> __('Site title font','coral-light'),
			'section' 			=> 'coral_light_typography_section',
			'settings' 			=> 'title_font_setting',
			'type'    			=> 'select',
			'priority'        	=> 5,
			'choices'    		=> $typoarr,
		) );
		$fontsizearr =  array('8' => '8px');
		for ($i = 8; $i <= 80; $i++) {
			$fontsizearr[$i]=$i."px";
		}
		$wp_customize->add_setting( 'coral_light_titlesize_setting', array(
			'default' => '38',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'coral_light_titlesize_control', array(
			'label' => __( 'Site title fontsize:', 'coral-light' ),
			'section' => 'coral_light_typography_section',
			'settings' => 'coral_light_titlesize_setting',
			'priority' => 10,
			'type' => 'select',
			'choices' => $fontsizearr,
		) );	
		$wp_customize->add_setting( 'tagline_font_setting', array(
			'default'        => 'Default font',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'coral_light_sanitize_typography',
		) );
		$wp_customize->add_control( 'tagline_font_control', array(
			'label'   			=> __('Tagline font','coral-light'),
			'section' 			=> 'coral_light_typography_section',
			'settings' 			=> 'tagline_font_setting',
			'type'    			=> 'select',
			'priority'        	=> 15,
			'choices'    		=> $typoarr,
		) );
		$wp_customize->add_setting( 'coral_light_taglinesize_setting', array(
			'default' => '15',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'coral_light_taglinesize_control', array(
			'label' => __( 'Tagline fontsize:', 'coral-light' ),
			'section' => 'coral_light_typography_section',
			'settings' => 'coral_light_taglinesize_setting',
			'priority' => 20,
			'type' => 'select',
			'choices' => $fontsizearr,
		) );	
		$wp_customize->add_setting( 'body_font_setting', array(
			'default'        => 'Default font',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'coral_light_sanitize_typography',
		) );
		$wp_customize->add_control( 'body_font_control', array(
			'label'   			=> __('Body font','coral-light'),
			'section' 			=> 'coral_light_typography_section',
			'settings' 			=> 'body_font_setting',
			'type'    			=> 'select',
			'priority'        	=> 25,
			'choices'    		=> $typoarr,
		) );
		$fontsizearr2 =  array('8' => '8px');
		for ($i = 8; $i <= 30; $i++) {
			$fontsizearr2[$i]=$i."px";
		}
		$wp_customize->add_setting( 'body_fontsize_setting', array(
			'default' => '14',
			'capability' => 'edit_theme_options',
            'sanitize_callback' => 'absint',
		) );

		$wp_customize->add_control( 'body_fontsize_control', array(
			'label' => __( 'Body fontsize:', 'coral-light' ),
			'section' => 'coral_light_typography_section',
			'settings' => 'body_fontsize_setting',
			'priority' => 30,
			'type' => 'select',
			'choices' => $fontsizearr2,
		) );
		$wp_customize->add_setting( 'heading_font_setting', array(
			'default'        => 'Default font',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'coral_light_sanitize_typography',
		) );
		$wp_customize->add_control( 'heading_font_control', array(
			'label'   			=> __('Heading font','coral-light'),
			'description' 		=> __( 'The h1, h2... fontsizes are based on the body fontsize', 'coral-light' ),
			'section' 			=> 'coral_light_typography_section',
			'settings' 			=> 'heading_font_setting',
			'type'    			=> 'select',
			'priority'        	=> 35,
			'choices'    		=> $typoarr,
		) );


// Slider section panel
		$wp_customize->add_section( 'coral_light_slider_section', array(
			'title' 			=> __( 'Slideshow', 'coral-light' ),
			'priority'			=> 35,
			'description' => __( 'Upload at least 980px wide images with the same aspect ratio!', 'coral-light' ),
		) );
		$wp_customize->add_setting( 'front_page_setting', array(
            'default'        	=> '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'coral_light_sanitize_checkbox',
        ) );
        $wp_customize->add_control( 'front_page_control', array(
            'label'   			=> __( 'Display slideshow on frontpage', 'coral-light' ),
            'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'front_page_setting',
            'type'    			=> 'checkbox',
            'priority' 			=> 3
        ) );
		$wp_customize->add_setting( 'posts_page_setting', array(
            'default'        	=> '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'coral_light_sanitize_checkbox',
        ) );
        $wp_customize->add_control( 'posts_page_control', array(
            'label'   			=> __( 'Display slideshow on blog page', 'coral-light'),
            'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'posts_page_setting',
            'type'    			=> 'checkbox',
            'priority' 			=> 4
        ) );
		$wp_customize->add_setting( 'allpages', array(
            'default'        	=> '1',
			'transport'         => 'refresh',
			'sanitize_callback' => 'coral_light_sanitize_checkbox',
        ) );
        $wp_customize->add_control( 'allpages_control', array(
            'label'   			=> __( 'Always display the slideshow', 'coral-light'),
            'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'allpages',
            'type'    			=> 'checkbox',
            'priority' 			=> 5
        ) );
		$wp_customize->add_setting( 'post_id_setting' , array(
			'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( new Coral_Light_Text_Description_Control( $wp_customize, 'post_id_control', array(
			'label' 			=> __( 'Comma separated IDs of posts/pages for which you need the slideshow (e.g. 1, 23, 54).', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'post_id_setting',
			'priority' 			=> 6,
		) ) );	
		//-----------------------------------------------
		$wp_customize->add_setting( 'slider_effect_setting', array(
			'default'        => 'fade',
			'sanitize_callback' => 'coral_light_sanitize_effect',
		) );
		
		$wp_customize->add_control( 'slider_effect_control', array(
			'label'   			=> __('Slideshow effect','coral-light'),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slider_effect_setting',
			'type'    			=> 'select',
			'priority'        	=> 7,
			'choices'    		=> array(
				'fade' => 'fade',
				'fold' => 'fold',
				'random' => 'random',
				'sliceDown' => 'sliceDown',
				'sliceDownLeft' => 'sliceDownLeft',
				'sliceDownLeft' => 'sliceDownLeft',
				'sliceUp' => 'sliceUp',
				'sliceUpLeft' => 'sliceUpLeft',
				'sliceUpDown' => 'sliceUpDown',
				'sliceUpDownLeft' => 'sliceUpDownLeft',
				'slideInRight' => 'slideInRight',
				'slideInLeft' => 'slideInLeft',
				'boxRandom' => 'boxRandom',
				'boxRain' => 'boxRain',
				'boxRainReverse' => 'boxRainReverse',
				'boxRainGrow' => 'boxRainGrow',
				'boxRainGrowReverse' => 'boxRainGrowReverse',
			),
		) );
		$wp_customize->add_setting( 'slide_animspeed_setting' , array(
			'default'           => '500',
            'sanitize_callback' => 'coral_light_sanitize_animspeed',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( new Coral_Light_Text_Description_Control( $wp_customize, 'slide_animspeed_control', array(
			'label' 			=> __( 'Animation speed (mS)', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slide_animspeed_setting',
			'priority' 			=> 8,
		) ) );	
		$wp_customize->add_setting( 'slide_pausetime_setting' , array(
			'default'           => '5000',
            'sanitize_callback' => 'coral_light_sanitize_pausetime',
			'transport'         => 'refresh',
		));
		$wp_customize->add_control( new Coral_Light_Text_Description_Control( $wp_customize, 'slide_pausetime_control', array(
			'label' 			=> __( 'Pause time (mS)', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slide_pausetime_setting',
			'priority' 			=> 9,
		) ) );	
		// ----------------------------------------------
		$wp_customize->add_setting( 'slide_title1' , array(
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		));
		$wp_customize->add_control( new Coral_Light_Text_Description_Control( $wp_customize, 'slide_title_control1', array(
			'label' 			=> __( 'Slide title 1', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slide_title1',
			'priority' 			=> 10,
		) ) );		
		$wp_customize->add_setting( 'slide_link1' , array(
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		));
		$wp_customize->add_control( new Coral_Light_Text_Description_Control( $wp_customize, 'slide_link_control1', array(
			'label' 			=> __( 'Title link 1 (start with http://)', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slide_link1',
			'priority' 			=> 14,
		) ) );		
		$slider_image = get_template_directory_uri() . '/images/balloons-grey.jpg';		
		$wp_customize->add_setting( 'slider_image1', array(
			'default'           => $slider_image,
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'slider_image_control1', array(
			'label'        		=> __( 'Slider image 1', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slider_image1',
			'priority' 			=> 16,
		) ) );
		// ----------------------------------------------
		$wp_customize->add_setting( 'slide_title2' , array(
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		));
		$wp_customize->add_control( new Coral_Light_Text_Description_Control( $wp_customize, 'slide_title_control2', array(
			'label' 			=> __( 'Slide title 2', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slide_title2',
			'priority' 			=> 20,
		) ) );		
		$wp_customize->add_setting( 'slide_link2' , array(
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		));
		$wp_customize->add_control( new Coral_Light_Text_Description_Control( $wp_customize, 'slide_link_control2', array(
			'label' 			=> __( 'Title link 2 (start with http://)', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slide_link2',
			'priority' 			=> 24,
		) ) );		
		$slider_image = get_template_directory_uri() . '/images/balloons.jpg';
		$wp_customize->add_setting( 'slider_image2', array(
			'default'           => $slider_image,
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'slider_image_control2', array(
			'label'        		=> __( 'Slider image 2', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slider_image2',
			'priority' 			=> 26,
		) ) );
		// ----------------------------------------------
		$wp_customize->add_setting( 'slide_title3' , array(
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		));
		$wp_customize->add_control( new Coral_Light_Text_Description_Control( $wp_customize, 'slide_title_control3', array(
			'label' 			=> __( 'Slide title 3', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slide_title3',
			'priority' 			=> 30,
		) ) );		
		$wp_customize->add_setting( 'slide_link3' , array(
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		));
		$wp_customize->add_control( new Coral_Light_Text_Description_Control( $wp_customize, 'slide_link_control3', array(
			'label' 			=> __( 'Title link 3 (start with http://)', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slide_link3',
			'priority' 			=> 34,
		) ) );		
		$wp_customize->add_setting( 'slider_image3', array(
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'slider_image_control3', array(
			'label'        		=> __( 'Slider image 3', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slider_image3',
			'priority' 			=> 36,
		) ) );
		// ----------------------------------------------
		$wp_customize->add_setting( 'slide_title4' , array(
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'sanitize_text_field',
		));
		$wp_customize->add_control( new Coral_Light_Text_Description_Control( $wp_customize, 'slide_title_control4', array(
			'label' 			=> __( 'Slide title 4', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slide_title4',
			'priority' 			=> 40,
		) ) );		
		$wp_customize->add_setting( 'slide_link4' , array(
			'default'           => '',
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		));
		$wp_customize->add_control( new Coral_Light_Text_Description_Control( $wp_customize, 'slide_link_control4', array(
			'label' 			=> __( 'Title link 4 (start with http://)', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slide_link4',
			'priority' 			=> 44,
		) ) );		
		$wp_customize->add_setting( 'slider_image4', array(
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'slider_image_control4', array(
			'label'        		=> __( 'Slider image 4', 'coral-light' ),
			'section' 			=> 'coral_light_slider_section',
			'settings' 			=> 'slider_image4',
			'priority' 			=> 46,
		) ) );
// Color section panel
		$wp_customize->add_section( 'colors', array(
			'title'          => __( 'Colors', 'coral-light' ),
			'priority'       => 40,
		) );		
		$wp_customize->add_setting( 'title_color_setting', array(
			'default'        => '000000',
			'capability' => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_hex_color_no_hash',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'title_color_control', array(
			'label'   => __( 'Site title color', 'coral-light' ),
			'section' => 'colors',
			'settings' => 'title_color_setting',
			'priority' => 4,
		) ) );
		$wp_customize->add_setting( 'tagline_color_setting', array(
			'default'        => '000000',
			'capability' => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_hex_color_no_hash',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tagline_color_control', array(
			'label'   => __( 'Tagline Color', 'coral-light' ),
			'section' => 'colors',
			'settings' => 'tagline_color_setting',
			'priority' => 6,
		) ) );
		$wp_customize->add_setting( 'background_color', array(
			'default'        => 'ffffff',
			'capability' => 'edit_theme_options',
			'sanitize_callback'    => 'sanitize_hex_color_no_hash',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
			'label'   => __( 'Background Color', 'coral-light' ),
			'section' => 'colors',
			'settings' => 'background_color',
			'priority' => 8,
		) ) );
}
add_action( 'customize_register', 'coral_light_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function coral_light_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function coral_light_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function coral_light_customize_preview_js() {
	wp_enqueue_script( 'coral_light_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'coral_light_customize_preview_js' );

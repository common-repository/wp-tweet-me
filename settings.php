<?php

//add_action('admin_print_scripts','acer_admin_scripts');

function wtm_plugin_menu() {  
  
    add_options_page(  
        'WP Tweet Me',           // The title to be displayed in the browser window for this page.  
        'WP Tweet Me',           // The text to be displayed for this menu item  
        'manage_options',           // Which type of users can see this menu item  
        'wtm_settings',   			// The unique ID - that is, the slug - for this menu item  
        'wtm_plugin_options'    	// The name of the function to call when rendering the page for this menu  
    );    
  
} // end sandbox_example_theme_menu  
add_action('admin_menu', 'wtm_plugin_menu'); 

function wtm_plugin_options() { 
?>
	<div class="wrap">
    	<div class="dashicons dashicons-admin-generic"></div>
        <h2>WP Tweet Me Options</h2>

        <form method="post" action="options.php">
        	<?php 
				settings_fields( 'wtm_settings' );
				do_settings_sections( 'wtm_settings' );
 				submit_button(); 
			?>
        </form>       
 <p class="description">If you like this plugin, please Tweet about it and follow me on twitter!</p>
<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://wordpress.org/plugins/wp-tweet-me/" data-text="I'm using WP Tweet Me on my site! It's amazing!" data-via="AndorNagy" data-count="none" data-hashtags="wptweetme">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script><a href="https://twitter.com/AndorNagy" class="twitter-follow-button" data-show-count="false">Follow @AndorNagy</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script> 
    </div>
<?php     
} // end acer_theme_display 
 
/* ------------------------------------------------------------------------ * 
 * Setting Registration 
 * ------------------------------------------------------------------------ */  

function wtm_initialize_display_options() { 

	if( false == get_option( 'wtm_general_options' ) ) {
		add_option( 'wtm_general_options' );	
	};
	
 
    // First, we register a section. This is necessary since all future options must belong to a  
    add_settings_section( 
        'wtm_settings_section',         				// ID used to identify this section and with which to register options 
        'Plugin Settings',                  			// Title to be displayed on the administration page 
        'wtm_options_callback',    						// Callback used to render the description of the section 
        'wtm_settings'        							// Page on which to add this section of options 
    ); 
      
	add_settings_field(   
		'wtm_twitter_user',                 			// ID used to identify the field throughout the theme  
		'Your Twitter User Name
		<P class="description">Without the @</p> ',  	// The label to the left of the option interface element
		'wtm_twitter_user_callback',      				// The name of the function responsible for rendering the option interface  
		'wtm_settings',       							// The page on which this option will be displayed  
		'wtm_settings_section',         				// The name of the section to which this field belongs  
		array(                             				// The array of arguments to pass to the callback. In this case, just a description.  
			' When someone tweets, this username will appear as "via @username".'  
		)  
	); 	 
	
	add_settings_field(   
		'wtm_twitter_user_rec',                     	// ID used to identify the field throughout the theme  
		'Recommanded a Twitter User 
		<P class="description">Without the @</p>',      // The label to the left of the option interface element
		'wtm_twitter_user_rec_callback',      			// The name of the function responsible for rendering the option interface  
		'wtm_settings',       							// The page on which this option will be displayed  
		'wtm_settings_section',         				// The name of the section to which this field belongs  
		array(                              			// The array of arguments to pass to the callback. In this case, just a description.  
			' A recommended user, appears after they tweeted.'  
		)  
	); 	  		
	  
    // Finally, we register the fields with WordPress  
    register_setting(  
        'wtm_settings',  
        'wtm_settings' 
    );  	
	
} // end acer_initialize_display_options  
add_action('admin_init', 'wtm_initialize_display_options');  

/* ------------------------------------------------------------------------ * 
 * Section Callbacks 
 * ------------------------------------------------------------------------ */   
  
/* General Settings */   
function wtm_options_callback() {  
    echo '<p>Enter your details below.</p>';  
} // end sandbox_general_options_callback  
  
/* ------------------------------------------------------------------------ * 
 * Section Field Callbacks 
 * ------------------------------------------------------------------------ */    
  
function wtm_twitter_user_callback( $args ) {
      
    // First, we read the social options collection  
    $options = get_option( 'wtm_settings' );  
      
    // Next, we need to make sure the element is defined in the options. If not, we'll set an empty string.  
    $url = ''; 
    if( isset( $options['wtm_twitter_user'] ) ) { 
        $url = $options['wtm_twitter_user']; 
    } // end if
			
	?><input type="text" id="twitter" name="wtm_settings[wtm_twitter_user]" value="<?php if( !empty ( $options['wtm_twitter_user'] ) ) { echo $options['wtm_twitter_user']; } ?>"/><label for="wtm_settings[wtm_twitter_user]"><?php echo $args[0] ?></label><?php
} 

function wtm_twitter_user_rec_callback( $args ) {
      
    // First, we read the social options collection  
    $options = get_option( 'wtm_settings' );  
      
    // Next, we need to make sure the element is defined in the options. If not, we'll set an empty string.  
    $url = ''; 
    if( isset( $options['wtm_twitter_user_rec'] ) ) { 
        $url = $options['wtm_twitter_user_rec']; 
    } // end if
			
	?><input type="text" id="twitter" name="wtm_settings[wtm_twitter_user_rec]" value="<?php if( !empty ( $options['wtm_twitter_user_rec'] ) ) { echo $options['wtm_twitter_user_rec']; } ?>"/><label for="wtm_settings[wtm_twitter_user_rec]"><?php echo $args[0] ?>(Optional)</label><?php
} 




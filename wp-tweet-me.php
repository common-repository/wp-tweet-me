<?php
/*
Plugin Name: Wp Tweet Me
Plugin URI: http://www.andornagy.com/my-plugins/wp-twitter-me
Description: Allows users to add twitter share links to words inside a text using a shortcode. Custom text, hashtags can also be set to each post/page.
Version: 0.1.0
Author: Andor Nagy
Author URI: http://www.andornagy.com
License: GPL2

  Copyright 2013  WP Tweet Me  (email : andornagy2012@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( dirname( __FILE__ ) . '/settings.php' );    		 	 // Including the base functions ( functions.php ) 

// Add Shortcode
function wp_tweet_me( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'text' => '',
	), $atts, 'wp-tweet-me' ) );
	
	// Getting Information about the current post/page
	global $post;
	
	// Getting the data
	$meta		 = get_post_meta($post->ID,'_wp_tweet_me',TRUE);
	$permalink 	 = get_permalink( $post->ID ); 
	$setting	 = get_option( 'wtm_settings' ); 

	// Checking for Hashtags
    $hashtags = ''; 
    if( isset( $meta['hashtags'] ) ) {
        $hashtags = '&hashtags='.$meta['hashtags'];
		// Removing Spaces
		$hashtags=preg_replace('/\s+/', '', $hashtags);
    } // end if
	
	// Checking if there is a custom text set or not
    $text = ''; 
    if( isset( $meta['description'] ) ) { 
        $text = '&text='.$meta['description'];
    } else {
		$text = '&text='.get_the_title($post->ID);
		}
	
	// Checking for twitter user
    $user = ''; 
    if( isset( $setting['wtm_twitter_user'] ) ) { 
        $user = '&via='.$setting['wtm_twitter_user'];
    } // end if			
	
	// Checking for recommanded twitter user
    $user_rec = ''; 
    if( isset( $setting['wtm_twitter_user_rec'] ) ) { 
        $user_rec = '&related='.$setting['wtm_twitter_user_rec'];
    } // end if			

	// Building the link
	$html = '<a href="https://twitter.com/share?original_referer='. $permalink . '&url=' . $permalink . $text . $user . $hashtags . $user_rec .'" target="_blank">'.$content.'</a>';

	return $html;
}

function register_shortcodes(){
   add_shortcode( 'wp-tweet-me', 'wp_tweet_me' );
}

add_action( 'init', 'register_shortcodes');

add_filter('widget_text', 'do_shortcode');

add_action('admin_init','wp_tweet_me_meta_init');

function wp_tweet_me_meta_init()
{
	// review the function reference for parameter details
	// http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	// http://codex.wordpress.org/Function_Reference/wp_enqueue_style

	// review the function reference for parameter details
	// http://codex.wordpress.org/Function_Reference/add_meta_box

	foreach (array('post','page') as $type) 
	{
		add_meta_box('all_wp_tweet_me', 'WP Tweet Me', 'wp_tweet_me_setup', $type, 'side', 'default');
	}
	
	add_action('save_post','wp_tweet_me_save');
}

function wp_tweet_me_setup()
{
	global $post;
 
	// using an underscore, prevents the meta variable
	// from showing up in the custom fields section
	$meta = get_post_meta($post->ID,'_wp_tweet_me',TRUE);
 ?>
<div class="my_meta_control">
	
	<p class="description">Enter the text and hashtags you want to be tweeted when somone tweets the article.</p>


	<label for="_my_meta[description]">Description <span>(optional)</span></label>

	<p>
		<textarea class="widefat" name="_wp_tweet_me[description]" rows="3"><?php if(!empty($meta['description'])) echo $meta['description']; ?></textarea>
		<span>Enter in a description ( If left empty, Post tile will be used )</span>
	</p>

	<label for="_wp_tweet_mea[hashtags]">Hashtags <span>(optional)</span></label>

	<p>
		<input type="text" class="widefat" name="_wp_tweet_me[hashtags]" size="30" value="<?php if(!empty($meta['hashtags'])) echo $meta['hashtags']; ?>"/>
		<span>Separate them with comma ( , )</span>
	</p>


</div>
<?php
	// create a custom nonce for submit verification later
	echo '<input type="hidden" name="wp_tweet_me_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}
 
function wp_tweet_me_save($post_id) 
{
	// authentication checks

	// make sure data came from our meta box
	if (!wp_verify_nonce($_POST['wp_tweet_me_noncename'],__FILE__)) return $post_id;

	// check user permissions
	if ($_POST['post_type'] == 'page') 
	{
		if (!current_user_can('edit_page', $post_id)) return $post_id;
	}
	else 
	{
		if (!current_user_can('edit_post', $post_id)) return $post_id;
	}

	// authentication passed, save data

	// var types
	// single: _my_meta[var]
	// array: _my_meta[var][]
	// grouped array: _my_meta[var_group][0][var_1], _my_meta[var_group][0][var_2]

	$current_data = get_post_meta($post_id, '_wp_tweet_me', TRUE);	
 
	$new_data = $_POST['_wp_tweet_me'];

	wp_tweet_me_clean($new_data);
	
	if ($current_data) 
	{
		if (is_null($new_data)) delete_post_meta($post_id,'_wp_tweet_me');
		else update_post_meta($post_id,'_wp_tweet_me',$new_data);
	}
	elseif (!is_null($new_data))
	{
		add_post_meta($post_id,'_wp_tweet_me',$new_data,TRUE);
	}

	return $post_id;
}

function wp_tweet_me_clean(&$arr)
{
	if (is_array($arr))
	{
		foreach ($arr as $i => $v)
		{
			if (is_array($arr[$i])) 
			{
				wp_tweet_me_clean($arr[$i]);

				if (!count($arr[$i])) 
				{
					unset($arr[$i]);
				}
			}
			else 
			{
				if (trim($arr[$i]) == '') 
				{
					unset($arr[$i]);
				}
			}
		}

		if (!count($arr)) 
		{
			$arr = NULL;
		}
	}
}


?>
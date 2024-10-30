<?php
/*
Plugin Name: Cudazi Latest Tweets
Plugin URI: http://plugins.svn.wordpress.org/cudazi-latest-tweets/
Description: A clean, easy way to display a latest tweets widget on your WordPress powered site.
Version: 0.1
Author: Cudazi
Author URI: http://cudazi.com
License: GPLv2

  Copyright 2011 Curt Ziegler (cudazi.com)

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

class Cudazi_Latest_Tweets extends WP_Widget {

	const name = 'Cudazi Latest Tweets';
	const slug = 'cudazi-latest-tweets';



	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/
	 
	function Cudazi_Latest_Tweets() {

		load_plugin_textdomain( 'cudazi', false, plugin_dir_path( __FILE__ ) . '/lang/' );

		$widget_opts = array(
			'classname' => self::slug, 
			'description' => __( 'Creates a custom widget to display latest tweets.', 'cudazi' )
		);	
		
		$this->WP_Widget( self::slug, __( self::name, 'cudazi' ), $widget_opts );
		
    	// Load JavaScript and stylesheets
    	$this->register_scripts_and_styles();
		
	} // end constructor



	/*--------------------------------------------------*/
	/* API Functions
	/*--------------------------------------------------*/

	function widget( $args, $instance ) {
		
		extract( $args, EXTR_SKIP );		
		
		echo $before_widget;				
		
    	$title = apply_filters('widget_title', $instance['title']);
    	$max = $instance['max'];
		$username = $instance['username'];    	
    	$widget_id = self::slug;
    	
    	?>
    	
    	<div id='<?php echo $widget_id; ?>'>	    	
			<?php if ( $title ) { echo sprintf( "<h3>%s</h3>", $title ); } ?>
	    	<div id='<?php echo $widget_id; ?>-tweets'></div>	    	
    	</div>
    	
    	<?php echo $after_widget; ?>
    	
			<script type="text/javascript">
				<?php /* Additional params at http://tweet.seaofclouds.com/ */ ?>
				/* <![CDATA[ */
					jQuery(function($) {
						$("#<?php echo $widget_id; ?>-tweets").tweet({								
							username: "<?php echo $username; ?>",
							count: <?php echo $max; ?>,								
							loading_text: "<?php _e( 'Loading tweets...', 'cudazi' ); ?>",							
							template: "{text}{time}",
							retweets: true						
						});
					});			
				/* ]]> */				
            </script>     		
    	<?php
    	
	} // end widget
	
	
	/*
	 * Processes the widget's options to be saved.
	 */
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		
		// Update the widget with the new values
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['max'] = strip_tags($new_instance['max']);
		$instance['username'] = strip_tags($new_instance['username']);
    	
		return $instance;
		
	} // end widget
	
	
	/*
	 * Generates the administration form for the widget.
	 */
	function form( $instance ) {
	
    	// define default values for your variables
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title' => '',				
				'max' => 3,
				'username' => 'cudazi'
			)
		);
	
		$title = esc_attr($instance['title']);
		$max = esc_attr($instance['max']);
		$username = esc_attr($instance['username']);
		
		?>
            <p>
            	<label for="<?php echo $this->get_field_id('title'); ?>">
            		<?php _e('Title:', 'cudazi'); ?> 
            		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            		<br /><small><?php _e('Leave blank and click save to disable title','cudazi'); ?></small>
            	</label>
            </p>
            
            <p>
	            <label for="<?php echo $this->get_field_id('max'); ?>">
	            	<?php _e('Maximum:', 'cudazi'); ?> 
	            	<input class="widefat" id="<?php echo $this->get_field_id('max'); ?>" name="<?php echo $this->get_field_name('max'); ?>" type="text" value="<?php echo $max; ?>" />
	            	<br /><small><?php _e('Suggested: 1 to 10','cudazi'); ?></small>
	            </label>
            </p>
            
            <p>
            	<label for="<?php echo $this->get_field_id('username'); ?>">
            		<?php _e('Twitter Username:', 'cudazi'); ?> 
            		<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" />
            		<br /><small><?php _e('Enter username without @ symbol.','cudazi'); ?></small>
            	</label>
            </p>
		<?php
		
		
	} // end form
	
	
	
	/*--------------------------------------------------*/
	/* Private Functions
	/*--------------------------------------------------*/
  
	/*
	 * Registers and enqueues stylesheets
	 */
	private function register_scripts_and_styles() {
		if ( is_admin() ) {
			// No admin styles or js for this plugin at this time
		} else { 
			$this->load_file( self::slug . '-script', '/js/widget.js', true );
			$this->load_file( self::slug . '-style', '/css/widget.css' );
		} // end if/else
	} // end register_scripts_and_styles

	/*
 	 * Helper function for registering and enqueueing scripts and styles.
 	 */
	private function load_file( $name, $file_path, $is_script = false ) {
		
		$url = WP_PLUGIN_URL . '/' . self::slug . '/' . $file_path;
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') );
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} // end if
		} // end if
    
	} // end load_file
	
} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("Cudazi_Latest_Tweets");' ) ); 



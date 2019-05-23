<?php

/*
Plugin Name: Cancer Wellness Category Listing for the Mega Menu
Plugin URI: http://www.cancerwellness.com
Description: This plugin was meant to show the categories on the mega menu.
Author: Kevin Davis
Version 1.0
Aurthor URI: http://www.cancerwellness.com
*/


function cw_cats_load_menu_widget() {
    register_widget( 'cw_cats_menu_widget' );
}
add_action( 'widgets_init', 'cw_cats_load_menu_widget' );

// Creating the cw categories widget 
class cw_cats_menu_widget extends WP_Widget {
 
	function __construct() {
		parent::__construct(
		
		// Base ID of your widget
		'cw_cats_menu_widget', 
		
		// Widget name will appear in UI
		__('Cancer Wellness Categories for the Menu', 'cw_cats_widget_domain'), 
		
		// Widget description
		array( 'description' => __( 'The widget for the to show the category in the Mega Menu', 'cw_cats_widget_domain' ), ) 
		);
	}
	
	// Creating cw cateogery widget for front-end sidebar
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];
		
		// This is where you run the code and display the output
		?>
		<div id="container">
				<div id="content" role="main">

				<?php
				// get all the categories from the database
				$categories = get_categories();
				//$cat_ids = array(49,66,50,58,52,56,51,57,60,54,55,53);
				// A simple foreach loop, to keep things in your required order
				
				// loop through the categries
				foreach ($categories as $cat) {
					// setup the cateogory ID
					$cat_id = $cat->term_id;
					//Check if Category is "uncategorized" and if so do nothing
					if($cat->name != 'Uncategorized' && $cat->name != 'Hero' && $cat->name !='Cancer Type' &&  $cat->name != 'Resources' && $cat->category_parent == 0 ) :
						$catURL = get_category_link($cat_id);
						// Make a header for the cateogry
						echo "<div class='side-cat' style='float:left!important;'><button>".$cat->name."</button><ul class='list'>";
						// create a custom wordpress query
						query_posts("cat=$cat_id&posts_per_page=2");
						// start the wordpress loop!
						if (have_posts()) : while (have_posts()) : the_post(); ?>

							<?php // create our link now that the post is setup ?>
							<li><a href="<?php the_permalink();?>"  class='limited-text' title="<?php the_title(); ?>"><?php the_title(); ?></a></li>

						<?php endwhile; wp_reset_query(); endif; // done our wordpress loop. Will start again for each category ?>
						<li><a href="<?php echo $catURL; ?>" title="Read All <?php echo $cat->name; ?> Stories">More. . .</a></li>
					
						</ul></div>
					<?php endif;
					
				} // done the foreach statement ?>

				</div><!-- #content -->
			</div>
			<?php
		echo $args['after_widget'];
	}
			
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
		$title = $instance[ 'title' ];
		}
		else {
		$title = __( 'New title', 'cw_cats_widget_domain' );
		}
		// Widget admin form
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}
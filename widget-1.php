<?php

function cw_events_menu_listing_widget() {
	register_widget('cw_events_widget');
}

add_action( 'widgets_init', 'cw_events_menu_listing_widget' );

class cw_events_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
  
        // Base ID of your widget
		'cw_events_widget', 

		// Widget name will appear in UI
		__('Cancer Wellness Event Listing', 'cw_events_widget_domain'), 
		
		// Widget description
		array( 'description' => __( 'The widget for the to show the current events in the Mega Menu', 'cw_events_widget_domain' ), ) 

	  );
   }


   public function widget($args, $instance) {
   	  $title = apply_filters('widget_title', $instance['title']);

   	  echo $args['before_widget'];
   	  if (! empty($title) )
   	  echo $args['before_title'] . $tite . $args['after_title'];

   	  ?>

   	  <div id="container" style="width: 75%;margin: 0px auto;">
         <div id="content" role="main">
             <?php

                 echo "<a href='/events/' id='view_all'>VIEW ALL UPCOMING EVENTS</a>&nbsp;&nbsp;&nbsp;<a href='/past-events/' id='view_all'>VIEW ALL PAST EVENTS</a>";

                 $EM_Events = EM_Events::get(array('scope'=>'future', 'limit'=>16, ));


 
                echo "<ul id='menu-items'>";               
                  foreach ($EM_Events as $EM_Event) { ?>
                  	<li id='cat-listing'><div ><a class='cat-menu-links' href='<?php echo $EM_Event->output('#_EVENTURL'); ?>' style='text-overflow: ellipsis!important; white-space: nowrap!important; max-width: 80%; overflow: hidden;'> 
                       <?php echo $EM_Event->output('#_EVENTNAME'); ?> 
                   </a></div></li>
                <?php
                  }
                echo ("</ul>");
             ?>

         </div>
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
		$title = __( 'New title', 'cw_events_widget_domain' );
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
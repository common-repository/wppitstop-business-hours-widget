<?php
/**
 * Plugin Name: WPPitstop.nl Business Hours Widget Plugin
 * Plugin URI: https://wppitstop.nl
 * Description: Adds a widget that displays a text between certain times on certain days.
 * Stable tag: 2.0
 * Version: 2.0
 * Author: Peter van der Laan - WPPitstop.nl
 * Author URI: https://www.wppitstop.nl
 * Text Domain: wppitstop-business-hours-widget
 */
 
function myplugin_load_textdomain() {
  load_plugin_textdomain( 'wppitstop-business-hours-widget', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'init', 'myplugin_load_textdomain' );

 
class WPPitstop_Tijdsafhankelijk_Widget extends WP_Widget {

  // Set up the widget name and description.
  public function __construct() {
    $widget_options = array( 'classname' => 'WPPitstop_Tijdsafhankelijk_Widget', 'description' => __('Adds a widget that displays a text between certain times.','wppitstop-business-hours-widget') );
    parent::__construct( 'WPPitstop_Tijdsafhankelijk_Widget', 'WPPitstop Business Hours Widget', $widget_options );
  }


  // Create the widget output.
  public function widget( $args, $instance ) {
	extract($args); 
    $title = apply_filters( 'widget_title', $instance[ 'title' ] );
	$tijdzone = ! empty(apply_filters( 'widget_title', $instance[ 'tijdzone' ] )) ? $instance['tijdzone'] : 'Europe/Amsterdam'; ;
	$weekday = apply_filters('widget_text', $instance['weekday'], $instance);
	$text = apply_filters('widget_text', $instance['text'], $instance);
	$replacetext = apply_filters('widget_text', $instance['replacetext'], $instance);
	$dagArray = array($instance[$this->get_field_id('weekdaycb0')],$instance[$this->get_field_id('weekdaycb1')],$instance[$this->get_field_id('weekdaycb2')],$instance[$this->get_field_id('weekdaycb3')],$instance[$this->get_field_id('weekdaycb4')],$instance[$this->get_field_id('weekdaycb5')],$instance[$this->get_field_id('weekdaycb6')]);
	
	
	date_default_timezone_set($tijdzone);
	$huidigetijd = new DateTime("NOW");
	$dag = $huidigetijd ->format('N');
	$huidigetijd->format('H:i:s');
	$startsplit = explode(":",$instance['starttijd']);
	$eindsplit = explode(":",$instance['eindtijd']);
	//$start = DateTime::createFromFormat('!H:i', $instance['starttijd']);
	$start = new DateTime("NOW");
	$start->format('H:i:s');
	$start->setTime($startsplit[0],$startsplit[1]);
    //$eind = DateTime::createFromFormat('!H:i', $instance['eindtijd']);
	$eind = new DateTime("NOW");
	$eind->format('H:i:s');
	$eind->setTime($eindsplit[0],$eindsplit[1]);
	
	echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title']; ?>
		<div class="wppitstop_textwidget">
			<?php 
				if ($start > $eind) $eind->modify('+1 day'); 
				if($dagArray[$dag] == 1 && ($start <= $huidigetijd && $huidigetijd <= $eind) || ($start <= $huidigetijd->modify('+1 day') && $huidigetijd <= $eind)){ ?>
				<?php echo $text;?>
			<?php } else { ?>
				<?php echo $replacetext; 
				}
			?>
		</div> 
    <?php 
			
	echo $args['after_widget'];
  }
  
  // Create the admin area widget settings form.
  public function form($instance) {
    $instance = wp_parse_args( (array) $instance, array( 'text' => '' ) );
	$title = ! empty( $instance['title'] ) ? $instance['title'] : ''; 
	$text = format_to_edit($instance['text']);
	$replacetext = format_to_edit($instance['replacetext']);
	$tijdzone = format_to_edit($instance['tijdzone']);
	$starttijd = format_to_edit($instance['starttijd']);
	$eindtijd = format_to_edit($instance['eindtijd']);
	$weekdaycb0 = $instance[$this->get_field_id('weekdaycb0')];
	$weekdaycb1 = $instance[$this->get_field_id('weekdaycb1')];
	$weekdaycb2 = $instance[$this->get_field_id('weekdaycb2')];
	$weekdaycb3 = $instance[$this->get_field_id('weekdaycb3')];
	$weekdaycb4 = $instance[$this->get_field_id('weekdaycb4')];
	$weekdaycb5 = $instance[$this->get_field_id('weekdaycb5')];
	$weekdaycb6 = $instance[$this->get_field_id('weekdaycb6')];
    ?>

	<p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','wppitstop-business-hours-widget') ?></label>
      <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" /><br>
	  <label for="<?php echo $this->get_field_id( 'timezone' ); ?>"><?php _e('Timezone:','wppitstop-business-hours-widget') ?></label>
	  <?php
		$OptionsArray = timezone_identifiers_list();
        $select= '<select id="'.$this->get_field_id( 'tijdzone' ).'" name="'.$this->get_field_name( 'tijdzone' ).'" >';
        while (list ($key, $row) = each ($OptionsArray) ){
            $select .='<option value="'.$row.'"';
            $select .= ($row == $tijdzone ? ' selected' : '');
            $select .= '>'.$row.'</option>';
        }  
        $select.='</select>';
			echo $select ?>	
	  
	  <label for="<?php echo $this->get_field_id( 'starttijd' ); ?>"><?php _e('Begin display on:','wppitstop-business-hours-widget')?></label>
	  <input type="time" id="<?php echo $this->get_field_id( 'starttijd' ); ?>" name="<?php echo $this->get_field_name( 'starttijd' ); ?>" value="<?php echo esc_attr( $starttijd ); ?>" /><br>
	  <label for="<?php echo $this->get_field_id( 'eindtijd' ); ?>"><?php _e('End display on:','wppitstop-business-hours-widget')?></label>
	  <input type="time" id="<?php echo $this->get_field_id( 'eindtijd' ); ?>" name="<?php echo $this->get_field_name( 'eindtijd' ); ?>" value="<?php echo esc_attr( $eindtijd ); ?>" /><br>
	  <p>
		<legend><?php _e('Display on:','wppitstop-business-hours-widget')?></legend>      
		<input type="checkbox" id="<?php echo $this->get_field_id('weekdaycb0')?>" name="<?php echo $this->get_field_id('weekdaycb0')?>" value="yes" <?php checked( $instance[$this->get_field_id('weekdaycb0')],1, true ); ?>><?php _e('Sunday','wppitstop-business-hours-widget')?><br>      
		<input type="checkbox" id="<?php echo $this->get_field_id('weekdaycb1')?>" name="<?php echo $this->get_field_id('weekdaycb1')?>" value="yes" <?php checked( $instance[$this->get_field_id('weekdaycb1')],1, true );?>><?php _e('Monday','wppitstop-business-hours-widget')?><br>      
		<input type="checkbox" id="<?php echo $this->get_field_id('weekdaycb2')?>" name="<?php echo $this->get_field_id('weekdaycb2')?>" value="yes" <?php checked( $instance[$this->get_field_id('weekdaycb2')],1, true );?>><?php _e('Tuesday','wppitstop-business-hours-widget')?><br>      
		<input type="checkbox" id="<?php echo $this->get_field_id('weekdaycb3')?>" name="<?php echo $this->get_field_id('weekdaycb3')?>" value="yes" <?php checked( $instance[$this->get_field_id('weekdaycb3')],1, true );?>><?php _e('Wednesday','wppitstop-business-hours-widget')?><br>      
		<input type="checkbox" id="<?php echo $this->get_field_id('weekdaycb4')?>" name="<?php echo $this->get_field_id('weekdaycb4')?>" value="yes" <?php checked( $instance[$this->get_field_id('weekdaycb4')],1, true ); ?>><?php _e('Thursday','wppitstop-business-hours-widget')?><br>      
		<input type="checkbox" id="<?php echo $this->get_field_id('weekdaycb5')?>" name="<?php echo $this->get_field_id('weekdaycb5')?>" value="yes" <?php checked( $instance[$this->get_field_id('weekdaycb5')],1, true );?>><?php _e('Friday','wppitstop-business-hours-widget')?><br>      
		<input type="checkbox" id="<?php echo $this->get_field_id('weekdaycb6')?>" name="<?php echo $this->get_field_id('weekdaycb6')?>" value="yes" <?php checked( $instance[$this->get_field_id('weekdaycb6')],1, true ); ?>><?php _e('Saturday','wppitstop-business-hours-widget')?><br>      
		</p>
	  <label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e('HTML or plain text to show when enabled:','wppitstop-business-hours-widget') ?></label>
	  <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
	  <label for="<?php echo $this->get_field_id( 'replacetext' ); ?>"><?php _e('HTML or plain text to show when disabled:','wppitstop-business-hours-widget') ?></label>
	  <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('replacetext'); ?>" name="<?php echo $this->get_field_name('replacetext'); ?>"><?php echo $replacetext; ?></textarea>
    </p><?php
  }


  // Apply settings to the widget instance.
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
	$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
	$instance[ 'tijdzone' ] = $new_instance[ 'tijdzone' ];
	$instance[ 'starttijd' ] = strip_tags( $new_instance[ 'starttijd' ] );
	$instance[ 'eindtijd' ] = strip_tags( $new_instance[ 'eindtijd' ] );
	$instance[ $this->get_field_id('weekdaycb0') ] = isset( $_POST[$this->get_field_id('weekdaycb0')] ) ? 1 : 0;
	$instance[ $this->get_field_id('weekdaycb1') ] = isset( $_POST[$this->get_field_id('weekdaycb1')] ) ? 1 : 0;
	$instance[ $this->get_field_id('weekdaycb2') ] = isset( $_POST[$this->get_field_id('weekdaycb2')] ) ? 1 : 0;
	$instance[ $this->get_field_id('weekdaycb3') ] = isset( $_POST[$this->get_field_id('weekdaycb3')] ) ? 1 : 0;
	$instance[ $this->get_field_id('weekdaycb4') ] = isset( $_POST[$this->get_field_id('weekdaycb4')] ) ? 1 : 0;
	$instance[ $this->get_field_id('weekdaycb5') ] = isset( $_POST[$this->get_field_id('weekdaycb5')] ) ? 1 : 0;
	$instance[ $this->get_field_id('weekdaycb6') ] = isset( $_POST[$this->get_field_id('weekdaycb6')] ) ? 1 : 0;
	
	if ( current_user_can('unfiltered_html') ){
            $instance['text'] =  $new_instance['text'];
	$instance['replacetext'] =  $new_instance['replacetext'];
	
	} else {
            $instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
			$instance['replacetext'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['replacetext']) ) ); 
    
  }
return $instance;
}
}

// Register the widget.
function WPPitstop_register_Tijdsafhankelijk_Widget() { 
  register_widget( 'WPPitstop_Tijdsafhankelijk_Widget' );
}
add_action( 'widgets_init', 'WPPitstop_register_Tijdsafhankelijk_Widget' );

?>
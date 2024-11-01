<?php

class YKS_WPMS_Widget extends WP_Widget {

	/**
	 * ウィジェット名などを設定
	 */
	public function __construct() {
		// widget actual processes
		parent::__construct(
			'yks_wpms_form',
			'WP Meta Search Form',
			array( 'description' => __('Search Postmeta, Categories, Tags' ))
			);
	}

	/**
	 * ウィジェットの内容を出力
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		// outputs the content of the widget

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		$opt = get_option('yks-form-settings');
		if($opt){
		?>
		<form class="yks_form" method="GET" action="<?php echo get_bloginfo('url'); ?>">
			<input type="hidden" name="s" value="" />
			<input type="hidden" name="yks" value="1" />
				<dl>
				    <?php foreach ($opt as $mod) {
				    if(!empty($mod['mod_id'])){

				    	switch ($mod['type']) {
				    		case 'keyword':
				    			yks_wpms_form_s($mod);
				    			break;
				    		case 'post_type':
				    			yks_wpms_form_pt($mod);
				    			break;
				    		case 'meta_query':
				    			yks_wpms_form_mq($mod);
				    			break;
				    		case 'tax_query':
				    			yks_wpms_form_tq($mod);
				    			break;
				    		case 'category':
				    			yks_wpms_form_tq($mod);
				    			break;
				    		case 'post_tag':
				    			yks_wpms_form_tq($mod);
				    			break;
				    		case 'range':
				    			yks_wpms_form_rg($mod);
				    			break;
				    		case 'order':
				    			yks_wpms_form_od($mod);
				    			break;
				    		default:
				    			break;
				    	}		
					 }
				   }

				   echo apply_filters( 'yks_wpms_form_add', '');
				   ?>
				    
				    <!-- <input type="submit" value="<?php _e('Search'); ?>" /> -->
				    <button class="submit_bt"><?php _e('Search'); ?></button>
				</dl>

		</form>

		<?php } ?>

		<?php echo $args['after_widget'];?>

		<?php
	}

	/**
	 * 管理用のオプションのフォームを出力
	 *
	 * @param array $instance ウィジェットオプション
	 */
	public function form( $instance ) {
		// 管理用のオプションのフォームを出力
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( '新しいタイトル', 'text_domain' );
?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'タイトル:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	 * ウィジェットオプションの保存処理
	 *
	 * @param array $new_instance 新しいオプション
	 * @param array $old_instance 以前のオプション
	 */
	public function update( $new_instance, $old_instance ) {
		// ウィジェットオプションの保存処理
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}


}



	/*
	output keyword form
	*/
	function yks_wpms_form_s($mod){
		$current = (isset($_GET['mod_s-'.$mod['mod_id']]))?sanitize_text_field($_GET['mod_s-'.$mod['mod_id']]):'';
								 ?>
								    <dt>
								        <label>
								            <?php esc_html($mod['label']); ?>
								        </label>
								    </dt>
								    <dd>
										<input type="text" class="" name="mod_s-<?php echo esc_attr($mod['mod_id']); ?>" value="<?php echo $current; ?>" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder'); ?>"/>
								    </dd>
	<?php
	}



	/*
	output post typeinput
	*/
	function yks_wpms_form_pt($mod){
		$current = (isset($_GET['mod_pt-'.$mod['mod_id']]))?sanitize_text_field($_GET['mod_pt-'.$mod['mod_id']]):'';
		?>

		<?php if($mod['hidden'] == 1){ ?>
			<input type="hidden" name="mod_pt-<?php echo esc_attr($mod['mod_id']); ?>" value="<?php echo esc_attr($mod['post_type']) ?>" />
		<?php }else{ ;?>
								    <dt>
								        <label>
								            <?php echo esc_html($mod['label']) ?>
								        </label>
								    </dt>
								    <dd>

									    <?php $pts = get_post_types(array('public'=>true),'objects'); ?>

									    <?php if((empty($mod['input'])) || ($mod['input'] == 'radio')){ ?>

									    	<?php foreach($pts as $ptn) { ?>
									    		<label><input type="radio" name="mod_pt-<?php echo esc_attr($mod['mod_id']); ?>" value="<?php echo esc_attr($ptn->name); ?>" <?php checked($ptn->name , $current) ?>/><?php echo esc_html($ptn->label); ?></label>
									    	<?php } ?>

									    <?php }else if($mod['input'] == 'select'){ ?>

									    	<select name="mod_pt-<?php echo esc_attr($mod['mod_id']); ?>">
									    		<option value=""><?php esc_html_e( 'Select &hellip;'); ?></option>
									        <?php foreach($pts as $ptn) { ?>
									        	<option value="<?php echo esc_attr($ptn->name); ?>" <?php selected($ptn->name , $current) ?>><?php echo esc_html($ptn->label); ?></option>
									        <?php } ?>
								        	</select>

									    <?php } ?>

								    <?php } ?>

									    
	<?php
	}

	/*
	output meta query form
	*/
	function yks_wpms_form_mq($mod){
		$vs = yks_wpms_get_meta_values($mod['meta_key']);
		$current = (!empty($_GET['mod_mv-'.$mod['mod_id']]))?(array)$_GET['mod_mv-'.$mod['mod_id']]:array('0'=>'');
		if(isset($mod['values']) && (!empty($mod['values']))){
			$values_a = explode("\n",trim($mod['values']));
			$values_a = str_replace(array("\r", "\n"), '', $values_a);
			$vs = $values_a;
		}

								 ?>
								    <dt>
								        <label>
								            <?php echo esc_html($mod['label']) ?>
								        </label>
								    </dt>
								    <dd>
								        <input type="hidden" name="mod_mk-<?php echo esc_attr($mod['mod_id']); ?>" value="<?php echo esc_attr($mod['meta_key']) ?>" />
								        
								    <?php if((empty($mod['input'])) || ($mod['input'] == 'radio')){

								    	if(isset($mod['all']) && $mod['all'] == 1){?>
											<label><input type="radio" name="mod_mv-<?php echo $mod['mod_id']; ?>" value="all" <?php echo ($current[0] == 'all')?'checked':''; ?>><?php //_e('All'); ?>こだわらない</label>
								    	<?php }
								     ?>


								        <?php foreach($vs as $v){ ?>
								        	<label><input type="radio" name="mod_mv-<?php echo $mod['mod_id']; ?>" value="<?php echo $v[0]; ?>" <?php echo (in_array($v[0], (array)$current))?'checked':''; ?>><?php echo esc_html($v[0]); ?></label>
								        <?php	} ?>

								    <?php }else if($mod['input'] == 'select'){ ?>
								    	<select name="mod_mv-<?php echo esc_attr($mod['mod_id']); ?>">
								    		<option value=""><?php _e( 'Select &hellip;'); ?></option>
								    	<?php foreach($vs as $v){ ?>
								    		<option value="<?php echo $v[0]; ?>" <?php echo (in_array($v[0], (array)$current))?'selected':''; ?>><?php echo esc_html($v[0]); ?></option>
								        <?php	} ?>
								        </select>

								    <?php }else if($mod['input'] == 'checkbox'){ ?>
								    	<?php foreach($vs as $v){ ?>
								    		<label><input type="checkbox" name="mod_mv-<?php echo esc_attr($mod['mod_id']); ?>[]" value="<?php echo esc_attr($v[0]); ?>"<?php echo (in_array($v[0], (array)$current))?'checked':''; ?> /><?php echo esc_html($v[0]); ?></label>
								        <?php } ?>
								    <?php } ?>

								    </dd>
	<?php
	}


	

function register_yks_wpms_widget() {
    register_widget( 'YKS_WPMS_Widget' );
}
add_action( 'widgets_init', 'register_yks_wpms_widget' );

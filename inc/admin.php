<?php


// add scripts and style
add_action( 'admin_enqueue_scripts', 'yks_wpms_custom_enqueue' );

function yks_wpms_custom_enqueue($hook_suffix) {
	if($hook_suffix == 'tools_page_yks_settings'){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-droppable');
		wp_enqueue_script('yks_js', YKS_WPMS_PLUGIN_URL.'/js/yks.js' , array('jquery'));
		wp_enqueue_style('yks_css', YKS_WPMS_PLUGIN_URL.'/css/yks.css');
	}
}


// create custom plugin settings menu
add_action('admin_menu',  'yks_wpms_create_menu');
 
//メニューを作ってくれる
function yks_wpms_create_menu() {
 
        add_submenu_page('tools.php',  __('WP Meta Search Settings' , YKS_WPMS_TD), __('WP Meta Search' , YKS_WPMS_TD), 'administrator', 'yks_settings', 'yks_wpms_settings_page');
 
	add_action( 'admin_init', 'yks_wpms_register_mysettings' );
}
 
//設定そのものをしてくれる部分
function yks_wpms_register_mysettings() {
	//register our settings
	register_setting(  'yks-settings-group', 'yks_form_id' );
	$options = array();
	$mod_ids_b = (isset($_POST['mod_id']))?(array)$_POST['mod_id']:array();
	$mod_ids = array();
	foreach ($mod_ids_b as $mod_id) {//sanitize array 'mod_id'
		$mod_ids[] = sanitize_text_field($mod_id);
	}
		if(!empty($_POST['yks_form_id'])){
			if(count($mod_ids)>0){
				foreach ($mod_ids as $mod_id) {
					$type =  (isset($_POST['mod_type-'.$mod_id]))?sanitize_text_field($_POST['mod_type-'.$mod_id]):'';
					$label =  (isset($_POST['mod_label-'.$mod_id]))?sanitize_text_field($_POST['mod_label-'.$mod_id]):'';
					$input =  (isset($_POST['mod_input-'.$mod_id]))?sanitize_text_field($_POST['mod_input-'.$mod_id]):'';
					$hidden =  (isset($_POST['mod_hid-'.$mod_id]))?sanitize_text_field($_POST['mod_hid-'.$mod_id]):'';
					$post_type =  (isset($_POST['mod_pt-'.$mod_id]))?sanitize_text_field($_POST['mod_pt-'.$mod_id]):'';
					$meta_key =  (isset($_POST['mod_mk-'.$mod_id]))?sanitize_text_field($_POST['mod_mk-'.$mod_id]):'';
					$taxonomy =  (isset($_POST['mod_tax-'.$mod_id]))?sanitize_text_field($_POST['mod_tax-'.$mod_id]):'';
					$range =  (isset($_POST['mod_range-'.$mod_id]))?sanitize_textarea_field($_POST['mod_range-'.$mod_id]):'';
					$all =  (isset($_POST['mod_all-'.$mod_id]))?sanitize_textarea_field($_POST['mod_all-'.$mod_id]):'';
					$values =  (isset($_POST['mod_values-'.$mod_id]))?sanitize_textarea_field($_POST['mod_values-'.$mod_id]):'';


					$options[$mod_id] = array(
							'mod_id' => $mod_id,
							'label' => $label,
							'type' => $type,
							'input' => $input,
							'hidden' => $hidden,
							'post_type' => $post_type,
							'meta_key' => $meta_key,
							'taxonomy' => $taxonomy,
							'range' => $range,
							'all' => $all,
							'values' => $values
						);
				}
				update_option( 'yks-form-settings', $options );

			}else{
				delete_option('yks-form-settings');
			}
		}

}
 
//設定ページの内容
function yks_wpms_settings_page() {
	global $yks_form_settings;
	$mods = $yks_form_settings;
?>
<div class="wrap">
    <h2><?php _e('WP Meta Search' , YKS_WPMS_TD); ?></h2>
<?php
     if ( isset( $_GET['settings-updated'] ) ) {
 // add settings saved message with the class of "updated"
 add_settings_error( 'yks_messages', 'yks_message', __( 'Settings Saved', YKS_WPMS_TD ), 'updated' );
 }
 // show error/update messages
 settings_errors( 'yks_messages' );

 ?>

    <form id="yks_settings" method="post" action="options.php">
 
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <!-- main content -->
          
          	<div class="modules_container">
                
                    <?php settings_fields(  'yks-settings-group' ); ?>
                    <?php do_settings_sections(  'yks-settings-group' ); ?>
                    <input type="hidden" name="yks_form_id" value="<?php echo uniqid(); ?>" />
                    <div id="mod_container" class="sortable">
                        <?php

                        if(!empty($mods)){
                         foreach($mods as $mod){
	                        if(!empty($mod['mod_id'])){

	                        	switch ($mod['type']) {
	                        		case 'keyword':
						    			yks_wpms_admin_mod_s($mod);
						    			break;
						    		case 'post_type':
						    			yks_wpms_admin_mod_pt($mod);
						    			break;
						    		case 'meta_query':
						    			yks_wpms_admin_mod_mq($mod);
						    			break;
						    		case 'tax_query':
						    			yks_wpms_admin_mod_tq($mod);
						    			break;
						    		case 'category':
						    			yks_wpms_admin_mod_c($mod);
						    			break;
						    		case 'post_tag':
						    			yks_wpms_admin_mod_t($mod);
						    			break;
						    		case 'range':
						    			yks_wpms_admin_mod_rg($mod);
						    			break;
						    		case 'order':
						    			yks_wpms_admin_mod_od($mod);
						    			break;	
						    		default:
						    			break;
				    			}	
					    	}
					    }
    		?>
                        <?php } ?>
                    </div>

                    <a class="add_mq button-secondary" data-mod="mq" /><?php esc_attr_e( 'Add new meta-query' , YKS_WPMS_TD); ?></a>
                    <a class="add_pt button-secondary" data-mod="pt" /><?php esc_attr_e( 'Add new post-type' , YKS_WPMS_TD); ?></a>
                    <a class="add_c button-secondary" data-mod="c" /><?php esc_attr_e( 'Add new category' , YKS_WPMS_TD); ?></a>
                    <a class="add_t button-secondary" data-mod="t" /><?php esc_attr_e( 'Add new tag' , YKS_WPMS_TD); ?></a>
                    <a class="add_s button-secondary" data-mod="s" /><?php esc_attr_e( 'Add new keyword' , YKS_WPMS_TD); ?></a>
                    <?php do_action( 'yks_admin_add_mod_btn' ); ?>

            </div>
                    <?php //submit_button(); ?>
                    <p class="submit"><a id="submit_option" class="button button-primary"><?php _e('Save' , YKS_WPMS_TD); ?></a></p>
        </div>
    </div>
    </form>

<!-- donate -->
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
  <input type="hidden" name="cmd" value="_donations">
  <input type="hidden" name="business" value="MVVSVT5929CHN">
  <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif"
    border="0" name="submit"
    alt="PayPal - The safer, easier way to pay online!">
</form>

    <div id="yks_modules">
    	<?php yks_wpms_admin_mod_s(); ?>
    	<?php yks_wpms_admin_mod_pt(); ?>
        <?php yks_wpms_admin_mod_mq(); ?>
        <?php yks_wpms_admin_mod_c(); ?>
        <?php yks_wpms_admin_mod_t(); ?>
        <?php do_action( 'yks_admin_add_mod' ); ?>
    </div>
    
</div>

<?php }


function yks_wpms_admin_mod_s($mod = null){
	global $yks_wpms_vars;

	if($mod === null){
			$mod = array(
				'mod_id'=>'',
				'label'=>'Keyword',
				'hidden'=>'',
				'input'=>'',
				'keyword'=>''
			);
		}
	?>
	<div class="yks_mod yks_mod_s postbox">
		<button type="button" class="handlediv button-link" aria-expanded="false"><span class="screen-reader-text">close keyword</span><span class="toggle-indicator" aria-hidden="true"><span class="dashicons dashicons-arrow-down"></span></span></button>

		<h2 class="hndle"><span><?php esc_attr_e( 'Keyword', YKS_WPMS_TD ); ?></span></h2>

		<div class="inside">
		    <table class="form-table">
	    	<input type="hidden" class="mod_id all-options" name="mod_id[]" value="<?php echo $mod['mod_id']; ?>" />
	    	<input type="hidden" class="mod_type all-options" name="mod_type-<?php echo $mod['mod_id']; ?>" value="keyword" />
	    	<tr valign="top">
	        <th scope="row"><?php _e('label' , YKS_WPMS_TD) ?></th>
	        <td><input type="text" class="mod_label all-options" name="mod_label-<?php echo $mod['mod_id']; ?>" value="<?php echo $mod['label'] ?>" /></td>
	        </tr>
	    	</table>
	    	<a class="delete"><?php _e('delete' , YKS_WPMS_TD) ?></a>
    	</div>
	</div>
	<?php
}


function yks_wpms_admin_mod_pt($mod = null){
	global $yks_wpms_vars;
	if(!$mod){
			$mod = array(
				'mod_id'=>'',
				'hidden'=>'',
				'label'=>'',
				'input'=>'',
				'post_type'=>''
			);
		}
	?>
	<div class="yks_mod yks_mod_pt postbox">
		<button type="button" class="handlediv button-link" aria-expanded="false"><span class="screen-reader-text">close post type</span><span class="toggle-indicator" aria-hidden="true"><span class="dashicons dashicons-arrow-down"></span></span></button>
		<h2 class="hndle"><span><?php esc_attr_e( 'Post Type', YKS_WPMS_TD ); ?></span></h2>

		<div class="inside">
		    <table class="form-table">
	    	<input type="hidden" class="mod_id all-options" name="mod_id[]" value="<?php echo $mod['mod_id']; ?>" />
	    	<input type="hidden" class="mod_type all-options" name="mod_type-<?php echo $mod['mod_id']; ?>" value="post_type" />
	    	<tr valign="top">
	        <th scope="row"><?php _e('label' , YKS_WPMS_TD) ?></th>
	        <td><input type="text" class="mod_label all-options" name="mod_label-<?php echo $mod['mod_id']; ?>" value="<?php echo $mod['label'] ?>" /></td>
	        </tr>
	        <tr valign="top">
	        <th scope="row"></th>
	        <td><label class="mod_pt_lb"><input type="checkbox" class="mod_hid" name="mod_hid-<?php echo $mod['mod_id']; ?>" value="1" <?php checked($mod['hidden'] , 1); ?>/><?php _e('hidden' , YKS_WPMS_TD); ?></label></td>
	        </tr>
	        <tr valign="top" class="toggle_n" style="<?php if($mod['hidden'] != 1)echo 'display:none;'; ?>">
	        <th scope="row"><?php _e('post type (fixed value)' , YKS_WPMS_TD);?></th>
	        <td>
	        <?php $pts = get_post_types(array('public'=>true),'objects'); ?>
	        <select class="mod_pt" name="mod_pt-<?php echo $mod['mod_id']; ?>">
	        	<option value=""><?php _e('select&hellip;' , YKS_WPMS_TD);?></option>
	        <?php $current = ($mod['post_type'])?$mod['post_type']:''; ?>
	        <?php foreach($pts as $ptn) { ?>
	        	<option value="<?php echo $ptn->name; ?>" <?php selected($current , $ptn->name) ?>><?php echo $ptn->label; ?></option>
	        <?php } ?>
	        </select>
	        </td>
	        </tr>
	        <tr valign="top" class="toggle" style="<?php if($mod['hidden'] == 1)echo 'display:none;'; ?>">
	        <th scope="row"><?php _e('input type' , YKS_WPMS_TD);?></th>
	        <td>
	        <select class="mod_input all-options" class="mod_input" name="mod_input-<?php echo $mod['mod_id']; ?>">
	        <?php foreach($yks_wpms_vars['input_type_single'] as $it => $iv){ ?>
	        	<option value="<?php echo $it; ?>" <?php selected($mod['input'] , $it ); ?>><?php echo $iv['name'] ?></option>
	        <?php } ?>
	        </select>
	        </td>
	        </tr>
	    	</table>
	    	<a class="delete"><?php _e('delete' , YKS_WPMS_TD) ?></a>
    	</div>
	</div>
	<?php
}


function yks_wpms_admin_mod_mq($mod = null){
	global $yks_wpms_vars;

	if(!$mod){
			$mod = array(
				'mod_id'=>'',
				'meta_key'=>'',
				'meta_value'=>'',
				'label'=>'',
				'input'=>'',
				'all' => ''
			);
		}
	?>
	<div class="yks_mod yks_mod_mq postbox">
		<button type="button" class="handlediv button-link" aria-expanded="false"><span class="screen-reader-text">close meta query</span><span class="toggle-indicator" aria-hidden="true"><span class="dashicons dashicons-arrow-down"></span></span></button>

		<h2 class="hndle"><span><?php esc_attr_e( 'Meta Query', YKS_WPMS_TD ); ?></span></h2>

		<div class="inside">
		    <table class="form-table">
	    	<input type="hidden" class="mod_id all-options" name="mod_id[]" value="<?php echo $mod['mod_id']; ?>" />
	    	<input type="hidden" class="mod_type all-options" name="mod_type-<?php echo $mod['mod_id']; ?>" value="meta_query" />
	        <tr valign="top">
	        <th scope="row">meta-key</th>
	        <td><input type="text" class="mod_mk all-options" name="mod_mk-<?php echo $mod['mod_id']; ?>" value="<?php echo $mod['meta_key']; ?>" /></td>
	        </tr>
	        <tr valign="top">
	        <th scope="row"><?php _e('label' , YKS_WPMS_TD) ?></th>
	        <td><input type="text" class="mod_label all-options" name="mod_label-<?php echo $mod['mod_id']; ?>" value="<?php echo $mod['label'] ?>" /></td>
	        </tr>
	        <tr valign="top">
	        <th scope="row"><?php _e('input type' , YKS_WPMS_TD); ?></th>
	        <td>
	        <select class="mod_input all-options" name="mod_input-<?php echo $mod['mod_id']; ?>">
	        <?php foreach($yks_wpms_vars['input_type'] as $it => $iv){ ?>
	        	<option value="<?php echo $it; ?>" <?php selected($mod['input'] , $it ); ?>><?php echo $iv['name'] ?></option>
	        <?php } ?>
	        </select>
	        </td>
	        </tr>
	        <tr valign="top">
	        <th scope="row"><?php _e('add all for radio' , YKS_WPMS_TD) ?></th>
	        <td>
	        <input type="checkbox" class="mod_all" name="mod_all-<?php echo $mod['mod_id']; ?>" value="1" <?php if($mod['all'] == 1)echo 'checked'; ?>/></td>
	        </tr>
	        <tr valign="top">
	        	<th scope="row"><?php _e('values' , YKS_WPMS_TD) ?></th>
	        	<td><textarea class="mod_values" name="mod_values-<?php echo $mod['mod_id']; ?>" rows="5"><?php if(!empty($mod['values']))echo $mod['values']; ?></textarea></td>
	        	</tr>
	    	</table>
	    	<a class="delete"><?php _e('delete' , YKS_WPMS_TD) ?></a>
    	</div>
	</div>
	<?php
}


function yks_wpms_admin_mod_c($mod = null){
	global $yks_wpms_vars;

	if(!$mod){
			$mod = array(
				'mod_id'=>'',
				'taxonomy'=>'category',
				'label'=>__('Category' , YKS_WPMS_TD),
				'input'=>'',
				'all' => ''
			);
		}
	?>
	<div class="yks_mod yks_mod_c postbox">
		<button type="button" class="handlediv button-link" aria-expanded="false"><span class="screen-reader-text">close category</span><span class="toggle-indicator" aria-hidden="true"><span class="dashicons dashicons-arrow-down"></span></span></button>

		<h2 class="hndle"><span><?php esc_attr_e( 'Category', YKS_WPMS_TD ); ?></span></h2>

		<div class="inside">
		    <table class="form-table">
	    	<input type="hidden" class="mod_id all-options" name="mod_id[]" value="<?php echo $mod['mod_id']; ?>" />
	    	<input type="hidden" class="mod_type all-options" name="mod_type-<?php echo $mod['mod_id']; ?>" value="category" />
	    	<input type="hidden" class="mod_tax all-options" name="mod_tax-<?php echo $mod['mod_id']; ?>" value="category" />
	        <tr valign="top">
	        <th scope="row"><?php _e('label' , YKS_WPMS_TD) ?></th>
	        <td><input type="text" class="mod_label all-options" name="mod_label-<?php echo $mod['mod_id']; ?>" value="<?php echo $mod['label'] ?>" /></td>
	        </tr>
	        <tr valign="top">
	        <th scope="row"><?php _e('input type' , YKS_WPMS_TD); ?></th>
	        <td>
	        <select class="mod_input all-options" name="mod_input-<?php echo $mod['mod_id']; ?>">
	        <?php foreach($yks_wpms_vars['input_type'] as $it => $iv){ ?>
	        	<option value="<?php echo $it; ?>" <?php selected($mod['input'] , $it ); ?>><?php echo $iv['name'] ?></option>
	        <?php } ?>
	        </select>

	        </td>
	        </tr>
	        <tr valign="top">
	        <th scope="row"><?php _e('add all for radio' , YKS_WPMS_TD) ?></th>
	        <td><input type="checkbox" class="mod_all" name="mod_all-<?php echo $mod['mod_id']; ?>" value="1" <?php if($mod['all'] == 1)echo 'checked'; ?>/></td>
	        </tr>
	    	</table>
	    	<a class="delete"><?php _e('delete' , YKS_WPMS_TD) ?></a>
    	</div>
	</div>
	<?php
}


function yks_wpms_admin_mod_t($mod = null){
	global $yks_wpms_vars;

	if(!$mod){
			$mod = array(
				'mod_id'=>'',
				'taxonomy'=>'post_tag',
				'label'=>__('Post tag' , YKS_WPMS_TD),
				'input'=>''
			);
		}
	?>
	<div class="yks_mod yks_mod_t postbox">
		<button type="button" class="handlediv button-link" aria-expanded="false"><span class="screen-reader-text">close category</span><span class="toggle-indicator" aria-hidden="true"><span class="dashicons dashicons-arrow-down"></span></span></button>

		<h2 class="hndle"><span><?php esc_attr_e( 'Tag', YKS_WPMS_TD ); ?></span></h2>

		<div class="inside">

		    <table class="form-table">
	    	<input type="hidden" class="mod_id all-options" name="mod_id[]" value="<?php echo $mod['mod_id']; ?>" />
	    	<input type="hidden" class="mod_type all-options" name="mod_type-<?php echo $mod['mod_id']; ?>" value="post_tag" />
	    	<input type="hidden" class="mod_tax all-options" name="mod_tax-<?php echo $mod['mod_id']; ?>" value="post_tag" />
	        <tr valign="top">
	        <th scope="row"><?php _e('label' , YKS_WPMS_TD) ?></th>
	        <td><input type="text" class="mod_label all-options" name="mod_label-<?php echo $mod['mod_id']; ?>" value="<?php echo $mod['label'] ?>" /></td>
	        </tr>
	        <tr valign="top">
	        <th scope="row"><?php _e('input type' , YKS_WPMS_TD); ?></th>
	        <td>
	        <select class="mod_input all-options" name="mod_input-<?php echo $mod['mod_id']; ?>">
	        <?php foreach($vars['input_type'] as $it => $iv){ ?>
	        	<option value="<?php echo $it; ?>" <?php selected($mod['input'] , $it ); ?>><?php echo $iv['name'] ?></option>
	        <?php } ?>
	        </select>
	        </td>
	        </tr>
	    	</table>
	    	<a class="delete"><?php _e('delete' , YKS_WPMS_TD) ?></a>
    	</div>
	</div>
	<?php
}

/*
row action
*/



// add_action("plugin_row_{$path}", function( $plugin_file, $plugin_data, $status ) {
//   echo '<span class="">';
//   echo '<a href="'.admin_url('tools.php?page=yks_wpms_settings').'">設定</a>';
//   echo '</span>';
 
// }, 10, 3 );

add_filter( 'plugin_row_meta', 'yks_wpms_custom_plugin_row_meta', 10, 2 );

function yks_wpms_custom_plugin_row_meta( $links, $file ) {

$path = YKS_WPMS_PLUGIN_BASENAME;

	if ( strpos( $file, $path ) !== false ) {
		$new_links = array(
				'settings' => '<a href="'.admin_url('tools.php?page=yks_settings').'" >'.__('Settings').'</a>'
				);
		
		$links = array_merge( $links, $new_links );
	}
	
	return $links;
}


/*
debugging
*/

function yks_wpms_current_pagehook(){
	global $hook_suffix;
	if( !current_user_can( 'manage_options') ) return;
	echo '<div class="updated"><p>hook_suffix : '.$hook_suffix.'</p></div>';
}
//add_action('admin_notices', 'yks_wpms_current_pagehook');


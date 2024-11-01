<?php

add_action( 'yks_admin_add_mod', 'yks_wpms_admin_mod_tq');

if(!function_exists('yks_wpms_admin_mod_tq')){
	function yks_wpms_admin_mod_tq($mod = null){

		if(YKS_WPMS_PLUGIN_GRADE < 2)return;

		global $yks_wpms_vars;

		if(!$mod){
				$mod = array(
					'mod_id'=>'',
					'taxonomy'=>'',
					'label'=>'',
					'input'=>'',
					'all' => '',
					'values' => ''
				);
			}
		?>
		<div class="yks_mod yks_mod_tq postbox">
			<!-- <div class="handlediv" title="Click to toggle"><br></div> -->
			<button type="button" class="handlediv button-link" aria-expanded="false"><span class="screen-reader-text">close tax query</span><span class="toggle-indicator" aria-hidden="true"><span class="dashicons dashicons-arrow-down"></span></span></button>
							<!-- Toggle -->

			<h2 class="hndle"><span><?php esc_attr_e( 'Taxonomy Query', YKS_WPMS_TD ); ?></span></h2>

			<div class="inside">
			    <table class="form-table">
		    	<input type="hidden" class="mod_id all-options" name="mod_id[]" value="<?php echo $mod['mod_id']; ?>" />
		    	<input type="hidden" class="mod_type all-options" name="mod_type-<?php echo $mod['mod_id']; ?>" value="tax_query" />
		        <tr valign="top">
		        <th scope="row">taxonomy</th>
		        <td><input type="text" class="mod_tax all-options" name="mod_tax-<?php echo $mod['mod_id']; ?>" value="<?php echo $mod['taxonomy']; ?>" /></td>
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
	        	<td><input type="checkbox" class="mod_all" name="mod_all-<?php echo $mod['mod_id']; ?>" value="1" <?php if($mod['all'] == 1)echo 'checked'; ?>/></td>
	        	</tr>
	        	</tr>
		        
		    	</table>
		    	<a class="delete"><?php _e('delete' , YKS_WPMS_TD) ?></a>
	    	</div>
		</div>
		<?php
	}
}



add_action( 'yks_admin_add_mod_btn','yks_wpms_admin_mod_tq_btn');

if(!function_exists('yks_wpms_admin_mod_tq_btn')){
	function yks_wpms_admin_mod_tq_btn(){
		if(YKS_WPMS_PLUGIN_GRADE < 2)return;
		?>
		<a class="add_tq button-secondary" data-mod="tq" /><?php esc_attr_e( 'Add new taxonomy-query' , YKS_WPMS_TD); ?></a>
	<?php }
}

/*
	output tax query input
	*/
	function yks_wpms_form_tq($mod){
		$current = (!empty($_GET['mod_term-'.$mod['mod_id']]))?sanitize_text_field($_GET['mod_term-'.$mod['mod_id']]):'';
		?>
								    <dt>
								        <label>
								            <?php echo esc_html($mod['label']) ?>
								        </label>
								    </dt>
								    <dd>
								        <input type="hidden" name="mod_tax-<?php echo esc_attr($mod['mod_id']); ?>" value="<?php echo esc_attr($mod['taxonomy']) ?>" />
								        <!-- <input type="text" name="mod_term-<?php echo $mod['mod_id']; ?>" value="" /> -->
								    <?php $terms = get_terms($mod['taxonomy'] , array('hide_empty'=>false));?>


								    <?php if((empty($mod['input'])) || ($mod['input'] == 'radio')){

								    	if(isset($mod['all']) && $mod['all'] == 1){?>
											<label><input type="radio" name="mod_mv-<?php echo $mod['mod_id']; ?>" value="all" <?php echo ($current == 'all')?'checked':''; ?>><?php //_e('All'); ?>こだわらない</label>
								    	<?php }
								     ?>

								        <?php foreach ($terms as $term) { ?>
									    	<label><input type="radio" name="mod_term-<?php echo $mod['mod_id']; ?>" value="<?php echo $term->term_id; ?>" <?php echo ($term->term_id == $current)?'checked':''; ?>><?php echo esc_html($term->name) ?></label>
										<?php } ?>

								    <?php }else if($mod['input'] == 'select'){ ?>
								    	<select name="mod_term-<?php echo esc_attr($mod['mod_id']); ?>">
								    		<option value=""><?php _e( 'Select &hellip;'); ?></option>
								    	<?php foreach($terms as $term){ ?>
								    		<option value="<?php echo esc_attr($term->term_id); ?>" <?php echo ($term->term_id == $current)?'selected':''; ?>><?php echo esc_html($term->name) ?></option>
								        <?php	} ?>
								        </select>

								    <?php }else if($mod['input'] == 'checkbox'){ ?>
								    	<?php foreach($terms as $term){ ?>
								    		<label><input type="checkbox" name="mod_term-<?php echo esc_attr($mod['mod_id']); ?>[]" value="<?php echo esc_attr($term->term_id); ?>"<?php echo (in_array($term->term_id, (array)$current))?'checked':''; ?> /><?php echo esc_html($term->name) ?></label>
								        <?php	} ?>

								    <?php } ?>

									    
	<?php
	}

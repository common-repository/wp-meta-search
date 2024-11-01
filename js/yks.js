function uniqid() {
    var ts=String(new Date().getTime()), i = 0, out = '';
    for(i=0;i<ts.length;i+=2) {        
       out+=Number(ts.substr(i, 2)).toString(36);    
    }
    return ('d'+out);
}




jQuery(document).ready(function($){

	$('.sortable').sortable();


	$('a[class^=add_]').click(function(){
		mod = $(this).data('mod');
		elm = $('#yks_modules .yks_mod_'+mod);//.prop('outerHTML');
		_elm = elm.clone();
		_elm.appendTo('#mod_container');
	});

	//$('#mod_container .yks_mod_pt .mod_pt').parent().click(
	$('#mod_container').on('click' , '.yks_mod_pt .mod_pt_lb' ,
		function(){
			if($(this).find('.mod_hid').prop('checked')){
				$(this).closest('.yks_mod_pt').find('.toggle').hide();
				$(this).closest('.yks_mod_pt').find('.toggle_n').show();
			}else{
				$(this).closest('.yks_mod_pt').find('.toggle').show();
				$(this).closest('.yks_mod_pt').find('.toggle_n').hide();
			}
		}
		);

	$('#submit_option').click(function(){
		//rename in order
		c = 1;
		$('#mod_container .yks_mod').each(function(){
			$(this).find('.mod_id').val(c);
			$(this).find('.mod_type').attr('name','mod_type-'+c);
			$(this).find('.mod_mk').attr('name','mod_mk-'+c);
			$(this).find('.mod_label').attr('name','mod_label-'+c);
			$(this).find('.mod_input').attr('name','mod_input-'+c);
			$(this).find('.mod_tax').attr('name','mod_tax-'+c);
			$(this).find('.mod_hid').attr('name','mod_hid-'+c);
			$(this).find('.mod_pt').attr('name','mod_pt-'+c);
			$(this).find('.mod_range').attr('name','mod_range-'+c);
			$(this).find('.mod_values').attr('name','mod_values-'+c);
			$(this).find('.mod_all').attr('name','mod_all-'+c);
			c += 1;
		});
		$('#yks_settings').submit();
	});

	/* admin modules */

	// $('.button-link').click(function(){
	// 	$(this).closest('.yks_mod').find('.inside').slideToggle(200);
	// });

	$('.modules_container').on('click' , '.button-link' , function(){
		$(this).closest('.yks_mod').find('.inside').slideToggle(200);
	});

	$('.modules_container').on('click' , '.delete' , function(){
		m = $(this).closest('.yks_mod');
		m.fadeOut('fast').queue(function(){m.remove();});
	});


});
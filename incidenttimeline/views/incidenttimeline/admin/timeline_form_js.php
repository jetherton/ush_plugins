<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Timeline - JavaScript View, all the cool javascript that lets us do fun things.
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Inciden Timeline - http://ethertontech.com
 */
?>
	

		jQuery(window).load(function() {
			
			/* Form Actions */
			// Action on Save Only
			$('.btn_save').on('click', function () {
				$("#save").attr("value", "dontclose");
				$(this).parents("form").submit();
				return false;
			});
			
			$('.btn_save_close').on('click', function () {
				$(this).parents("form").submit();
				return false;
			});

			$('.btn_save_add_new').on('click', function () {
				$("#save").attr("value", "addnew");
				$(this).parents("form").submit();
				return false;
			});
			
			// Delete Action
			$('.btn_delete').on('click', function () {
				var agree=confirm("<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> <?php echo Kohana::lang('ui_admin.delete_action'); ?>?");
				if (agree){
					$("#save").attr("value", "delete");
					$(this).parents("form").submit();
					return false;
				}
				return false;
			});
			
			// Toggle Date Editor
			$('a#date_toggle').click(function() {
		    	$('#datetime_edit').show(400);
				$('#datetime_default').hide();
		    	return false;
			});
			
			// Date Picker JS
			$("#date").datepicker({ 
			    showOn: "both", 
			    buttonImage: "<?php echo url::file_loc('img') ?>media/img/icon-calendar.gif", 
			    buttonImageOnly: true 
			});
			

		});
		
		function deletePhoto (id, div)
		{
			var answer = confirm("<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to_delete_this_photo'); ?>?");
		    if (answer){
				$("#" + div).effect("highlight", {}, 800);
				$.get("<?php echo url::base() . 'admin/incidenttimeline/deletePhoto/' ?>" + id);
				$("#" + div).remove();
		    }
			else{
				return false;
		    }
		}
		
		

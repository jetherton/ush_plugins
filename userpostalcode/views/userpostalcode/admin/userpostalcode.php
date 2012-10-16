<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Postal Code data entry view
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Postal Code - http://ethertontech.com
 */
?>


	
	<div class="row">
		<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("userpostalcode.postal_code_why"); ?>"><?php echo Kohana::lang('userpostalcode.postal_code');?></a> <span class="required"><?php echo Kohana::lang('ui_main.required'); ?></span></h4>		
		<?php print form::input('postalcode', $form['postalcode'], ' class="text "'); ?>
	</div>
	


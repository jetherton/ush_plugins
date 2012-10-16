<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Postal Code data entry view for the login page
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Postal Code - http://ethertontech.com
 */
?>


		
	<tr>
		<td><strong><?php echo Kohana::lang('userpostalcode.postal_code'); ?>:</strong><br />
		<?php print form::input('postalcode', $form['postalcode'], 'class="login_text"'); ?></td>
	</tr>
	


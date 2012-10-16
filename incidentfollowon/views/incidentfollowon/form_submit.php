<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Follow On view for letting users add their social media data
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident Follow On - http://ethertontech.com
 */
?>

<div class="report_row">
	<h4>
		<?php echo Kohana::lang('incidentfollowon.twitter_name')?>
		<span class="example"><?php echo Kohana::lang('incidentfollowon.twitter_name_description')?></span>		
	</h4>	
	<input type="text" id="ifo_twitter" name="ifo_twitter" value="<?php echo $twitter ? $twitter : '';?>" class="text title">							
</div>


<div class="report_row">
	<h4>
		<?php echo Kohana::lang('incidentfollowon.facebook_url')?>
		<span class="example"><?php echo Kohana::lang('incidentfollowon.facebook_url_description')?></span>		
	</h4>
	
	<input type="text" id="ifo_twitter" name="ifo_facebook" value="<?php echo $facebook ? $facebook : '';?>" class="text title">							
</div>


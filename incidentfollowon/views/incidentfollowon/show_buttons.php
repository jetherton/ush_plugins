<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Follow On view for letting users add their social media data
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident Follow On - http://ethertontech.com
 */
?>

</div> <!-- close the category div -->
<div id="incidentfollowon_div" class="report-media">
	<?php if($twitter) {?>
		<a href="https://twitter.com/<?php echo $twitter;?>" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @<?php echo $twitter;?></a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	<?php } if($facebook AND $twitter){echo "&nbsp;&nbsp;&nbsp;&nbsp;";} if($facebook){?>
	
			
		<a title="<?php echo Kohana::lang('incidentfollowon.followonfacebook');?>" href="<?php echo $facebook;?>"><img src="<?php echo url::base();?>plugins/incidentfollowon/media/facebook.png"/></a>
		
	<?php }?>
</div>

<div> <!-- start the closing of the category div tag that the tag aboved really closes. That's complex -->

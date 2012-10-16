<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident delta notify view for the incident page when a user isn't logged in.
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident delta notify - http://ethertontech.com
 */
?>


	</div> <!-- close the category div -->
	<div id="incidentdeltanotify_div" class="report-media">	
		<br/>
			<a
				id="incidentdeltanotify_follow" style="<?php if($is_following){echo "display:none;";} ?>" 
				title="<?php echo Kohana::lang('incidentdeltanotify.subscribe_to_this_idea_explain');?>" 
				onclick="idn_subscribe(<?php echo $user_id;?>, <?php echo $incident_id; ?>); return false;">
					<?php echo Kohana::lang('incidentdeltanotify.subscribe_to_this_idea');?>
			</a>
		
		
			<a 
				id="incidentdeltanotify_unfollow" style="<?php if(!$is_following){echo "display:none;";} ?>" 
				title="<?php echo Kohana::lang('incidentdeltanotify.unsubscribe_to_this_idea_explain');?>" 
				onclick="idn_unsubscribe(<?php echo $user_id;?>, <?php echo $incident_id; ?>); return false;">
				<?php echo Kohana::lang('incidentdeltanotify.unsubscribe_to_this_idea');?>
			</a>
			<img id="incidentdeltanotify_wait" src="<?php echo url::base();?>media/img/loading_g.gif" style="display:none;"/>
			<span style="display:none;" id="incidentdeltanotify_error"><?php echo Kohana::lang('incidentdeltanotify.error_please_try_again');?></span>
			<span style="display:none;" id="incidentdeltanotify_success_sub"><?php echo Kohana::lang('incidentdeltanotify.you_have_been_sub');?></span>
			<span style="display:none;" id="incidentdeltanotify_success_unsub"><?php echo Kohana::lang('incidentdeltanotify.you_have_been_unsub');?></span>
		<br/>			
	</div>
	<script type="text/javascript">
		/** We'll put our java script here**/
		function idn_subscribe(userId, incidentId)
		{
			$("#incidentdeltanotify_wait").show();
			//setup the subscribe url
			var url = "<?php echo url::base();?>/incidentdeltanotify/subscribe?incident_id="+incidentId;

			//call the URL
			$.getJSON(url, function(data) {

				$("#incidentdeltanotify_wait").hide();
				//was it successful
				if(data.status == "success")
				{
					//show successful subscribe
					$("#incidentdeltanotify_follow").hide();
					$("#incidentdeltanotify_unfollow").fadeIn('slow');
					$("#incidentdeltanotify_success_sub").fadeIn('slow').delay(2000).fadeOut('slow');
				}
				else
				{
					$("#incidentdeltanotify_error").fadeIn('slow').delay(2000).fadeOut('slow');
				}
				  
				});
			
		}

		function idn_unsubscribe(userId, incidentId)
		{
			$("#incidentdeltanotify_wait").show();
			//setup the subscribe url
			var url = "<?php echo url::base();?>/incidentdeltanotify/unsubscribe_json?incident_id="+incidentId;

			//call the URL
			$.getJSON(url, function(data) {

				$("#incidentdeltanotify_wait").hide();
				//was it successful
				if(data.status == "success")
				{
					//show successful subscribe
					$("#incidentdeltanotify_unfollow").hide();
					$("#incidentdeltanotify_follow").fadeIn('slow');
					$("#incidentdeltanotify_success_unsub").fadeIn('slow').delay(2000).fadeOut('slow');
				}
				else
				{
					$("#incidentdeltanotify_error").fadeIn('slow').delay(2000).fadeOut('slow');
				}
				  
				});
			
		}
		
	</script>
	<div> <!-- start the closing of the category div tag that the tag aboved really closes. That's complex -->
	


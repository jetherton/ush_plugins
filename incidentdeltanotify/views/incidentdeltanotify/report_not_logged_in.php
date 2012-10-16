<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident delta notify view for the incident page when a user isn't logged in.
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident delta notify - http://ethertontech.com
 */
?>


	</div> <!-- close the category div -->
	
	<div class="report-media">	
		<br/>
		<div >
		<!-- <a id="incidentdeltanotify_notlogged" title="<?php //echo Kohana::lang('incidentdeltanotify.login_to_follow_explain');?>" onclick="$('.header_nav_has_dropdown > a').click(); $('#username').focus(); $(window).scrollTop(0); return false;"><?php //echo Kohana::lang('incidentdeltanotify.login_to_follow');?></a> -->
		<?php //create back link
			$back_link = urlencode(url::base(). 'reports/view/'.$incident_id);
		?>
		<a id="incidentdeltanotify_notlogged" title="<?php echo Kohana::lang('incidentdeltanotify.login_to_follow_explain');?>" href="<?php echo url::base() . 'idnlogin?p='.$back_link; ?>"><?php echo Kohana::lang('incidentdeltanotify.login_to_follow');?></a>
		</div>
		<br/>		
	</div>
	<div> <!-- start the closing of the category div tag that the tag aboved really closes. That's complex -->
	


<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident delta notify view for telling the user they have to log in to unsubscribe
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident delta notify - http://ethertontech.com
 */
?>

<div id="main" class="report-detail" style="min-height:400px;">
	<h1 class="report-title"><?php echo Kohana::lang('incidentdeltanotify.unsubscribe_login');?></h1>
	
	<h2><?php echo Kohana::lang('incidentdeltanotify.unsubscribe_successful');?></h2>
	
	<h2>
		<?php echo $user->name?> <?php echo Kohana::lang('incidentdeltanotify.you_have_been_unsubscribed_from');?> 
		<a href="<?php echo url::base();?>reports/view/<?php echo $incident->id;?>"><?php echo $incident->incident_title;?></a>
	</h2>
</div>


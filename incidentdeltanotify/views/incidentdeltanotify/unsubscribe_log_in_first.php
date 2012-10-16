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
	
	<h2><?php echo Kohana::lang('incidentdeltanotify.sorry_login_first');?></h2>
	
	<h2><a href="<?php echo url::base();?>/login"><?php echo Kohana::lang('incidentdeltanotify.click_here_to_log_in');?></a></h2>
</div>


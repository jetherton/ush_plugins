<?php defined('SYSPATH') or die('No direct script access.');
/**
 * ZDS Admin Alerts alert settings view
 *
 * @author	   Z Data Solutions <info@zdatasolutions.net> 
 * @package	   ZDS Admin Alerts - http://www.zdatasolutions.net/
 */
?>

<script type="text/javascript">

	


function toggle_zdsadminalert()
{
	if($("#zds_enable").val() == '1')
	{
		$("#zdsAdminAlertsInfo").show('slow');
	}
	else
	{
		$("#zdsAdminAlertsInfo").hide('slow');
	}
}

$(document).ready(function () 
{
	$("#notify").parent().hide();
	if('<?php echo $form['zds_enable']; ?>' == '1')
	{
		toggle_zdsadminalert();
	}
});

	
</script>

<div class="row">
	<h4><?php echo Kohana::lang('ui_main.receive_notifications');?>?</h4>
	<?php print form::dropdown('zds_enable', $yesno_array, $form['zds_enable'], 'onchange="toggle_zdsadminalert(); return false;"'); ?>
</div>
<div id="zdsAdminAlertsInfo" style="display:none;">
	<div class="row">
		<h4><?php echo Kohana::lang('zdsadminalerts.receive_notifications_for_categories');?>:</h4>
		<?php echo Kohana::lang('zdsadminalerts.receive_notifications_for_categories_detail'); ?>
		<br/>
		<?php echo '<div id="zdsadminalert_cat_tree">'.category::tree($categories, FALSE, $form['selected_categories'], 'zds_category', 2) . '</div>'; ?>
	</div>
	<div class="row">
		<h4><?php echo Kohana::lang('zdsadminalerts.receive_notifications_when');?>:</h4>
		<table class="zdsadminalerts_table">
			<tr>
				<td>
					<?php
						print form::checkbox('zds_sms', 'sms_reciept', $form['zds_sms']);
						print form::label('zds_sms', Kohana::lang('zdsadminalerts.sms_received'));						
					?>
				</td>
				<td>
					<?php
						print form::checkbox('zds_email', 'email_reciept', $form['zds_email']);
						print form::label('zds_email', Kohana::lang('zdsadminalerts.email_received'));						
					?>
				</td>				
				<td>
					<?php
						print form::checkbox('zds_web_report', 'web_report_reciept', $form['zds_web']);
						print form::label('zds_web_report', Kohana::lang('zdsadminalerts.web_report_reciept'));						
					?>
				</td>
				<td>
					<?php
						print form::checkbox('zds_admin_report', 'admin_report_reciept', $form['zds_admin']);
						print form::label('zds_admin_report', Kohana::lang('zdsadminalerts.admin_report_reciept'));						
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php
						print form::checkbox('zds_api_report', 'api_report_reciept', $form['zds_api']);
						print form::label('zds_api_report', Kohana::lang('zdsadminalerts.api_report_reciept'));						
					?>
				</td>
				<td>
					<?php
						print form::checkbox('zds_report_approved', 'report_approved', $form['zds_approved']);
						print form::label('zds_report_approved', Kohana::lang('zdsadminalerts.report_approved'));						
					?>
				</td>
				<td>
					<?php
						print form::checkbox('zds_report_verified', 'report_verified', $form['zds_verified']);
						print form::label('zds_report_verified', Kohana::lang('zdsadminalerts.report_verified'));						
					?>
				</td>			
			</tr>
		</table>
	</div>
</div>
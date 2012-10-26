<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Timeline view for the homepage widget
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident Timeline - http://ethertontech.com
 */
?>

<li style="width:592px;">
<div style="clear:both;"></div>
<div class="content-block incidenttimeline_home_view">
<h5><?php echo Kohana::lang('incidenttimeline.upcoming')?></h5>

<div id="timeline_homepage_widget">
	<table>
		<tr>
			<th><?php echo Kohana::lang('incidenttimeline.date');?></th>
			<th><?php echo Kohana::lang('incidenttimeline.milestone');?></th>
			<th><?php echo Kohana::lang('incidenttimeline.incident');?></th>
		</tr>
		
		<?php $i = 0; foreach($milestones as $milestone) { $i++;?>
			<tr class="<?php echo $i % 2 == 0 ? 'odd':'even';?>">
				<td>
					<?php echo date( 'l, F jS' ,strtotime($milestone->date)). '<br/>'. date( 'g:ia' ,strtotime($milestone->date));?>					
				</td>				
				<td>
					<a href="<?php echo url::base().'incidenttimeline/view?id='.$milestone->milestone_id;?>"><?php echo $milestone->title;?></a>
				</td>
				<td>
					<a href="<?php echo url::base().'reports/view/'.$milestone->incident_id;?>"><?php echo $milestone->incident_title;?></a>
				</td>
			</tr>
		<?php }?>
	</table>
</div>
</div>
</li>
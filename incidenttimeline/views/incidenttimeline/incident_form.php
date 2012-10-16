<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Timeline view for letting users add new elements to the timeline
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident Timeline - http://ethertontech.com
 */
?>

<div class="row">
	<h4>
		<?php echo Kohana::lang('incidenttimeline.timeline')?>
	</h4>
	<div class="btns">
		<ul>
			<li>
				<?php $url = (strpos(url::current(), "admin/") === 0) ? 'admin/incidenttimeline/edit?incident_id=' : 'members/incidenttimeline/edit?incident_id=';?>
				<a id="incidenttimeline_add" class="butn_save_close" href="<?php echo url::base() . $url. $incident_id;?>"><?php echo $count == 0 ? Kohana::lang('incidenttimeline.add_timeline_element_first'): Kohana::lang('incidenttimeline.add_timeline_element');?></a>
			</li>
		</ul>
	</div>
	<?php if ($count > 0){?>
	<br/>
	<div id="timeline">
		<div id="my-timeline" style="height: 200px; border: 1px solid #aaa"></div>
	</div>					
	<?php }?>			
</div>

<?php if ($count > 0){?>
<script type="text/javascript">


var timeline_data = <?php echo $data; ?>;

var tl;
var resizeTimerID = null;

$(document).ready(function() {	  

	var eventSource = new Timeline.DefaultEventSource();
	var bandInfos = [
		Timeline.createBandInfo({
			eventSource:    eventSource,
			width:          "70%", 
			intervalUnit:   Timeline.DateTime.MONTH, 
			intervalPixels: 100
		}),
		Timeline.createBandInfo({
			eventSource:    eventSource,
	        trackHeight:    0.5,
	        trackGap:       0.2,
			width:          "30%", 
			intervalUnit:   Timeline.DateTime.YEAR, 
			intervalPixels: 200,
			showEventText:  false,
			overview: true
		})
	];
	bandInfos[1].syncWith = 0;
	bandInfos[1].highlight = true;
	
	tl = Timeline.create(document.getElementById("my-timeline"), bandInfos);
	var url = '.'; // The base url for image, icon and background image
    eventSource.loadJSON(timeline_data, url);
    tl.layout();

  
});




</script>
<?php }?>



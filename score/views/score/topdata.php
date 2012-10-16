<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Score data view for the incident page when a user isn't logged in.
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   score - http://ethertontech.com
 */
?>
<li style="width:592px;">
<div style="clear:both;"></div>
<div class="content-block">
<h5>DemocraScore</h5>

<table class="score_tab_heading">
	<tr><td rowspan="2">DemocraCitizens</td><td colspan="2">Ideas</td></tr>
	<tr><td>All Time</td><td>This Month</td></tr>
</table>
<div id="score_top_data_tabs">       
    <ul>
        <li><a href="#tabs-1">All Time</a></li>
        <li><a href="#tabs-2">This Month</a></li>
        <li><a href="#tabs-3">%</a></li>
        <li><a href="#tabs-5">Count</a></li>
        <li><a href="#tabs-4">%</a></li>        
        <li><a href="#tabs-6">Count</a></li>
    </ul>

    <div id="tabs-1">
        <p>	
        	<table class = "score_table">
        	<tr><th>Rank</th><th>Name</th><th>Score</th></tr>
        		<?php $i =1;
        			foreach($all_time_users_score as $id=>$score) {?>
        			<tr><td><?php echo $i;?></td>
        			<td><a href="<?php echo url::base().'profile/user/'.$all_time_users[$id]['username'];?>"><?php echo $all_time_users[$id]['name'];?></a></td>
        			<td><?php echo $score;?></td></tr>
        			<?php if($i == 10){break;}?>
				<?php $i++;}?>
        	</table>  
        </p>    
    </div>
    <div id="tabs-2">
    	<p>
    		<table class = "score_table">
        	<tr><th>Rank</th><th>Name</th><th>Score</th></tr>
        		<?php $i =1;
        			foreach($month_users_score as $id=>$score) {?>
        			<tr><td><?php echo $i;?></td>
        			<td><a href="<?php echo url::base().'profile/user/'.$all_time_users[$id]['username'];?>"><?php echo $all_time_users[$id]['name'];?></a></td>
        			<td><?php echo $score;?></td></tr>
        			<?php if($i == 10){break;}?>
				<?php $i++;}?>
        	</table>   
        </p>        
    </div>
    <div id="tabs-3">
        <p>	
        	<table class = "score_table">
        	<tr><th>Rank</th><th>Name</th><th>Votes For</th><th>Votes Against</th><th>Percent For</th></tr>
        		<?php $i =1;
        			foreach($all_time_incidents_scores as $id=>$score) {?>
        			<tr><td><?php echo $i;?></td>
        			<td><a href="<?php echo url::base().'reports/view/'.$id;?>"><?php echo $all_time_incidents[$id]['title'];?></a></td>
        			<td><?php echo $all_time_incidents[$id]['votes_for']?></td>
        			<td><?php echo $all_time_incidents[$id]['votes_against']?></td>
        			<td><?php echo number_format($score['score'], 2, '.','');?>%</td></tr>
        			<?php if($i == 10){break;}?>
				<?php $i++;}?>
        	</table>   
        </p>        
    </div>
    <div id="tabs-4">
    	<p>
			<table class = "score_table">
        	<tr><th>Rank</th><th>Name</th><th>Votes For</th><th>Votes Against</th><th>Percent For</th></tr>
        		<?php $i =1;
        			foreach($month_incidents_scores as $id=>$score) {?>
        			<tr><td><?php echo $i;?></td>
        			<td><a href="<?php echo url::base().'reports/view/'.$id;?>"><?php echo $month_incidents[$id]['title'];?></a></td>
        			<td><?php echo $month_incidents[$id]['votes_for']?></td>
        			<td><?php echo $month_incidents[$id]['votes_against']?></td>
        			<td><?php echo number_format($score['score'], 2, '.','');?>%</td></tr>
        			<?php if($i == 10){break;}?>
				<?php $i++;}?>
        	</table>       	 
        </p>        
    </div>
    <div id="tabs-5">
        <p>	
        	<table class = "score_table">
        	<tr><th>Rank</th><th>Name</th><th>Votes For</th><th>Votes Against</th><th>Total Votes</th></tr>
        		<?php $i =1;
        			foreach($all_time_incidents_counts as $id=>$score) {?>
        			<tr><td><?php echo $i;?></td>
        			<td><a href="<?php echo url::base().'reports/view/'.$id;?>"><?php echo $all_time_incidents[$id]['title'];?></a></td>
        			<td><?php echo $all_time_incidents[$id]['votes_for']?></td>
        			<td><?php echo $all_time_incidents[$id]['votes_against']?></td>
        			<td><?php echo number_format($score, 0, '',',');?></td></tr>
        			<?php if($i == 10){break;}?>
				<?php $i++;}?>
        	</table>   
        </p>        
    </div>
    <div id="tabs-6">
    	<p>
			<table class = "score_table">
        	<tr><th>Rank</th><th>Name</th><th>Votes For</th><th>Votes Against</th><th>Total Votes</th></tr>
        		<?php $i =1;
        			foreach($month_incidents_counts as $id=>$score) {?>
        			<tr><td><?php echo $i;?></td>
        			<td><a href="<?php echo url::base().'reports/view/'.$id;?>"><?php echo $month_incidents[$id]['title'];?></a></td>
        			<td><?php echo $month_incidents[$id]['votes_for']?></td>
        			<td><?php echo $month_incidents[$id]['votes_against']?></td>
        			<td><?php echo number_format($score, 0, '',',');?></td></tr>
        			<?php if($i == 10){break;}?>
				<?php $i++;}?>
        	</table>       	 
        </p>        
    </div>
</div>


 <script>
    $(function() {
        $( "#score_top_data_tabs" ).tabs();
    });
    </script>
    
<div style="clear:both;"></div>
</div>
</li>
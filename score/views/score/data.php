<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Score data view for the incident page when a user isn't logged in.
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   score - http://ethertontech.com
 */
?>


<div id="score_data_tabs">
    <ul>
        <li><a href="#tabs-1">To Date</a></li>
        <li><a href="#tabs-2">This Month</a></li>
    </ul>
    <div id="tabs-1">
        <p>	
        	<?php echo '<span class="vote">'.Kohana::lang('score.votes for'). ': </span> <span class="vote_val">' .$up_all .'</span>';?>
        	&nbsp;&nbsp;&nbsp; 
        	<?php echo '<span class="vote votes_against">'.Kohana::lang('score.votes against'). ': </span> <span class="vote_val">' .$down_all .'</span>';?>
        	&nbsp;&nbsp;<span class="vote votes_ratio">        	
        	<?php if(($up_all + $down_all) > 0) {echo Kohana::lang('score.ratio'). ': ' . round(floatval( ($up_all/($down_all+$up_all)) * 100 ),2).'%';}?>
        	</span>
        </p>
        
    </div>
    <div id="tabs-2">
    	<p>
        <?php echo '<span class="vote">'.Kohana::lang('score.votes for'). ': </span> <span class="vote_val">' .$up_month .'</span>';?>
        	&nbsp;&nbsp;&nbsp; 
        	<?php echo '<span class="vote votes_against">'.Kohana::lang('score.votes against'). ': </span> <span class="vote_val">' .$down_month .'</span>';?>
        	&nbsp;&nbsp;<span class="vote votes_ratio">        	
        	<?php if(($up_month + $down_month) > 0) {echo Kohana::lang('score.ratio'). ': ' . round(floatval( ($up_month/($down_month+$up_month)) * 100 ),2).'%';}?>
        	</span>
        </p>        
    </div>
</div>
<div class="vote_counts">
	<?php if($logged_in AND $your_vote != 3) {?>
		<table id="vote_buttons_table">
			<tr>
				<td>
					<a class="vote_for" href="#" onclick="castVote(1); return false;"><?php echo Kohana::lang('score.vote for');?></a>
				</td>
				<td id="vote_status">
					<span>
					<?php if($your_vote == 1){echo Kohana::lang('score.you_have_voted_for');}
					if($your_vote == -1) {echo Kohana::lang('score.you_have_voted_against');}
					if($your_vote == 0) {echo Kohana::lang('score.you_have_not_cast_your_vote');}?>
					</span>
				</td>
				<td>
					<a class="vote_against" href="#" onclick="castVote(-1);return false;"><?php echo Kohana::lang('score.vote against');?></a>
				</td>
			</tr>
		</table>
	<?php } elseif($logged_in AND $your_vote == 3) {?>
		<h5 class="score_login"><?php echo Kohana::lang('score.cantvoteyouridea');?></h5>
	<?php } else {?>
		<h5 class="score_login"><a  href="<?php echo url::base()?>scorelogin?p=<?php echo urlencode(url::base().'reports/view/'.$incident_id);?>"><?php echo Kohana::lang('score.login to vote');?></a></h5>
	<?php }?>
</div>

 <script>
    $(function() {
        $( "#score_data_tabs" ).tabs();
    });
    </script>
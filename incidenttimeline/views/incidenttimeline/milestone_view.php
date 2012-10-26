<div id="main" class="report_detail">

	<div class="left-col" style="float:left;width:520px; margin-right:20px">
	
  	 

		<h1 class="report-title"><?php
			echo $milestone->title;			
		?></h1>
		
		
		<div class="report-description-text">
			<h5><?php echo Kohana::lang('incidenttimeline.from_incident');?></h5>
			<?php echo '<a href="'.url::base().'reports/view/'.$incident->id.'">'.$incident->incident_title.'</a>'; ?>
		</div>
		
		<div class="report-description-text">
			<h5><?php echo Kohana::lang('incidenttimeline.date');?></h5>
			<?php echo date( 'l, F jS Y' ,strtotime($milestone->date)). ' at '. date( 'g:ia' ,strtotime($milestone->date)); ?>
		</div>
	
		
		<div class="report-description-text">
			<h5><?php echo Kohana::lang('incidenttimeline.description');?></h5>
			<?php echo $milestone->description; ?>
			<br/>
		</div>
		
		<?php if($milestone->people != null AND $milestone->people != ''){?>
		<div class="report-description-text">
			<h5><?php echo Kohana::lang('incidenttimeline.people_needed');?></h5>
			<?php echo $milestone->people; ?>
			<br/>
		</div>
		<?php }?>
		
		<?php if($milestone->resources != null AND $milestone->resources != ''){?>
		<div class="report-description-text">
			<h5><?php echo Kohana::lang('incidenttimeline.resources_needed');?></h5>
			<?php echo $milestone->resources; ?>
			<br/>
		</div>
		<?php }?>
	
		
	</div>
	
	<div style="float:right;width:450px;">
		
		<div class="report-description-text">
			<h5><?php echo $milestone->is_completed == 0 ? Kohana::lang('incidenttimeline.is_not_completed'):Kohana::lang('incidenttimeline.is_completed');?></h5>
		</div>
		
		<?php if($milestone->link != null AND $milestone->link != ''){?>
		<div class="report-description-text">
			<h5><?php echo Kohana::lang('incidenttimeline.link_to_resources');?></h5>
			<?php echo '<a href="'.$milestone->link.'">'.$milestone->link.'</a>'; ?>
			<br/>
		</div>
		<?php }?>
		
		<?php if($milestone->photo != null AND $milestone->photo != ''){?>
		<div class="row link-row">
			
								<h4><?php echo Kohana::lang('incidenttimeline.picture');?></h4>
								<?php								
    								if ($milestone->photo)
                        			{
                       						$photo = url::convert_uploaded_to_abs($milestone->photo); 
                       						?>
                      						<div class="report_thumbs" id="photo_">
                      						<a class="photothumb" rel="lightbox-group1" href="<?php echo $photo; ?>">
                      						<img style="max-width:400px;"src="<?php echo $photo; ?>" />
                      						</a>
           						<?php
                        			}
			                    ?>
							</div>
		<?php }?>
				
					<br/>
					<?php
						Event::run('incidenttimeline_action.view_milestone_form', $id);
					?>
		

	</div>
	
	<div style="clear:both;"></div>
   
	

</div>
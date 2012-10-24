<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incident Timeline - View, Shows the form that lets users add/edit/delete timeline elements
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Inciden Timeline - http://ethertontech.com
 */
?>
			<div class="bg">
				<h2>
					<?php admin::reports_subtabs("create milestone"); ?>
					<?php echo Kohana::lang('incidenttimeline.create_milestone');?>
				</h2>
				<?php print form::open(NULL, array('enctype' => 'multipart/form-data', 'id' => 'reportForm', 'name' => 'reportForm')); ?>
					<input type="hidden" name="save" id="save" value="">
					<input type="hidden" name="timeline_id" id="timeline_id" value="<?php print $form['id']; ?>">					
					<!-- report-form -->
					<div class="report-form">
						<?php
						if ($form_error) {
						?>
							<!-- red-box -->
							<div class="red-box">
								<h3><?php echo Kohana::lang('ui_main.error');?></h3>
								<ul>
								<?php
								foreach ($errors as $error_item => $error_description)
								{
									print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
								}
								?>
								</ul>
							</div>
						<?php
						}

						if ($form_saved) {
						?>
							<!-- green-box -->
							<div class="green-box">
								<h3><?php echo Kohana::lang('incidenttimeline.timeline_saved');?></h3>
							</div>
						<?php
						}
						?>
						<div class="head">
							<h3><?php echo $id ? Kohana::lang('incidenttimeline.edit_timeline') . "#". $incident->id. ' - '.$incident->incident_title : Kohana::lang('incidenttimeline.new_timeline'); ?></h3>
							<div class="btns" style="float:right;">
								<ul>
									<li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('incidenttimeline.save_timeline'));?></a></li>
									<li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
									<li><a href="<?php echo url::base().$url.'/reports/edit/'.$incident_id;?>" ><?php echo strtoupper(Kohana::lang('incidenttimeline.return_to_incident'));?></a>&nbsp;&nbsp;&nbsp;</li>
									<li><a href="#" class="btn_delete btns_red"><?php echo strtoupper(Kohana::lang('ui_main.delete'));?></a></li>
								</ul>
							</div>
						</div>
						<!-- f-col -->
						<div class="f-col">
							
							<div class="row">
								<h4><?php echo Kohana::lang('incidenttimeline.milestonetitle');?> <span class="required">*</span></h4>
								<?php print form::input('title', $form['title']) ?>
							</div>
																
							<div class="row">
								<h4><?php echo Kohana::lang('incidenttimeline.milestonedescription');?> <span class="required">*</span></h4>
								<?php print form::textarea('description', $form['description'], ' rows="12" cols="40"') ?>
							</div>
							
							<div class="row">
								<h4><?php echo Kohana::lang('incidenttimeline.people_needed');?> </h4>
								<?php print form::textarea('people', $form['people'], ' rows="12" cols="40"') ?>
							</div>
							
							<div class="row">
								<h4><?php echo Kohana::lang('incidenttimeline.resources_needed');?> </h4>
								<?php print form::textarea('resources', $form['resources'], ' rows="12" cols="40"') ?>
							</div>

							
							
							

						</div>
						<!-- f-col-1 -->
						<div class="f-col-1">
						
						
						

							<div class="row" id="datetime_edit">
								<div class="date-box">
									<h4><?php echo Kohana::lang('ui_main.date');?> <span><?php echo Kohana::lang('ui_main.date_format');?></span></h4>
									<?php print form::input('date', $form['date'], ' class="text"'); ?>								
									<?php print $date_picker_js; ?>				    
								</div>
								<div class="time">
									<h4><?php echo Kohana::lang('ui_main.time');?> <span>(<?php echo Kohana::lang('ui_main.approximate');?>)</span></h4>
									<?php
									print '<span class="sel-holder">' .
								    form::dropdown('hour', $hour_array,
									$form['hour']) . '</span>';
									
									print '<span class="dots">:</span>';
									
									print '<span class="sel-holder">' .
									form::dropdown('minute',
									$minute_array, $form['minute']) .
									'</span>';
									print '<span class="dots">:</span>';
									
									print '<span class="sel-holder">' .
									form::dropdown('ampm', $ampm_array,
									$form['ampm']) . '</span>';
									?>
								</div>
							</div>
							
							<div class="row">
								<h4><?php echo Kohana::lang('incidenttimeline.link_to_resources');?> </h4>
								<?php print form::input('link', $form['link'], ' class="text long"') ?>
							</div>
							
							<div class="row">
								<h4><?php echo Kohana::lang('incidenttimeline.is_this_complete');?> </h4>
								<?php print form::radio('is_completed', '1', intval($form['is_completed'])) ?>
								<?php print form::label('is_completed', Kohana::lang('incidenttimeline.complete'));?>
								<?php print form::radio('is_completed', '0', !intval($form['is_completed'])) ?>
								<?php print form::label('is_completed', Kohana::lang('incidenttimeline.not_complete'));?>
							</div>

							<!-- Photo Fields -->
							<div class="row link-row">
								<h4><?php echo Kohana::lang('ui_main.reports_photos');?></h4>
								<?php								
    								if ($form['photo'])
                        			{
                        			
                        					
                        						
                        						$photo = url::convert_uploaded_to_abs($form['photo']); 
                        						?>
                        						<div class="report_thumbs" id="photo_">
                        						<a class="photothumb" rel="lightbox-group1" href="<?php echo $photo; ?>">
                        						<img src="<?php echo $photo; ?>" />
                        						</a>
												&nbsp;&nbsp;
												<a href="#" onClick="deletePhoto('<?php echo $id; ?>', 'photo_'); return false;" ><?php echo Kohana::lang('ui_main.delete'); ?></a>
                        						</div>
                        						<?php
                        					
                        			
                        			}
			                    ?>
							</div>
							<div id="divPhoto">
								<?php
					
								$i = 1;
								print "<div class=\"row link-row\">";
								print form::upload('photo' . '[]', '', ' class="text long"');									
								print "</div>";
								
								?>
							</div>
							
							<?php
								Event::run('incidenttimeline_action.edit_milestone_form', $id);
							?>
							
						</div>
						<!-- f-col-bottom -->
						<div class="f-col-bottom-container">
						</div>
						<div class="btns">
							<ul>
								<li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_main.save_report'));?></a></li>
								<li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
									<li><a href="#" class="btn_save_add_new"><?php echo strtoupper(Kohana::lang('ui_main.save_add_new'));?></a></li>
								<?php 
								if($id)
								{
									echo "<li><a href=\"#\" class=\"btn_delete btns_red\">".strtoupper(Kohana::lang('ui_main.delete_report'))."</a></li>";
								}
								?>
								<li><a href="<?php echo url::site().'admin/reports/';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
								<li><a href="<?php echo url::base().'admin/reports/edit/'.$incident_id;?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
							</ul>
						</div>						
					</div>
				<?php print form::close(); ?>
				<?php
				if($id)
				{
					// Hidden Form to Perform the Delete function
					print form::open(url::site().'admin/reports/', array('id' => 'reportMain', 'name' => 'reportMain'));
					$array=array('action'=>'d','incident_id[]'=>$id);
					print form::hidden($array);
					print form::close();
				}
				?>
			</div>

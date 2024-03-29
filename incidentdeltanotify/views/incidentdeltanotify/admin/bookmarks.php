<?php
/**
 * Incident delta notify view. Lets admins see the ideas they've bookmarked
 * Shamelessly stolen from /application/views/admin/reports
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Incident delta notify - http://ethertontech.com
 */
?>
			<div class="bg">
				<h2><?php echo Kohana::lang('incidentdeltanotify.bookmarks'); ?></h2>
				<!-- report-table -->
				<?php print form::open(NULL, array('id' => 'reportMain', 'name' => 'reportMain')); ?>
					<input type="hidden" name="action" id="action" value="">
					<input type="hidden" name="incident_id[]" id="incident_single" value="">
					<div class="table-holder">
						<table class="table">
							<thead>
								<tr>
									<th class="col-1"></th>
									<th class="col-2"><?php echo Kohana::lang('ui_main.report_details');?></th>
									<th class="col-3"><?php echo Kohana::lang('ui_main.date');?></th>
									<th class="col-4"><?php echo Kohana::lang('ui_main.actions');?></th>
								</tr>
							</thead>
							<tfoot>
								<tr class="foot">
									<td colspan="4">
										<?php echo $pagination; ?>
									</td>
								</tr>
							</tfoot>
							<tbody>
							<?php if ($total_items == 0): ?>
								<tr>
									<td colspan="4" class="col">
										<h3><?php echo Kohana::lang('incidentdeltanotify.no_bookmarks');?></h3>
									</td>
								</tr>
							<?php endif; ?>
							<?php
								foreach ($incidents as $incident)
								{
									$incident_id = $incident->id;
									$incident_title = strip_tags($incident->incident_title);
									$incident_description = text::limit_chars(strip_tags($incident->incident_description), 150, "...", true);
									$incident_date = $incident->incident_date;
									$incident_date = date('Y-m-d', strtotime($incident->incident_date));
									
									// Mode of submission... WEB/SMS/EMAIL?
									$incident_mode = $incident->incident_mode;
									
									// Get the incident ORM
									$incident_orm = ORM::factory('incident', $incident_id);
									
									// Get the person submitting the report
									$incident_person = $incident_orm->incident_person;
									
									//XXX incident_Mode will be discontinued in favour of $service_id
									if ($incident_mode == 1)	// Submitted via WEB
									{
										$submit_mode = "WEB";
										// Who submitted the report?
										if ($incident_person->loaded)
										{
											// Report was submitted by a visitor
											$submit_by = $incident_person->person_first . " " . $incident_person->person_last;
										}
										else
										{
											if ($incident_orm->user_id)					// Report Was Submitted By Administrator
											{
												$submit_by = $incident_orm->user->name;
											}
											else
											{
												$submit_by = Kohana::lang('ui_admin.unknown');
											}
										}
									}
									elseif ($incident_mode == 2) 	// Submitted via SMS
									{
										$submit_mode = "SMS";
										$submit_by = $incident_orm->message->message_from;
									}
									elseif ($incident_mode == 3) 	// Submitted via Email
									{
										$submit_mode = "EMAIL";
										$submit_by = $incident_orm->message->message_from;
									}
									elseif ($incident_mode == 4) 	// Submitted via Twitter
									{
										$submit_mode = "TWITTER";
										$submit_by = $incident_orm->message->message_from;
									}
									
									// Get the country name

									$country_name = ($incident->location->country_id != 0)
										? $countries[$incident->location->country_id] 
										: $countries[Kohana::config('settings.default_country')]; 
									
							
									// Retrieve Incident Categories
									$incident_category = "";
									foreach($incident_orm->incident_category as $category)
									{
										$incident_category .= $category->category->category_title ."&nbsp;&nbsp;";
									}

									// Incident Status
									$incident_approved = $incident->incident_active;
									$incident_verified = $incident->incident_verified;
									
									// Get Edit Log
									$edit_count = $incident_orm->verify->count();
									$edit_css = ($edit_count == 0) ? "post-edit-log-gray" : "post-edit-log-blue";
									
									$edit_log  = "<div class=\"".$edit_css."\">"
										. "<a href=\"javascript:showLog('edit_log_".$incident_id."')\">".Kohana::lang('ui_admin.edit_log').":</a> (".$edit_count.")</div>"
										. "<div id=\"edit_log_".$incident_id."\" class=\"post-edit-log\"><ul>";
									
									foreach ($incident_orm->verify as $verify)
									{
										$edit_log .= "<li>".Kohana::lang('ui_admin.edited_by')." ".html::specialchars($verify->user->name)." : ".$verify->verified_date."</li>";
									}
									$edit_log .= "</ul></div>";

									// Get Any Translations
									$i = 1;
									$incident_translation  = "<div class=\"post-trans-new\">"
											. "<a href=\"" . url::base() . 'admin/reports/translate/?iid='.$incident_id."\">"
											. strtoupper(Kohana::lang('ui_main.add_translation')).":</a></div>";
											
									foreach ($incident_orm->incident_lang as $translation)
									{
										$incident_translation .= "<div class=\"post-trans\">"
											. Kohana::lang('ui_main.translation'). $i . ": "
											. "<a href=\"" . url::base() . 'admin/reports/translate/'. $translation->id .'/?iid=' . $incident_id . "\">"
											. text::limit_chars($translation->incident_title, 150, "...", TRUE)
											. "</a>"
											. "</div>";
									}
									?>
									<tr>
										<td class="col-1">
										</td>
										<td class="col-2">
											<div class="post">
												<h4>
													<a href="<?php echo url::site() . 'reports/view/' . $incident_id; ?>" class="more">
														<?php echo $incident_title; ?>
													</a>
												</h4>
												<p><?php echo $incident_description; ?>... 
													<a href="<?php echo url::base() . 'reports/view/' . $incident_id; ?>" class="more">
														<?php echo Kohana::lang('ui_main.more');?>
													</a>
												</p>
											</div>
											<ul class="info">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.location');?>: 
													<strong><?php echo html::specialchars($incident->location->location_name); ?></strong>, <strong><?php echo html::specialchars($country_name); ?></strong>
												</li>
												<li><?php echo Kohana::lang('ui_main.submitted_by');?> 
													<strong><?php echo html::specialchars($submit_by); ?></strong> via <strong><?php echo html::specialchars($submit_mode); ?></strong>
												</li>
											</ul>
											<ul class="links">
												<li class="none-separator"><?php echo Kohana::lang('ui_main.categories');?>:
													<strong><?php echo $incident_category;?></strong>
												</li>
											</ul>
											<?php
											
											
											// Action::report_extra_admin - Add items to the report list in admin
											Event::run('ushahidi_action.report_extra_admin', $incident);
											?>
										</td>
										<td class="col-3"><?php echo $incident_date; ?></td>
										<td class="col-4">
											<ul>

												<?php if (admin::permissions($user, "reports_edit")){?>
														<li class="none-separator"><a href="<?php echo url::base(). 'admin/reports/edit/'.$incident_id;?>"><?php echo Kohana::lang('ui_main.edit')?></a></li>
												<?php }?>
											</ul>
										</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				<?php print form::close(); ?>				
			</div>

=== About ===
name: User Postal Code
website: http://ethertontech.com
description: Allows users to add their postal code as part of their user profile
version: 1.0
requires: 2.4
tested up to: 2.4
author: John Etherton
author website: http://ethertontech.com

== Description ==
Lets users add their postal code as part of their user profile

== Installation ==
1. Copy the entire /userpostalcode/ directory into your /plugins/ directory.
2. Activate the plugin.
3. This plugin requires non-standard event hooks for version 2.4. You'll need to add the following:
	to /application/controllers/login.php around line 246 add:
		Event::run('ushahidi_action.users_add_login_form', $post);
	to /application/controllers/login.php around line 270 add: 
		Event::run('ushahidi_action.user_edit', $user); 
	to /themes/default/views/login.php (or your custom theme login.php file) around line 199 add:
		<?php Event::run('ushahidi_action.login_new_user_form');?>
	to /application/controllers/admin/profile.php around line 69 add:
		Event::run('ushahidi_action.profile_post_admin', $post);
	to /application/controllers/admin/profile.php around line 82 add: 
		Event::run('ushahidi_action.profile_edit_admin', $user);
	to /application/controllers/members/profile.php around line 77 add:
		Event::run('ushahidi_action.profile_post_member', $post);
	to /application/controllers/members/profile.php around line 11 add: 
		Event::run('ushahidi_action.profile_edit_member', $user);
		

== Changelog ==
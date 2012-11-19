=== About ===
name: Score
website: http://ethertontech.com
description: Lets users vote up and down and idea, plus keeps tabs on users and how active they are
version: 1.0
requires: 2.4
tested up to: 2.4
author: John Etherton
author website: http://ethertontech.com

== Description ==
Lets users vote up and down and idea, plus keeps tabs on users and how active they are. Shows results for all-time and for the current month, resetting each month. Shows the highest voted ideas and the most active users on the home page 

== Installation ==
1. Copy the entire /score/ directory into your /plugins/ directory.
2. Activate the plugin.
3. On line 73 of /hooks/score.php, set the jquery selector of the DOM element that you want to replace with the Score UI on reports/view/N pages
		

== Changelog ==
2012-11-19 - Etherton - Made it so you have to set the selector of the DOM element you want to replace with the Score UI when viewing reports
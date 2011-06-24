<?php
/*
 * Facts Plugin for WolfCMS <http://www.wolfcms.org>
 * Copyright (C) 2011 Shannon Brooks <shannon@brooksworks.com>
 */

//	security measure
if (!defined('IN_CMS')) { exit(); }

Plugin::setInfos(array(
	'id'          => 'facts',
	'title'       => __('Facts'),
	'description' => __('Provides interface to manage informational facts in a central location.'),
	'version'     => '0.0.2',
	'license'     => 'GPL',
	'author'      => 'Shannon Brooks',
	'website'     => 'http://www.brooksworks.com/',
	'require_wolf_version' => '0.7.2'
));

//	define paths
define ('FACTS_PLUGIN_ROOT',CORE_ROOT.'/plugins/facts');

//	watch for requests
Observer::observe('page_requested', 'facts_catch_click');

// Add the plugin's tab and controller
Plugin::addController('facts', __('Facts'),'facts_view,facts_new,facts_edit,facts_delete,facts_settings');

// Load the class into the system.
AutoLoader::addFile('Fact', CORE_ROOT.'/plugins/facts/Fact.php');

// redirect urls already set up
function facts_catch_click($args) {

	//	check for fact click
	if (preg_match('#^/fact-supporting-info/(\d+)$#i',$args,$matches)) {
		
		//	update the click count
		$id = (int)$matches[1];
		if (!$fact = Fact::findById($id)) return $args;
		$fact->clicks++;
		$fact->save();
		
		//	redirect to the requested url
		header ('HTTP/1.1 301 Moved Permanently', true);
		header ('Location: '.$fact->url);

		exit;
	}
	
	//	no click so keep going
	return $args;
}

//	output a link by id
function getfact($which) {

	//	check to see if we should call out facts on the page
	$callout = isset($_GET['showfacts']) ? true : false;
	
	//	get record
	if (is_numeric($which)) {
		if (!$fact = Fact::findById($which)) return '<span class="fact-broken" title="broken fact"'.($callout ? ' style="background:#FFCFCF;" ' : '' ).'>[fact id('.$which.') not found]</span>';
	} else {
		if (!$fact = Fact::findByName($which)) return '<span class="fact-broken" title="broken fact"'.($callout ? ' style="background:#FFCFCF;" ' : '' ).'>[fact "'.$which.'" not found]</span>';
	}
	
	//	return fact
	return '<span class="fact" title="Fact #'.$fact->id.' | '.$fact->name.'"'.($callout ? ' style="background:#A1DFC1;" ' : '' ).'>'.$fact->data.'</span>'.(!empty($fact->url) ? '<sup><a class="fact-link" href="/fact-supporting-info/'.$fact->id.'" target="_blank" rel="nofollow">**</a></sup>' : '');
}



?>
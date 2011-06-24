<?php
/*
 * Facts Plugin for WolfCMS <http://www.wolfcms.org>
 * Copyright (C) 2011 Shannon Brooks <shannon@brooksworks.com>
 */

//	security measure
if (!defined('IN_CMS')) { exit(); }

//	include the Installer helper
use_helper('Installer');

//	only support MySQL
$driver = Installer::getDriver();
if ( $driver != 'mysql' ) Installer::failInstall( 'facts', __('Only MySQL is supported!') );

//	get plugin version
$version = Plugin::getSetting('version', 'facts');

switch ($version) {

	//	no version found so we do a clean install
	default:
	
		//	sanity check to make sure we are really dealing with a clean install
		if ($version !== false) Installer::failInstall( 'facts', __('Unknown Version!') );
		
		//	create tables
		
		$facts_table = TABLE_PREFIX . 'facts';
		$facts_table_sql =<<<SQL
			CREATE TABLE IF NOT EXISTS {$facts_table}  (
				`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
				`name` VARCHAR( 255 ) NULL DEFAULT NULL ,
				`data` MEDIUMTEXT NULL DEFAULT NULL ,
				`url` VARCHAR( 255 ) NULL DEFAULT NULL ,
				`clicks` INT( 11 ) NOT NULL DEFAULT '0' ,
				`created` DATETIME NULL DEFAULT NULL ,
				`updated` DATETIME NULL DEFAULT NULL ,
				PRIMARY KEY ( `id` )
			) ENGINE=MYISAM DEFAULT CHARSET=utf8
SQL;
		if ( ! Installer::createTable($facts_table,$facts_table_sql) ) Installer::failInstall( 'facts', __('Could not create table 1 of 1.') );
		
		//	create new permissions
		if ( ! Installer::createPermissions('facts_view,facts_new,facts_edit,facts_delete') ) Installer::failInstall( 'facts' );

		//	create new roles
		if ( ! Installer::createRoles('facts admin,facts editor,facts user') ) Installer::failInstall( 'facts' );
			
		//	assign permissions
		//	note: admin_view is needed in case they don't have any other permissions, otherwise they won't be able to log in to admin interface
		if ( ! Installer::assignPermissions('administrator','facts_view,facts_new,facts_edit,facts_delete') ) Installer::failInstall( 'facts' );
		if ( ! Installer::assignPermissions('editor','facts_view') ) Installer::failInstall( 'facts' );
		if ( ! Installer::assignPermissions('facts admin','admin_view,facts_view,facts_new,facts_edit,facts_delete') ) Installer::failInstall( 'facts' );
		if ( ! Installer::assignPermissions('facts editor','admin_view,facts_view,facts_new,facts_edit,facts_delete') ) Installer::failInstall( 'facts' );
		if ( ! Installer::assignPermissions('facts user','admin_view,facts_view') ) Installer::failInstall( 'facts' );
		
		//	setup plugin settings
		$settings = array(
			'version'		=>	'0.0.2'
		);
		if ( ! Plugin::setAllSettings($settings, 'facts') ) Installer::failInstall( 'facts', __('Unable to store plugin settings!') );
			
		Flash::set('success', __('Successfully installed Facts plugin.'));
		
		//	we must exit the switch so upgrades are not applied to new installation (they should already be integrated for new installs)
		break;
		
	//	upgrade 0.0.2 to 0.0.3
	case '0.0.2':
		// nothing here because we're still on 0.0.2, if we were on 0.0.1 and this was 0.0.3 upgrades would process in order
	
}
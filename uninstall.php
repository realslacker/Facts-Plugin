<?php
/*
 * Facts Plugin for WolfCMS <http://www.wolfcms.org>
 * Copyright (C) 2011 Shannon Brooks <shannon@brooksworks.com>
 */

//	security measure
if (!defined('IN_CMS')) { exit(); }

//	include the Installer helper
use_helper('Installer');

if ( ! Installer::removeTable(TABLE_PREFIX.'facts') ) Installer::failUninstall( 'facts', __('Could not remove table 1 of 1.') );

if ( ! Installer::removePermissions('facts_view,facts_new,facts_edit,facts_delete') ) Installer::failUninstall( 'facts' );

if ( ! Installer::removeRoles('facts admin,facts editor,facts user') ) Installer::failUninstall( 'facts' );

if ( ! Plugin::deleteAllSettings('facts') ) Installer::failUninstall( 'facts', __('Could not remove plugin settings.') );

Flash::set('success', __('Successfully uninstalled plugin.'));
redirect(get_url('setting'));
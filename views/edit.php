<?php
/*
 * Facts Plugin for WolfCMS <http://www.wolfcms.org>
 * Copyright (C) 2011 Shannon Brooks <shannon@brooksworks.com>
 */

//	security measure
if (!defined('IN_CMS')) { exit(); }

?>
<h1><?=isset($id) ? __('Edit Fact') . " #{$id}: {$name}" : __('New Fact');?></h1>
<form method="post" enctype="multipart/form-data" action="<?=isset($id) ? get_url('plugin/facts/update/'.$id) : get_url('plugin/facts/create'); ?>">
	<fieldset style="padding: 0.5em;">
		<legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('Information'); ?></legend>
		<table class="fieldset" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="label"><label for="name"><?php echo __('Name:');?> </label></td>
				<td class="field"><input name="name" id="name" type="text" size="35" maxsize="255" value="<?=isset($name) ? $name : '';?>"/></td>
				<td class="help"><?php echo __('The fact name; be descriptive'); ?></td>
			</tr>
			<tr>
				<td class="label"><label for="data"><?php echo __('Data:');?> </label></td>
				<td class="field"><textarea name="data" id="data" cols="35" rows="4"><?=isset($data) ? $data : '';?></textarea></td>
				<td class="help"><?php echo __('This is the data that will be inserted into the page.'); ?></td>
			</tr>
			<tr>
				<td class="label"><label for="url"><?php echo __('Citation URL:');?> </label></td>
				<td class="field"><input name="url" id="url" type="text" size="35" maxsize="255" value="<?=isset($url) ? $url : '';?>"/></td>
				<td class="help"><?php echo __('Link to supporting information for fact.'); ?></td>
			</tr>
		</table>
	</fieldset>
	<p class="buttons">
		<input class="button" name="commit" type="submit" accesskey="s" value="<?php echo __('Save');?>" />
	</p>
</form>
<script type="text/javascript">
// <![CDATA[
function setConfirmUnload(on, msg) {
	window.onbeforeunload = (on) ? unloadMessage : null;
	return true;
}
function unloadMessage() {
	return '<?php echo __('You have modified this page.  If you navigate away from this page without first saving your data, the changes will be lost.'); ?>';
}
$(document).ready(function() {
	// Prevent accidentally navigating away
	$(':input').bind('change', function() { setConfirmUnload(true); });
	$('form').submit(function() { setConfirmUnload(false); return true; });
});
// ]]>
</script>
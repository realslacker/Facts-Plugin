<?php
/*
 * Facts Plugin for WolfCMS <http://www.wolfcms.org>
 * Copyright (C) 2011 Shannon Brooks <shannon@brooksworks.com>
 */

//	security measure
if (!defined('IN_CMS')) { exit(); }

function truncate_string($string,$max){
    if(strlen($string)>$max){
        $string = substr($string,0,$max);
        if (strrpos($string," ")!==false) $string = substr($string,0,strrpos($string," "));
        $string = $string."...";
    }
    return $string;
}


?>
<h1>Facts</h1>
<table id="files-list" class="index" cellpadding="0" cellspacing="0" border="0">
  <thead>
    <tr>
      <th class="name"><?=__('Name'); ?></th>
      <th class="name"><?=__('Data'); ?></th>
      <th class="id"><?=__('ID');?></th>
      <th class="link"><?=__('Link'); ?></th>
      <th class="clicks"><?=__('Clicks'); ?></th>
      <th class="action"><?=__('Action');?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($facts as $fact): ?>
	<tr class="<?=odd_even(); ?>">
		<td><a href="<?=get_url('plugin/facts/edit/'.$fact->id); ?>"><?=$fact->name; ?></a></td>
		<td><code title="<?=strip_tags($fact->data);?>"><?=truncate_string(strip_tags($fact->data),35);?></code></td>
		<td><code><?=$fact->id;?></code></td>
		<td><?=!empty($fact->url) ? "<a href=\"{$fact->url}\" target=\"_blank\" title=\"{$fact->name} Supporting Info\"><img src=\"".PLUGINS_URI."/facts/images/icon-open.png\" alt=\"view icon\" title=\"View Supporting Info\"></a>" : ''; ?></td>
		<td><code><?=$fact->clicks;?></code></td>
		<td>
			<a class="edit-link" href="<?=get_url('plugin/facts/edit/'.$fact->id); ?>"><img src="<?=PLUGINS_URI;?>/facts/images/icon-edit.png" alt="edit icon" title="Edit"></a>
			<a class="delete-link" href="<?=get_url('plugin/facts/delete/'.$fact->id); ?>"><img src="<?=PLUGINS_URI;?>/facts/images/icon-trash.png" alt="delete file icon" title="Delete"></a>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
<script type="text/javascript">
<!--
$(function(){

	$('.delete-link').click(function(e){
		if (!confirm("Are you sure you want to delete this fact?\nPress OK to delete this fact permenently.")) {
			e.preventDefault();
		}
	});

});
//-->
</script>
<?php
/*
 * Facts Plugin for WolfCMS <http://www.wolfcms.org>
 * Copyright (C) 2011 Shannon Brooks <shannon@brooksworks.com>
 */

//	security measure
if (!defined('IN_CMS')) { exit(); }

?>
<p class="button"><a href="<?=get_url('plugin/facts'); ?>"><img src="<?=PLUGINS_URI;?>/facts/images/list.png" align="middle" /><?php echo __('List'); ?></a></p>
<p class="button"><a href="<?=get_url('plugin/facts/add'); ?>"><img src="<?=PLUGINS_URI;?>/facts/images/new.png" align="middle" /><?php echo __('Add New'); ?></a></p>
<div class="box">
<h2><?php echo __('Facts Plugin');?></h2>
<p>
<?php echo __('Plugin Version').': '.Plugin::getSetting('version', 'facts'); ?>
</p>
<br />
<h2><?=__('Usage');?></h2>
<p><strong>Embed:</strong><br /><code style="font-size: 14px;">&lt;?=getfact($id);?&gt;</code></p>
<p><strong>Tips:</strong></p>
<ol style="margin-left: 20px;">
<li>Add ?showfacts=1 to any url, facts will be highlighted.</li>
<li>Hover over a fact on a page to see it's name.</li>
<li>Facts with a <em>Citation URL</em> will show up with a ** linking to the website.</li>
</ol>
</div>

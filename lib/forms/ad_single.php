<?php
	$link = get_post_meta($ad->ID, 'wdca_plugin_url', true);
	$link = $link ? $link : '#';
	$appearance = get_post_meta($ad->ID, 'wdca_appearance', true);
	$theme_class = @$appearance['strip_class'] ? '' : 'wdca_custom_ad';
	$appearance_classes = @$appearance['strip_class'] ? '' : $appearance_classes;
?>
<div class="<?php echo $system_classes;?> <?php echo $theme_class;?> <?php echo $appearance_classes;?>" <?php if ($forced) { echo 'data-forced="yes"'; } ?> >
	<div class="wdca_stars"></div>
	<?php if (!@$appearance['hide_title']) { ?>
	<h4><?php echo $msg_header; ?>
		<a href="<?php echo $link;?>" <?php echo $link_target; ?> ><span class="wdca_title"><?php echo $ad->post_title; ?></span></a>
	</h4>
	<?php } ?>
	<?php if (!@$appearance['hide_body']) { ?>
	<div class="wdca_ad_body_full">
		<?php echo $ad->post_content; ?>
	</div>
	<?php } ?>
	<?php if (!@$appearance['hide_footer']) { ?>
	<?php echo $msg_footer; ?><a href="<?php echo $link;?>" <?php echo $link_target; ?> class="wdca_read_more button wdca_button"><span><?php echo $msg_link;?></span></a>
	<?php } ?>
</div>
<?php
	$link = get_post_meta($ad->ID, 'wdca_plugin_url', true);
	$link = $link ? $link : '#';
	$appearance = get_post_meta($ad->ID, 'wdca_appearance', true);
	$theme_class = @$appearance['strip_class'] ? '' : 'wdca_custom_ad';
	$appearance_classes = @$appearance['strip_class'] ? '' : $appearance_classes;
	$thumb_id = function_exists('get_post_thumbnail_id') ? get_post_thumbnail_id($ad->ID) : false;
	$thumbnail = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'thumbnail') : false;
?>
<div class="<?php echo $system_classes;?> <?php echo $theme_class;?> <?php echo $appearance_classes;?>" <?php if (!empty($forced)) { echo 'data-forced="yes"'; } ?> >
	<div class="wdca_ad_info">
	<?php if (empty($appearance['hide_title'])) { ?>
		<div class="wdca_ad_featured"><?php echo $msg_header; ?>&raquo;</div>
		<div class="wdca_ad_title">
			<a href="<?php echo $link;?>" <?php echo $link_target; ?> ><span class="wdca_title"><?php echo $ad->post_title; ?></span></a>
		</div>
	<?php } ?>
		
	<?php if (empty($appearance['hide_body'])) { ?>
		<div class="wdca_ad_body_full">
			<?php echo $ad->post_content; ?>
		</div>
	<?php } ?>
	</div>

<?php if (empty($appearance['hide_footer'])) { ?>
	<?php echo $msg_footer; ?>

	<?php if ($thumbnail && !empty($thumbnail[0])) { ?>
	<div class="wdca_ad_thumb">
		<a href="<?php echo $link; ?>" <?php echo $link_target; ?> ><img src="<?php echo $thumbnail[0]; ?>" border="0"></a>
	</div>
	<?php } ?>

	<div class="wdca_ad_button">
		<a href="<?php echo $link; ?>" <?php echo $link_target; ?> class="wdca_ad_cta"><?php echo $msg_link;?></a>
	</div>

	<div class="wdca_ad_ribbon">
		<a href="<?php echo $link; ?>" <?php echo $link_target; ?> ><img src="<?php echo WDCA_PLUGIN_URL . '/img/ribbon.png'; ?>" border="0"></a>
	</div>
<?php } ?>
</div>
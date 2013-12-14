<?php
	$link = get_post_meta($ad->ID, 'wdca_plugin_url', true);
	$link = $link ? $link : '#';
	if (preg_match('/^www\./', $link)) $link = esc_url($link);
	$appearance = get_post_meta($ad->ID, 'wdca_appearance', true);
	$theme_class = @$appearance['strip_class'] ? '' : Wdca_CustomAd::wrap('custom_ad');
	$appearance_classes = @$appearance['strip_class'] ? '' : $appearance_classes;
?>
<div class="<?php echo $system_classes;?> <?php echo $theme_class;?> <?php echo $appearance_classes;?>" <?php if (!empty($forced)) { echo 'data-forced="yes"'; } ?> >
	<div class="<?php echo Wdca_CustomAd::wrap('stars'); ?>"></div>
	<?php if (!@$appearance['hide_title']) { ?>
	<h4><?php echo $msg_header; ?>
		<a href="<?php echo $link;?>" <?php echo $link_target; ?> ><span class="<?php echo Wdca_CustomAd::wrap('title'); ?>"><?php echo $ad->post_title; ?></span></a>
	</h4>
	<?php } ?>
	<?php if (!@$appearance['hide_body']) { ?>
	<div class="<?php echo Wdca_CustomAd::wrap('ad_body_full'); ?>">
		<?php echo (
			apply_filters('wdca_the_content', $ad->post_content)
		); ?>
	</div>
	<?php } ?>
	<?php if (!@$appearance['hide_footer']) { ?>
	<?php 
		if (!empty($msg_link)) { 
			?><a href="<?php echo $link;?>" <?php echo $link_target; ?> class="<?php echo Wdca_CustomAd::wrap('read_more'); ?> button <?php echo Wdca_CustomAd::wrap('button'); ?>"><span><?php echo $msg_link;?></span></a><?php 
		}
		echo '<p class="' . Wdca_CustomAd::wrap('footer') . '">' . $msg_footer . '</p>';
	} ?>
</div>
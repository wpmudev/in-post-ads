<div class="wrap">
	<h2><?php echo $title; ?></h2>

	<form action="" method="post">

	<?php settings_fields($option_key . '-options'); ?>
	<?php do_settings_sections($option_key . '-options'); ?>
	<p class="submit">
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
	</p>
	</form>

</div>

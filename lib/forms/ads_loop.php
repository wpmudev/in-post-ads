<div id="wdca_ads_root">
<?php
if ($data) foreach ($data as $ad) {
	$appearance_classes = '';
	$system_classes = 'wdca_ad_item wdca_not_placed';

	include $this->_wdca->get_ad_template();
}
?>
</div>
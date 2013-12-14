<?php
echo defined('WDCA_FLAG_FORCE_NON_INDEXING_WRAPPER') && WDCA_FLAG_FORCE_NON_INDEXING_WRAPPER
	? '<script type="text/template" id="' . Wdca_CustomAd::wrap('ads_root') . '">'
	: '<div id="' . Wdca_CustomAd::wrap('ads_root') . '">'
;
if ($data) foreach ($data as $ad) {
	$appearance_classes = '';
	$system_classes = Wdca_CustomAd::wrap('ad_item') . ' ' . Wdca_CustomAd::wrap('not_placed');

	include $this->_wdca->get_ad_template();
}
echo defined('WDCA_FLAG_FORCE_NON_INDEXING_WRAPPER') && WDCA_FLAG_FORCE_NON_INDEXING_WRAPPER
	? '</script>'
	: '</div>'
;
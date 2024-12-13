<?php

class ModelExtensionModulePMPDataSourcesGlobalCustom extends Model {
	
	public function getData($setting) {
		$product_data = [];

		$product_data = array_slice($setting['products'], 0, $setting['limit']);

		return $product_data;
	}
}

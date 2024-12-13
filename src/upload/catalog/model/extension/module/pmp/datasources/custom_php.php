<?php

class ModelExtensionModulePMPDataSourcesCustomPHP extends Model {
	
	public function getData($setting) {
		if (isset($setting['php'])) {
			return eval($setting['php']);
		}
	}
}
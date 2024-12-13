<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ModelExtensionModulePMPDataSourcesCustomSQL extends Model {
	
	public function getData($setting) {
		$product_data = [];

		$query = $this->db->query($setting['sql']);
		foreach ($query->rows as $result) {
			$product_data[] = $result['product_id'];
		}

		return $product_data;
	}
}
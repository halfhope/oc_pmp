<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

require_once DIR_APPLICATION . "model/extension/module/pmp/groups/arg.php";

class ModelExtensionModulePMPDataSourcesARGMostViewed extends ModelExtensionModulePMPGroupsARG {
	
	public function getData($setting) {
		$product_data = [];

		list($fields, $join, $where, $sort_order) = $this->buildProductQuery($setting);

		$sql = "SELECT p.product_id " . $fields . "
		FROM " . DB_PREFIX . "product p 
			" . $join . " 
		WHERE 
			" . $where . " 
			AND p.viewed > 0
		GROUP BY p.product_id 
		ORDER BY p.viewed DESC, " . $sort_order;

		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$product_data[] = $result['product_id'];
		}

		return $product_data;
	}
}

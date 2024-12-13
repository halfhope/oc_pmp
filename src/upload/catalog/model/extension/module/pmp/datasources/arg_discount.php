<?php

require_once DIR_APPLICATION . "model/extension/module/pmp/groups/arg.php";

class ModelExtensionModulePMPDataSourcesARGDiscount extends ModelExtensionModulePMPGroupsARG {
	
	public function getData($setting) {
		$product_data = [];
		list($fields, $join, $where, $sort_order) = $this->buildProductQuery($setting);

		$sql = "SELECT p.product_id " . $fields . "
		FROM " . DB_PREFIX . "product p 
			LEFT JOIN " . DB_PREFIX . "product_discount pd2 ON (p.product_id = pd2.product_id) 
			" . $join . " 
		WHERE  
			" . $where . " 
			AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' 
			AND pd2.quantity > 0
			AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW()))
		GROUP BY p.product_id 
		ORDER BY pd2.priority ASC, " . $sort_order;

		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$product_data[] = $result['product_id'];
		}

		return $product_data;
	}
}

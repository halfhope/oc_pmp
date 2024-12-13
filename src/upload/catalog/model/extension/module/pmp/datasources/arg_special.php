<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

require_once DIR_APPLICATION . "model/extension/module/pmp/groups/arg.php";

class ModelExtensionModulePMPDataSourcesARGSpecial extends ModelExtensionModulePMPGroupsARG {
	
	public function getData($setting) {
		$product_data = [];

		list($fields, $join, $where, $sort_order) = $this->buildProductQuery($setting);

		$sql = "SELECT p.product_id " . $fields . "
		FROM " . DB_PREFIX . "product p 
			LEFT JOIN " . DB_PREFIX . "product_special ps ON (p.product_id = ps.product_id) 
			" . $join . " 
		WHERE  
			" . $where . " 
			AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' 
			AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
		GROUP BY p.product_id 
		ORDER BY ps.priority ASC, " . $sort_order;

		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$product_data[] = $result['product_id'];
		}

		return $product_data;
	}
}

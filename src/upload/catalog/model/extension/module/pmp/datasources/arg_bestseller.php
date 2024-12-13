<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

require_once DIR_APPLICATION . "model/extension/module/pmp/groups/arg.php";

class ModelExtensionModulePMPDataSourcesARGBestseller extends ModelExtensionModulePMPGroupsARG {
	
	public function getData($setting) {
		$product_data = [];

		list($fields, $join, $where, $sort_order) = $this->buildProductQuery($setting);

		$sql = "SELECT p.product_id, SUM(op.quantity) as total " . $fields . "
		FROM " . DB_PREFIX . "product p 
			LEFT JOIN " . DB_PREFIX . "order_product op ON (p.product_id = op.product_id) 
			LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) 
			" . $join . " 
		WHERE 
			" . $where . " 
			AND o.order_status_id > '0' 
		GROUP BY p.product_id 
		ORDER BY total DESC, " . $sort_order;

		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$product_data[] = $result['product_id'];
		}

		return $product_data;
	}
}

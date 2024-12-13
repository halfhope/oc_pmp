<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

require_once DIR_APPLICATION . "model/extension/module/pmp/groups/arg.php";

class ModelExtensionModulePMPDataSourcesARGDiscussed extends ModelExtensionModulePMPGroupsARG {
	
	public function getData($setting) {
		$product_data = [];

		list($fields, $join, $where, $sort_order) = $this->buildProductQuery($setting);

		$sql = "SELECT p.product_id, COUNT(r1.product_id) AS review_count " . $fields . "
		FROM " . DB_PREFIX . "product p 
			LEFT JOIN " . DB_PREFIX . "review r1 ON (r1.product_id = p.product_id) 
			" . $join . " 
		WHERE 
			" . $where . " 
			AND r1.status = '1'
		GROUP BY p.product_id 
		ORDER BY review_count DESC, " . $sort_order;

		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$product_data[] = $result['product_id'];
		}

		return $product_data;
	}
}

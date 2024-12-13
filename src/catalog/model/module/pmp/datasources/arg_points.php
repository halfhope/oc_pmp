<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Catalog\Model\Extension\Pmp\Module\Pmp\Datasources;
class ARGPoints extends \Opencart\Catalog\Model\Extension\Pmp\Module\Pmp\Groups\ARG {

	public function getData($setting) {
		$product_data = [];

		list($fields, $join, $where, $sort_order) = $this->buildProductQuery($setting);

		$sql = "SELECT p.product_id, pr.points " . $fields . "
		FROM " . DB_PREFIX . "product p 
			LEFT JOIN " . DB_PREFIX . "product_reward pr ON (p.product_id = pr.product_id) 
			" . $join . " 
		WHERE  
			" . $where . " 
			AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' 
			AND pr.points > 0
		GROUP BY p.product_id 
		ORDER BY pr.points DESC, " . $sort_order;

		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$product_data[] = $result['product_id'];
		}

		return $product_data;
	}
}

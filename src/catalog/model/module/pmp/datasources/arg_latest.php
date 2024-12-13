<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Catalog\Model\Extension\Pmp\Module\Pmp\Datasources;
class ARGLatest extends \Opencart\Catalog\Model\Extension\Pmp\Module\Pmp\Groups\ARG {

	public function getData($setting) {
		$product_data = [];

		list($fields, $join, $where, $sort_order) = $this->buildProductQuery($setting);

		$sql = "SELECT p.product_id " . $fields . "
		FROM " . DB_PREFIX . "product p 
			" . $join . " 
		WHERE  
			" . $where . " 
		ORDER BY p.date_added DESC, " . $sort_order;

		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$product_data[] = $result['product_id'];
		}

		return $product_data;
	}
}

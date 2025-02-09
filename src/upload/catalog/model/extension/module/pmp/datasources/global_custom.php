<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ModelExtensionModulePMPDataSourcesGlobalCustom extends Model {
	
	public function getData($setting) {
		$product_data = [];
		
		if (!isset($setting['products']) || empty($setting['products'])) {
			return $product_data;
		}
		
		$setting['product_ids'] = array_keys($setting['products']);

		list($fields, $join, $where, $sort_order, $limit) = $this->buildProductQuery($setting);

		$sql = "SELECT p.product_id " . $fields . "
		FROM " . DB_PREFIX . "product p 
			" . $join . " 
		WHERE 
			" . $where . " 
		" . (!empty($sort_order) ? 'ORDER BY ' . $sort_order : '' ) . $limit;

		$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$product_data[] = $result['product_id'];
		}

		return $product_data;
	}

	public function buildProductQuery($setting) {
		// checkbox
		$setting['invert'] = (isset($setting['invert']) ? $setting['invert'] : false);
		
		// migration
		$setting['stock_status'] = (isset($setting['stock_status']) ? $setting['stock_status'] : []);
		$setting['quantity_expression'] = isset($setting['quantity_expression']) ? htmlspecialchars_decode($setting['quantity_expression']) : '>';
		$setting['quantity'] = isset($setting['quantity']) ? $setting['quantity'] : 0;

		$config_customer_group_id = (int) $this->config->get('config_customer_group_id');
		$config_language_id = (int) $this->config->get('config_language_id');
		$config_store_id = (int) $this->config->get('config_store_id');

		$fields = [];
		if ($setting['sort'] == 'rating') {
			$fields[] = "(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating";
		}
		if ($setting['sort'] == 'p.price') {
			$fields[] = "(SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . $config_customer_group_id . "' AND pd2.quantity > 0 AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount";
			$fields[] = "(SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . $config_customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
		}
		$fields = ($fields) ? (', ' . implode(', ', $fields)) : '';

		$join[] = "LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";
		$join[] = "LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
		$join = implode(' ', array_filter($join));

		$where[] = "p.product_id IN(" . implode(',', $setting['product_ids']) . ")";
		$where[] = "p.date_available <= NOW()";
		$where[] = "pd.language_id = '" . $config_language_id . "'";
		$where[] = "p2s.store_id = '" . $config_store_id . "'";
		$where[] = "p.status = '1'";
		$where[] = "p.quantity " . $setting['quantity_expression'] . " " . (int) $setting['quantity'];
		
		if ($setting['stock_status']) {
			$where[] = "p.stock_status_id IN (" . implode(',', $setting['stock_status']) . ")"; 
		}

		$where = implode(' AND ', array_filter($where));

		if (isset($setting['sort']) && $setting['sort'] === 'as_is') {
			$sort_order = "FIELD (p.product_id, " . implode(',', $setting['product_ids']) . ")";
		} else {
			$sort_order = $this->getSortOrder($setting);
		}

		$limit = $this->getLimit($setting);

		return [$fields, $join, $where, $sort_order, $limit];
	}

	public function getSortOrder($data) {
		
		$sql = '';
		
		if (isset($data['sort'])) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
			} else {
				$sql .= " " . $data['sort'];
			}
		} else {
			$sql .= " p.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}

		return $sql;
	}

	public function getLimit($data) {
		
		$sql = '';
		
		if (isset($data['start']) || isset($data['limit'])) {
			if (!isset($data['start']) || $data['start'] < 0) {
				$data['start'] = 0;
			}

			if (!isset($data['limit']) || $data['limit'] < 1) {
				$data['limit'] = 5;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		
		return $sql;
	}
}

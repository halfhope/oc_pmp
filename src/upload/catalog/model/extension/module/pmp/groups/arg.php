<?php

class ModelExtensionModulePMPGroupsARG extends Model {

	protected $use_main_category_id = false;
	protected $category_ids = [];
	
	public function getCachePrecomputedParams(&$setting) {
		if ($setting['mode'] == 2) {
			$setting['precomputed_categories'] = $this->getCategoryIds($setting); 
			$setting['precomputed_manufacturers'] = $this->getManufacturerId($setting);

			$index = hash('crc32b', json_encode([
				'precomputed_categories' => $setting['precomputed_categories'], 
				'precomputed_manufacturers' => $setting['precomputed_manufacturers']
			]));
		} else {
			$index = 1;
		}
		return $index;
	}

	public function getCategoryIds($consider_categories = true) {
		$category_ids = [];
		if (isset($this->request->get['path'])) {
			$path = explode('_', $this->request->get['path']);
			foreach ($path as $key => $value) {
				$category_ids[] = (int) $value;
			}
		} elseif (isset($this->request->get['category_id'])) {
			$category_ids[] = (int) $this->request->get['category_id'];
		} elseif ($this->request->get['route'] == 'product/product' && $consider_categories) {

			if (isset($this->request->get['product_id'])) {
				$product_id = (int) $this->request->get['product_id'];
				$field_name = ($this->use_main_category_id) ? 'main_category_id' : 'category_id';
				$query = $this->db->query("SELECT `$field_name` FROM " . DB_PREFIX . "product_to_category WHERE product_id = " . (int) $product_id);
				foreach ($query->rows as $key => $value) {
					$category_ids[] = (int) $value[$field_name];
				}
			}

		}
		return $category_ids;
	}

	public function getManufacturerId($consider_manufacturers) {
		$manufacturer_id = 0;
		if (isset($this->request->get['manufacturer_id'])) {
			$manufacturer_id = (int) $this->request->get['manufacturer_id'];
		} elseif ($this->request->get['route'] == 'product/product' && $consider_manufacturers) {
			$product_id      = (int) $this->request->get['product_id'];
			$query           = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "product WHERE product_id = " . (int) $product_id);
			$manufacturer_id = (int) $query->num_rows ? $query->row['manufacturer_id'] : 0;
		}
		return $manufacturer_id;
	}

	public function buildQueryAbsoluteProductCategories($selected_categories, $invert = false) {
		$join = ' LEFT JOIN ' . DB_PREFIX . 'product_to_category p2c ON (p.product_id = p2c.product_id) ';
		$field_name = ($this->use_main_category_id) ? 'main_category_id' : 'category_id';
		$not = ($invert ? ' NOT ' : ''); 
		$where = ' p2c.' . $field_name . $not . ' IN (' . implode(',', $selected_categories) . ') ';
		return [$join, $where];
	}

	public function buildQueryAbsoluteProductManufacturers($selected_manufacturers, $invert = false) {
		$where = null;
		if (isset($selected_manufacturers) && !empty($selected_manufacturers)) {
			$not = ($invert ? ' NOT ' : ''); 
			$where = ' p.manufacturer_id ' . $not . ' IN (' . implode(',', $selected_manufacturers) . ') ';
		}
		return $where;
	}

	public function buildQueryRelativeProductCategories($setting) {
		$join = '';
		$where = '';
		$category_ids = (isset($setting['precomputed_categories']) ? $setting['precomputed_categories'] : $this->getCategoryIds($setting['consider_categories']));
		$category_id = end($category_ids);
		if ($category_id > 0) {
			$join = ' LEFT JOIN ' . DB_PREFIX . 'product_to_category p2c ON (p.product_id = p2c.product_id) ';
			$field_name = ($this->use_main_category_id) ? 'main_category_id' : 'category_id';
			$where = ' p2c.' . $field_name . ' = ' . (int) $category_id;
		}
		return [$join, $where];
	}

	public function buildQueryRelativeProductManufacturers($setting) {
		$where = '';
		$manufacturer_id = (isset($setting['precomputed_manufacturers']) ? $setting['precomputed_manufacturers'] : $this->getManufacturerId($setting['consider_manufacturers']));
		if ($manufacturer_id) {
			$where = ' p.manufacturer_id = ' . (int) $manufacturer_id;
		}
		return $where;
	}

	public function buildProductQuery($setting) {
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

		switch ($setting['mode']) {
			case 1: // absolute
				if (isset($setting['selected_categories'])) {
					list($join[], $where[]) = $this->buildQueryAbsoluteProductCategories($setting['selected_categories'], $setting['invert']);
				}
				if (isset($setting['selected_manufacturers'])) {
					$where[] = $this->buildQueryAbsoluteProductManufacturers($setting['selected_manufacturers'], $setting['invert']);
				}
			break;
			case 2: // relative
				$where[] = $this->buildQueryRelativeProductManufacturers($setting);
				list($join[], $where[]) = $this->buildQueryRelativeProductCategories($setting);
			break;
		}

		$join[] = "LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";
		$join[] = "LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
		$join = implode(' ', array_filter($join));

		$where[] = "p.date_available <= NOW()";
		$where[] = "pd.language_id = '" . $config_language_id . "'";
		$where[] = "p2s.store_id = '" . $config_store_id . "'";
		$where[] = "p.status = '1'";
		$where[] = "p.quantity > 1";
		$where = implode(' AND ', array_filter($where));

		$sort_order = $this->getSortOrder($setting);

		return [$fields, $join, $where, $sort_order];
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

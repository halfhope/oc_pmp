<?php

class ControllerExtensionModulePMPGroupsARG extends Controller {

	protected $_group_route = 'extension/module/pmp/groups/arg';
	
    public function getGroupFormData($module_id = 0) {

		$data = $this->load->language($this->_group_route);

		$this->load->model('catalog/category');
        $this->load->model('catalog/manufacturer');

        $data['categories']    = $this->model_catalog_category->getCategories(0);
        $data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();

        $data['sorts'] = [
			'p.sort_order' => $this->language->get('text_sort_default'),
			'p.model' => $this->language->get('text_sort_model'),
			'pd.name' => $this->language->get('text_sort_name'),
			'p.price' => $this->language->get('text_sort_price'),
			'p.quantity' => $this->language->get('text_sort_quantity'),
			'p.rating' => $this->language->get('text_sort_rating'),
			'p.date_added' => $this->language->get('text_sort_added')
		];

		$data['orders'] = [
			'ASC' => $this->language->get('text_order_asc'),
			'DESC' => $this->language->get('text_order_desc')
		];

		$module_info = [];
		if ($module_id !== 0) {
			$this->load->model('extension/module');
			$module_info = $this->model_extension_module->getModule($module_id);
		}

		if (isset($module_info['mode'])) {
			$data['mode'] = $module_info['mode'];
		} else {
			$data['mode'] = 1;
		}

		if (isset($module_info['selected_categories'])) {
			if (!empty($module_info['selected_categories'])) {
				$data['selected_categories'] = $module_info['selected_categories'];
			} else {
				$data['selected_categories'] = [];
			}
		} else {
			$data['selected_categories'] = [];
		}

		if (isset($module_info['selected_manufacturers'])) {
			if (!empty($module_info['selected_manufacturers'])) {
				$data['selected_manufacturers'] = $module_info['selected_manufacturers'];
			} else {
				$data['selected_manufacturers'] = [];
			}
		} else {
			$data['selected_manufacturers'] = [];
		}

		if (isset($module_info['consider_categories'])) {
			$data['consider_categories'] = $module_info['consider_categories'];
		} else {
			$data['consider_categories'] = 1;
		}

		if (isset($module_info['consider_manufacturers'])) {
			$data['consider_manufacturers'] = $module_info['consider_manufacturers'];
		} else {
			$data['consider_manufacturers'] = 1;
		}

		if (isset($module_info['invert'])) {
			$data['invert'] = $module_info['invert'];
		} else {
			$data['invert'] = false;
		}

		if (isset($module_info['sort'])) {
			$data['sort'] = $module_info['sort'];
		} else {
			$data['sort'] = 'p.sort_order';
		}

		if (isset($module_info['order'])) {
			$data['order'] = $module_info['order'];
		} else {
			$data['order'] = 'ASC';
		}
		
        return $data;
    }
}

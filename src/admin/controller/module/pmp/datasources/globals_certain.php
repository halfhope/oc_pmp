<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Admin\Controller\Extension\Pmp\Module\Pmp\Datasources;
class GlobalsCertain extends \Opencart\Admin\Controller\Extension\Pmp\Module\Pmp\Groups\Globals {

	private $_route = 'extension/pmp/module/pmp/datasources/globals_certain';

	public function getForm($module_id = 0) {
		
		$data = $this->load->language($this->_route);
		
		$this->load->model('localisation/stock_status');

		$data['stock_statuses']		= $this->model_localisation_stock_status->getStockStatuses([]);
		$data['qty_expressions']	= ['<', '<=', '=', '>=', '>'];

		$data['sorts'] = [
			'as_is' => $this->language->get('text_sort_order_as_is'),
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
			$this->load->model('setting/module');
			$module_info = $this->model_setting_module->getModule($module_id);
		}

		if (isset($module_info['products'])) {
			if (!empty($module_info['products'])) {
				$products = $module_info['products'];
			} else {
				$products = [];
			}
		} else {
			$products = [];
		}

		$data['products'] = [];

		$this->load->model('catalog/product');

		foreach ($products as $product_id => $product_form_data) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			
			if ($product_info) {
				$data['products'][] = [
					'id' => $product_info['product_id'],
					'sort_order' => $product_form_data['sort_order'],
					'name' => html_entity_decode($product_info['name']),
					'selected' => true
				];
			}
		}

		$data['autocomplete'] = html_entity_decode($this->url->link($this->_route . '|product_autocomplete', 'user_token=' . $this->session->data['user_token'], true));

		if (isset($module_info['quantity'])) {
			$data['quantity'] = $module_info['quantity'];
		} else {
			$data['quantity'] = 0;
		}

		if (isset($module_info['quantity_expression'])) {
			$data['quantity_expression'] = htmlspecialchars_decode($module_info['quantity_expression']);
		} else {
			$data['quantity_expression'] = '>';
		}

		if (isset($module_info['stock_status'])) {
			if (!empty($module_info['stock_status'])) {
				$data['stock_status'] = $module_info['stock_status'];
			} else {
				$data['stock_status'] = [];
			}
		} else {
			$data['stock_status'] = [];
		}

		if (isset($module_info['sort'])) {
			$data['sort'] = $module_info['sort'];
		} else {
			$data['sort'] = 'as_is';
		}

		if (isset($module_info['order'])) {
			$data['order'] = $module_info['order'];
		} else {
			$data['order'] = 'ASC';
		}

		return $this->load->view($this->_route, $data);
	}
	
	public function product_autocomplete() {
		
		$json = [];
		
		if (isset($this->request->get['term'])) {
			
			$this->load->model('catalog/product');
			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}
			
			$filter_data = [
				'filter_name' => $this->request->get['term'],
				'filter_model' => '',
				'start' => 0,
				'limit' => $limit
			];
			
			$results = $this->model_catalog_product->getProducts($filter_data);
			
			foreach ($results as $result) {
				$json['results'][] = [
					'id' => (int) $result['product_id'],
					'text' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				];
			}
		}
		
		$this->response->addHeader('Content-type:application/json;charset=utf-8');
		$this->response->setOutput(json_encode($json));
	}
}

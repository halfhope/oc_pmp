<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ControllerExtensionModulePMPDataSourcesGlobalCustom extends Controller {

	private $_route = 'extension/module/pmp/datasources/global_custom';
	
	public function getForm($module_id = 0) {
		
		$data = $this->load->language($this->_route);
		
		$module_info = [];
		if ($module_id !== 0) {
			$this->load->model('extension/module');
			$module_info = $this->model_extension_module->getModule($module_id);
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

		foreach ($products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			
			if ($product_info) {
				$data['products'][] = [
					'id' => $product_info['product_id'],
					'name' => html_entity_decode($product_info['name']),
					'selected' => true
				];
			}
		}

		$data['autocomplete'] = html_entity_decode($this->url->link($this->_route . '/product_autocomplete', 'token=' . $this->session->data['token'], true));

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

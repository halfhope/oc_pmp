<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Catalog\Controller\Extension\Pmp\Module;
use Opencart\System\Helper as Helper;
use Opencart\System\Library as Library;
class PMP extends \Opencart\System\Engine\Controller {

	private $_route = 'extension/pmp/module/pmp';
	private $_model = 'model_extension_pmp_module_pmp';
	private $_data_source_route = '';
	private $_data_source_model = '';

	public function index($setting) {

		$this->load->language($this->_route);

		$this->load->model($this->_route);

		$this->_data_source_route = $this->_route . '/datasources/' . $setting['data_source'];
		$this->_data_source_model = $this->_model . '_datasources_' . $setting['data_source'];

		$this->load->model($this->_data_source_route);

		$config_language_id = $this->config->get('config_language_id');

		$data['heading_title'] = $setting['title'][$config_language_id];
		
		$data_items = [];
		
		if ($setting['cache']) {
			$this->initCache($setting['cache_expire']);

			$cache_params = $this->cacheName($setting);

			$data_items = $this->getCache($cache_params);
		}
		// if cached is not set or cache is disabled 
		if (($setting['cache'] && $data_items == null) || !$setting['cache']) {
			
			$data_items = $this->{$this->_data_source_model}->getData($setting);

			if ($setting['cache']) {
				$this->setCache($cache_params, $data_items);
			}
		}

		if ($data_items) {

			if ($setting['shuffle']) {
				shuffle($data_items);
			}
			
			// compatibility mode
			if ($setting['compat'] ) {			

				$setting['product'] = $data_items;
				$setting['heading_title'] = $setting['title'][$config_language_id];

				return $this->load->controller('extension/opencart/module/featured', $setting);
			}

			$this->load->model('catalog/product');
			$this->load->model('tool/image');

			foreach ($data_items as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize(html_entity_decode($product_info['image'], ENT_QUOTES, 'UTF-8'), $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}
	
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}
	
					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}
	
					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					$product_data = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
						'description' => Helper\Utf8\substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('config_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'minimum'     => $product_info['minimum'] > 0 ? $product_info['minimum'] : 1,
						'rating'      => (int)$product_info['rating'],
						'href'        => $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $product_info['product_id'])
					);

					$data['products'][] = $this->load->controller('product/thumb', $product_data);
				}
			}

			if (!empty($setting['template'])) {
				$template = $setting['template'];
			} else {
				$template = $this->_route;
			}

			return $this->load->view($template, $data);
		}
	}

	/*
	* FEATURED MODULE COMPATIBILITY EVENTS
	*/
	
	public function eventPMPFeaturedAddData(&$route, &$args) {
		if (isset($args[0]['data_source'])) {
			$setting = $args[0];

			$language_id = $this->config->get('config_language_id');
			$heading_title = isset($setting['title'][$language_id]) ? $setting['title'][$language_id] : '';

			$pmp_featured = new \stdClass();
			$pmp_featured->heading_title = $heading_title;
			$pmp_featured->template = $setting['template'];
			
			$pmp_featured->setting = $setting;

			$this->registry->set('pmp_featured', $pmp_featured);
		}
	}

	public function eventPMPFeaturedReplaceView(&$route, &$data) {
		if ($this->registry->has('pmp_featured')) {

			$pmp_featured = $this->registry->get('pmp_featured');

			if (!empty($pmp_featured->heading_title)) {
				$data['heading_title'] = $pmp_featured->heading_title;
			}

			if (!empty($pmp_featured->template)) {
				$route = $pmp_featured->template;
			}
		}
	}

	/*
	* CACHE
	*/

	private function initCache($expire) {
		if (!$this->registry->has('pmp_cache')) {
			$expire = $expire * 3600;

			$this->registry->set('pmp_cache', new Library\Cache($this->config->get('cache_engine'), $expire));
		}
	}

	private function cacheName(&$setting) {

		$name = implode('.', [
			'pmp',
			$setting['data_source'],
			$this->config->get('config_store_id'),
			$this->config->get('config_language_id'),
			$this->config->get('config_customer_group_id'),
			hash('crc32b', json_encode($setting))
		]);

		$class_name = 'Opencart\\' . $this->config->get('application') . '\Model\\' . str_replace(['_', '/'], ['', '\\'], ucwords($this->_data_source_route, '_/'));

		if (method_exists($class_name, 'getCachePrecomputedParams')) {
			$setting = $this->{$this->_data_source_model}->getCachePrecomputedParams($setting);
			$index = $setting['index'];
		} else {
			$index = 1;
		}

		return [
			'name' 	=> $name,
			'index' => $index
		];
	}

	private function getCache($params) {
		$cache = $this->pmp_cache->get($params['name']);

		if (is_array($cache) && isset($cache[$params['index']])) {
			return $cache[$params['index']];
		}

		return null;
	}

	private function setCache($params, $value) {
		$cache = $this->pmp_cache->get($params['name']);

		$cache[$params['index']] = $value;

		$this->pmp_cache->set($params['name'], $cache);
	}
}
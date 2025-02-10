<?php
class ControllerExtensionModulePMPLanding extends Controller {

	private $_route = 'extension/module/pmp_landing';
	private $_model = 'model_extension_module_pmp';
	private $_module_route = 'extension/module/pmp';
	private $_data_source_route = '';
	private $_data_source_model = '';

	public function index() {
		// Get module ID
		if (isset($this->request->get['module_id'])) {
			$module_id = (int) $this->request->get['module_id'];
		} else {
			$this->response->redirect($this->url->link('common/not_found', '', true));
		}

		$this->load->language($this->_route);

		// Get module settings 
		$this->load->model('setting/module');
		$setting = $this->model_setting_module->getModule($module_id);

		// Load Set model
		$this->_data_source_route = $this->_module_route . '/datasources/' . $setting['data_source'];
		$this->_data_source_model = $this->_model . '_datasources_' . $setting['data_source'];

		$this->load->model($this->_data_source_route);

		// Set title
		$config_language_id = $this->config->get('config_language_id');

		$data['heading_title'] = $setting['title'][$config_language_id];

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_pagination');
		}

		$this->document->setTitle($data['heading_title']);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))
		);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['module_id'])) {
			$url .= '&module_id=' . $this->request->get['module_id'];
		}

		if (isset($this->request->get['params'])) {
			$url .= '&params=' . $this->request->get['params'];
		}

		$data['breadcrumbs'][] = array(
			'text' => $data['heading_title'],
			'href' => $this->url->link($this->_route, 'language=' . $this->config->get('config_language'), $url)
		);

		$data['text_empty'] = $this->language->get('text_empty');
		$data['text_quantity'] = $this->language->get('text_quantity');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_model'] = $this->language->get('text_model');
		$data['text_price'] = $this->language->get('text_price');
		$data['text_tax'] = $this->language->get('text_tax');
		$data['text_points'] = $this->language->get('text_points');
		$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		$data['text_sort'] = $this->language->get('text_sort');
		$data['text_limit'] = $this->language->get('text_limit');

		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['button_list'] = $this->language->get('button_list');
		$data['button_grid'] = $this->language->get('button_grid');
		$data['button_continue'] = $this->language->get('button_continue');

		$data['compare'] = $this->url->link('product/compare', 'language=' . $this->config->get('config_language'));

		$data['products'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		);

		if (isset($this->request->get['params'])) {
			$params = json_decode(base64_decode($this->request->get['params']), true);

			if (isset($params['categories'])) {
				$this->request->get['path'] = implode('_', $params['categories']);
			}
			
			if (isset($params['manufacturers'])) {
				$this->request->get['manufacturer_id'] = $params['manufacturers'];
			}
		}	

		$query_params = array_replace_recursive($setting, $filter_data);
		$results = $this->{$this->_data_source_model}->getData($query_params);
		$product_total = $this->{$this->_data_source_model}->getDataTotal($query_params);

		foreach ($results as $product_id) {
			
			$result = $this->model_catalog_product->getProduct($product_id);

			if (is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
				$image = $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$price = false;
			}

			if ((float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$special = false;
			}

			if ($this->config->get('config_tax')) {
				$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
			} else {
				$tax = false;
			}

			if ($this->config->get('config_review_status')) {
				$rating = (int)$result['rating'];
			} else {
				$rating = false;
			}

			$product_data = array(
				'product_id'  => $result['product_id'],
				'thumb'       => $image,
				'name'        => $result['name'],
				'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
				'price'       => $price,
				'special'     => $special,
				'tax'         => $tax,
				'minimum'     => ($result['minimum'] > 0) ? $result['minimum'] : 1,
				'rating'      => $rating,
				'href'        => $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $result['product_id'] . $url)
			);
			
			$data['products'][] = $this->load->controller('product/thumb', $product_data);
		}

		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['module_id'])) {
			$url .= '&module_id=' . $this->request->get['module_id'];
		}

		if (isset($this->request->get['params'])) {
			$url .= '&params=' . $this->request->get['params'];
		}

		$data['sorts'] = array();

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_default'),
			'value' => 'p.sort_order-ASC',
			'href'  => $this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&sort=p.sort_order&order=ASC' . $url)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_name_asc'),
			'value' => 'pd.name-ASC',
			'href'  => $this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&sort=pd.name&order=ASC' . $url)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_name_desc'),
			'value' => 'pd.name-DESC',
			'href'  => $this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&sort=pd.name&order=DESC' . $url)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_price_asc'),
			'value' => 'ps.price-ASC',
			'href'  => $this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&sort=ps.price&order=ASC' . $url)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_price_desc'),
			'value' => 'ps.price-DESC',
			'href'  => $this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&sort=ps.price&order=DESC' . $url)
		);

		if ($this->config->get('config_review_status')) {
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_desc'),
				'value' => 'rating-DESC',
				'href'  => $this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&sort=rating&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_rating_asc'),
				'value' => 'rating-ASC',
				'href'  => $this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&sort=rating&order=ASC' . $url)
			);
		}

		$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&sort=p.model&order=ASC' . $url)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_model_desc'),
			'value' => 'p.model-DESC',
			'href'  => $this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&sort=p.model&order=DESC' . $url)
		);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['module_id'])) {
			$url .= '&module_id=' . $this->request->get['module_id'];
		}

		if (isset($this->request->get['params'])) {
			$url .= '&params=' . $this->request->get['params'];
		}

		$data['limits'] = array();

		$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

		sort($limits);

		foreach($limits as $value) {
			$data['limits'][] = array(
				'text'  => $value,
				'value' => $value,
				'href'  => $this->url->link($this->_route, $url . '&limit=' . $value)
			);
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		if (isset($this->request->get['module_id'])) {
			$url .= '&module_id=' . $this->request->get['module_id'];
		}

		if (isset($this->request->get['params'])) {
			$url .= '&params=' . $this->request->get['params'];
		}

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link($this->_route, $url . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

        if (!$this->config->get('config_canonical_method')) {
            // http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
            if ($page == 1) {
                $this->document->addLink($this->url->link($this->_route, 'language=' . $this->config->get('config_language'), true), 'canonical');
            } elseif ($page == 2) {
                $this->document->addLink($this->url->link($this->_route, 'language=' . $this->config->get('config_language'), true), 'prev');
            } else {
                $this->document->addLink($this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&page=' . ($page - 1), true), 'prev');
            }

            if ($limit && ceil($product_total / $limit) > $page) {
                $this->document->addLink($this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&page=' . ($page + 1), true), 'next');
            }
        } else {
            if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
                $server = $this->config->get('config_ssl');
            } else {
                $server = $this->config->get('config_url');
            };

            $request_url = rtrim($server, '/') . $this->request->server['REQUEST_URI'];
            $canonical_url = $this->url->link($this->_route, 'language=' . $this->config->get('config_language'), true);


            if (($request_url != $canonical_url) || $this->config->get('config_canonical_self')) {
                $this->document->addLink($canonical_url, 'canonical');
            }

            if ($this->config->get('config_add_prevnext')) {

                if ($page == 2) {
                    $this->document->addLink($this->url->link($this->_route, 'language=' . $this->config->get('config_language'), true), 'prev');
                } elseif ($page > 2)  {
                    $this->document->addLink($this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&page=' . ($page - 1), true), 'prev');
                }

                if ($limit && ceil($product_total / $limit) > $page) {
                    $this->document->addLink($this->url->link($this->_route, 'language=' . $this->config->get('config_language') . '&page=' . ($page + 1), true), 'next');
                }
            }
        }

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['limit'] = $limit;

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view($this->_route, $data));
	}
}

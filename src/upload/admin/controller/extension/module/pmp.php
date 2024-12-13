<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ControllerExtensionModulePMP extends Controller {

	public 	$_route 		= 'extension/module/pmp';
	public 	$_model 		= 'model_extension_module_pmp';
	private $_version 		= '1.2';

	private $_events = [
		[
			'trigger'	=> 'catalog/controller/extension/module/featured/before',
			'action'	=> '/eventPMPFeaturedAddData'
		],
		[
			'trigger'	=> 'catalog/view/extension/module/featured/before',
			'action'	=> '/eventPMPFeaturedReplaceView'
		]
	];

	private $error = [];

	public function install() {
		$this->load->model($this->_route);
		$this->{$this->_model}->install();

		$this->load->model('extension/event');
		foreach ($this->_events as $key => $_event) {
			$_event = [
				'code' 		  => 'pmp_' . substr(md5(http_build_query($_event)), 4),
				'trigger'	  => $_event['trigger'],
				'action'	  => $this->_route . $_event['action'],
				'description' => '',
				'status' 	  => 1,
				'sort_order'  => 1
			];

			if(!$result = $this->model_extension_event->getEvent($_event['code'], $_event['trigger'], $_event['action'])) {
				$this->model_extension_event->addEvent($_event['code'], $_event['trigger'], $_event['action']);
			}
		}
	}

	public function uninstall() {
		$this->load->model($this->_route);
		$this->{$this->_model}->uninstall();

		$this->load->model('extension/event');
		foreach ($this->_events as $key => $_event) {
			$_event['code'] = 'pmp_' . substr(md5(http_build_query($_event)), 4);
			$this->model_extension_event->deleteEvent($_event['code']);
		}
	}

	public function index() {
		$this->load->model($this->_route);

		$data = $this->load->language($this->_route);
		
		$this->document->setTitle($this->language->get('heading_title'));
		$data['version'] = $this->_version;

		$this->load->model('extension/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->install();

			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule('pmp', $this->request->post);
				$module_id = $this->db->getLastId();
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
				$module_id = $this->request->get['module_id'];
			}

			$cache_name = implode('.', ['pmp', $this->request->post['data_source']]);
			$this->cache->delete($cache_name);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link($this->_route, 'token=' . $this->session->data['token'] . '&module_id=' . $module_id, true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		];

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link($this->_route, 'token=' . $this->session->data['token'], true)
			];
		} else {
			$data['breadcrumbs'][] = [
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link($this->_route, 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true)
			];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link($this->_route, 'token=' . $this->session->data['token'], true);
		} else {
			$data['action'] = $this->url->link($this->_route, 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
		$data['data_source_link'] = html_entity_decode($this->url->link($this->_route . '/data_source', 'token=' . $this->session->data['token'], true));
		
		$data['module_id'] = isset($this->request->get['module_id']) ? $this->request->get['module_id'] : 0;
		
		$data['add_module'] = $this->url->link($this->_route, 'token=' . $this->session->data['token'], true);
		$data['modules_link'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		$this->load->model('extension/module');
		$data['modules'] = $this->model_extension_module->getModulesByCode('pmp');
		foreach ($data['modules'] as $key => $value) {
			$data['modules'][$key]['edit'] = $this->url->link($this->_route, 'token=' . $this->session->data['token'] . '&module_id=' . $value['module_id'], true);
		}

		$data['groups'] = [];

		$grps = glob(DIR_APPLICATION . 'controller/extension/module/pmp/groups/*.php', GLOB_BRACE);

		foreach ($grps as $file) {
			$group = basename($file, '.php');
			$datasources = [];

			$algs = glob(DIR_APPLICATION . 'controller/extension/module/pmp/datasources/' . $group . '_*.php', GLOB_BRACE);

			foreach ($algs as $file) {
				$datasource = basename($file, '.php');

				$this->load->language('extension/module/pmp/datasources/' . $datasource, 'extension');

				$datasources[] = [
					'code' => $datasource,
					'text' => $this->language->get('heading_title')
				];
			}
			
			$this->load->language('extension/module/pmp/groups/' . $group, 'extension');

			if (!empty($datasources)) {
				
				uasort($datasources, function ($a, $b) {
					return strnatcmp($b['text'], $a['text']);
				});

				$data['groups'][] = [
					'code' => $group,
					'text' => $this->language->get('heading_title'),
					'datasources' => $datasources
				];
			}
		}

		$sections = glob(DIR_APPLICATION . 'controller/extension/module/pmp/sections/*.php', GLOB_BRACE);

		foreach ($sections as $file) {
			$section = basename($file, '.php');

			$this->load->language('extension/module/pmp/sections/' . $section, 'extension');

			$data['sections'][] = [
				'code' => $section,
				'text' => $this->language->get('heading_title'),
				'icon' => $this->language->get('extension')->get('fontawesome_icon'),
				'href' => $this->url->link($this->_route . '/sections/' . $section, 'token=' . $this->session->data['token'], true)
			];
		}

		$data['cache_expire_options'] = [
			1 	=>  $this->language->get('text_cache_1'),
			4	=>  $this->language->get('text_cache_4'),
			24 	=> 	$this->language->get('text_cache_24'),
			168 => 	$this->language->get('text_cache_168'),
			744 => 	$this->language->get('text_cache_744')
		];
		
		$this->load->model('localisation/language');
		$this->load->model('catalog/product');
		
		$data['languages']     = $this->model_localisation_language->getLanguages();

		$module_info = [];
		if (isset($this->request->get['module_id'])) {
			$this->load->model('extension/module');
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['limit'])) {
			$data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = 4;
		}
		
		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = $this->config->get('theme_default_image_product_width');
		}
		
		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = $this->config->get('theme_default_image_product_height');
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = false;
		}

		if (isset($this->request->post['title'])) {
			$data['title'] = $this->request->post['title'];
		} elseif (!empty($module_info)) {
			if (!empty($module_info['title'])) {
				$data['title'] = $module_info['title'];
			} else {
				$data['title'] = [];
			}
		} else {
			$data['title'] = [];
		}

		if (isset($this->request->post['data_source'])) {
			$data['data_source'] = $this->request->post['data_source'];
		} elseif (!empty($module_info)) {
			$data['data_source'] = $module_info['data_source'];
		} else {
			$data['data_source'] = 'arg_latest';
		}

		if (isset($this->request->post['shuffle'])) {
			$data['shuffle'] = $this->request->post['shuffle'];
		} elseif (!empty($module_info)) {
			$data['shuffle'] = $module_info['shuffle'];
		} else {
			$data['shuffle'] = false;
		}
		
		if (isset($this->request->post['cache'])) {
			$data['cache'] = $this->request->post['cache'];
		} elseif (!empty($module_info)) {
			$data['cache'] = $module_info['cache'];
		} else {
			$data['cache'] = 0;
		}
		
		if (isset($this->request->post['cache_expire'])) {
			$data['cache_expire'] = $this->request->post['cache_expire'];
		} elseif (!empty($module_info)) {
			$data['cache_expire'] = $module_info['cache_expire'];
		} else {
			$data['cache_expire'] = 1;
		}

		if (isset($this->request->post['compat'])) {
			$data['compat'] = $this->request->post['compat'];
		} elseif (!empty($module_info)) {
			$data['compat'] = $module_info['compat'];
		} else {
			$data['compat'] = false;
		}

		if (isset($this->request->post['template'])) {
			$data['template'] = $this->request->post['template'];
		} elseif (!empty($module_info)) {
			$data['template'] = $module_info['template'];
		} else {
			$data['template'] = 'extension/module/pmp';
		}

		if (isset($this->request->get['module_id']) && isset($module_info['data_source'])) {
			$data['data_source_form_data'] = $this->load->controller('extension/module/pmp/datasources/' . $module_info['data_source'] . '/getForm', (int) $this->request->get['module_id']);
		} else {
			$data['data_source_form_data'] = $this->load->controller('extension/module/pmp/datasources/' . $data['data_source'] . '/getForm', 0);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->_route, $data));
	}

	public function data_source() {
		if (($this->request->server['REQUEST_METHOD'] == 'GET')) {
			$datasource = $this->request->get['datasource'];
			$module_id = (int) $this->request->get['module_id'];
			$response = $this->load->controller('extension/module/pmp/datasources/' . $datasource . '/getForm', $module_id);
		} else {
			$response = '';
		}
		$this->response->setOutput($response);
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->_route)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (count($this->request->post, COUNT_RECURSIVE) >= ini_get('max_input_vars')) {
			$this->error['warning'] = $this->language->get('error_max_input_vars');
		}
		return !$this->error;
	}
}
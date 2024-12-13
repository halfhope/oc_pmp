<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Admin\Controller\Extension\Pmp\Module\Pmp\Datasources;
class CustomPHP extends \Opencart\Admin\Controller\Extension\Pmp\Module\Pmp\Groups\Custom {

	private $_route = 'extension/pmp/module/pmp/datasources/custom_php';

	public function getForm($module_id = 0) {
		$this->load->language($this->_route);
		
		$module_info = [];
		if ($module_id !== 0) {
			$this->load->model('setting/module');
			$module_info = $this->model_setting_module->getModule($module_id);
		}

		if (isset($module_info['php'])) {
			if (!empty($module_info['php'])) {
				$data['php'] = $module_info['php'];
			} else {
				$data['php'] = '';
			}
		} else {
			$data['php'] = '';
		}
		
		return $this->load->view($this->_route, $data);
	}
	
}

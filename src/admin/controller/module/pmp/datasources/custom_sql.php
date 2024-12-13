<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Admin\Controller\Extension\Pmp\Module\Pmp\Datasources;
class CustomSQL extends \Opencart\Admin\Controller\Extension\Pmp\Module\Pmp\Groups\Custom {

	private $_route = 'extension/pmp/module/pmp/datasources/custom_sql';
 
	public function getForm($module_id = 0) {
		$this->load->language($this->_route);
		
		$module_info = [];
		if ($module_id !== 0) {
			$this->load->model('setting/module');
			$module_info = $this->model_setting_module->getModule($module_id);
		}

		if (isset($module_info['sql'])) {
			if (!empty($module_info['sql'])) {
				$data['sql'] = $module_info['sql'];
			} else {
				$data['sql'] = '';
			}
		} else {
			$data['sql'] = '';
		}

		return $this->load->view($this->_route, $data);
	}
	
}

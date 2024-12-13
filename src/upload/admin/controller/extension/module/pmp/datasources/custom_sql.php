<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

class ControllerExtensionModulePMPDataSourcesCustomSQL extends Controller {

	private $_route = 'extension/module/pmp/datasources/custom_sql';
	
	public function getForm($module_id = 0) {
		$data = $this->load->language($this->_route);
		
		$module_info = [];
		if ($module_id !== 0) {
			$this->load->model('extension/module');
			$module_info = $this->model_extension_module->getModule($module_id);
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

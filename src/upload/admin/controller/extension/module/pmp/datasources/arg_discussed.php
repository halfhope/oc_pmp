<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

require_once DIR_APPLICATION . "controller/extension/module/pmp/groups/arg.php";

class ControllerExtensionModulePMPDataSourcesARGDiscussed extends ControllerExtensionModulePMPGroupsARG {

	private $_route = 'extension/module/pmp/datasources/arg_discussed';

	public function getForm($module_id = 0) {
		// get group form data
		$data = $this->getGroupFormData($module_id);

		// draw group form with data
		$response = $this->load->view($this->_group_route, $data);

		return $response;
	}
	
}

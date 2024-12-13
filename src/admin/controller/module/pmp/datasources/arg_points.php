<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Admin\Controller\Extension\Pmp\Module\Pmp\Datasources;
class ARGPoints extends \Opencart\Admin\Controller\Extension\Pmp\Module\Pmp\Groups\ARG {

	private $_route = 'extension/pmp/module/pmp/datasources/arg_points';

	public function getForm($module_id = 0) {
		// get group form data
		$data = $this->getGroupFormData($module_id);

		// draw group form with data
		$response = $this->load->view($this->_group_route, $data);

		return $response;
	}
	
}

<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Catalog\Model\Extension\Pmp\Module\Pmp\Datasources;
class ARGCustomPHP extends \Opencart\System\Engine\Model {

	public function getData($setting) {
		if (isset($setting['php'])) {
			return eval($setting['php']);
		}
	}
}
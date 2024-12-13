<?php
/**
 * @author Shashakhmetov Talgat <talgatks@gmail.com>
 */

namespace Opencart\Catalog\Model\Extension\Pmp\Module\Pmp\Datasources;
class ARGGlobalsCertain extends \Opencart\System\Engine\Model {

	public function getData($setting) {
		$product_data = [];

		$product_data = array_slice($setting['products'], 0, $setting['limit']);

		return $product_data;
	}
}

<?php
/**
 * Item controller
 *
 * @author Hemant Mann
 */
use Framework\RequestMethods as RequestMethods;
use Framework\Registry as Registry;

class Items extends Admin {
	/**
	 * @before _secure, _admin, changeLayout
	 */
	public function remove($item_id, $type) {
		$this->noview();

		try {
			$item = $type::first(array("id = ?" => $item_id));
			if ($item) {
				$property = null;
				if (property_exists($item, "_image")) {
					$property = "image";
				} elseif (property_exists($item, "_base_im")) {
					$property = "base_im";
				}

				if ($property) {
					@unlink(APP_PATH.'/public/assets/uploads/images/'. $item->$property);
				}
				$item->delete();
			}
		} catch (\Exception $e) {
			// die($e->getMessage());
		}
		$this->redirect($_SERVER['HTTP_REFERER']);
	}
}

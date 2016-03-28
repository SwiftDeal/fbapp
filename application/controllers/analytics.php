<?php
/**
 * @author Faizan Ayubi
 */
use Framework\RequestMethods as RequestMethods;
use Framework\Registry as Registry;
use \Curl\Curl;

class Analytics extends Admin {
	
	/**
     * @before _secure, changeLayout, _admin
     */
	public function game() {
		$this->seo(array("title" => "Analytics", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
	}

	/**
     * @before _secure, changeLayout, _admin
     */
	public function participants($campaign_id = NULL) {
		$this->seo(array("title" => "Participants", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
	}
}
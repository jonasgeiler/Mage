<?php

namespace Controllers;

use View;

class Privacy {

	/**
	 * @return void
	 */
	public function index (): void {
		echo View::instance()->render('privacy.php');
	}

}

<?php

namespace Controllers;

use View;

class Privacy {

	public function index (): void {
		echo View::instance()->render('privacy.php');
	}

}

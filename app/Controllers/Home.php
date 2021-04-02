<?php

namespace Controllers;

use View;

class Home {

	public function index (): void {
		echo View::instance()->render('home.php');
	}

}

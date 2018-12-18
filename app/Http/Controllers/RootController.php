<?php
namespace Http\Controllers {

	class RootController {

		public function __construct() {
		}

		public function __invoke($path = []) {

			return [
				'HTTPStatusCode' => '200',
				'view'           => 'home/index',
				'title'          => 'Great White Whale - Holy Grail'
			];

		}

	}

}

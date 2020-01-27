<?php
	namespace App\Controller;

	class Handler {
		public $logic = null;
		public $user  = null;
		public $renderT = null;
		public $last_error = '';

		private $db      = null;
		private $enviro  = null;

		public function __construct() {
			$this->enviro  = new \App\Model\Environment();
			$this->db      = new \App\Model\DataBase();
			$this->logic   = new \App\Controller\Logic();
			$this->user    = new \App\Controller\User();
			$this->renderT = new \App\Controller\Render([]);
			
			$this->logic->setdb($this->db);
			$this->user->setdb($this->db);
			$this->logic->setUser($this->user);
		}

		public function render($data = []) {
			$this->renderT = new \App\Controller\Render($data);
			$this->renderT->twigRender();
		}

		public function getChannelData($channelid = ''): array {
			$status_success = $this->logic->initClient();
			if(!$status_success) {
				$this->last_error = 'Failed to initialize connection to Utopia client';
				return [
					'founded' => false
				];
			}
			$channel_data = $this->logic->getChannelData($channelid);
			if($channel_data == []) {
				//inherit error data
				$this->last_error = $this->logic->last_error;
				return [
					'founded' => false
				];
			} else {
				//use this parameter when displaying the page
				$channel_data['founded'] = true;
				return $channel_data;
			}
		}
	}

<?php
	namespace App\Controller;

	class Logic {
		public $user = null;      //App\Src\User object
		public $last_error = '';  //string

		protected $db  = null;    //App\Model\DataBase object
		protected $client = null; //UtopiaLib\Client object

		public function __construct() {
			//
		}
		
		public function setdb($db) {
			$this->db = &$db;
		}

		public function setUser($user): void {
			$this->user = &$user;
		}

		public function initClient(): bool {
			$this->client = new \UtopiaLib\Client(
				getenv('api_token'),
				getenv('api_host'),
				getenv('api_port')
			);
			return $this->client->checkClientConnection();
		}

		public function getChannelData($channelid = ''): array {
			if($channelid == '') {
				$this->last_error = 'empty channel id given';
				return [];
			}
			if(! \UtopiaLib\Utilities::verifyChannelID($channelid)) {
				$this->last_error = 'the given channel id is not valid';
				return [];
			}
			$channel_query = "SELECT id,title,utopia_channelid,description,is_readonly,is_readonly_privacy,channel_type FROM channels WHERE utopia_channelid='" . $channelid . "' LIMIT 1";
			$channel_data = $this->db->query2arr($channel_query);
			if($channel_data == []) {
				//channel id not found in db
				//find channel in utopia ecosystem
				$channel_info = $this->client->getChannelInfo($channelid);
				if($channel_info == []) {
					$this->last_error = 'channel not found in utopia ecosystem';
					return [];
				}
				//just in case, we filter the data
				$data_fields = ['title', 'description', 'readonly', 'readonly_privacy', 'type'];
				$data_filtered = \App\Model\Utilities::checkFields($channel_info, $data_fields, $this->db);
				//" .  . "
				//form a request to add chat data to the database
				$sql_query = "INSERT INTO channels SET title='" . $data_filtered['title'] . "', utopia_channelid='" . $channelid . "', description='" . $data_filtered['description'] . "', is_readonly='" . (int)$data_filtered['readonly'] . "', is_readonly_privacy='" . (int)$data_filtered['readonly_privacy'] . "', channel_type='" . $data_filtered['type'] . "'";
				if(! $this->db->tryQuery($sql_query)) {
					$this->last_error = 'Failed to save channel data';
					return [];
				}
				$channel_data = $this->db->query2arr($channel_query);
				if($channel_data == []) {
					$this->last_error = 'Failed to find channel data. Code 05C3NZ';
					return [];
				}
				$status_success = $this->client->joinChannel($channelid);
				if(!$status_success) {
					//failed to join channel
					$channel_data['messages'] = [
						'count' => 0, 'array' => []
					];
					return $channel_data;
				}
			}
			$sql_query = "SELECT id,message_text,user_nickname FROM messages WHERE channel_index=" . $channel_data['id'];
			$channel_data['messages'] = $this->db->query2multiArr($sql_query);
			return $channel_data;
		}
		
		public function updateChannels() {
			//connect to utopia client & check connection
			if(! $this->initClient()) {
				return;
			}
			
			//get channels in db
			$sql_query = "SELECT id,utopia_channelid AS ucid FROM channels WHERE channel_type='public' & title!=''";
			$channels_data = $this->db->query2multiArr($sql_query);
			if($channels_data == [] || $channels_data['array'] == []) {
				return;
			}
			
			$messages_sortBy = '';
			$messages_offset = '';
			$messages_limit = 20;
			$query_filter = new \UtopiaLib\Filter(
				$messages_sortBy,
				$messages_offset,
				$messages_limit
			);
			for($i=0; $i < $channels_data['count']; $i++) {
				$messages_arr = $this->client->getChannelMessages($channels_data['array'][$i]['ucid'], $query_filter);
				if($messages_arr == []) {
					//messages not found
					break;
				}
				//delete last messages
				$sql_query = "DELETE FROM messages WHERE channel_index=" . $channels_data['array'][$i]['id'];
				$this->db->query($sql_query);
				for($j = 0; $j < $messages_limit; $j++) {
				//for($j = count($messages_arr)-1; $j >= count($messages_arr)-$messages_limit-1; $j--) {
					//TODO: optimize?
					if($messages_arr[$j]['text'] != '') {
						$sql_query = "INSERT IGNORE INTO messages SET channel_index=" . $channels_data['array'][$i]['id'] . ", message_text='" . $messages_arr[$j]['text'] . "', user_nickname='" . $messages_arr[$j]['nick'] . "', utopia_messageid=" . $messages_arr[$j]['id'];
						$this->db->query($sql_query);
					}
				}
			}
		}
		
		public function getChannels(): array {
			$sql_query = "SELECT id,title,utopia_channelid,description FROM channels LIMIT 100";
			return $this->db->query2multiArr($sql_query);
		}
	}

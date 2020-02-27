<?php

class Listamester {
	private $APIbase = LISTAMESTER_API_BASE;
	private $APIuser = LISTAMESTER_API_USER;
	private $APIpassword = LISTAMESTER_API_PASSWORD;
	public $groupID = LISTAMESTER_GROUP_ID;
	/**
	 * call the Listamester API
	 * @param		string							$endpoint			API endpoint, required
	 * @param		string							$postDatas		POST datas in JSON, optional
	 * @return	bool false	=> endpoint is empty or the curl result false
	 *					array				=> result array
	 *					string			=> result string
	 **/
	private function callAPI($endpoint = '', $postDatas = ''){
		if($endpoint == ''){
			return false;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->APIbase.$endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_USERPWD, $this->APIuser.':'.$this->APIpassword);
		if($postDatas != ''){
			$headers = array();
			$headers[] = 'Content-Type: application/json';
			$headers[] = 'Content-Length: '.strlen($postDatas);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postDatas);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		$curlResult = curl_exec($ch);
		curl_close($ch);
		if($curlResult === false){
			return false;
		}
		if($this->isJson($curlResult)){
			return json_decode($curlResult, true);
		}
		return $curlResult;
	}
	/**
	 * return the list of groups with group IDs
	 *
	 * @return	array
	 **/
	public function getGroups(){
		return $this->callAPI('/Groups');
	}
	/**
	 * check that member exists in the group
	 *
	 * @param		string		$email
	 * @return	bool
	 **/
	public function memberExists($email){
		if(!$this->isEmail($email)){
			return false;
		}
		$getMember = $this->getMember($email);
		if(isset($getMember[0])){
			return true;
		}
		return false;
	}
	/**
	 * return an array:		empty array					=> email address is not exists
	 *										array with one item	=> item 0 contains the member datas
	 *
	 * @param		string		$email		valid email address, required
	 * @return	array
	 **/
	public function getMember($email){
		if(!$this->isEmail($email)){
			return false;
		}
		return $this->callAPI('/SearchMember/?searchType=byEmail&searchPhrase='.$email.'&groupId='.LISTAMESTER_GROUP_ID);
	}
	/**
	 * add new member to the group
	 *
	 * @param		string		$name			subscriber name, required
	 * @param		string		$email		subscriber valid email address, required
	 * @return	bool (false)				=> invalid email
	 *					string ("exists")		=> email exists in group
	 *					array								=> status ("OK", string) and new member ID (int)
	 **/
	public function subscribe($name, $email){
		if(!$this->isEmail($email)){
			return false;
		}
		if($this->memberExists($email)){
			return 'exists';
		}
		$subscribeDatas = array('name' => $name, 'email' => $email);
		return $this->callAPI('/SubscribeToGroup/'.LISTAMESTER_GROUP_ID, json_encode($subscribeDatas));
	}
	/**
	 * email address validator
	 *
	 * @param		string		$email
	 * @return	bool
	 **/
	private function isEmail($email){
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	/**
	 * JSON validator
	 *
	 * @param		string		$string
	 * @return	bool
	 **/
	private function isJson($string){
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
}

?>

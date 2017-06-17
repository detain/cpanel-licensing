<?php

namespace Detain\Cpanel;

class Cpanel {
	public $format;
	public $curl;
	public $opts;

	public function __construct ($user, $pass) {
	$this->opts = [];
		$this->format = "simplexml";
		$this->setCredentials($user, $pass);
		$this->setopt(CURLOPT_RETURNTRANSFER,1);
		$this->setopt(CURLOPT_USERAGENT, 'cPanel Licensing Agent (php) 3.5' );
	}

	public function setopt($option, $value) {
		$this->opts[$option] = $value;
	}

	public function setCredentials ($user, $pass) {
		$this->setopt(CURLOPT_USERPWD, $user . ":" . $pass);
	}

	public function set_format ($format) {
		if ($format != "xml" && $format != "json" && $format != "yaml" && $format != "simplexml") {
			error_log("set_format requires that the format is xml, json, yaml or simplexml");
			return;
		}
		else {
			$this->format = $format;
		}
	}

	private function get ($function, $args = array()) {
		if (!$function) {
			error_log("cPanelLicensing::get requires that a function is defined");
			return;
		}
		if ($this->format != "simplexml") {
			$args['output'] = $this->format;
		}
		$query = "https://manage2.cpanel.net/" . $function . "?" . http_build_query($args);
		$this->setopt(CURLOPT_URL, $query);
		$this->curl = curl_init();
		foreach ($this->opts as $option => $value) {
			curl_setopt($this->curl, $option, $value);
		}
		$result = curl_exec($this->curl);
		curl_close($this->curl);
		if ($result == false) {
			error_log("cPanelLicensing::get failed: \"" . curl_error($this->curl) . "\"");
			return;
		}
		if ($this->format == "simplexml") {
			function_requirements('xml2array');
			$result = xml2array($result, 1, 'attribute');
			$result = $result[str_replace('.cgi', '', $function)];
			$result = $this->format_result($result);			
			return $result;
		}
		else {
			return $result;
		}
	}

	/**
	 * formats the response
	 * @param array $result the result array to format
	 * @return array the rormatted arrray
	 */
	private function format_result($result) {
		if (is_array($result)) {
			foreach ($result as $key => $value) {
				if (is_array($value)) {
					if (isset($value['attr']) && is_array($value['attr'])) {
						$result[$key] = $value['attr'];
					} else {
						$result[$key] = $this->format_result($value);
					}
				}
			}
		}
		return $result;
	}

	private function validateID ($id) {
		if (preg_match("/^(L|P|G)?\d*$/", $id)) {
			return 1;
		}
		else {
			return 0;
		}
	}

	private function validateIP ($ip) {
		return preg_match("/^\d*\.\d*\.\d*\.\d*$/", $ip);
	}

	public function reactivateLicense ($args) {
		if (!array_key_exists('liscid', $args)) {
			error_log("cpanelLicensing::reactivateLicense requires that the argument array contains element liscid");
			return;
		}
		if (!$this->validateID($args['liscid'])) {
			error_log("The liscid passed to cpanelLicenseing::reactivateLicense was invalid");
			return;
		}
		return $this->get("XMLlicenseReActivate.cgi", $args);
	}

	public function expireLicense ($args) {
		if (!array_key_exists("liscid", $args)) {
			error_log("cPanelLicensing::expireLicense requires that liscid elements exists in the array passed to it");
			return;
		}
		if (!$this->validateID($args['liscid'])) {
			error_log("the liscense ID passed to cpanelLicensing::expireLiscense was invalid");
			return;
		}
		return $this->get("XMLlicenseExpire.cgi", $args);
	}

	public function extendOnetimeUpdates ($args) {
		if (!array_key_exists("ip", $args)) {
			error_log("cpanelLicensing::extendOnetimeUpdates requires that the element ip exists in the array is passed to it");
			return;
		}
		if (!$this->validateIP($args['ip'])) {
			error_log("cpanelLicensing::extendOnetimeUpdates was passed an invalid ip");
			return;
		}
		return $this->get( "XMLonetimeext.cgi", $args );
	}

	public function changeip ($args) {
		if (!array_key_exists("oldip", $args) || !array_key_exists("newip", $args)) {
			error_log("cpanelLicensing::changeip requires that oldip and newip elements exist in the array passed to it");
			return;
		}
		if (!$this->validateIP($args["newip"])) {
			error_log("the newip passed to cpanelLicensing::changeip was invalid");
			return;
		}
		return $this->get("XMLtransfer.cgi", $args);
	}

	public function requestTransfer ($args) {
		if (!array_key_exists("ip", $args) || !array_key_exists("groupid", $args ) || !array_key_exists("packagegroup", $args)) {
			error_log("cpanelLicensing::requestTransfer requires that ip, groupid and packageid elements exist in the array passed to it");
			return;
		}
		if (!$this->validateID($args["groupid"])) {
			error_log("The groupid passed to cpanelLicensing::requestTransfer is invalid");
			return;
		}
		if (!$this->validateID( $args["packageid"])) {
			error_log("The package id passed to cpanelLicensing::requestTransfer is invalid");
			return;
		}
		if (!$this->validateIP($args['ip'])) {
			error_log("the ip passed to cpanelLicensing::requestTransfer was invalid");
			return;
		}
		return $this->get("XMLtransferRequest.cgi", $args);
	}

	public function activateLicense ($args) {
		if (!array_key_exists("ip", $args) || !array_key_exists("groupid", $args) || !array_key_exists("packageid", $args)) {
			error_log("cpanelLicensing::activateLicense requires that ip, groupid and packageid elements exist in the array passed to it");
			return;
		}
		if (!$this->validateID($args['groupid'])) {
			error_log("cpanelLicensing::acivateLicense was passed an invalid groupid");
			return;
		}
		if (!$this->validateIP($args['ip'])) {
			error_log("cpanelLicensing::activateLicense was passed an invalid IP");
			return;
		}
		if (!$this->validateID($args['packageid'])) {
			error_log("cpanelLicensing::activateLicense was passed an invalid packageid");
			return;
		}
		$args['legal'] = 1;
		return $this->get("XMLlicenseAdd.cgi", $args);
	}

	public function addPickupPass ($args) {
		if (!array_key_exists("pickup", $args)) {
			error_log("cPanelLicensing::addPickupPass requires a pickup param");
			return;
		}
		return $this->get("XMLaddPickupPass.cgi", $args);
	}

	public function registerAuth ($args) {
		if (!array_key_exists("user", $args)) {
			error_log("cPanelLicensing::registerAuth requires a user param");
			return;
		}
		if (!array_key_exists("pickup", $args)) {
			error_log("cPanelLicensing::registerAuth requires a pickup param");
			return;
		}
		if (!array_key_exists("service", $args)) {
			error_log("cPanelLicensing::registerAuth requires a service param");
			return;
		}
		$response = $this->get("XMLregisterAuth.cgi", $args);
		if ($this->format == "simplexml") {
			$this->setCredentials($args["user"], $response["key"]);
		}
		return $response;
	}

	public function fetchGroups () {
		return $this->get("XMLgroupInfo.cgi");
	}

	public function fetchLicenseRiskData ($args) {
		if (!array_key_exists("ip", $args)) {
			error_log("cpanelLicensing::fetchLicenseRiskData requires that ip exists as an element in the array is passed to it");
			return;
		}
		if (!$this->validateIP($args['ip'])) {
			error_log("cpanelLicensing::fetchLicenseRiskData was passed an invalid ip");
			return;
		}
		return $this->get("XMLsecverify.cgi", $args);
	}

	public function fetchLicenseRaw ($args) {
		$args = array_merge(array("all" => 1), $args);
		if (!array_key_exists("ip", $args)) {
			error_log("cpanelLicesning::fetchLicenseRaw requires that ip exists as an element in the array is passed to it");
			return;
		}
		if (!$this->validateIP($args['ip'])) {
			error_log("cpanelLicensing::fetchLicenseRaw was passed an invalid ip");
			return;
		}
		return $this->get("XMLRawlookup.cgi", $args);
	}

	public function fetchLicenseId ($args) {
		$args = array_merge(array("all" => 1), $args);
		if (!array_key_exists('ip', $args)) {
			error_log("cpanelLicensing::getLicenseId requires that an IP is passed to it");
			return;
		}
		if (!$this->validateIP($args['ip'])) {
			error_log("cpanelLicensing::fetchLicenseId was passed an invalid ip");
			return;
		}
		return $this->get("XMLlookup.cgi", $args);
	}

	public function fetchPackages () {
		return $this->get("XMLpackageInfo.cgi");
	}

	public function fetchLicenses () {
		return $this->get("XMLlicenseInfo.cgi");
	}

	public function fetchExpiredLicenses () {
		return $this->get("XMLlicenseInfo.cgi", array("expired" => '1'));
	}

	public function findKey ($search, $xmlObj) {
		$xmlObj = (array) $xmlObj;
		if (array_key_exists("packages", $xmlObj)) {
			$type = "packages";
		}
		else if (array_key_exists("groups", $xmlObj)) {
			$type = "groups";
		}
		else {
			error_log("cPanelLicensing::findKey with an object that is not groups or packages");
			return;
		}
		foreach ((array) $xmlObj[$type] as $element) {
			foreach ((array) $element as $key => $value) {
				if ((string) $value == $search) {
					return (string)$key;
				}
			}
		}
		error_log("Could not find $type that matches $search");
	}
}


<?php
/*------------------------------------------------------------*/
class CrmUtils extends Mcontroller {
	/*------------------------------------------------------------*/
	private $logger;
	/*------------------------------------------------------------*/
	public function __construct($logFile = null) {
		parent::__construct();
		$this->logger = new Logger($logFile);
	}
	/*------------------------------------------------------------*/
	public function countries() {
		$sql = "select * from countries order by name";
		$countries = $this->Mmodel->getRows($sql, 24*3600);
		return($countries);
	}
	/*------------------------------------------------------------*/
	public function countryName($countryCode) {
		$sql = "select name from countries where code = '$countryCode";
		$countryName = $this->Mmodel->getString($sql, 24*3600);
		return(@$countries[$countryCode]);
	}
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
	public static function tagName($tagId) {
		global $Mmodel;
		if ( ! $Mmodel )
			$Mmodel = new Mmodel;
		if ( ! $tagId )
			return("(?)");
		$sql = "select name from crmTags where id = $tagId";
		$name = $Mmodel->getString($sql, 24*3600);
		return($name);
	}
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
}
/*------------------------------------------------------------*/

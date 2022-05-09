<?php
/*------------------------------------------------------------*/
require_once("crmConfig.php");
require_once(M_DIR."/mfiles.php");
require_once("crmFiles.php");
require_once("Crm.class.php");
/*------------------------------------------------------------*/
$startTime = microtime(true);
/*------------------------------------------------------------*/
$ua = @$_SERVER['HTTP_USER_AGENT'];
if (
	! $ua
	|| stristr($ua, "bot")
	|| stristr($ua, "crawl")
	|| stristr($ua, "spider")
	) {
	http_response_code(204);
	exit;
}
/*------------------------------------------------------------*/
global $Mview;
global $Mmodel;
$Mview = new Mview;
$Mmodel = new Mmodel;
$Mview->holdOutput();
/*------------------------------------------------------------*/
$crmLogin = new CrmLogin;
if ( isset($_REQUEST['logOut']) ) {
	$crmLogin->logOut();
} else {
	$crmLogin->enterSession();
}
$crm = new Crm($startTime);
$crm->control();
$Mview->flushOutput();
/*------------------------------------------------------------*/
/*------------------------------------------------------------*/

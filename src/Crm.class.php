<?php
/*------------------------------------------------------------*/
class Crm extends Mcontroller {
	/*------------------------------*/
	protected $loginEmail;
	protected $loginId;
	protected $role;
	/*------------------------------*/
	protected $logger;
	protected $crmUtils;
	/*------------------------------------------------------------*/
	public function __construct() {
		parent::__construct();

		$topDir = dirname(__DIR__);
		$logsDir = "$topDir/logs/crm";
		$today = date("Y-m-d");
		$logFileName = "crm.$today.log";
		$logFile = "$logsDir/$logFileName";
		$this->loginId = CrmLogin::loginId();
		$this->loginEmail = CrmLogin::loginEmail();
		$this->role = CrmLogin::role();
		$this->crmUtils = new CrmUtils($logFile);
		$this->logger = new Logger($logFile);
	}
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
	protected function permit() {
		$ok = Mrecaptcha::ok();
		if ( ! $ok )
			return(false);
		$action = $this->action;
		if ( in_array($action, array('index', 'forgotpass', ) ) )
			return(true);
		$loginId = $this->loginId;
		if ( $loginId )
			return(true);
		 return(false);
	}
	/*------------------------------*/
	protected function before() {
		$this->Mview->assign(array(
			'controller' => $this->controller,
			'action' => $this->action,
		));
		if ( $this->showMargins()) {
			$this->Mview->showTpl("head.tpl");
			$this->Mview->showTpl("header.tpl");
			$this->Mview->assign("RE_CAPTACH_SITE_KEY", RE_CAPTACH_SITE_KEY);
			if ( $this->loginId ) {
				$menu = new Menu();
				$menu->index();
			}
			$this->Mview->showMsgs();
		}
	}
	/*------------------------------*/
	protected function after() {
		if ( ! $this->showMargins())
			return;
		$this->Mview->showTpl("footer.tpl");
		$this->Mview->showTpl("foot.tpl");
	}
	/*------------------------------------------------------------*/
	public function index() {
		if ( $this->loginId )
			$this->redirect("/crmGo");
		else
			$this->Mview->showTpl("login.tpl");
	}
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
	public function forgotPass() {
		$email = $_REQUEST['email'];
		if ( ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->Mview->msg("register: '$email': Not an email");
			return;
		}
		$str = $this->Mmodel->str($email);
		$sql = "select * from users where loginEmail = '$str'";
		$loginRow = $this->Mmodel->getRow($sql);
		if ( ! $loginRow ) {
			$this->Mview->error("No such email");
			return;
		}
		$rnd = rand(100, 1000);
		$sha1 = sha1($rnd);
		$passwd = substr($sha1, 17, 6);
		$dbPasswd = sha1($passwd);
		$this->Mmodel->dbUpdate("users", $loginRow['id'], array(
			'passwd' => $dbPasswd,
		));
		$m = new MmailJet;
		$httpCode = null;

		$message = $this->Mview->render("forgotPassEmail.tpl", array(
			'passwd' => $passwd,
		));
		$m->mail($email, "New password @ crm.theora.com", $message, $httpCode);
		if ( $httpCode == 200 )
			$this->Mview->msg("New password sent to email");
		else
			$this->Mview->error("Email error");
	}
	/*------------------------------------------------------------*/
	public function changePasswd() {
		$this->Mview->showTpl("admin/changePasswd.tpl",  array(
			'loginEmail' => $this->loginEmail,
		));
	}
	/*------------------------------*/
	public function updatePasswd() {
		$loginEmail = $this->loginEmail;
		$oldPasswd = @$_REQUEST['oldPasswd'];
		$newPasswd = @$_REQUEST['newPasswd'];
		$newPasswd2 = @$_REQUEST['newPasswd2'];
		if ( ! $oldPasswd || ! $newPasswd || ! $newPasswd2 ) {
			$this->Mview->error("updatePasswd: please fill in all 3 fields");
			return;
		}
		if ( $newPasswd != $newPasswd2 ) {
			$this->Mview->error("updatePasswd: new passwords are not the same");
			return;
		}
		$sql = "select * from users where loginEmail = '$loginEmail'";
		$loginRow = $this->Mmodel->getRow($sql);
		if ( ! $loginRow ) {
			$this->Mview->error("updatePasswd: no login row");
			return;
		}
		$dbPasswd = $loginRow['passwd'];
		if ( $dbPasswd != $oldPasswd && $dbPasswd != sha1($oldPasswd) ) {
			$this->Mview->error("updatePasswd: old password incorrect");
			return;
		}
		$newDbPasswd = sha1($newPasswd);
		$this->dbUpdate("users", $loginRow['id'], array(
			'passwd' => $newDbPasswd,
		));
		$this->Mview->msg("Password changed");
	}
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
	protected function dbInsert($tableName, $data) {
		if ( $this->loginEmail )
			return($this->Mmodel->dbInsert($tableName, $data));
		$this->Mview->msg("Not logged in. insert ignored");
		return(null);
	}
	/*------------------------------*/
	protected function dbUpdate($tableName, $id, $data) {
		if ( $this->loginEmail )
			return($this->Mmodel->dbUpdate($tableName, $id, $data));
		$this->Mview->error("Not logged in. Update ignored");
		return(null);
	}
	/*------------------------------*/
	protected function dbDelete($tableName, $id) {
		if ( $this->loginEmail )
			return($this->Mmodel->dbDelete($tableName, $id));
		$this->Mview->error("Not logged in. delete ignored");
		return(null);
	}
	/*------------------------------*/
	protected function sql($sql) {
		if ( $this->loginEmail )
			return($this->Mmodel->sql($sql));
		$this->Mview->error("Not logged in. db change ignored");
		return(null);
		
	}
	/*------------------------------------------------------------*/
	private function showMargins() {
		$nots = array(
			'crm' => array(
				'export',
			),
		);
		$controller = $this->controller;
		$action = $this->action;
		foreach( $nots as $notClassName => $notClass )
			foreach( $notClass as $notAction )
				if ( strcasecmp($notClassName, $controller) == 0
						&& 
						( strcasecmp($notAction, $action) == 0 || $notAction == 'any' )
					) {
						return(false);
					}
		return(true);
	}
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
}

<?php
/*------------------------------------------------------------*/
class CrmLogin extends Mcontroller {
	/*------------------------------------------------------------*/
	public function enterSession() {
		if ( isset($_REQUEST['LogOut']) ) {
			Mlogin::logout();
			return(false);
		}
		if ( Mlogin::is() )
				return(true);
		$loginEmail = @$_REQUEST['loginEmail'];
		$passwd = @$_REQUEST['passwd'];
		if ( ! $loginEmail || ! $passwd )
			return(false);
		if ( ($loginRec = $this->dbEnter($loginEmail, $passwd)) != null ) {
			Mlogin::login($loginRec['id'], $loginEmail, $loginRec['role']);
			return(true);
		}
		$this->Mview->tell("Incorrect password for $loginEmail", array(
			'silent' => true,
			'rememberForNextPage' => true,
		));
		return(false);
	}
	/*------------------------------------------------------------*/
	public function dbEnter($loginEmail, $passwd) {
		$fields = "id, loginEmail, role, passwd";
		$str = $this->Mmodel->str($loginEmail);
		$sql = "select $fields from users where loginEmail = '$str'";
		$loginRecs = $this->Mmodel->getRows($sql);
		foreach ( $loginRecs as $loginRec ) {
			$dbPasswd = $loginRec['passwd'];
			if ( $passwd == $dbPasswd || sha1($passwd) == $dbPasswd) {
				return($loginRec);
			}
		}
		return(null);
	}
	/*------------------------------------------------------------*/
	public function logOut() {
		Mlogin::logout();
	}
	/*------------------------------------------------------------*/
	public static function loginEmail() { return(Mlogin::get("MloginName")); }
	public static function role() { return(Mlogin::get("MloginType")); }
	public static function loginId() { return(Mlogin::get("MloginId")); }
	/*------------------------------------------------------------*/
}
/*------------------------------------------------------------*/

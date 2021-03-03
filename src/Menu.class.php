<?php
/*------------------------------------------------------------*/
class Menu extends Mcontroller {
	/*------------------------------------------------------------*/
	public function index() {
			$this->Mview->showTpl("menuDriver.tpl", array(
				'menu' => $this->dd(),
			));
	}
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
	private function roleMenu() {
		$menu = array(
			'manager' => array(
				'Recent Contacts' => "/crmGo/recentContacts",
				'users' => "/crmGo/users",
				'online' => "/crmGo/online",
			),
			'user' => array(
				'contacts' => "/crmGo/contacts",
				'unFocus' => "/crmGo/unFocus",
			),
		);
		$role = CrmLogin::role();
		if ( $role == 'user' )
			$menu = array(
				'user' => $menu['user'],
			);
		return($menu);
	}
	/*------------------------------------------------------------*/
	private function dd() {
		$role = CrmLogin::role();
		if ( $role == 'user' ) {
			$crmMenu = array(
				array(
					'name' => 'contacts',
					'title' => 'Contacts',
					'url' => "/crmGo/contacts",
				),
				array(
					'name' => 'unFocus',
					'title' => 'unFocus',
					'url' => "/crmGo/unFocus",
				),
			);
		} else { // manager
			$crmMenu = array(
				array(
					'name' => 'contacts',
					'title' => 'Contacts',
					'url' => "/crmGo/contacts",
				),
				array(
					'name' => 'unFocus',
					'title' => 'unFocus',
					'url' => "/crmGo/unFocus",
				),
				array(
					'name' => 'recentContacts',
					'title' => 'Recent Contacts',
					'url' => "/crmGo/recentContacts",
				),
				array(
					'name' => 'users',
					'title' => 'users',
					'url' => "/crmGo/users",
				),
				array(
					'name' => 'online',
					'title' => 'online',
					'url' => "/crmGo/online",
				),
			);
		}
		$userMenu = array(
			array(
				'name' => 'chpass',
				'title' => 'Change Password',
				'url' => "/crm/changePasswd",
			),
			array(
				'name' => 'logout',
				'title' => 'Log Off',
				'url' => "/?logOut=logOut",
			),
		);
		$adminMenu = array(
			array(
				'name' => 'showSource',
				'title' => 'Show Source Code',
				'url' => "/showSource",
			),
			array(
				'name' => 'clone',
				'title' => 'Clone',
				'url' => "https://github.com/ohadaloni/crm",
				'target' => "clone",
			),
		);


		$menu = array(
			'crm' => $crmMenu,
			'admin' => $adminMenu,
			CrmLogin::loginEmail() => $userMenu,
		);
		return($menu);
	}
	/*------------------------------------------------------------*/
}

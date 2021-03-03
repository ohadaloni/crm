<?php
/*------------------------------------------------------------*/
/*------------------------------------------------------------*/
require_once("CrmUtils.class.php");
/*------------------------------------------------------------*/
/*------------------------------------------------------------*/
use com\google\i18n\phonenumbers\PhoneNumberUtil;
use com\google\i18n\phonenumbers\PhoneNumberFormat;
use com\google\i18n\phonenumbers\NumberParseException;
/*------------------------------------------------------------*/
class CrmGo extends Crm {
	/*------------------------------------------------------------*/
	private $priorities = array(
		'none' => 'none.png',
		'lower' => 'grayCircle16x16.png',
		'low' => 'greenCircle16x16.png',
		'medium' => 'yellowCircle16x16.png',
		'high' => 'orangeCircle16x16.png',
		'higher' => 'redCircle16x16.png',
		'urgent' => 'buzzCircle16x16.png',
	);
	/*------------------------------------------------------------*/
	private $userId;
	private $userName;
	private $orgId;
	private $orgName;
	private $crmUser;
	private $mgrId;
	private $mgrName;
	private $priorityJoin = "join crmPriorities on crmContacts.priority = crmPriorities.priority";
	private $contactsOrderBy = "order by crmPriorities.orderBy desc, crmContacts.name";
	/*------------------------------------------------------------*/
	public function __construct() {
		parent::__construct();
		$this->crmUser = $this->crmUser();
		if ( ! $this->crmUser )
			return;
		$this->userId = $this->crmUser['id'];
		$this->orgId = $this->crmUser['orgId'];
		$this->orgName = $this->Mmodel->name("crmOrgs", "org", $this->orgId);
		$this->role = $this->crmUser['role'];
		$this->mgrId = $this->crmUser['mgrId'];
		$this->userName = $this->userName($this->userId);
	}
	/*------------------------------------------------------------*/
	protected function before() {
		parent::before();
		$loginEmail = $this->loginEmail;
		if ( ! $this->crmUser ) {
			$this->Mview->error("before: $loginEmail: Eh?");
			return;
		}
		$this->Mview->register_modifier("userName", array("Crm", "userName",));
		$this->Mview->register_modifier("tagName", array("Crm", "tagName",));
		$this->Mview->register_modifier("numAssigned", array("Crm", "numAssigned",));
		$args = array(
			'userId' => $this->userId,
			'userName' => $this->userName,
			'orgId' => $this->orgId,
			'orgName' => $this->orgName,
			'crmUser' => $this->crmUser,
			'mgrId' => $this->mgrId,
			'mgrName' => $this->mgrName,
			'priorities' => $this->priorities,
			'countries' => $this->tasUtils->countries(),
			'myAssignees' => $this->myAssignees(),
		);
		$this->Mview->assign($args);
		$this->menu();
		$this->seeMe();
	}
	/*------------------------------*/
	public static function numAssigned($userId) {
		global $Mmodel;
		if (  ! $Mmodel )
			$Mmodel = new Mmodel;
		$sql = "select count(*) from crmContacts where assignedId = $userId";
		$numAssigned = $Mmodel->getInt($sql);
		return($numAssigned);
	}
	/*------------------------------*/
	public static function userName($userId, $truncate = null) {
		global $Mmodel;
		if (  ! $Mmodel )
			$Mmodel = new Mmodel;
		if ( ! $userId )
			return("(?)");
		$sql = "select * from users where id = $userId";
		$crmUser = $Mmodel->getRow($sql);
		$name = @$crmUser['name'] ? $crmUser['name'] : @$crmUser['loginEmail'] ;
		if ( $truncate !== null )
			$name = substr($name, 0, $truncate);
		return($name);
	}
	/*------------------------------------------------------------*/
	public static function tagName($tagId) {
		return(CrmUtils::tagName($tagId));
	}
	/*------------------------------------------------------------*/
	public function index() {
		if ( ! $this->crmUser )
			return;
		$this->contacts();
	}
	/*------------------------------------------------------------*/
	public function bulkAssignUp() {
		$myUserId = $this->userId;
		$userId = $_REQUEST['userId'];
		$cond = "assignedId = $userId";
		$now =  date("Y-m-d G:i:s");
		$set = "assignedId = $myUserId, lastTouch = '$now'";
		$sql = "update crmContacts set $set where $cond";
		$affected = $this->Mmodel->sql($sql);
		$assignee = $this->userName($myUserId);
		$assigner = $this->userName($userId);
		$s = $affected == 1 ? "" : "s";
		$this->Mview->tell("$affected contact$s assigned from $assigner to $assignee", array(
			'silent' => true,
			'rememberForNextPage' => true,
		));
		$this->healMe($userId);
		$this->redirect("/crmGo/users");
		
	}
	/*------------------------------------------------------------*/
	public function bulkAssign() {
		$myUserId = $this->userId;
		$userId = $_REQUEST['userId'];
		$howMany = $_REQUEST['howMany'];
		$cond = "assignedId = $myUserId";
		$limit = "limit $howMany";
		$now =  date("Y-m-d G:i:s");
		$set = "assignedId = $userId, lastTouch = '$now'";
		$sql = "update crmContacts set $set where $cond $limit";
		$affected = $this->Mmodel->sql($sql);
		$assigner = $this->userName($myUserId);
		$assignee = $this->userName($userId);
		$s = $affected == 1 ? "" : "s";
		$this->Mview->tell("$affected contact$s assigned from $assigner to $assignee", array(
			'silent' => true,
			'rememberForNextPage' => true,
		));
		$this->healMe($userId);
		$this->redirect("/crmGo/users");
		
	}
	/*------------------------------------------------------------*/
	public function qualiPhone() {
		$phone = $_REQUEST['qualiPhone'];
		$isQuali = $this->_qualiPhone($phone, "IL");
		$show = $isQuali ? "Valid" : "NOT valid";
		$msg = "$phone is $show";
		if ( $isValid )
			$this->Mview->msg($msg);
		else
			$this->Mview->error($msg);
		$this->contacts();
	}
	/*------------------------------------------------------------*/
	private function trafficSourcesMenu() {
		$orgCond = $this->orgCond();
		$fields = "trafficSource, count(*) as cnt";
		$cond = $orgCond;
		$assignedCond = $this->assignedCond();
		$cond .= " and $assignedCond";
		$cond .= " and quali = 1";
		$cond .= " and priority != 'none'";
		$groupBy = "group by 1";
		$having = "having count(*) > 0";
		$orderBy = "order by 2 desc";
		$limit = "limit 21";
		$sql = "select $fields from crmContacts where $cond $groupBy $having $orderBy $limit";
		$trafficSourceCounts = $this->Mmodel->getRows($sql, 15*60);
		$trafficSourceCounts = Mutils::reindexBy($trafficSourceCounts, "trafficSource");
		$trafficSources = array();
		foreach ( $trafficSourceCounts as $trafficSource => $trafficSourceCount )
			$trafficSources[] = array(
				'trafficSource' => $trafficSource,
				'cnt' => $trafficSourceCount['cnt'],
			);
		$this->Mview->showTpl("crm/trafficSources.tpl", array(
			'trafficSources' => $trafficSources,
		));
	}
	/*------------------------------*/
	private function campaignsMenu() {
		$orgCond = $this->orgCond();
		$fields = "campaign, count(*) as cnt";
		$cond = $orgCond;
		$assignedCond = $this->assignedCond();
		$cond .= " and $assignedCond";
		$cond .= " and quali = 1";
		$cond .= " and priority != 'none'";
		$groupBy = "group by 1";
		$having = "having count(*) > 0";
		$orderBy = "order by 2 desc";
		$limit = "limit 21";
		$sql = "select $fields from crmContacts where $cond $groupBy $having $orderBy $limit";
		$campaignCounts = $this->Mmodel->getRows($sql, 15*60);
		$campaignCounts = Mutils::reindexBy($campaignCounts, "campaign");
		$campaigns = array();
		foreach ( $campaignCounts as $campaign => $campaignCount )
			$campaigns[] = array(
				'campaign' => $campaign,
				'cnt' => $campaignCount['cnt'],
			);
		$this->Mview->showTpl("crm/campaigns.tpl", array(
			'campaigns' => $campaigns,
		));
	}
	/*------------------------------*/
	private function flagsMenu() {
		$orgCond = $this->orgCond();
		$sql = "select distinct countryCode from crmContacts where $orgCond order by 1";
		$orgCountryCodes = $this->Mmodel->getStrings($sql, 15*60);

		$fields = "countryCode, count(*) as cnt";
		$cond = $orgCond;
		$assignedCond = $this->assignedCond();
		$cond .= " and $assignedCond";
		$cond .= " and quali = 1";
		$cond .= " and priority != 'none'";
		$groupBy = "group by 1";
		$having = "having count(*) > 0";
		$orderBy = "order by 2 desc";
		$limit = "limit 21";
		$sql = "select $fields from crmContacts where $cond $groupBy $having $orderBy $limit";
		$flagCounts = $this->Mmodel->getRows($sql, 15*60);
		$flagCounts = Mutils::reindexBy($flagCounts, "countryCode");
		$flags = array();
		foreach ( $flagCounts as $countryCode => $flagCount ) {
			$lcase = strtolower($countryCode);
			$img = "$lcase.png";
			$flag = array(
				'countryCode' => $countryCode,
				'country' => $this->tasUtils->countryName($countryCode),
				'img' => $img,
				'cnt' => $flagCount['cnt'],
			);
			$flags[] = $flag;
		}
		$this->Mview->showTpl("crm/flags.tpl", array(
			'flags' => $flags,
		));
	}
	/*------------------------------*/
	public function menu() {
		$this->flagsMenu();
		$this->campaignsMenu();
		$this->trafficSourcesMenu();
	}
	/*------------------------------------------------------------*/
	public function recentContacts() {
		$since = time() - 7*24*60*60;
		$since = date("Y-m-d G:i:s", $since);
		$cond = "lastTouch > '$since'";
		$this->contacts($cond);
	}
	/*------------------------------------------------------------*/
	public function online() {
		$ids = $this->myAssignees();
		$role = $this->role;
		if ( ! $ids ) {
			$this->Mview->error("No users for {$this->userName}");
			return;
		}
		$idList = implode(",", $ids);
		$now = time();
		$since = $now - 15*60;
		$lately = "lastSeen > $since";
		$cond = "id in ( $idList ) and $lately";
		$sql = "select * from users where $cond";
		$users = $this->Mmodel->getRows($sql);
		$this->Mview->msg("\nCurrently Online (by {$this->userName})\n");
		$this->Mview->showTpl("crm/users.tpl", array(
			'users' => $users,
		));
	}
	/*------------------------------------------------------------*/
	public function users() {
		$ids = $this->myAssignees();
		$role = $this->role;
		if ( ! $ids ) {
			$this->Mview->error("No users for {$this->userName}");
			return;
		}
		$idList = implode(",", $ids);
		$sql = "select * from users where id in ( $idList )";
		$users = $this->Mmodel->getRows($sql);
		$this->Mview->msg("\nUsers\n");
		$this->Mview->showTpl("crm/newUser.tpl", array(
		));
		$this->Mview->showTpl("crm/users.tpl", array(
			'users' => $users,
		));
	}
	/*------------------------------------------------------------*/
	public function newUser() {
		$name = $_REQUEST['name'];
		$manager = @$_REQUEST['manager'] != null;
		$loginEmail = $_REQUEST['loginEmail'];
		$passwd = $_REQUEST['passwd'];
		$phone = $_REQUEST['phone'];
		$newUser = array(
			'orgId' => $this->orgId,
			'mgrId' => $this->userId,
			'name' => $name,
			'loginEmail' => $loginEmail,
			'passwd' => $passwd,
			'role' => $manager ? "manager" : "user",
		);
		$this->Mmodel->dbInsert("users", $newUser);
		$this->redirect("/crmGo/users");
	}
	/*------------------------------------------------------------*/
	public function search() {
		$q = $_REQUEST['q'];
		if ( ! $q ) {
			$this->Mview->msg("Search what?");
			return;
		}
		$searchUtils = new SearchUtils;
		$searchTg = $searchUtils->tg($q);
		$cond = $this->contactsCond();
		$sql = "select * from crmContacts where $cond";
		$candidates = $this->Mmodel->getRows($sql);
		$contacts = array();
		foreach ( $candidates as $key => $candidate ) {
			$values = array_values($candidate);
			$runnerStr = implode(" ", $values);
			$runnerTg = $searchUtils->tg($runnerStr, 15*60);
			$closeness = $searchUtils->strCloseness($searchTg, $runnerTg);
			if ( $closeness == 0 )
				continue;
			$candidate['closeness'] = $closeness;
			$contacts[] = $candidate;
		}
		if ( ! $contacts ) {
			$this->Mview->msg("search:$q: Nothing Found\n\n");
			return;
		}
		usort($contacts, array($this, 'cmpCloseness'));
		$slice = array_slice($contacts, 0, 21);
		$this->Mview->showTpl("crm/contacts.tpl", array(
			'contacts' => $slice,
		));
	}
	/*------------------------------*/
	private function cmpCloseness($c1, $c2) {
		$ret = $c2['closeness'] - $c1['closeness'];
		return($ret);
	}
	/*------------------------------------------------------------*/
	private function prioritySums() {
		$orgId = $this->crmUser['orgId'];
		$myUserId = $this->userId;
		$priorities = array_keys($this->priorities);
		$prioritySums = array();
		foreach ( $priorities as $priority ) {
			$cond = "orgId = $orgId and assignedId = $myUserId and priority = '$priority'";
			$sql = "select count(*) from crmContacts where $cond";
			$sum = $this->Mmodel->getInt($sql);
			$prioritySums[$priority] = $sum;
		}
		return($prioritySums);
	}
	/*------------------------------*/
	private function tagSums() {
		$orgId = $this->crmUser['orgId'];
		$myUserId = $this->userId;
		$tags = $this->tags();
		$tagSums = array();
		foreach ( $tags as $tag ) {
			$tagId = $tag['id'];
			$froms = "crmContacts, crmContactTags";
			$join = "crmContacts.id = crmContactTags.contactId";
			$cond =
				"crmContacts.orgId = $orgId"
				." and crmContacts.assignedId = $myUserId"
				." and crmContactTags.tagId = $tagId"
			;
			$sql = "select count(*) from $froms where $join and $cond";
			$sum = $this->Mmodel->getInt($sql);
			$tagSums[$tagId] = $sum;
		}
		return($tagSums);
	}
	/*------------------------------*/
	private function tagIdCond($tagId) {
		$orgId = $this->crmUser['orgId'];
		$myUserId = $this->userId;
		$froms = "crmContacts, crmContactTags";
		$join = "crmContacts.id = crmContactTags.contactId";
		$cond =
			"crmContacts.orgId = $orgId"
			." and crmContacts.assignedId = $myUserId"
			." and crmContacts.priority != 'none'"
			." and crmContactTags.tagId = $tagId"
			;
		$sql = "select contactId from $froms where $join and $cond";
		$contactIds = $this->Mmodel->getStrings($sql);
		if ( ! $contactIds ) {
			/*	Mview::print_r($sql, "sql", basename(__FILE__), __LINE__);	*/
			return("false");
		}
		$contactIdList = implode(",", $contactIds);
		$tagIdCond = "crmContacts.id in ( $contactIdList)";
		return($tagIdCond);
	}
	/*------------------------------*/
	private $narrowFname = array(
			'priority',
			'tagId',
			'countryCode',
			'campaign',
			'trafficSource',
		);
	/*------------------------------*/
	private function narrowCond() {
		$narrowConds = array();
		foreach ( $this->narrowFname as $fname ) {
			$fvalue = @$_REQUEST[$fname];
			if ( $fvalue )
				$this->Mview->setCookie($fname, $fvalue);
			else
				$fvalue = @$_COOKIE[$fname];
			if ( ! $fvalue )
				continue;
			if ( $fname == 'tagId' ) {
				$narrowConds[] = $this->tagIdCond($fvalue);
			} else {
				$fvalueStr = $this->Mmodel->str($fvalue);
				$narrowConds[] = "crmContacts.$fname = '$fvalueStr'";
			}
		}
		if ( ! $narrowConds )
			return(null);
		$narrowCond = implode(" and ", $narrowConds);
		return($narrowCond);
	}
	/*------------------------------*/
	public function unFocus() {
		foreach ( $this->narrowFname as $fname )
			$this->Mview->setCookie($fname, null, -1);
		$this->contacts();
	}
	/*------------------------------*/
	public function contacts($moreCond = null) {
		$restrictCond = "crmContacts.priority != 'none'";
		$cond = $this->contactsCond();
		if ( $moreCond )
			$cond .= " and ( $moreCond )";
		$narrowCond = $this->narrowCond();
		if ( $narrowCond )
			$cond .= " and ( $narrowCond )";
		else
			$cond .= " and ( $restrictCond )";
		$prioritySums = $this->prioritySums();
		$priorityJoin = $this->priorityJoin;
		$orderBy = $this->contactsOrderBy;
		$limit = "limit 20";
		$sql = "select crmContacts.* from crmContacts $priorityJoin where $cond $orderBy $limit";
		$contacts = $this->Mmodel->getRows($sql); // never cache this?
		foreach ( $contacts as $key => $contact ) {
			if ( ! $contact['priority'] )
				$contacts[$key]['priority'] = 'medium';
			$contacts[$key]['priorityImage'] = $this->priorities[$contacts[$key]['priority']];
			$phone = $contact['phone'];
			$phone = trim($phone, "+");
			$pfx = substr($phone, 0, 2);
			if ( $pfx != "00" )
				$phone = "00$phone";
			$contacts[$key]['phone'] = $phone;
			$contacts[$key]['tags'] = $this->contactTags($contact['id']);
			$contacts[$key]['country'] = $this->tasUtils->countryName($contacts[$key]['countryCode']);;
		}
		$this->Mview->showTpl("crm/contacts.tpl", array(
			'contacts' => $contacts,
			'prioritySums' => $prioritySums,
			'tagSums' => $this->tagSums(),
		));
		$this->newContact();
	}
	/*------------------------------------------------------------*/
	public function contact() {
		$contactId = $_REQUEST['contactId'];
		$contact = $this->Mmodel->getById("crmContacts", $contactId);
		$orderBy = "order by id";
		$cond = "contactId = $contactId";
		$sql = "select * from crmComments where $cond $orderBy";
		$comments = $this->Mmodel->getRows($sql);
		$cond = "contactId = $contactId";
		$orderBy = "order by datetime";
		$sql = "select * from crmRings where $cond $orderBy";
		$calls = $this->Mmodel->getRows($sql);
		$this->Mview->showTpl("crm/contact.tpl", array(
			'contact' => $contact,
			'calls' => $calls,
			'comments' => $comments,
		));
	}
	/*------------------------------------------------------------*/
	public function tag() {
		$contactId = $_REQUEST['contactId'];
		$tagId = $_REQUEST['tagId'];
		$cond = "contactId = $contactId and tagId = $tagId";
		$crmContactTag = array(
			'contactId' => $contactId,
			'tagId' => $tagId,
		);
		$this->Mmodel->dbInsert("crmContactTags", $crmContactTag);
		$this->redirect("/crmGo/contacts");
	}
	/*------------------------------*/
	public function unTag() {
		$contactId = $_REQUEST['contactId'];
		$tagId = $_REQUEST['tagId'];
		$cond = "contactId = $contactId and tagId = $tagId";
		$sql = "delete from crmContactTags where $cond";
		$this->Mmodel->sql($sql);
		$this->redirect("/crmGo/contacts");
	}
	/*------------------------------------------------------------*/
	public function comment() {
		$contactId = $_REQUEST['contactId'];
		$comment = $_REQUEST['comment'];
		$this->_comment($contactId, $comment);
		$this->redirect("/crmGo/contact?contactId=$contactId");
	}
	/*------------------------------------------------------------*/
	public function newContact() {
		if ( ! @$_REQUEST['name'] ) {
			$this->Mview->showTpl("crm/newContact.tpl", array(
			));
			return;
		}
		$contact = $_REQUEST;
		$contact['assignedId'] = $this->userId;
		$contact['lastTouch'] = date("Y-m-d G:i:s");
		$contactId = $this->Mmodel->dbInsert("crmContacts", $contact);
		$this->redirect("/crmGo/contact?contactId=$contactId");
	}
	/*------------------------------------------------------------*/
	public function editContact() {
		$contactId = @$_REQUEST['contactId'];
		$contact = $this->Mmodel->getById("crmContacts", $contactId);
		if ( ! @$_REQUEST['phone'] ) {
			$this->Mview->showTpl("crm/editContact.tpl", array(
				'contact' => $contact,
			));
			return;
		}
		$data = $_REQUEST;
		if ( $data['phone'] && $data['countryCode']  )
			$data['quali'] = $this->_qualiPhone($data['phone'], $data['countryCode']);
		if ( $data['phone2'] && $data['countryCode']  )
			$data['quali2'] = $this->_qualiPhone($data['phone2'], $data['countryCode']);

		$data['lastTouch'] = date("Y-m-d G:i:s");
		$this->Mmodel->dbUpdate("crmContacts", $contactId, $data);
		$this->redirect("/crmGo/contact?contactId=$contactId");
	}
	/*------------------------------------------------------------*/
	public function setPriority() {
		$contactId = $_REQUEST['contactId'];
		$priority = $_REQUEST['priority'];
		$back = @$_REQUEST['back'];
		$affected = $this->Mmodel->dbUpdate("crmContacts", $contactId, array(
			'priority' => $priority,
			'lastTouch' => date("Y-m-d G:i:s"),
		));
		if ( $back == 'contact' )
			$url ="/crmGo/contact?contactId=$contactId";
		else
			$url ="/crmGo/contacts";
		$this->redirect($url);
	}
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
	public function user() {
		$userId = $_REQUEST['userId'];
		$user = $this->Mmodel->getById("users", $userId);
		if ( ! $user ) {
			$this->Mview->error("user: userId=$userId: Eh?");
			return;
		}
		$this->feelMe($userId);
		$loginEmail = $user['loginEmail'];
		$contactsCond = $this->contactsCond($userId);
		$restrictCond = "crmContacts.priority != 'none'";
		$cond ="$contactsCond and $restrictCond";
		$orderBy = $this->contactsOrderBy;
		$limit = "limit 20";
		$priorityJoin = $this->priorityJoin;
		$sql = "select crmContacts.* from crmContacts $priorityJoin where $cond $orderBy $limit";
		$contacts = $this->Mmodel->getRows($sql);

		$this->Mview->msg("\nContacts for $loginEmail\n\n");
		$this->Mview->showTpl("crm/contacts.tpl", array(
			'user' => $user,
			'contacts' => $contacts,
		));
	}
	/*------------------------------------------------------------*/
	private function ring($answer) {
		$contactId = $_REQUEST['contactId'];
		$cond = "contactId = '$contactId'";
		$orderBy = "order by epoc desc";
		$limit = "limit 1";
		$sql = "select epoc from crmRings where $cond $orderBy $limt";
		$lastEpoc = $this->Mmodel->getInt($sql);
		$now = time();
		if ( ! $lastEpoc || ( $now - $lastEpoc ) > 60 ) {
			// ignore anything repeating in the last minute for this
			$crmRing = array(
				'contactId' => $contactId,
				'date' => date("Y-m-d"),
				'datetime' => date("Y-m-d G:i:s"),
				'epoc' => $now,
				'answer' => $answer,
			);
			$this->Mmodel->dbInsert("crmRings", $crmRing);
			$this->touch($contactId);
		}
		$this->touchMe();
	}
	/*------------------------------*/
	public function answered() {
		$this->ring(1);
		$this->redirect("/crmGo/contacts");
	}
	/*------------------------------*/
	public function noAnswer() {
		$this->ring(0);
		$this->redirect("/crmGo/contacts");
	}
	/*------------------------------------------------------------*/
	public function assign() {
		$contactId = $_REQUEST['contactId'];
		if ( $this->role == 'user' )
			$assignedId = $this->mgrId;
		else
			$assignedId = $_REQUEST['assignedId'];
		$affected = $this->Mmodel->dbUpdate("crmContacts", $contactId, array(
			'assignedId' => $assignedId,
		));
		if ( ! $affected ) {
			$error = $this->Mmodel->lastError();
			$this->Mview->error("Not assigned $contactId => $assignedId: $error");
			return;
		}
		$this->touch($contactId);
		$this->touchMe();
		$this->healMe($assignedId);
		$back = @$_REQUEST['back'];
		if ( $back == 'contact' )
			$url ="/crmGo/contact?contactId=$contactId";
		else
			$url ="/crmGo/contacts";
		$this->redirect($url);
	}
	/*------------------------------------------------------------*/
	private function settingsForm($crmUser) {
		$this->Mview->showTpl("crm/settings.tpl", array(
			'crmUser' => $crmUser,
		));
	}
	/*------------------------------*/
	public function settings() {
		$userId = $this->userId;
		if ( ! $userId ) {
			exit;
		}
		$crmUser = $this->Mmodel->getById("users", $userId);
		if ( ! $crmUser ) {
			exit;
		}
		if ( ! @$_REQUEST['passwd'] ) {
			$this->settingsForm($user);
			return;
		}
		$passwd = @$_REQUEST['passwd'];
		if ( ! $passwd || ( $passwd != $crmUser['passwd'] && sha1($passwd) != $crmUser['passwd'] ) ) {
			Mview::error("Password incorrect");
			$this->settingsForm($user);
			return;
		}
		$settings = array();
		if ( $_REQUEST['loginEmail'] && $_REQUEST['loginEmail'] != $crmUser['loginEmail'] )
			$settings['loginEmail'] = $_REQUEST['loginEmail'];
		if ( strlen($_REQUEST['passwd1']) >= 4 && $_REQUEST['passwd1'] == $_REQUEST['passwd2'] )
			$settings['passwd'] = sha1($_REQUEST['passwd1']);
		elseif (
				$_REQUEST['passwd1'] &&
				( strlen($_REQUEST['passwd1']) < 4 || $_REQUEST['passwd1'] != $_REQUEST['passwd2'] )
				)
			$this->Mview->error("New passwords are not the same");
		if ( $settings ) {
			$this->Mmodel->dbUpdate("users", $userId, $settings);
			$msg = "Updated";
		} else {
			$msg = "Nothing changed";
		}
		$this->Mview->tell($msg, array(
			'silent' => true,
			'rememberForNextPage' => true,
		));
		$this->redirect("/crm");
	}
	/*------------------------------------------------------------*/
	public function land() {
		$userId = $this->userId;
		$referer = @$_SERVER['HTTP_REFERER'];
		if ( ! $referer ) {
			Mview::error("land: no referer");
			return;
		}
		$pu = parse_url($referer);
		$url = trim($pu['path'], "/");
		$id = $this->Mmodel->sql("select id from crmLandings where userId = '$userId'");
		if ( $url == "/" || $url == "/crm" ) {
			if ( $id )
				$this->Mmodel->dbDelete("landings", $id);
			$this->redirect("/crm");
			return; // notreached
		}
		if ( $id )
			$this->Mmodel->dbUpdate("crmLandings", $id, array(
				'url' => $url,
			));
		else
			$this->Mmodel->dbInsert("crmLandings", array(
				'userId' => $userId,
				'url' => $url,
			));
		Mview::tell("Landed", array(
			'silent' => true,
			'rememberForNextPage' => true,
		));
		$this->redirect($url);
	}
	/*------------------------------------------------------------*/
	private function loadLine($line, $lineNo) {
		static $num = 0;
		// list csv fields - independent of db fields
		$fnames = array(
			'trafficSource',
			'campaign',
			'firstName',
			'lastName',
			'phone',
			'countryCode',
			'email',
		);
		$numFieldsExpected = count($fnames);
		$fields = explode(",", $line);
		$numFields = count($fields);
		if ( $numFields < $numFieldsExpected ) {
			echo "loadLine: too few columns: $line<br />\n";
			return(false);
		}

		$data = array();
		foreach ( $fnames as $key => $fname ) {
			$fvalue = $fields[$key];
			if ( $fname != 'email' )
				$fvalue = preg_replace("/[^A-Za-z0-9 ]*/", "", $fvalue);
			$fvalue = trim($fvalue);
			if ( ! $fvalue ) {
				echo "loadLine: column $key: '$fvalue'<br />\n";
				return(false);
			}
			$data[$fname] = $fvalue;
		}

		// match csv data to crmContacts
		$firstName = $data['firstName'];
		$lastName = $data['lastName'];
		$name = "$lastName, $firstName";
		$name = str_replace("\r\n", "\n", $name);
		$name = preg_replace("/[\s]+/", " ", $name);
		$name = trim($name);
		$phone = $data['phone'];
		$countryCode = $data['countryCode'];
		$quali = $this->_qualiPhone($phone, $countryCode);
		$crmContact = array(
			'orgId' => 1,
			'name' => $name,
			'trafficSource' => $data['trafficSource'],
			'campaign' => $data['campaign'],
			'firstName' => $firstName,
			'lastName' => $lastName,
			'email' => $data['email'],
			'countryCode' => $countryCode,
			'phone' => $phone,
			'quali' => $quali,
			'assignedId' => 1,
			'priority' => "urgent",
			'lastTouch' => date("Y-m-d G:i:s"),
		);
		$id = $this->Mmodel->dbInsert("crmContacts", $crmContact);
		return($id != null);
	}
	/*------------------------------*/
	public function load() {
		$fileName = "crm/upload_16.09.2011.csv";
		$content = @file_get_contents($fileName);
		if ( ! $content ) {
			$cwd = trim(`pwd`);
			$this->Mview->error("load: $cwd/$fileName not found");
			return;
		}
		$content = str_replace("\r\n", "\n", $content);
		$lines = explode("\n", $content);
		array_pop($lines);
		$lineNo = 1;
		$loaded = 0;
		foreach ( $lines as $line )
			$loaded += $this->loadLine($line, $lineNo++) ? 1 : 0 ;
		$numLines = count($lines);
		$this->Mview->msg("$numLines lines, $loaded loaded");
	}
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
	private function isUsAreaCode($areaCode) {
		static $usAreaCodes;

		if ( $areaCode == '555' )
			return(false);
		if ( ! $usAreaCodes )
			$usAreaCodes = $this->Mmodel->getStrings("select code from usAreaCodes", 0);
		return(in_array($areaCode, $usAreaCodes));
	}
	/*------------------------------*/
	private function qualiUS($phone) {
		$phone = str_replace("\s", "", $phone);
		$phone = str_replace("-", "", $phone);
		$phone = trim($phone);
		if ( $phone[0] == '1' )
			$phone = substr($phone, 1);
		$areaCode = substr($phone, 0, 3);
		$rest = substr($phone, 3);
		if (
				! is_numeric($areaCode) 
				|| ! is_numeric($rest)
				|| strlen($rest) != 7
				)
			return(false);
		if ( ! $this->isUsAreaCode($areaCode) )
			return(false);
		return(true);
	}
	/*------------------------------*/
	private function _qualiPhone($phone, $countryCode) {
		if ( substr($phone, 0, 2) == "00" )
			$phone = substr($phone, 2);
		if ( $countryCode == 'US' )
			return($this->qualiUS($phone));
		require_once("libphonenumber-for-PHP/PhoneNumberUtil.php");
		static $phoneUtil;
		if ( ! $phoneUtil )
			$phoneUtil = PhoneNumberUtil::getInstance();
		if ( ! $countryCode ) {
			echo "_qualiPhone: No country code for '$phone'";
		}
		try {
			$proto = $phoneUtil->parse($phone, $countryCode);
		} catch (NumberParseException $e) {
			/*	$error = $e->getMessage();	*/
			/*	echo "_qualiPhone: $error<br />\n";	*/
			return(null);
		}
		try {
			$isValid = $phoneUtil->isValidNumber($proto);
		} catch (Exception $e) {
			/*	$error = $e->getMessage();	*/
			/*	echo "_qualiPhone: $error<br />\n";	*/
			return(null);
		}
		if ( $isValid ) {
			/*	$msg = "_qualiPhone: $phone is valid for $countryCode";	*/
			/*	echo "$msg<br />\n";	*/
		} else {
			/*	$msg = "_qualiPhone: $phone is NOT valid for $countryCode";	*/
			/*	echo "$msg< br/>\n";	*/
		}
		return($isValid == true);
	}
	/*------------------------------------------------------------*/
	private function _comment($contactId, $comment) {
		$this->Mmodel->dbInsert("crmComments", array(
			'contactId' => $contactId,
			'comment' => $comment,
			'date' => date("Y-m-d"),
			'datetime' => date("Y-m-d G:i:s"),
		));
		$this->touch($contactId);
	}
	/*------------------------------*/
	private function touch($contactId) {
		$this->Mmodel->dbUpdate("crmContacts", $contactId, array(
			'lastTouch' => date("Y-m-d G:i:s"),
		));
	}
	/*------------------------------------------------------------*/
	private function crmUser() {
		$loginId = $this->loginId;
		$sql = "select * from users where loginId = '$loginId'";
		$crmUser = $this->Mmodel->getRow($sql);
		return($crmUser);
	}
	/*------------------------------------------------------------*/
	private function contactsCond($userId = null) {
		$orgCond = $this->orgCond();
		$assignedCond = $this->assignedCond($userId);
		$qualiCond = "quali = 1";
 		$cond = "$orgCond and $assignedCond and $qualiCond";
		return($cond);
	}
	/*------------------------------*/
	private function myAssignees() {
		$role = $this->role;
		$userId = $this->userId;
		$mgrId = $this->mgrId;
		$orgCond = $this->orgCond();
		$meCond = "id = $userId";
		$myCond = "mgrId = $userId";
		$myMgrCond = $mgrId ? "mgrId = $mgrId" : "false" ;
		$notMyMgrCond = $mgrId ? "id != $mgrId" : "false" ;
		$mgrsCond = "(role = 'manager' or role = 'accountManager')";
		switch (  $role ) {
			case 'accountManager' :
					$cond = "$orgCond";
				break;
			case 'manager' :
					$cond = "$orgCond and $notMyMgrCond and ( $myCond or $mgrsCond )";
				break;
			case 'user' :
					$cond = "$orgCond and $notMyMgrCond and $mgrsCond";
				break;
			default:
				$this->Mview->error("myAssignees: role='$role': Eh?");
				return(null);
		}
		$sql = "select id from users where $cond";
		$ids = $this->Mmodel->getStrings($sql);
		return($ids);
	}
	/*------------------------------*/
	private function orgCond() {
		$orgId = $this->crmUser['orgId'];
		$cond = "orgId = $orgId";
		return($cond);
	}
	/*------------------------------*/
	private function assignedCond($userId = null) {
		$myUserId = $this->userId;
		$userIdCond = $userId ? "assignedId = $userId" : null;
		$role = $this->role;
		switch (  $role ) {
			case 'user' :
				$myUserIdCond = "assignedId = $myUserId";
				return($myUserIdCond);
			case 'accountManager' :
				return($userId ? $userIdCond : "true");
			case 'manager' :
				break;
			default:
				$this->Mview->error("assignedCond: role='$role': Eh?");
				return("false");
		}
		// manager
		$sql = "select id from users where mgrId = $myUserId";
		$ids = $this->Mmodel->getStrings($sql);
		$ids[] = $myUserId;
		$ids = array_unique($ids);
		if ( $userId && ! in_array($userId, $ids) )
				return("false");
		if ( $userId )
			return($userIdCond);
		if ( ! $ids )
			return("false");
		$idList = implode(",", $ids);
		$assignedCond = "assignedId in ( $idList )";
		return($assignedCond);
	}
	/*------------------------------------------------------------*/
	private function tommy($fname, $userId) {
		$this->Mmodel->dbUpdate("users", $userId, array(
			$fname => time(),
		));
	}
	/*------------------------------*/
	// user was seen around - is online
	private function seeMe() {
		$this->tommy('lastSeen', $this->userId);
	}
	/*------------------------------*/
	// user is observed - by manager or accountManger in individual page
	private function feelMe($userId) {
		$this->tommy('lastFelt', $userId);
	}
	/*------------------------------*/
	// user did something  - is active
	private function touchMe() {
		$this->tommy('lastTouched', $this->userId);
	}
	/*------------------------------*/
	// user was handled ( assigned, or unassigen a contact, etc).
	private function healMe($userId) {
		$this->tommy('lastHealed', $userId);
	}
	/*------------------------------------------------------------*/
	/*------------------------------------------------------------*/
	private function tags() {
		$sql = "select * from crmTags order by orderBy";
		$tags = $this->Mmodel->getRows($sql, 15*60);
		return($tags);
	}
	/*------------------------------------------------------------*/
	private function contactTags($contactId) {
		$sql = "select tagId from crmContactTags where contactId = $contactId";
		$contactTagIds = $this->Mmodel->getStrings($sql);
		$contactTags = $this->tags();
		foreach ( $contactTags as $key => $tag )
			$contactTags[$key]['onOff'] = in_array($tag['id'], $contactTagIds);
		return($contactTags);
	}
}
/*------------------------------------------------------------*/

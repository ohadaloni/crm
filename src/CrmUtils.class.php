<?php
/*------------------------------------------------------------*/
class CrmUtils extends Mcontroller {
	/*------------------------------------------------------------*/
	public static function tagName($tagId) {
		global $Mmodel;
		if (  ! $Mmodel )
			$Mmodel = new Mmodel;
		if ( ! $tagId )
			return("(?)");
		$sql = "select name from crmTags where id = $tagId";
		$name = $Mmodel->getString($sql, 0);
		return($name);
	}
	/*------------------------------------------------------------*/
}
/*------------------------------------------------------------*/

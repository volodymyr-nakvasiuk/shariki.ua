<?php
class ArOn_Acl_Plugin_Location extends ArOn_Acl_Plugin_Abstract {

	public static function toStorage($uid, $gid) {
		$db = ArOn_Db_Table::getDefaultAdapter();

		$storage = array();
		$storage['user']['allow'] = array();
		$storage['user']['deny'] = array();
		$storage['group']['allow'] = array();
		$storage['group']['deny'] = array();

		$sql = "SELECT
					`rules`.`param` as `param`
				FROM
					`acl_rules` `rules`
					INNER JOIN `acl_users_rules` `users_rules` ON `rules`.`id` = `users_rules`.`rule_id`
				WHERE
					`users_rules`.`user_id` = $uid 
					AND `rules`.`type` = 'location'
					AND `rules`.`perm` = 'allow'";
		$rules = $db->fetchCol($sql);
		$storage['user']['allow'] = array_values($rules);

		$sql = "SELECT
					`rules`.`param` as `param`
				FROM
					`acl_rules` `rules`
					INNER JOIN `acl_users_rules` `users_rules` ON `rules`.`id` = `users_rules`.`rule_id`
				WHERE
					`users_rules`.`user_id` = $uid 
					AND `rules`.`type` = 'location'
					AND `rules`.`perm` = 'deny'";
		$rules = $db->fetchCol($sql);
		$storage['user']['deny'] = array_values($rules);

		$sql = "SELECT
					`rules`.`param` as `param`
				FROM
					`acl_rules` `rules`
					INNER JOIN `acl_groups_rules` `groups_rules` ON `rules`.`id` = `groups_rules`.`rule_id`
				WHERE
					`groups_rules`.`group_id` = $gid
					AND `rules`.`type` = 'location'
					AND `rules`.`perm` = 'allow'";
		$rules = $db->fetchCol($sql);
		$storage['group']['allow']= $rules;

		$sql = "SELECT
					`rules`.`param` as `param`
				FROM
					`acl_rules` `rules`
					INNER JOIN `acl_groups_rules` `groups_rules` ON `rules`.`id` = `groups_rules`.`rule_id`
				WHERE
					`groups_rules`.`group_id` = $gid
					AND `rules`.`type` = 'location'
					AND `rules`.`perm` = 'deny'";
		$rules = $db->fetchCol($sql);
		$storage['group']['deny']= $rules;

		return $storage;
	}

	public function check($value) {
		$ga = &$this->_storage['group']['allow'];
		$gd = &$this->_storage['group']['deny'];
		$ua = &$this->_storage['user']['allow'];
		$ud = &$this->_storage['user']['deny'];


		$ok = false;
		foreach ($ga as $rule) {
			if (preg_match($rule, $value)) {
				$ok = true;
				break;
			}
		}
		if ($ok) {
			foreach ($gd as $rule) {
				if (preg_match($rule, $value)) {
					$ok = false;
					break;
				}
			}
		}
		if (!$ok) {
			foreach ($ua as $rule) {
				if (preg_match($rule, $value)) {
					$ok = true;
					break;
				}
			}
		}
		if ($ok) {
			foreach ($ud as $rule) {
				if (preg_match($rule, $value)) {
					$ok = false;
					break;
				}
			}
		}
		return $ok;
	}
}
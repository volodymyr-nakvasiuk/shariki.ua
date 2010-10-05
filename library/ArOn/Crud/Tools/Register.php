<?php
class ArOn_Crud_Tools_Register {
	static public function saveWizard($params, $advertiser_id, $UPLOAD_IMAGES_PATH = null, $IMAGE_ALTERNATIVE_PATH = null) {

		$params ['advertiser_id'] = $advertiser_id;
		$campaign = new Crud_Form_Partner_Campaigns ( );
		$campaign->createForm ();
		$params ['name'] = $params ['campaign_name'];
		if ($result = $campaign->saveData ( $params )) {
			$campaign_id = $result;
		} else {
			return array ('error' => 'campaign not save' );
		}

		$params ['ad_campaign_id'] = $campaign_id;
		$group = new Crud_Form_Partner_Groups ( );
		$group->createForm ();
		$params ['name'] = $params ['group_name'];
		if ($result = $group->saveData ( $params )) {
			$group_id = $result;
		} else {
			return array ('error' => 'groups not save' );
		}

		$params ['ad_group_id'] = $group_id;

		if (isset ( $params ['kwd'] ) && ! empty ( $params ['kwd'] )) {
				
			foreach ( $params ['kwd'] as $info ) {
				if (empty ( $info ['keyword'] ))
				continue;
				$keywords = new Crud_Form_Partner_Keywords ( );
				$keywords->createForm ();

				$result = $keywords->saveData ( array ('is_active' => $info ['status'], 'match_type' => 'broad', 'ad_group_id' => $group_id, 'kwd' => $info ['keyword'], 'bid' => $info ['bid'] ) );
				$errors = $keywords->getErrorMessages ();
				if ($result === false && count ( $errors ) > 0) {
					return array ('error' => 'keywords not save' );
				}

				if (is_array ( $result )) {
					return array ('error' => 'keywords not save' );
				}
					
			}

		}

		$creative = new Crud_Form_Partner_Banners ( );
		$creative->createForm ();
		if (! $creative_id = $creative->saveData ( $params )) {
			return array ('error' => 'creative not save' );
		}
		if (isset ( $params ['upload_image_guid'] ) && ! empty ( $params ['upload_image_guid'] ) && $UPLOAD_IMAGES_PATH != null) {
			$file_info = pathinfo ( $params ['upload_image_guid'] );
			$file_name = 'creatives-' . $creative_id;
			$file_type = $file_info ['extension'];
				
			$tmpFilePath = $UPLOAD_IMAGES_PATH . "/" . $params ['upload_image_guid'];
			$fileFullName = $UPLOAD_IMAGES_PATH . "/" . $file_name . "." . $file_type;
			if (file_exists ( $tmpFilePath ))
			rename ( $tmpFilePath, $fileFullName );
				
			if ($IMAGE_ALTERNATIVE_PATH != null) {
				$tmpFilePath = $UPLOAD_IMAGES_PATH . "/" . $IMAGE_ALTERNATIVE_PATH . "/" . $params ['upload_image_guid'];
				$fileFullName = $UPLOAD_IMAGES_PATH . "/" . $IMAGE_ALTERNATIVE_PATH . "/" . $file_name . "." . $file_type;
				if (file_exists ( $tmpFilePath ))
				rename ( $tmpFilePath, $fileFullName );
			}
				
			$def_data = array ('image' => $file_name . "." . $file_type );
			$creative->getModel ()->update ( $def_data, $creative->getModel ()->getAdapter ()->quoteInto ( $creative->getModel ()->getPrimary () . " = ?", $creative_id ) );
		}
		return true;
	}

	public function __construct() {
		$this->registerData ();
	}

	public static function registerData() {

		Zend_Registry::set ( 'cities' ,
		array ( '1' => 'Винница', '2' => 'Днепропетровск', '3' => 'Донецк', '4' => 'Житомир', '5' => 'Запорожье', '6' => 'Ивано-Франковск', '7' => 'Киев', '8' => 'Кировоград', '9' => 'Луганск', '10' => 'Луцк', '11' => 'Львов', '12' => 'Николаев', '13' => 'Одесса', '14' => 'Полтава', '15' => 'Ровно', '16' => 'Симферополь', '17' => 'Сумы', '18' => 'Тернополь', '19' => 'Ужгород', '20' => 'Харьков', '21' => 'Херсон', '22' => 'Хмельницкий', '23' => 'Черкассы', '24' => 'Чернигов', '25' => 'Черновцы' ) );
		Zend_Registry::set ( 'regions',
		array ( '1' => 'Винницкая', '2' => 'Днепропетровская', '3' => 'Донецкая', '4' => 'Житомирская', '5' => 'Запорожская', '6' => 'Ивано-Франковская', '7' => 'Киевская', '8' => 'Кировоградская', '9' => 'Луганская', '10' => 'Волынская', '11' => 'Львовская', '12' => 'Николаевская', '13' => 'Одесская', '14' => 'Полтавская', '15' => 'Ровенская', '16' => 'Республика Крым', '17' => 'Сумская', '18' => 'Тернопольская', '19' => 'Закарпатская', '20' => 'Харьковская', '21' => 'Херсонская', '22' => 'Хмельницкая', '23' => 'Черкасская', '24' => 'Черниговская', '25' => 'Черновецкая' ) );
		Zend_Registry::set ( 'colors' ,
		array ( '1' => 'Бежевый', '2' => 'Белый', '3' => 'Бирюзовый', '4' => 'Бордовый', '5' => 'Бронзовый', '6' => 'Вишнёвый', '7' => 'Голубой', '8' => 'Желтый', '9' => 'Зеленый', '10' => 'Золотистый', '11' => 'Коричневый', '12' => 'Красный', '13' => 'Малиновый', '14' => 'Оливковый', '15' => 'Розовый', '16' => 'Салатовый', '17' => 'Серебристый', '18' => 'Светло-серый', '19' => 'Серый', '20' => 'Тёмно-серый', '21' => 'Синий', '22' => 'Фиолетовый', '23' => 'Черный' ) );
		Zend_Registry::set ( 'currency' ,
		array ('1' => '$(USD)', '2' => '€(EUR)', '3' => 'грн.(UAH)' ) );
		Zend_Registry::set ( 'currency_a',
		array ('1' => '$', '2' => '€', '3' => 'грн.' ) );

		$years = array();
		for ($i_year=(int)date('Y'); $i_year>=1900; $i_year--) {
			$years[$i_year] = $i_year;
		}
		Zend_Registry::set ( 'year' , $years );


	}

	static function getsql($query, $bind) {

		$aq = explode ( '?', $query );
		if (count ( $aq ) != (count ( $bind ) + 1)) {
			throw new Exception ( "Не совпадает шаблон prepare() и количество вызовов bind()" );
		}

		$a = array_values ( $bind );
		$q = '';
		for($i = 0; $i < count ( $aq ) - 1; $i ++) {
			$q .= $aq [$i] . "'" . ($a [$i]) . "'";
		}
		$q .= $aq [$i];
		return $q;
	}

}
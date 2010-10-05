<?php
class ArOn_Crud_Tools_Date{
	
	public static function russian_date() {
		$translation = array(
			"am" => "дп",
			"pm" => "пп",
			"AM" => "ДП",
			"PM" => "ПП",
		
			"Monday" => "Понедельник",
			"Mon" => "Пн",
			"Tuesday" => "Вторник",
			"Tue" => "Вт",
			"Wednesday" => "Среда",
			"Wed" => "Ср",
			"Thursday" => "Четверг",
			"Thu" => "Чт",
			"Friday" => "Пятница",
			"Fri" => "Пт",
			"Saturday" => "Суббота",
			"Sat" => "Сб",
			"Sunday" => "Воскресенье",
			"Sun" => "Вс",
		
			"January" => "Января",
			"{{January}}" => "Январь",
			"Jan" => "Янв",
		
			"February" => "Февраля",
			"{{February}}" => "Февраль",
			"Feb" => "Фев",
		
			"March" => "Марта",
			"{{March}}" => "Март",
		
			"Mar" => "Мар",
			"April" => "Апреля",
			"{{April}}" => "Апрель",
		
			"Apr" => "Апр",
			"{{May}}" => "Май",
			"May" => "Мая",
		
			"June" => "Июня",
			"{{June}}" => "Июнь",
			"Jun" => "Июн",
		
			"July" => "Июля",
			"{{July}}" => "Июль",
			"Jul" => "Июл",
		
			"August" => "Августа",
			"{{August}}" => "Августь",
			"Aug" => "Авг",
		
			"September" => "Сентября",
			"{{September}}" => "Сентябрь",
			"Sep" => "Сен",
		
			"October" => "Октября",
			"{{October}}" => "Октябрь",
			"Oct" => "Окт",
		
			"November" => "Ноября",
			"{{November}}" => "Ноябрь",
			"Nov" => "Ноя",
		
			"December" => "Декабря",
			"{{December}}" => "Декабрь",
			"Dec" => "Дек",
		
			"st" => "ое",
			"nd" => "ое",
			"rd" => "е",
			"th" => "ое",
		);
		if (func_num_args() > 1) {
			$timestamp = func_get_arg(1);
			return strtr(date(func_get_arg(0), $timestamp), $translation);
		} else {
			return strtr(date(func_get_arg(0)), $translation);
		};
	}

}
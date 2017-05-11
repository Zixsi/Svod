<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Application
{
	// Текущий адрес страницы
	public static function GetUrl()
	{
		$CI =& get_instance();
		return $CI->uri->uri_string();
	}
	
	// Установить сообщение
	public static function SetMsg($key, $value = null)
	{
		$_SESSION[$key] = $value;
	}
	
	// Получить сообщение
	public static function GetMsg($key)
	{
		$_data = (!empty($_SESSION[$key]))?$_SESSION[$key]:null;
		unset($_SESSION[$key]);
		return $_data;
	}
	
	// Вывод сообщения
	public static function ShowMessage($data = array())
	{
		if(!empty($data['type']) && !empty($data['text']))
		{
			$_type = '';
			switch($data['type'])
			{
				case 'success': $_type = 'success'; break;
				case 'error': $_type = 'danger'; break;
				case 'warning': $_type = 'warning'; break;
				default: $_type = 'info'; break;
			}
		
			echo '<div class="alert alert-'.$_type.'">';
			foreach($data['text'] as $text)
				echo '<p>'.$text.'</p>';
			echo '</div>';
		}
	}
	
		// Данные для вывода
	public static function MakeData($array1 = array(), $array2 = array())
	{
		if(!is_array($array1))
			$array1 = array();
		elseif(array_key_exists('arResult', $array1))
			$array1 = $array1['arResult'];
			
		if(!is_array($array2))
			$array2 = array();
		elseif(array_key_exists('arResult', $array2))
			$array2 = $array2['arResult'];
			
		return array('arResult' => array_merge($array1, $array2));
	}
	
	//Генерация ключа безопасности
	public static function GetKey($pref = null)
	{
		$CI =& get_instance();
		$CI->load->helper('string');
	
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		if(!empty($pref))
			$pref = '_'.$pref;
		$_SESSION['csrfkey'.$pref] = $key;
		$_SESSION['csrfvalue'.$pref] = $value;
		return array('key' => $key, 'value' => $value);
	}

	//Проверка ключа безопасности
	public static function ValidKey($pref = null)
	{
		if(!empty($pref))
			$pref = '_'.$pref;
			
		return (!empty($_SESSION['csrfkey'.$pref]) && !empty($_POST[$_SESSION['csrfkey'.$pref]]) && $_POST[$_SESSION['csrfkey'.$pref]] == $_SESSION['csrfvalue'.$pref])?true:false;
	}
	
	// Валидация email
	public static function ValidEmail($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}
	
	// Только буквы
	public static function ValidAlpha($str)
	{
		return (!preg_match("/^([а-яА-ЯЁёa-zA-Z\s])+$/ui", $str)) ? FALSE : TRUE;
	}
	
	// Буквы и цифры
	public static function ValidAlphaNumeric($str)
	{
		return ( ! preg_match("/^([а-яА-ЯЁёa-zA-Z0-9\s])+$/ui", $str)) ? FALSE : TRUE;
	}

	// Цифры
	public static function ValidNumeric($str)
	{
		return (!preg_match("/^([0-9])+$/ui", $str)) ? FALSE : TRUE;
	}
	
	// Буквы, цифры и знаки тере и подчеркивание
	public static function ValidAlphaDash($str)
	{
		return ( ! preg_match("/^([а-яА-ЯЁёa-zA-Z0-9_-\s])+$/ui", $str)) ? FALSE : TRUE;
	}
	
	// A-Z, цифры, знаки тере и подчеркивание
	public static function ValidAZAlphaDash($str)
	{
		return ( ! preg_match("/^([a-zA-Z0-9_-])+$/ui", $str)) ? FALSE : TRUE;
	}
	
	
	// Очистить маску ввода телефона
	public static function ClearMaskPhone($str = '')
	{
		$str = str_replace('+7', '', $str);
		$str = str_replace(' ', '', $str);
		if(substr($str, 0 , 1).'' == '8' && strlen($str) >= 11)
			$str = substr_replace($str, '', 0, 1);
		return $str;
	}
	
	// Очистить маску ввода СНИЛС
	public static function ClearMaskSnils($str = '')
	{
		$str = str_replace(' ', '', $str);
		$str = str_replace('-', '', $str);
		return $str;
	}
	
	public static function SnilsFormat($str = '')
	{
		$res = '';
		$res = substr($str, 0, 3).'-'.substr($str, 3, 3).'-'.substr($str, 6, 3).' '.substr($str, 9, 2);
		return $res;
	}
	
	public static function PageNavConfig($url ='', $by = 10, $total = 10)
	{
		$config = array();
		$config['base_url'] = $url;
		$config['page_query_string'] = TRUE;
		$config['total_rows'] = $total;
		$config['per_page'] = $by;
		$config['num_links'] = 2;
		$config['full_tag_open'] = '<ul class="pagination">';
		$config['full_tag_close'] = '</ul>';
		$config['first_link'] = '';
		$config['first_tag_open'] = '';
		$config['first_tag_close'] = '';
		$config['first_url'] = '';
		$config['last_link'] = '';
		$config['last_tag_open'] = '';
		$config['last_tag_close'] = '';
		$config['last_url'] = '';
		$config['next_link'] = '&raquo;';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_link'] = '&laquo;';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a>';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		
		return $config;
	}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Debug
{
	// Вывод массива
	public static function Log($data = array())
	{
		echo '<pre>'; print_r($data); echo '</pre>';
	}
}

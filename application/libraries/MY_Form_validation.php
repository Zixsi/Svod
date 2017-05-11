<?php
class MY_Form_validation extends CI_Form_validation
{
	public function __construct($rules = array())
	{
		parent::__construct($rules);
	}
	
	/**
	 * Valid Email
	 *
	 * @param	string
	 * @return	bool
	 */
	public function valid_email($str)
	{
		return (bool) preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str);
	}

	/**
	 * Valid Emails
	 *
	 * @param	string
	 * @return	bool
	 */
	public function valid_emails($str)
	{
		$res = true;
		
		if(strpos($str, ',') === FALSE)
			$res = $this->valid_email(trim($str));

		foreach (explode(',', $str) as $email)
		{
			if (trim($email) !== '' && $this->valid_email(trim($email)) === FALSE)
				$res = FALSE;
		}

		return $res;
	}
	
	/**
	* Кирилица, латиница
	* 
	* @param string
	* @return bool
	**/
	public function all_alpha($str)
	{
		return (bool) preg_match('/^[а-яА-ЯЁёa-zA-Z]+$/ui', $str);
	}
	
	/**
	* Кирилица, латиница, пробел
	* 
	* @param string
	* @return bool
	**/
	public function all_alpha_spaces($str)
	{
		return (bool) preg_match('/^[а-яА-ЯЁёa-zA-Z ]+$/ui', $str);
	}
	
	/**
	* Кирилица, латиница, цифры
	* 
	* @param string
	* @return bool
	**/
	public function all_alpha_numeric($str)
	{
		return (bool) preg_match('/^[а-яА-ЯЁёa-zA-Z0-9]+$/ui', $str);
	}

	/**
	* Кирилица, латиница, цифры и пробел
	* 
	* @param string
	* @return bool
	**/
	public function all_alpha_numeric_spaces($str)
	{
		return (bool) preg_match('/^[а-яА-ЯЁёa-zA-Z0-9 ]+$/ui', $str);
	}

	/**
	 * Кирилица, латиница, цифры, знаки подчеркивания и тире
	 *
	 * @param string
	 * @return bool
	 */
	public function all_alpha_dash($str)
	{
		return (bool) preg_match('/^[а-яА-ЯЁёa-zA-Z0-9_-]+$/ui', $str);
	}
	
	/**
	* Кирилица
	* 
	* @param string
	* @return bool
	**/
	public function ru_alpha($str)
	{
		return (bool) preg_match('/^[а-яА-ЯЁё]+$/ui', $str);
	}
	
	/**
	* Кирилица, пробел
	* 
	* @param string
	* @return bool
	**/
	public function ru_alpha_spaces($str)
	{
		return (bool) preg_match('/^[а-яА-ЯЁё ]+$/ui', $str);
	}
	
	/**
	* Кирилица, цифры
	* 
	* @param string
	* @return bool
	**/
	public function ru_alpha_numeric($str)
	{
		return (bool) preg_match('/^[а-яА-ЯЁё0-9]+$/ui', $str);
	}

	/**
	* Кирилица, цифры и пробел
	* 
	* @param string
	* @return bool
	**/
	public function ru_alpha_numeric_spaces($str)
	{
		return (bool) preg_match('/^[а-яА-ЯЁё0-9 ]+$/ui', $str);
	}

	/**
	 * Кирилица, цифры, знаки подчеркивания и тире
	 *
	 * @param string
	 * @return bool
	 */
	public function ru_alpha_dash($str)
	{
		return (bool) preg_match('/^[а-яА-ЯЁё0-9_-]+$/ui', $str);
	}
}

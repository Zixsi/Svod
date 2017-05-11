<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class CTransaction extends CI_Model
{
	// конфигурация
	private $table = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		
		// Агенты
		$this->table['transaction'] = 'transaction';
	}
	
	// select запроса к БД
	public function select($data)
	{
		if(isset($data) && !empty($data))
				$this->db->select($data);
		return $this;
	}

	// where запроса к БД
	public function where($data, $val = null)
	{
		if(isset($data) && !empty($data))
		{
			if(!is_array($data))
				$this->db->where($data, $val);
			else
				$this->db->where($data);
		}
		return $this;
	}

	// order by запроса к БД
	public function order_by($by, $order='desc')
	{
		if(isset($by) && !empty($by))
			$this->db->order_by($by, $order);
		return $this;
	}

	// limit запроса БД
	public function limit($limit, $offset = 0)
	{
		if((int)$limit > 0)
		{
			if($offset > 0)
				$this->db->limit($limit, $offset);
			else
				$this->db->limit($limit);
		}
		return $this;
	}

	// like запроса БД
	public function like($like, $value = NULL, $position = 'both')
	{
		if(isset($like) && !empty($like))
		{
			if(!is_array($like))
			{
				$like = array($like => array(
					'value' => $value,
					'position' => $position,
				));
			}

			$this->db->like($like);
		}
		return $this;
	}

	// Строка из результатов запроса в виде объекта
	public function row()
	{
		return $this->response->row();
	}

	// Строка из результатов запроса в виде массива
	public function row_array()
	{
		return $this->response->row_array();
	}

	// Результат запроса в виде объекта
	public function result()
	{
		return $this->response->result();
	}

	// Результат запроса в виде массива
	public function result_array()
	{
		return $this->response->result_array();
	}

	/**
	 * Кол-во строк в результате запроса
	 * 
	 * @return type
	 */
	public function num_rows()
	{
		return $this->response->num_rows();
	}

	/*=============================================================*/
	
	public function Get()
	{
		$arFields = array();
		
		$this->select(array());
		$num = $this->db->get($this->table['transaction'])->num_rows();
		if(!$num)
		{
			$arFields = array('ID' => 1, 'STATUS' => 0, 'DATE' => date('Y.m.d H:i:s'));
			$this->Add($arFields);
		}
		else
		{
			$arFields = $this->db->get($this->table['transaction'])->row_array();
		}
		return $arFields;
	}
	
	public function NewTransaction()
	{
		$t = $this->Get();
		$newID = intval($t['ID']) + 1;
		$this->Update($t['ID'], array('ID' => $newID, 'STATUS' => 0));
		return $this->Get();
	}
	
	/**
	* Добавить
	* @param array $arrFields
	* @return boll | int
	**/
	private function Add($arrFields = array())
	{
		$this->db->insert($this->table['transaction'], $arrFields);
		$id = $this->db->insert_id();
		return $id;
	}
	
	/**
	* Обновить
	* @param array $arrFields
	* @return boll | int
	**/
	public function Update($id, $arrFields = array())
	{
		$res = false;
		
		if(!empty($id) && $id > 0 && !empty($arrFields))
		{
			$arrFields['DATE'] = date('Y.m.d H:i:s');
			$this->db->where(array('ID' => $id));
			if($this->db->update($this->table['transaction'], $arrFields))
				$res = true;
		}
		
		return $res;
	}
}

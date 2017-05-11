<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class CReport extends CI_Model
{
	// конфигурация
	private $table = array();
	private $_transaction = 0;
	
	private $_needCsvHeader = array(
		
		'2' => 'Принадлежность к базе',
		'5' => 'Блок СС',
		'8' => 'СтатусПроверки',
		'11' => 'Блоки',
		'13' => 'ЦПП',
		'14' => 'ЯвляетсяДублем',
		'15' => 'Категория',
		'16' => 'Подкатегория',
		'20' => 'Месяц',
		'53' => 'СтатусОплаты',
	);
	
	private $_mirrorHeaderArray = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		
		$this->table['records'] = 'records';
		$this->table['reports'] = 'reports';
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
	
	/*public function CreateTableFields($n = 0)
	{
		for($i = 0; $i < $n; $i++)
		{
			$this->db->query('ALTER TABLE `'.$this->table['records'].'` ADD `FIELD_'.$i.'` VARCHAR(255) NOT NULL');
		}
	}*/
	
	public function GetList()
	{
		$this->order_by('DATE', 'DESC');
		return $this->db->get($this->table['reports'])->result_array();
	}
	
	
	public function SetTransaction($id)
	{
		if($id > 0)
			$this->_transaction = $id;
	}
	
	public function CsvHeader($filePath)
	{
		$file = fopen($filePath, "rb");
		$data = fgetcsv($file, 10000, ";");
		$num = count($data);
		echo '<h3>CSV Header:</h3>';
		for($c = 0; $c < $num; $c++)
		{
			$data[$c] = iconv('windows-1251', 'utf-8', $data[$c]); 
			echo '['.$c.']: '.$data[$c].'<br>';
		}
		echo '=========================<br>';
	}
	
	public function CsvCheckHeader($filePath)
	{
		$result = array(
			'success' => true,
			'msg' => array(),
		);
		
		$file = fopen($filePath, "rb");
		$data = fgetcsv($file, 10000, ";");
		$num = count($data);
		for($c = 0; $c < $num; $c++)
		{
			$data[$c] = iconv('windows-1251', 'utf-8', $data[$c]); 
		}
		
		foreach($this->_needCsvHeader as $key => $val)
		{
			if(!in_array($val, $data))
			{
				$result['msg'] = 'Не найден заголовок "'.$val.'"';
				if($result['success'])
					$result['success'] = false;
			}
			else
			{
				$k = array_search($val, $data);
				$this->_mirrorHeaderArray[$k] = $key;
			}
		}
		
		return $result;
	}
	
	public function CsvToSQL($filePath, $start = 0, $limit = 10)
	{
		$result = false;
		
		if(!$this->_transaction)
		{
			die("Не задана транзакция");
			return false;
		}
		
		if($start < 1)
			$start = 1;
		
		if(!count($this->_mirrorHeaderArray))
			$this->CsvCheckHeader($filePath);
		
		$file = fopen($filePath, "rb");
		
		$empty = 0;
		$n = 0;
		for($i = 0; $data = fgetcsv($file, 10000, ";"); $i++)
		{
			if($start > $i)
				continue;
			
			if($empty >= 3)
				break;
			
			if(empty($data[0]))
			{
				$empty++;
				continue;
			}
			else
				$empty = 0;
			
			
			
			$num = count($data);
			$arrFields['TRANSACTION'] = $this->_transaction;
			
			$fillCell = array();
			for($c = 0; $c < $num; $c++)
			{
				if(array_key_exists($c, $this->_mirrorHeaderArray))
				{
					$data[$c] = iconv('windows-1251', 'utf-8', $data[$c]);
					
					$cell = $this->_mirrorHeaderArray[$c];
					$fillCell[] = $cell;
					$arrFields['FIELD_'.$cell] = $data[$c];
				}
				
				if(!in_array($c, $fillCell))
					$arrFields['FIELD_'.$c] = 0;
				
				//$arrFields['FIELD_'.$c] = $data[$c];
				//echo '['.$c.']: '.$data[$c].'<br>';
			}
			
			$this->Add($arrFields);
			$n++;
			
			if($n >= $limit)
			{
				$result = true;
				break;
			}
		}
		
		return $result;
	}
	
	
	
	public function CreateDataReport()
	{
		$this->load->library('RPartner');
		
		$data = array(
			'SUMM' => null, // Суммарная статистика
			'PARTNERS' => array(), // Статистика по партнерам
		);
		
		// Список партнеров
		$this->select(array('FIELD_3 as VAL'));
		$this->where(array('FIELD_3 !=' => '', 'TRANSACTION' => $this->_transaction));
		$this->db->group_by('FIELD_3');
		$res = $this->db->get($this->table['records'])->result_array();
		
		foreach($res as $val)
		{
			$partner = new RPartner();
			$partner->name = $val['VAL'];
			$data['PARTNERS'][] = $partner;
		}
		unset($res);
		
		// Данные для каждого из партнеров
		foreach($data['PARTNERS'] as &$val)
		{
			$this->where(array('FIELD_20 !=' => '', 'FIELD_3' => $val->name, 'TRANSACTION' => $this->_transaction));
			$res = $this->db->get($this->table['records'])->result_array();
			if($res)
				$val->SetData($res);
			$val->Calc();
			
			if(!$data['SUMM'])
				$data['SUMM'] = new RPartnerRow();
	
			for($i = 1; $i <= 38; $i++)
				$data['SUMM']->{"F$i"} += $val->summ->{"F$i"};
			
			unset($res);
			//break;
		}
		
		return $data;
	}
	
	/**
	* Добавить
	* @param array $arrFields
	* @return boll | int
	**/
	public function Add($arrFields = array())
	{
		$this->db->insert($this->table['records'], $arrFields);
		$id = $this->db->insert_id();
		return $id;
	}
	
	/**
	* Удалить
	* @param int $transaction
	* @return boll
	**/
	public function Delete()
	{
		$result = false;
		
		if(!empty($this->_transaction) && $this->_transaction > 0)
		{
			if($this->db->delete($this->table['records'], array('TRANSACTION' => $this->_transaction)))
				$result = true;
		}
		
		return $result;
	}
	
		/**
	* Добавить
	* @param array $arrFields
	* @return boll | int
	**/
	public function AddReport($arrFields = array())
	{
		$arrFields['DATE'] = date('Y.m.d H:i:s', time());
		
		$this->db->insert($this->table['reports'], $arrFields);
		$id = $this->db->insert_id();
		return $id;
	}
	
	/**
	* Удалить
	* @param int $transaction
	* @return boll
	**/
	public function DeleteOld()
	{
		$result = false;
		
		if($this->db->delete($this->table['reports'], array('DATE <' => date('Y.m.d H:i:s', strtotime("-1 year", time())))))
			$result = true;
		
		return $result;
	}
}

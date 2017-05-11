<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Партнер
class RPartner
{
	private $_data = array(); // Все данные
	
	public $name = '';
	public $summ = null; // Суммарная информация
	public $month = array(); // Месяца
	
	public function __construct()
	{
		$this->summ = new RPartnerRow();
	}
	
	// Устанавливаем данные
	public function SetData($data = array())
	{
		$this->_data = $data;
	}
	
	// Расчет всех значений для партнера
	public function Calc()
	{
		$this->GetMonth();
		$this->SummMonth();
	}
	
	// Разбиваем данные по месяцам
	private function GetMonth()
	{
		foreach($this->_data as $row)
		{
			$key = md5($row['FIELD_20']);
			if(!array_key_exists($key, $this->month))
			{
				$this->month[$key] = new RPartnerMonth();
				$this->month[$key]->name = $row['FIELD_20'];
			}
			
			$this->month[$key]->AddData($row);
			$this->month[$key]->Calc();
		}
		
		//Debug::Log($this->month);
		
		unset($this->_data);
	}
	
	private function SummMonth()
	{
		foreach($this->month as $m)
		{
			for($i = 1; $i <= 38; $i++)
				$this->summ->{"F$i"} += $m->row->{"F$i"};
		}
	}
}

class RPartnerMonth
{
	public $name = '';
	public $row = null;
	
	private $_data;
	
	public function __construct()
	{
		$this->row = new RPartnerRow();
	}
	
	// Устанавливаем данные
	public function SetData($data = array())
	{
		$this->_data = $data;
	}
	
	// Добавляем данные
	public function AddData($data = array())
	{
		$this->_data[] = $data;
	}
	
	// Расчет значений для месяца
	public function Calc()
	{
		foreach($this->_data as $row)
		{
			switch($row['FIELD_2'])
			{
				case '1С':
					$this->row->F3++; // Заведено в 1С
					
					if($row['FIELD_15'] == 'нужно доработать')
					{
						if($row['FIELD_8'] == 'Недозвон')
							$this->row->F16++; // недозвон
						elseif($row['FIELD_8'] == 'Сформированные СЗ')
							$this->row->F17++; // Сформированные СЗ
						elseif($row['FIELD_8'] == 'В работе')
							$this->row->F18++; // в работе
							
						if($row['FIELD_13'] == 'Ошибки в бумаге')
							$this->row->F19++; // ошибки
						elseif($row['FIELD_13'] == 'Нет бумаги')
							$this->row->F20++; // нет бумаги
						elseif($row['FIELD_13'] == 'Бумага ОК')
							$this->row->F24++; // Бумага Ок
							
							
						if($row['FIELD_11'] == 'дубль телефонов')
							$this->row->F21++; // дубли телефонов
						elseif($row['FIELD_11'] == 'блок по адресам')
							$this->row->F22++; // блок по адресам
						elseif($row['FIELD_11'] == 'неверные данные')
							$this->row->F23++; // неверные данные
					}
					
					if($row['FIELD_15'] == 'не проходит в оплату')
					{
						if($row['FIELD_14'] == 'Да')
							$this->row->F25++; // дубль картель
						
						if($row['FIELD_11'] == 'Дубль сверки')
							$this->row->F26++; // дубль сверки
						elseif($row['FIELD_11'] == 'блок паспорта')
							$this->row->F27++; // блок паспорта
						elseif($row['FIELD_11'] == 'Не пройдена СМС верификация')
							$this->row->F28++; // Не пройдена СМС верификация
						elseif(in_array($row['FIELD_11'], array('агент Рождественская СС - доп.проверка', 'агент Чернаков ПВ - доп. проверка', 'блок СБ')))
							$this->row->F29++; // блок СБ
						elseif($row['FIELD_11'] == 'блок по решению партнера')
							$this->row->F30++; // блок по решению партнера
						elseif($row['FIELD_11'] == 'умер ЗЛ')
							$this->row->F32++; // умер ЗЛ
						elseif($row['FIELD_11'] == 'блок партнер')
							$this->row->F33++; // блок партнер
						elseif(in_array($row['FIELD_11'], array('блок по умершим', 'блок по умершим (на проверке)')))
							$this->row->F34++; // блок по умершим
						elseif($row['FIELD_11'] == 'террорист')
							$this->row->F35++; // террорист
							
						if($row['FIELD_5'] == '' && $row['FIELD_11'] == '' && ($row['FIELD_14'] == '' || $row['FIELD_14'] == 'Нет'))
						{
							if($row['FIELD_8'] == 'Отказ')
								$this->row->F36++; // отказ
							elseif($row['FIELD_8'] == 'Договор заключал, но будет расторгать')
								$this->row->F37++; // закл, но будет расторг.
						}
						
						if($row['FIELD_13'] == 'Бумага ОК')
							$this->row->F38++; // Бумага ОК
					}
					
					if($row['FIELD_5'] == 'блок')
						$this->row->F31++; // блок СС
				break;
				
				case 'Сатурн/выгружено':
					$this->row->F4++; // Заведено в Сатурн
					
					if($row['FIELD_15'] == 'нужно доработать')
					{
						if($row['FIELD_8'] == 'Недозвон')
							$this->row->F16++; // недозвон
						elseif($row['FIELD_8'] == 'Сформированные СЗ')
							$this->row->F17++; // Сформированные СЗ
						elseif($row['FIELD_8'] == 'В работе')
							$this->row->F18++; // в работе
							
						if($row['FIELD_13'] == 'Ошибки в бумаге')
							$this->row->F19++; // ошибки
						elseif($row['FIELD_13'] == 'Нет бумаги')
							$this->row->F20++; // нет бумаги
						elseif($row['FIELD_13'] == 'Бумага ОК')
							$this->row->F24++; // Бумага Ок
							
						if($row['FIELD_11'] == 'дубль телефонов')
							$this->row->F21++; // дубли телефонов
						elseif($row['FIELD_11'] == 'блок по адресам')
							$this->row->F22++; // блок по адресам
						elseif($row['FIELD_11'] == 'неверные данные')
							$this->row->F23++; // неверные данные
					}
					
					if($row['FIELD_15'] == 'не проходит в оплату')
					{
						if($row['FIELD_14'] == 'Да')
							$this->row->F25++; // дубль картель
						
						if($row['FIELD_11'] == 'Дубль сверки')
							$this->row->F26++; // дубль сверки
						elseif($row['FIELD_11'] == 'блок паспорта')
							$this->row->F27++; // блок паспорта
						elseif($row['FIELD_11'] == 'Не пройдена СМС верификация')
							$this->row->F28++; // Не пройдена СМС верификация
						elseif(in_array($row['FIELD_11'], array('агент Рождественская СС - доп.проверка', 'агент Чернаков ПВ - доп. проверка', 'блок СБ')))
							$this->row->F29++; // блок СБ
						elseif($row['FIELD_11'] == 'блок по решению партнера')
							$this->row->F30++; // блок по решению партнера
						elseif($row['FIELD_11'] == 'умер ЗЛ')
							$this->row->F32++; // умер ЗЛ
						elseif($row['FIELD_11'] == 'блок партнер')
							$this->row->F33++; // блок партнер
						elseif(in_array($row['FIELD_11'], array('блок по умершим', 'блок по умершим (на проверке)')))
							$this->row->F34++; // блок по умершим
						elseif($row['FIELD_11'] == 'террорист')
							$this->row->F35++; // террорист
							
						if($row['FIELD_5'] == '' && $row['FIELD_11'] == '' && ($row['FIELD_14'] == '' || $row['FIELD_14'] == 'Нет'))
						{
							if($row['FIELD_8'] == 'Отказ')
								$this->row->F36++; // отказ
							elseif($row['FIELD_8'] == 'Договор заключал, но будет расторгать')
								$this->row->F37++; // закл, но будет расторг.
						}
						
						if($row['FIELD_13'] == 'Бумага ОК')
							$this->row->F38++; // Бумага ОК
					}
					
					if($row['FIELD_5'] == 'блок')
						$this->row->F31++; // блок СС
				break;
				
				case 'Сатурн/не выгружено':
					$this->row->F4++; // Заведено в Сатурн
					
					if($row['FIELD_16'] == 'блок')
						$this->row->F7++; // блоки
					
					if($row['FIELD_16'] == 'фальсификат')
						$this->row->F8++; // фальсификат
					
					if($row['FIELD_16'] == 'кц' /*|| $row['FIELD_16'] == 'кц/бумага'*/)
						$this->row->F9++; // КЦ
				break;
				
				case 'Сатурн/не выгружено/к выгрузке':
					$this->row->F4++; // Заведено в Сатурн
					$this->row->F6++; // к выгрузке
				break;
				default: break;
			}
			
			if($row['FIELD_53'] == 'Оплата')
				$this->row->F10++; // Оплачено Фондом
			
			$variants = array('Вычет/Оплата', 'Оплата', 'Оплата/вычет/оплата', 'Оплата/картель', 'Проавансировано/актировано', 'Проавансировано/актировано/вычет/оплата', 'Проавансировано/не актировано/оплачено');
			if(in_array($row['FIELD_16'], $variants ))
				$this->row->F11++; // оплачено
			
			if($row['FIELD_16'] == 'Проавансировано')
				$this->row->F12++; // проавансировано
			
			$variants = array('К авансу', 'К авансу от ...');
			if(in_array($row['FIELD_16'], $variants))
				$this->row->F13++; // к авансу
			
			if($row['FIELD_16'] == 'к оплате')
				$this->row->F14++; // к оплате
			
			if($row['FIELD_16'] == 'к оплате/нет СС')
				$this->row->F15++; // к оплате/нет СС
		}
		
		$this->Clear();
	}
	
	// Освобождаем память
	public function Clear()
	{
		unset($this->_data);
	}
}

// Строка данных
class RPartnerRow
{
	public $F1 = 0; // Всего заведено  в две базы
	public $F2 = 0; // Всего заведено в 1С
	public $F3 = 0; // Заведено в 1С
	public $F4 = 0; // Заведено в Сатурн
	public $F5 = 0; // Выгружено из Сатурна в 1С
	public $F6 = 0; // к выгрузке
	public $F7 = 0; // блоки
	public $F8 = 0; // фальсификат
	public $F9 = 0; // КЦ

	public $F10 = 0; // Оплачено Фондом
	public $F11 = 0; // оплачено
	public $F12 = 0; //проавансировано 
	public $F13 = 0; // к авансу
	public $F14 = 0; // к оплате
	public $F15 = 0; // к оплате/нет СС

	public $F16 = 0; // недозвон
	public $F17 = 0; // Сформированные СЗ
	public $F18 = 0; // в работе
	public $F19 = 0; // ошибки
	public $F20 = 0; // нет бумаги
	public $F21 = 0; // дубли телефонов
	public $F22 = 0; // блок по адресам
	public $F23 = 0; // неверные данные
	public $F24 = 0; // Бумага Ок

	public $F25 = 0; // дубль картель
	public $F26 = 0; // дубль сверки
	public $F27 = 0; // блок паспорта
	public $F28 = 0; // не прошел СМС вериф.
	public $F29 = 0; // блок СБ
	public $F30 = 0; // блок по решению партнера
	public $F31 = 0; // блок СС
	public $F32 = 0; // умер ЗЛ
	public $F33 = 0; // блок партнер
	public $F34 = 0; // блок по умершим
	public $F35 = 0; // террорист
	public $F36 = 0; // отказ
	public $F37 = 0; // закл, но будет расторг.
	public $F38 = 0; // Бумага Ок
	
	public function __construct()
	{

	}
}
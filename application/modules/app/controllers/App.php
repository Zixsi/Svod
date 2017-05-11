<?php
class App extends MX_Controller
{
	private $arrParams = array(
		'title' => '',
	);
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('app/CReport');
		$this->load->helper('file');
	}
	
	public function index()
	{
		$transaction = $this->CTransaction->Get();
		
		if(Application::ValidKey('report'))
		{
			$fileArray = array();
			
			if(!empty($_FILES))
			{
				$options = array(
					'upload_path' => './upload/in/',
					'allowed_types' => 'csv',
					'encrypt_name' => true,
					'remove_spaces' => true,
					'file_ext_tolower' => true,
				);
				
				$this->upload->initialize($options, false);
				if(!$this->upload->do_upload('file'))
				{
					//$this->arrParams['msg']['type'] = 'error';
					//$this->arrParams['msg']['text'][] = $this->upload->display_errors();
					$this->arrParams['error'] = $this->upload->display_errors();
				}
				else
				{
					$fileArray = $this->upload->data();
				}
			}
			
			if($fileArray)
			{
				//Debug::Log($fileArray);
				
				$checkHeader = $this->CReport->CsvCheckHeader($fileArray['full_path']);
				
				if($checkHeader['success'])
				{
					$this->session->set_userdata('FILE', $fileArray['full_path']);
					$this->session->set_userdata('PROCESS', 1);
					
					$this->CReport->SetTransaction($transaction['ID']);
					$this->CReport->Delete();
					
					$transaction = $this->CTransaction->NewTransaction();
					$this->CTransaction->Update($transaction['ID'], array('STATUS' => 1));
					$transaction['STATUS'] = 1;
				}
				else
				{
					$this->arrParams['error'] = $checkHeader['msg'];
				}
			}
		}
		
		if($transaction['STATUS'] == 0)
		{
			$this->arrParams['secret_key'] = Application::GetKey('report');
			$this->arrParams['list'] = $this->CReport->GetList();
			
			//$this->CReport->CsvHeader('./upload/in/test2.csv');
			$this->load->view('index',  Application::MakeData($this->arrParams));
		}
		else
		{
			$this->_StartReport();
		}
	}
	
	private function _StartReport()
	{
		set_time_limit(1800);
		ini_set('memory_limit', '1024M');
		//$this->output->enable_profiler(true);
		
		$limit = 10000;
		$file = $this->session->userdata('FILE');
		$process = $this->session->userdata('PROCESS');
		$transaction = $this->CTransaction->Get();
		$time = time() - strtotime($transaction['DATE']);
		
		if($transaction['STATUS'] > 0 && !$process)
			$transaction['STATUS'] = 4;
		
		if($time < 900)
		{
			if($transaction['STATUS'] == 1)
			{
				$this->CReport->SetTransaction($transaction['ID']);
				$res = $this->session->userdata('START_ITEM');
				if(!isset($res))
				{
					$this->session->set_userdata('START_ITEM', 0);
					$this->CReport->Delete();
				}
				
				$start = $this->session->userdata('START_ITEM');
				
				if($this->CReport->CsvToSQL($file, $start, $limit))
				{
					$start += ($limit + 1);
					$this->session->set_userdata('START_ITEM', $start);
					$this->CTransaction->Update($transaction['ID'], array('DATE' => date('Y.m.d H:i:s')));
					$this->load->view('reloader',  Application::MakeData($this->arrParams));
				}
				else
				{
					$this->CTransaction->Update($transaction['ID'], array('STATUS' => 2));
					$this->session->set_userdata('START_ITEM', 0);
					$this->load->view('reloader',  Application::MakeData($this->arrParams));
				}
			}
			
			if($transaction['STATUS'] == 2)
			{
				$this->CReport->SetTransaction($transaction['ID']);
				//$this->CReport->CsvHeader($file);
				$data = $this->CReport->CreateDataReport();
				$data = serialize($data);
				$this->cache->file->save('transaction_'.$transaction['ID'], $data , 36000000);
				
				$this->CTransaction->Update($transaction['ID'], array('STATUS' => 3));
				$this->load->view('reloader',  Application::MakeData($this->arrParams));
			}
			
			if($transaction['STATUS'] == 3)
			{
				$this->load->library('RPartner');
				
				if($res = $this->cache->file->get('transaction_'.$transaction['ID']))
				{
					$data = unserialize($res);
					$this->_createXLSX($data);
					$this->CTransaction->Update($transaction['ID'], array('STATUS' => 0));
					delete_files('./upload/in/');
					$this->load->view('reloader',  Application::MakeData($this->arrParams));
					//Debug::Log($data);
				}
			}
			
			if($transaction['STATUS'] == 4)
			{
				$this->load->view('process',  Application::MakeData($this->arrParams));
			}
		}
		else
		{
			$this->CReport->SetTransaction($transaction['ID']);
			$this->CReport->Delete();
			$this->CTransaction->Update($transaction['ID'], array('STATUS' => 0));
			delete_files('./upload/in/');
			$this->load->view('reloader',  Application::MakeData($this->arrParams));
		}
	}
	
	private function _createXLSX($data = array())
	{
		$this->load->library('Excel');
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("PHP Script")->setLastModifiedBy("server")->setTitle("Свод")->setSubject("Свод")->setDescription("")->setKeywords("")->setCategory("");
		//$objWorkSheet = $objPHPExcel->createSheet(0);
		$objWorkSheet = $objPHPExcel->getActiveSheet();
		$objWorkSheet->setTitle('Свод');
		$objWorkSheet->getSheetView()->setZoomScale(75);
		
		// Формируем шапку
		
		$objWorkSheet->setCellValue('A1', 'Доверие');
		$objWorkSheet->mergeCells('A1:A4');
		
		$objWorkSheet->setCellValue('B1', 'Всего заведено  в две базы');
		$objWorkSheet->mergeCells('B1:B4');
		
		$objWorkSheet->setCellValue('C1', 'Всего заведено в 1С');
		$objWorkSheet->mergeCells('C1:C4');
		
		$objWorkSheet->setCellValue('D1', 'Выгрузка');
		$objWorkSheet->mergeCells('D1:F1');
		
		$objWorkSheet->setCellValue('D2', 'Заведено в 1С');
		$objWorkSheet->mergeCells('D2:D4');
		
		$objWorkSheet->setCellValue('E2', 'Заведено в Сатурн');
		$objWorkSheet->mergeCells('E2:E4');
		
		$objWorkSheet->setCellValue('F2', 'Выгружено из Сатурна в 1С');
		$objWorkSheet->mergeCells('F2:F4');
		
		$objWorkSheet->setCellValue('G1', 'Не выгружено');
		$objWorkSheet->mergeCells('G1:J1');
		
		$objWorkSheet->setCellValue('G2', 'к выгрузке');
		$objWorkSheet->mergeCells('G2:G4');
		
		$objWorkSheet->setCellValue('H2', 'блоки');
		$objWorkSheet->mergeCells('H2:H4');
		
		$objWorkSheet->setCellValue('I2', 'фальсификат');
		$objWorkSheet->mergeCells('I2:I4');
		
		$objWorkSheet->setCellValue('J2', 'КЦ');
		$objWorkSheet->mergeCells('J2:J4');
		
		//===========================================
		
		$objWorkSheet->setCellValue('L1', 'Оплачено Фондом');
		$objWorkSheet->mergeCells('L1:L4');
		
		$objWorkSheet->setCellValue('M1', 'категория оплаты');
		$objWorkSheet->mergeCells('M1:Q2');
		
		$objWorkSheet->setCellValue('M3', 'оплачено');
		$objWorkSheet->mergeCells('M3:M4');
		
		$objWorkSheet->setCellValue('N3', 'проавансировано');
		$objWorkSheet->mergeCells('N3:N4');
		
		$objWorkSheet->setCellValue('O3', 'к авансу');
		$objWorkSheet->mergeCells('O3:O4');
		
		$objWorkSheet->setCellValue('P3', 'к оплате');
		$objWorkSheet->mergeCells('P3:P4');
		
		$objWorkSheet->setCellValue('Q3', 'к оплате/нет СС');
		$objWorkSheet->mergeCells('Q3:Q4');
		
		//===========================================
		
		$objWorkSheet->setCellValue('S1', 'нужно доработать');
		$objWorkSheet->mergeCells('S1:Z1');
		
		$objWorkSheet->setCellValue('S2', 'КЦ');
		$objWorkSheet->mergeCells('S2:U2');
		
		$objWorkSheet->setCellValue('S3', 'недозвон');
		$objWorkSheet->mergeCells('S3:S4');
		
		$objWorkSheet->setCellValue('T3', 'Сформированные СЗ');
		$objWorkSheet->mergeCells('T3:T4');
		
		$objWorkSheet->setCellValue('U3', 'в работе');
		$objWorkSheet->mergeCells('U3:U4');
		
		$objWorkSheet->setCellValue('V2', 'бумага');
		$objWorkSheet->mergeCells('V2:W2');

		$objWorkSheet->setCellValue('V3', 'ошибки');
		$objWorkSheet->mergeCells('V3:V4');
		
		$objWorkSheet->setCellValue('W3', 'нет бумаги');
		$objWorkSheet->mergeCells('W3:W4');
		
		$objWorkSheet->setCellValue('X2', 'СБ');
		$objWorkSheet->mergeCells('X2:Z2');
		
		$objWorkSheet->setCellValue('X3', 'дубли телефонов');
		$objWorkSheet->mergeCells('X3:X4');
		
		$objWorkSheet->setCellValue('Y3', 'блок по адресам');
		$objWorkSheet->mergeCells('Y3:Y4');
		
		$objWorkSheet->setCellValue('Z3', 'неверные данные');
		$objWorkSheet->mergeCells('Z3:Z4');
		
		$objWorkSheet->setCellValue('AA1', 'Бумага Ок');
		$objWorkSheet->mergeCells('AA1:AA4');
		
		//===========================================
		
		$objWorkSheet->setCellValue('AC1', 'не пройдет в оплату');
		$objWorkSheet->mergeCells('AC1:AO1');
		
		$objWorkSheet->setCellValue('AC2', 'блоки');
		$objWorkSheet->mergeCells('AC2:AM2');
		
		$objWorkSheet->setCellValue('AC3', 'дубль картель');
		$objWorkSheet->mergeCells('AC3:AC4');
		
		$objWorkSheet->setCellValue('AD3', 'дубль сверки');
		$objWorkSheet->mergeCells('AD3:AD4');
		
		$objWorkSheet->setCellValue('AE3', 'блок паспорта');
		$objWorkSheet->mergeCells('AE3:AE4');
		
		$objWorkSheet->setCellValue('AF3', 'не прошел СМС вериф.');
		$objWorkSheet->mergeCells('AF3:AF4');
		
		$objWorkSheet->setCellValue('AG3', 'блок СБ');
		$objWorkSheet->mergeCells('AG3:AG4');
		
		$objWorkSheet->setCellValue('AH3', 'блок по решению партнера');
		$objWorkSheet->mergeCells('AH3:AH4');
		
		$objWorkSheet->setCellValue('AI3', 'блок СС');
		$objWorkSheet->mergeCells('AI3:AI4');
		
		$objWorkSheet->setCellValue('AJ3', 'умер ЗЛ');
		$objWorkSheet->mergeCells('AJ3:AJ4');
		
		$objWorkSheet->setCellValue('AK3', 'блок партнер');
		$objWorkSheet->mergeCells('AK3:AK4');
		
		$objWorkSheet->setCellValue('AL3', 'блок по умершим');
		$objWorkSheet->mergeCells('AL3:AL4');
		
		$objWorkSheet->setCellValue('AM3', 'террорист');
		$objWorkSheet->mergeCells('AM3:AM4');
		
		$objWorkSheet->setCellValue('AN2', 'КЦ');
		$objWorkSheet->mergeCells('AN2:AO2');
		
		$objWorkSheet->setCellValue('AN3', 'отказ');
		$objWorkSheet->mergeCells('AN3:AN4');
		
		$objWorkSheet->setCellValue('AO3', 'закл, но будет расторг.');
		$objWorkSheet->mergeCells('AO3:AO4');
		
		$objWorkSheet->setCellValue('AP1', 'Бумага Ок');
		$objWorkSheet->mergeCells('AP1:AP4');
		
		//===========================================
		
		$objWorkSheet->getColumnDimension('A')->setWidth(20);
		
		$styleHeaderArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
				),
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => true,
			),
			'font' => array(
				'size' => 11,
			),
		);
		
		$styleFillGreen = array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'startcolor' => array(
				'rgb' => 'E2EFD9',
			),
		);
		
		$styleFillGrey = array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'startcolor' => array(
				'rgb' => 'D8D8D8',
			),
		);
		
		$objWorkSheet->getStyle('A1:J4')->applyFromArray($styleHeaderArray);
		$objWorkSheet->getStyle('L1:Q4')->applyFromArray($styleHeaderArray);
		$objWorkSheet->getStyle('S1:AA4')->applyFromArray($styleHeaderArray);
		$objWorkSheet->getStyle('AC1:AP4')->applyFromArray($styleHeaderArray);
		$objWorkSheet->getStyle('D1:J1')->getFill()->applyFromArray($styleFillGreen); 
		$objWorkSheet->getStyle('M1:Q2')->getFill()->applyFromArray($styleFillGreen); 
		$objWorkSheet->getStyle('S1:Z2')->getFill()->applyFromArray($styleFillGreen); 
		$objWorkSheet->getStyle('AA1:AA4')->getFill()->applyFromArray($styleFillGreen); 
		$objWorkSheet->getStyle('AC1:AO2')->getFill()->applyFromArray($styleFillGreen); 
		$objWorkSheet->getStyle('AP1:AP4')->getFill()->applyFromArray($styleFillGreen); 
		
		// End Формируем шапку
		
		// Заполняем лист
		//Debug::Log($data['SUMM']->F3);
		$row = 5;
		$cellsArray = array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'L', 'M', 'N', 'O', 'P', 'Q', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'Ak', 'AL', 'AM', 'AN', 'AO', 'AP');
		
		$objWorkSheet->setCellValue('A'.$row, 'Итого');
		$objWorkSheet->setCellValue('B'.$row, '0');
		$objWorkSheet->setCellValue('C'.$row, '0');
		
		$i = 3;
		foreach($cellsArray as $cell)
		{
			$objWorkSheet->setCellValue($cell.$row, $data['SUMM']->{"F$i"});
			$i++;
		}
		
		$objWorkSheet->setCellValue('C'.$row, '=D'.$row.'+F'.$row);
		$objWorkSheet->setCellValue('B'.$row, '=D'.$row.'+E'.$row);
		
		$objWorkSheet->getStyle('A'.$row.':J'.$row)->getFill()->applyFromArray($styleFillGrey);
		$objWorkSheet->getStyle('L'.$row.':Q'.$row)->getFill()->applyFromArray($styleFillGrey);
		$objWorkSheet->getStyle('S'.$row.':AA'.$row)->getFill()->applyFromArray($styleFillGrey);
		$objWorkSheet->getStyle('AC'.$row.':AP'.$row)->getFill()->applyFromArray($styleFillGrey);
		
		$styleBorder = array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM));
		$objWorkSheet->getStyle('A'.$row.':J'.$row)->getBorders()->applyFromArray($styleBorder);
		$objWorkSheet->getStyle('L'.$row.':Q'.$row)->getBorders()->applyFromArray($styleBorder);
		$objWorkSheet->getStyle('S'.$row.':AA'.$row)->getBorders()->applyFromArray($styleBorder);
		$objWorkSheet->getStyle('AC'.$row.':AP'.$row)->getBorders()->applyFromArray($styleBorder);
		
		$objWorkSheet->getStyle('A'.$row.':AP'.$row)->getFont()->applyFromArray(array(
			'bold' => true,
			'size' => 12,
		));
		$objWorkSheet->getStyle('B'.$row.':AP'.$row)->getAlignment()->applyFromArray(array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			'wrap' => true,
		));
		$objWorkSheet->getStyle('A'.$row)->getAlignment()->applyFromArray(array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			'wrap' => true,
		));
		
		$row = 6;
		foreach($data['PARTNERS'] as $partner)
		{
			$objWorkSheet->setCellValue('A'.$row, $partner->name);
			$objWorkSheet->setCellValue('B'.$row, '0');
			$objWorkSheet->setCellValue('C'.$row, '0');
			
			$i = 3;
			foreach($cellsArray as $cell)
			{
				$objWorkSheet->setCellValue($cell.$row, $partner->summ->{"F$i"});
				$i++;
			}
			
			$objWorkSheet->setCellValue('C'.$row, '=D'.$row.'+F'.$row);
			$objWorkSheet->setCellValue('B'.$row, '=D'.$row.'+E'.$row);
			
			$objWorkSheet->getStyle('A'.$row.':J'.$row)->getFill()->applyFromArray($styleFillGrey);
			$objWorkSheet->getStyle('L'.$row.':Q'.$row)->getFill()->applyFromArray($styleFillGrey);
			$objWorkSheet->getStyle('S'.$row.':AA'.$row)->getFill()->applyFromArray($styleFillGrey);
			$objWorkSheet->getStyle('AC'.$row.':AP'.$row)->getFill()->applyFromArray($styleFillGrey);
			
			$styleBorder = array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM));
			$objWorkSheet->getStyle('A'.$row.':J'.$row)->getBorders()->applyFromArray($styleBorder);
			$objWorkSheet->getStyle('L'.$row.':Q'.$row)->getBorders()->applyFromArray($styleBorder);
			$objWorkSheet->getStyle('S'.$row.':AA'.$row)->getBorders()->applyFromArray($styleBorder);
			$objWorkSheet->getStyle('AC'.$row.':AP'.$row)->getBorders()->applyFromArray($styleBorder);
			
			$objWorkSheet->getStyle('A'.$row.':AP'.$row)->getFont()->applyFromArray(array(
				'bold' => true,
				'size' => 12,
			));
			$objWorkSheet->getStyle('B'.$row.':AP'.$row)->getAlignment()->applyFromArray(array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => false,
			));
			$objWorkSheet->getStyle('A'.$row)->getAlignment()->applyFromArray(array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => false,
			));
			
			$row++;
			
			foreach($partner->month as $month)
			{
				$objWorkSheet->setCellValue('A'.$row, $month->name);
				$objWorkSheet->setCellValue('B'.$row, '0');
				$objWorkSheet->setCellValue('C'.$row, '0');
				
				$i = 3;
				foreach($cellsArray as $cell)
				{
					$objWorkSheet->setCellValue($cell.$row, $month->row->{"F$i"});
					$i++;
				}
				
				$objWorkSheet->setCellValue('C'.$row, '=D'.$row.'+F'.$row);
				$objWorkSheet->setCellValue('B'.$row, '=D'.$row.'+E'.$row);
				
				$styleBorder = array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM));
				$objWorkSheet->getStyle('A'.$row.':J'.$row)->getBorders()->applyFromArray($styleBorder);
				$objWorkSheet->getStyle('L'.$row.':Q'.$row)->getBorders()->applyFromArray($styleBorder);
				$objWorkSheet->getStyle('S'.$row.':AA'.$row)->getBorders()->applyFromArray($styleBorder);
				$objWorkSheet->getStyle('AC'.$row.':AP'.$row)->getBorders()->applyFromArray($styleBorder);
				
				$objWorkSheet->getStyle('A'.$row.':AP'.$row)->getFont()->applyFromArray(array(
					'size' => 12,
				));
				
				$row++;
			}
		}
		
		
		// End Заполняем лист
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$name = 'upload/out/svod'.date('d_m_Y_H_i_s').'.xlsx';
		$objWriter->save($name);
		
		$this->CReport->AddReport(array('NAME' => 'Сводная таблица от '.date('d.m.Y H:i:s'), 'FILE' => $name));
	}
}
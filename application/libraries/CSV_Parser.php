<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CSV_Parser
{
	public function __construct()
	{

	}
	
	public function fgetcsv($f, $length, $d=",", $q='"')
	{
		$list = array();
		$st = fgets($f, $length);
		if ($st === false || $st === null) return $st;
		if (trim($st) === "") return array("");
		while ($st !== "" && $st !== false)
		{
			if ($st[0] !== $q)
			{
				list ($field) = explode($d, $st, 2);
				$st = substr($st, strlen($field)+strlen($d));
			}
			else
			{
				$st = substr($st, 1);
				$field = "";
				while(1)
				{
					preg_match("/^((?:[^$q]+|$q$q)*)/sx", $st, $p);
					$part = $p[1];
					$partlen = strlen($part);
					$st = substr($st, strlen($p[0]));
					$field .= str_replace($q.$q, $q, $part);
					if (strlen($st) && $st[0] === $q)
					{
						list ($dummy) = explode($d, $st, 2);
						$st = substr($st, strlen($dummy)+strlen($d));
						break;
					}
					else
					{
						$st = fgets($f, $length);
					}
				}

			}
			$list[] = $field;
		}
		return $list;
	}

	public function fputcsv($f, $list, $d=",", $q='"')
	{
		$line = "";
		foreach ($list as $field)
		{
			$field = str_replace("\r\n", "\n", $field);
			if(preg_match("/[$d$q\n\r]/", $field))
			{
				$field = $q.str_replace($q, $q.$q, $field).$q;
			}
			$line .= $field.$d;
		}

		$line = substr($line, 0, -1);
		$line .= "\n";
		return fputs($f, $line);
	}
}
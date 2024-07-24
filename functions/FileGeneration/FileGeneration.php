<?php

defined('ABSPATH') || exit;

class FileGeneration
{

	public static function generateJSON($array, $pretty = false)
	{
		if (!$pretty) return json_encode($array, true);

		return json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}

	public static function getPDF($id)
	{
		if (function_exists('get_field') && $id) {
			$file = get_field('pdf_file', $id);

			return get_field('pdf_file', $id);
		}
	}

	public static function generateCSV($array)
	{
		if (!is_array($array)) return;

		$json = self::generateJSON($array);

		// JSON to CSV
		$json = new JsonToCsv(false, "report.json", $json);
		// To set a conversion option then convert JSON to CSV and save
		$json->setConversionKey('utf8_encoding', true);

		return $json->convertAndReturnData();
	}
}

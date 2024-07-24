<?php

defined('ABSPATH') || exit;

class DataCollection
{

	private static $ids;

	/**
	 * @param $flag - 'all' or ''
	 */
	public static function generateData($childId, $flag = '', $parentId = '')
	{
		self::$ids = [$childId];

		if ($flag) self::$ids = self::getPostCommitments($childId, $flag);
		if ($parentId) self::$ids = self::getPostCommitments($childId, $flag, $parentId);

		$chapters = array_map(function ($id) {
			return get_field('chapter', $id)[0];
		}, self::$ids);

		if (!$chapters) return;

		$response = array_map(function ($item) {

			return [
				'chapter'           => $item['chapter_number']->name,
				'commitment'        => $item['commitment_number']->name,
				'specific_measures' => self::handleSpecific($item),
				'measures_group'    => self::handleMeasuresGroup($item),
				'measures_group_10' => self::handleMeasuresGroup10($item),
			];
		}, $chapters);

		return $response;
	}

	public static function getPostCommitments($childId, $flag, $parentId = '')
	{
		$args = [
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_type'      => 'signatory-report',
			'order'          => 'ASC',
		];

		if ($flag) {
			$parentPostID = get_post_parent($childId)->ID;
			$args['post_parent'] = $parentPostID;
		} elseif ($parentId) {
			$args['post_parent'] = $parentId;
		} else {
			$args['post_in'] = [$childId];
		}

		$posts = get_posts($args);

		return isset($posts) ? $posts : false;
	}

	private static function handleMeasuresGroup($array)
	{
		$response = '';

		if (isset($array['measure_group'])) {
			if (is_array($array['measure_group'])) {
				$response = array_map(function ($index, $measure_group) {

					return [
						'measures_number'     => $measure_group['measure_number']->name,
						'measure_by_services' => self::handleMeasuresGroupByServices($measure_group),
					];
				}, array_keys($array['measure_group']), $array['measure_group']);
			}
		}

		return self::isEmptyRecursive($response) ? [] : self::returnNotNull($response);
	}

	private static function handleMeasuresGroup10($array)
	{
		$response = '';

		if (isset($array['measure_chapter_10'])) {
			if (is_array($array['measure_chapter_10'])) {
				$response = array_map(function ($index, $measure_chapter_10) {

					return [
						'title'  => self::handleMeasuresGroup10Title($measure_chapter_10),
						'tables' => self::handleMeasuresGroup10Tables($measure_chapter_10),
					];
				}, array_keys($array['measure_chapter_10']), $array['measure_chapter_10']);
			}
		}

		return self::isEmptyRecursive($response) ? [] : self::returnNotNull($response);
	}

	private static function handleMeasuresGroup10Title($array)
	{
		$response = '';

		if (isset($array['title'])) {
			if (is_array($array['title'])) {
				$response = array_map(function ($index, $title) {
					return $title['title_content'];
				}, array_keys($array['title']), $array['title']);
			}
		}

		return self::isEmptyRecursive($response) ? [] : self::returnNotNull($response);
	}

	private static function handleMeasuresGroup10Tables($array)
	{
		$response = [];

		if (isset($array['tables'])) {
			if (is_array($array['tables'])) {
				$response = array_map(function ($tables) {

					$data = [
						'table_title' => $tables['table_title'],
						'thead'  => $tables['table_head'],
						'tbody'  => $tables['table_body'],
					];

					return self::returnNotEmpty($data);
				}, $array['tables']);
			}
		}

		return self::returnNotNull($response);
	}

	private static function handleMeasuresGroupByServices($array)
	{
		$response = [];

		if (isset($array['measure_by_services'])) {
			if (is_array($array['measure_by_services'])) {
				$response = array_map(function ($measure_service) {

					return [
						'services' => self::issetCallbackFn($measure_service['services'], 'self::returnNotNull'),
						'qre'      => self::handleMeasuresGroupByQRE($measure_service),
						'sli'      => self::handleMeasuresGroupBySLI($measure_service),
						'content'  => $measure_service['content'],
					];
				}, $array['measure_by_services']);
			}
		}

		return self::isEmptyRecursive($response) ? [] : self::returnNotNull($response);
	}

	private static function handleMeasuresGroupByQRE($array)
	{
		$response = [];

		if (isset($array['qre_group'])) {
			if (is_array($array['qre_group'])) {
				$response = array_map(function ($qre_group) {

					$data = [
						'number'  => self::issetCallbackFn($qre_group['qre_number'], 'self::returnNotNull'),
						'content' => $qre_group['qre_content'],
					];

					return self::returnNotEmpty($data);
				}, $array['qre_group']);
			}
		}

		return self::returnNotNull($response);
	}

	private static function handleMeasuresGroupBySLI($array)
	{
		$response = [];

		if (isset($array['sli_group'])) {
			if (is_array($array['sli_group'])) {
				$response = array_map(function ($sli_group) {

					$data = [
						'number'  => self::issetCallbackFn($sli_group['sli_number'], 'self::returnNotNull'),
						'content' => $sli_group['sli_content'],
						'tables'  => self::handleMeasuresGroupBySLItables($sli_group),
					];

					return self::returnNotEmpty($data);
				}, $array['sli_group']);
			}
		}

		if (isset($array['ttp_or_action_group'])) {
			if (is_array($array['ttp_or_action_group'])) {
				$response = array_map(function ($tt_group) {

					$data = [
						'number'  => self::issetCallbackFn($tt_group['sli_number'], 'self::returnNotNull'),
						'content' => $tt_group['sli_content'],
						'tables'  => self::handleMeasuresGroupBySLItables($tt_group),
					];

					return self::returnNotEmpty($data);
				}, $array['ttp_or_action_group']);
			}
		}

		return self::returnNotNull($response);
	}

	private static function handleMeasuresGroupBySLItables($array)
	{
		$response = [];

		if (isset($array['tables'])) {
			if (is_array($array['tables'])) {
				$response = array_map(function ($tables) {

					$data = [
						'type_of_action'    => isset($tables['type_of_action']->name) ? $tables['type_of_action']->name : $tables['type_of_action'],
						'table_type'        => $tables['table_type'],
						'per_language'      => self::handleMeasuresGroupBySLItablePerLanguage($tables),
						'per_member_states' => self::handleMeasuresGroupBySLItablePerMember($tables),
					];

					return self::returnNotEmpty($data);
				}, $array['tables']);
			}
		}

		return self::returnNotNull($response);
	}

	private static function handleMeasuresGroupBySLItablePerLanguage($array)
	{
		$response = [];

		if (isset($array['per_language_thead'])) {
			if (is_array($array['per_language_thead']) && is_array($array['per_language'])) {
				$response['thead'] = array_map(function ($per_language) {
					$data = [
						'language'        => '',
						'title_column_1' => isset($per_language['title_column_1']) ? $per_language['title_column_1'] : '',
						'title_column_2' => isset($per_language['title_column_2']) ? $per_language['title_column_2'] : '',
					];

					return self::returnNotEmpty($data);
				}, $array['per_language_thead']);

				$response['tbody'] = array_map(function ($per_language) {

					$data = [
						'language'       => isset($per_language['language']) && isset($per_language['language']->name)  ? $per_language['language']->name : '',
						'body_column_1' => isset($per_language['body_column_1']) ? $per_language['body_column_1'] : '',
						'body_column_2' => isset($per_language['body_column_2']) ? $per_language['body_column_2'] : '',
					];

					return self::returnNotEmpty($data);
				}, $array['per_language']);
			}
		}

		return self::returnNotNull($response);
	}

	private static function handleMeasuresGroupBySLItablePerMember($array)
	{
		$response = [];

		if (isset($array['per_member_states_thead'])) {
			if (is_array($array['per_member_states_thead']) && is_array($array['per_member_states'])) {
				$response['thead'] = array_map(function ($per_member_states) {
					$data = [
						'country'        => '',
						'title_column_1' => isset($per_member_states['title_column_1']) ? $per_member_states['title_column_1'] : '',
						'title_column_2' => isset($per_member_states['title_column_2']) ? $per_member_states['title_column_2'] : '',
						'title_column_3' => isset($per_member_states['title_column_3']) ? $per_member_states['title_column_3'] : '',
						'title_column_4' => isset($per_member_states['title_column_4']) ? $per_member_states['title_column_4'] : '',
					];

					return self::returnNotEmpty($data);
				}, $array['per_member_states_thead']);

				$response['tbody'] = array_map(function ($per_member_states) {
					$data = [
						'country'       => isset($per_member_states['country']) && isset($per_member_states['country']->name) ? $per_member_states['country']->name : '',
						'body_column_1' => isset($per_member_states['body_column_1']) ? $per_member_states['body_column_1'] : '',
						'body_column_2' => isset($per_member_states['body_column_2']) ? $per_member_states['body_column_2'] : '',
						'body_column_3' => isset($per_member_states['body_column_3']) ? $per_member_states['body_column_3'] : '',
						'body_column_4' => isset($per_member_states['body_column_4']) ? $per_member_states['body_column_4'] : '',
					];

					return self::returnNotEmpty($data);
				}, $array['per_member_states']);
			}
		}

		return self::returnNotNull($response);
	}

	private static function handleCommitments($array)
	{
		$response = '';

		if (isset($array['commitments']->name)) {
			return $array['commitments']->name;
		}

		return $response;
	}

	private static function handleService($array)
	{
		$service = '';

		if (isset($array['services'])) {
			if (is_array($array['services'])) {
				$service = array_map(function ($service) {
					return isset($service->name) ? $service->name : '';
				}, $array['services']);
			} elseif (isset($array['services']->name)) {
				return $array['services']->name;
			}
		}

		return $service;
	}

	private static function handleMeasures($array)
	{
		$measures = '';

		if (isset($array['measures'])) {
			if (is_array($array['measures'])) {
				$measures = array_map(function ($measure) {
					return isset($measure->name) ? $measure->name : '';
				}, $array['measures']);
			} elseif (isset($array['measures']->name)) {
				return  $array['measures']->name;
			}
		}

		// Case with Yes/No
		if (isset($array['yesno'])) {
			return $array['yesno'] === true ? 'Yes' : 'No';
		}

		return $measures;
	}

	private static function handleSpecific($array)
	{
		$response = '';

		if (isset($array['short_report'])) {
			if (is_array($array['short_report'])) {
				$response = array_map(function ($short_report) {

					return [
						'title'                => $short_report['question'],
						'specific_commitments' => self::handleSpecificCommitments($short_report),
						'specific_measures'    => self::handleSpecificMeasures($short_report),
						'specific_services'    => self::handleSpecificService($short_report),
						'specific_content'     => self::handleSpecificContent($short_report),
					];
				}, $array['short_report']);
			}
		}

		return self::isEmptyRecursive($response) ? [] : self::returnNotNull($response);
	}

	private static function handleSpecificCommitments($array)
	{
		$response = [];
		if ($array['specific_commitments']) {
			$response = array_map(function ($sm) {
				$data = [
					'commitments' => self::handleCommitments($sm),
					'services'    => self::handleService($sm),
				];

				return self::returnNotEmpty($data);
			}, $array['specific_commitments']);
		}

		return self::returnNotNull($response);
	}

	private static function handleSpecificMeasures($array)
	{
		$response = [];
		if ($array['specific_measures']) {
			$response = array_map(function ($sm) {
				$data = [
					'measures' => self::handleMeasures($sm),
					'services' => self::handleService($sm),
				];

				return self::returnNotEmpty($data);
			}, $array['specific_measures']);
		}

		return self::returnNotNull($response);
	}

	private static function handleSpecificContent($array)
	{
		$response = [];

		if ($array['specific_content']) {
			$response = $array['specific_content'];
		}

		return self::returnNotNull($response);
	}

	private static function handleSpecificService($array)
	{
		$response = [];

		if ($array['specific_services']) {
			$response[] = array_map(function ($sm) {
				$data = [
					'services' => self::handleService($sm),
					'measures' => self::handleMeasures($sm),
				];

				return self::returnNotEmpty($data);
			}, $array['specific_services']);
		}

		if ($array['services_yn']) {
			$response[] = array_map(function ($sm) {
				$data = [
					'services' => self::handleService($sm),
					'measures' => self::handleMeasures($sm),
				];

				return self::returnNotEmpty($data);
			}, $array['services_yn']);
		}

		return self::returnNotNull($response);
	}

	public static function generateJSON($array, $pretty = false)
	{
		if (!$pretty) return json_encode($array, true);

		return json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}

	private static function isEmptyRecursive($value)
	{
		if (is_array($value)) {
			$empty = TRUE;
			array_walk_recursive($value, function ($item) use (&$empty) {
				$empty = $empty && empty($item);
			});
		} else {
			$empty = empty($value);
		}
		return $empty;
	}

	private static function issetCallbackFn($var, $fn)
	{
		return isset($var->name) ? call_user_func_array($fn, array($var->name)) : '';
	}

	public static function returnNotNull($var)
	{

		if (gettype($var) === 'string') {
			return !is_null($var) ? $var : '';
		} elseif (is_array($var)) {
			return array_filter($var, function ($val) {
				return $val !== null;
			});
		} else {
			return '';
		}
	}

	public static function returnNotEmpty($var)
	{
		if (!is_array($var)) return;

		return  self::isEmptyRecursive($var) ? null : $var;
	}
}

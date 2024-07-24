<?php

function sortTitleWithNumbers($array)
{
	if (!is_array($array)) return;

	foreach ($array as $key => $value) {
		$currentValue = $value->name ?? $value['name'];
		if (!isset($currentValue)) return;
		preg_match('!\d+!', $currentValue, $matches);

		isset($matches[0]) ? $number[$matches[0]] = $value : $number[$key] = $value;
	}

	// sort your array on Key               
	isset($number) && ksort($number, SORT_NUMERIC);

	return isset($number) ? $number : false;
}

function firstTitleWithNumber($string, $separator = ' ')
{
	if (!$string) return;

	$character = substr($string, 0, 1);

	preg_match('/[0-9].?[0-9]?/i', $string, $matches);

	return strtoupper($character) . $separator . $matches[0];
}

/**
 * Set max length for Wysiwyg editor
 *
 */
add_filter('acf/validate_value/name=qre_content', function ($valid, $value, $field, $input) {
	return validate_wysiwyg_value($valid, $value, 3000);
}, 10, 4);

add_filter('acf/validate_value/name=sli_content', function ($valid, $value, $field, $input) {
	return validate_wysiwyg_value($valid, $value, 500);
}, 10, 4);

add_filter('acf/validate_value/name=table_body_column_2', function ($valid, $value, $field, $input) {
	return validate_wysiwyg_value($valid, $value, 500);
}, 10, 4);

add_filter('acf/validate_value/name=table_body_column_3', function ($valid, $value, $field, $input) {
	return validate_wysiwyg_value($valid, $value, 3000);
}, 10, 4);

function validate_wysiwyg_value($valid, $value, $limit)
{
	$cleaned_value = wp_strip_all_tags($value);

	if (strlen($cleaned_value) > $limit) {
		$valid = '<div style="text-align: center"><h3>Unable to update your report</h3>
        <p>We were unable to update your report because this field is longer than ' . $limit . ' characters.</p></div>';
	}

	return $valid;
}

/**
 * Search by title
 *
 */
function title_filter($where, $wp_query)
{
	global $wpdb;
	if ($search_term = $wp_query->get('search_title')) {
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql($wpdb->esc_like($search_term)) . '%\'';
	}
	return $where;
}

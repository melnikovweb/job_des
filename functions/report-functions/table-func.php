<?php

function generateTable($tableData, $tableType, $search = true, $sortable = true)
{
    if (empty($tableData)) return;

    $options = get_fields('options');

    // thead
    $theadData = (isset($tableData['thead']) && !empty($tableData['thead'][0])) ? $tableData['thead'][0] : [];
    $thead = array_map(function ($item) {
        return ['item' => $item];
    }, $theadData);

    $thead = [];
    $index = 1;
    if ($theadData) {
        foreach ($theadData as $key => $value) {

            if ($index != 1 && empty($value)) break;

            $sortBy = 'column_' . $index;

            switch ($key) {
                case 'language':
                    $title = __('Language', THEME_TD);
                    $sortType = 'text';
                    break;
                case 'country':
                    $title = __('Country', THEME_TD);
                    $sortType = 'text';
                    break;
                default:
                    $title = $value;
                    $sortType = 'number';
                    break;
            }


            $thead[] = [
                'title' => $title,
                'sortBy' => $sortable ? $sortBy : '',
                'sortType' => $sortType,
            ];

            $index++;
        }
    }
    // END thead

    // tbody
    $countThead = count($thead);
    $tbodyData = isset($tableData['tbody']) ? $tableData['tbody'] : [];

    $tbody = [];
    if ($tbodyData) {
        foreach ($tbodyData as $keyCol => $cols) {
            $index = 1;

            if (empty($cols)) break;

            if ('simple_table' != $tableType) {
                $firstKey = array_key_exists('language', $cols) ? 'language' : 'country';

                if (empty($cols[$firstKey])) break;

                $tbody[$keyCol]['sort'] = ('Total EEA' == $cols[$firstKey] || 'Total EU' == $cols[$firstKey]) ? false : true;
            }

            foreach ($cols as $key => $col) {

                if ($index > $countThead) {
                    break;
                }

                $sortBy = 'column_' . $index;

                $tbody[$keyCol]['cols'][] = [
                    'title' => $col,
                    'sortBy' => $sortBy,
                ];
                $index++;
            }
        }
    }
    // END tbody

    // search data
    $titleAccordion = '';
    switch ($tableType) {
        case 'per_member_states':
            $titleAccordion = checker_value($options, 'title_table_per_member_states') ?: __('List of actions per Member States', THEME_TD);
            $labelSearch = __('Search by Member States', THEME_TD);
            $helpSearchText = __('Search below to find out more on individual member states, or click on columns to sort data.', THEME_TD);
            break;

        case 'per_language':
            $titleAccordion = checker_value($options, 'title_table_per_language') ?: __('Number by actions enforcing policies above (per language)', THEME_TD);
            $labelSearch = __('Search by Language', THEME_TD);
            $helpSearchText = __('Search below to find out more on individual languages, or click on columns to sort data.', THEME_TD);
            break;

        default:
            break;
    }
    // END search data

    $searchData = [];
    if ($search) {
        // optionsSearch
        $optionsSearch = array_map(function ($item) {
            if (isset($item)) return ['name' => reset($item)];
        }, $tableData['tbody']);
        // END optionsSearch

        $searchData = [
            'id' => 'table_search',
            'name' => 'table_search',
            'label' => $labelSearch,
            'placeholder' => __('Search', THEME_TD),
            'helpText' => $helpSearchText,
            'inputIcon' => 'search-input',
            'options' => $optionsSearch,
            'disableURLParams' => true
        ];
    }

    return [
        'titleAcc' => $titleAccordion,
        'table' => [
            'search' => $searchData,
            'thead' => $thead,
            'tbody' => $tbody,
            'sortable' => $sortable,
        ]
    ];
}

<?php

function measuresGroupItem($tableData)
{
    if (empty($tableData)) return;

    $tableItemTitle = isset($tableData['title']) && !empty($tableData['title']) ? $tableData['title'] : [];

    $resultData = [];
    $content = '<div class="page-content__methodology">';
    foreach ($tableItemTitle as $key => $value) {
        switch ($key) {
            case 0:
                $resultData['title'] = $value;
                break;
            case 1:
                $resultData['accordion']['title'] = $value;
                break;
            default:
                if (!empty($value)) $content .= $value;
        }
    }

    $content .= '</div>';
    $resultData['accordion']['content'] = $content;

    $tableItemTables = isset($tableData['tables']) && !empty($tableData['tables']) ? $tableData['tables'] : [];

    $tables = [];
    foreach ($tableItemTables as $key => $table) {
        $tables[] = [
            'title' => $table['table_title'],
            'data' => generateTable($table, 'simple_table', false, false),
        ];
    }

    return [
        'content' => $resultData,
        'tables' => $tables,
    ];
}

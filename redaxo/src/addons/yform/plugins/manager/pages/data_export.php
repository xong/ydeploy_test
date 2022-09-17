<?php

/**
 * @var rex_yform_manager $this
 * @psalm-scope-this rex_yform_manager
 */

$rex_yform_filter = $rex_yform_filter ?? [];
$rex_yform_set = $rex_yform_set ?? [];
$searchObject = $searchObject ?? null;

$query = $this->table->query();
$query = $this->getDataListQuery($query, array_merge( $rex_yform_filter, $rex_yform_set), $searchObject);
$query = rex_extension::registerPoint(new rex_extension_point('YFORM_DATA_TABLE_EXPORT', $query, ['filter' => $rex_yform_filter, 'set' => $rex_yform_set, 'searchObject' => $searchObject]));

$fields = ['id' => '"id"'];
foreach ($query->getTable()->getFields() as $field) {
    if ('value' == $field->getType() && 'none' != $field->getDatabaseFieldType()) {
        $fields[$field->getName()] = '"' . $field->getName() . '"';
    }
}

$exportDataset = [];
foreach ($query->find() as $data) {
    $exportData = [];
    foreach ($fields as $fieldName => $fV) {
        $exportData[$fieldName] = '"' . str_replace(['"', "\n", "\r"], ['""', '', ''], @$data->getValue($fieldName)) . '"';
    }
    $exportDataset[] = implode(';', $exportData);
}

$fileContent = pack('CCC', 0xef, 0xbb, 0xbf);
$fileContent .= implode(';', $fields);
$fileContent .= "\n".implode("\n", $exportDataset);

$fileName = 'export_data_' . date('YmdHis') . '.csv';
header('Content-Disposition: attachment; filename="' . $fileName . '"; charset=utf-8');
rex_response::sendContent($fileContent, 'application/octetstream');

exit;

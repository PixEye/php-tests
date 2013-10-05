<?php
/**
 * PHPExcel is licensed under LGPL (GNU LESSER GENERAL PUBLIC LICENSE)
 *
 * @link https://github.com/PHPOffice/PHPExcel
 * @link https://github.com/PHPOffice/PHPExcel/blob/develop/Documentation/markdown/Overview/03-Creating-a-Spreadsheet.md
 */

@include_once 'PHPExcel/Classes/PHPExcel.php'; // not a require to handle the error:
if (!class_exists('PHPExcel'))
	die('PHPExcel library is required!'.
		' See: https://github.com/PHPOffice/PHPExcel'.PHP_EOL);

/** Create a new PHPExcel Object **/
$objPHPExcel = new PHPExcel();

/**
 * @link https://github.com/PHPOffice/PHPExcel/blob/develop/Documentation/markdown/Overview/07-Accessing-Cells.md
 */

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Numéro de téléphone');

// Set cell A1 with a numeric value, but tell PHPExcel it should be treated as a string
$objPHPExcel->getActiveSheet()->setCellValueExplicit(
	'A2',
	'01513789642',
	PHPExcel_Cell_DataType::TYPE_STRING
);

// Set cell A2 with a numeric value
$objPHPExcel->getActiveSheet()->setCellValue('A3', 12345.6789);

// Set cell A3 with a boolean value
$objPHPExcel->getActiveSheet()->setCellValue('A4', TRUE);

// Set cell A4 with a formula
$objPHPExcel->getActiveSheet()->setCellValue(
	'A5', 
	'=IF(A3, CONCATENATE(A1, " ", A2), CONCATENATE(A2, " ", A1))'
);


$styleArray = array(
	'font' => array(
		'bold' => true,
	),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
	),
	'borders' => array(
		'top' => array(
			'style' => PHPExcel_Style_Border::BORDER_THICK,
			'color' => array('argb' => 'FFFF0000'),
		),
	),
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
		'startcolor' => array(
			'argb' => 'FFA0A0A0',
		),
	),
);

$objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->mergeCells('B1:C1');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

/* Here there will be some code where you create $objPHPExcel */
// redirect output to client browser
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="myfile.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

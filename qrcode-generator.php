<?php
ini_set('display_errors',0);
require_once 'vendor/autoload.php';

use \Milon\Barcode\DNS1D;
use \Milon\Barcode\DNS2D;

$requestMethod = $_SERVER['REQUEST_METHOD'];

if($requestMethod != 'POST'){
	echo "<h1>Unsupported Method \"{$requestMethod}\".</h1>";
	echo "<a href='generator.html'>Go Home</a>";
	die;
}

function dd($message){
	echo "<pre style='background-color: black; color: lightgreen'>";
	var_dump($message);
	echo "</pre>";
	die;
}

$params = json_decode(file_get_contents('php://input'));
extract( get_object_vars($params) );

header('Content-Type: application/json; charset=utf-8');
$data = ['Result' => 'NO'];

// Request Validation
$rules = [
	"C39" => ['length' => '', 'charset' => ''],
	"C39+" => ['length' => '', 'charset' => ''],
	"C39E" => ['length' => '', 'charset' => ''],
	"C39E+" => ['length' => '', 'charset' => ''],
	"C93" => ['length' => '', 'charset' => ''],
	"S25" => ['length' => '', 'charset' => ''],
	"S25+" => ['length' => '', 'charset' => ''],
	"I25" => ['length' => '', 'charset' => ''],
	"I25+" => ['length' => '', 'charset' => ''],
	"C128" => ['length' => '', 'charset' => ''],
	"C128A" => ['length' => '', 'charset' => ''],
	"C128B" => ['length' => '', 'charset' => ''],
	"C128C" => ['length' => '', 'charset' => ''],
	"EAN2" => ['length' => '', 'charset' => ''],
	"EAN5" => ['length' => '', 'charset' => ''],
	"EAN8" => ['length' => 8, 'charset' => '/^[0-9]+$/'],
	"EAN13" => ['length' => 13, 'charset' => '/^[0-9]+$/'],
	"UPCA" => ['length' => 12, 'charset' => '/^[0-9]+$/'],
	"UPCE" => ['length' => 8, 'charset' => '/^[0-9]+$/'],
	"MSI" => ['length' => '', 'charset' => ''],
	"MSI+" => ['length' => '', 'charset' => ''],
	"POSTNET" => ['length' => '', 'charset' => ''],
	"PLANET" => ['length' => '', 'charset' => ''],
	"RMS4CC" => ['length' => '', 'charset' => ''],
	"KIX" => ['length' => '', 'charset' => ''],
	"IMB" => ['length' => '', 'charset' => ''],
	"CODABAR" => ['length' => '', 'charset' => ''],
	"CODE11" => ['length' => '', 'charset' => ''],
	"PHARMA" => ['length' => '', 'charset' => ''],
	"PHARMA2T" => ['length' => '', 'charset' => '/^[0-9]+$/'],
	"QRCODE" => ['length' => '', 'charset' => ''],
	"DATAMATRIX" => ['length' => '', 'charset' => ''],
	"PDF417" => ['length' => '', 'charset' => '']
];

if (!array_key_exists($type, $rules)) {
	$data['error'] = 'Invalid Barcode Type';
	echo json_encode($data);
	exit();
}

if (strlen($text) != $rules[$type]['len'] && $rules[$type]['len'] != '') {
	$data['error'] = 'Invalid Barcode Length';
	echo json_encode($data);
	exit();
}

if (!preg_match($rules[$type]['charset'], $text) && $rules[$type]['charset'] != '') {
	$data['error'] = 'Invalid Barcode Characters';
	echo json_encode($data);
	exit();
}

// Barcode Generation
try{
	$d = !$is2D ? new DNS1D() : new DNS2D();
	$d->setStorPath(__DIR__.'/cache/');

	$output = '<img class="max-w-full' . ($is2D ? ' w-full' : ' w-1/6') . '" src="data:image/svg+xml;base64,' .
		base64_encode($d->getBarcodeSvg($text, $type)) . '" alt="barcode"   />';
	
	$data['data'] = $output;
	$data['Result'] = 'OK';
}catch(Exception $ex){
	$data['error'] = $ex->getMessage();
}

echo json_encode($data);
exit();

?>
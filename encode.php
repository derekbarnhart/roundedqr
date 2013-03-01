<?php    
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

    include "qrlib.php";    

   $json_data = http_get_request_body();
			
			//Decode into an array
	$json_array = json_decode($json_data,true);

	$error_level = $json_array["error_level"];
	$request_data = $json_array["qr_data"];
	//$error_level = $_REQUEST['error_level'];
	//$request_data = $_REQUEST['qr_data'];
	
    error_reporting(E_ERROR | E_PARSE);
	
    ob_start("ob_gzhandler");
    header('Content-type: application/json');
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    
    
    
    $errorCorrectionLevel = 'H';
    if (isset($error_level) && in_array($error_level, array('L','M','Q','H')))
        $errorCorrectionLevel = $error_level;    

    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);
		
    if (isset($request_data)) { 
    
        //it's very important!
        if (trim($request_data) == '')
            die('data cannot be empty! <a href="?">back</a>');
    
        //QRcode::raw($_REQUEST['data'],$filename,$matrixPointSize,$matrixPointSize,2);
    	$encoded = QRcode::text($request_data,false, $errorCorrectionLevel,1);
    
    } else {    
    
    	$response['result_code'] = 400;
    	$response['msg'] = var_dump($json_data);
		//$response['msg'] = "Must provide data to be encoded";
		echo json_encode($response);
		die();
 }    

	$array_size = count($encoded);
	
	
	//Create an empty row
	for($i = 0;$i<$array_size+2;$i++)
	{
		$final_array[]=0;
	}
	
	//Print the data
	for($i = 0;$i<$array_size;$i++)
	{
		$final_array[]=0;//Pad an extra 0
		for($o = 0;$o<$array_size;$o++)
		{
			$final_array[]= intval($encoded[$i][$o]);
		}
	
		$final_array[]=0;//Pad an extra 0
	}
	
	//Create an empty row
	for($i = 0;$i<$array_size+2;$i++)
	{
		$final_array[]=0;
	}
	
	$response['result_code'] = 200;
	$response['array_width'] = $array_size+2;
	$response['data']=$final_array;
	echo json_encode($response);
   // QRtools::timeBenchmark();    

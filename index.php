<html>
<head></head>
<script type="text/javascript" src="jscolor/jscolor.js"></script>
<link href='http://fonts.googleapis.com/css?family=Cabin' rel='stylesheet' type='text/css'>


<style>
body {background:#fc8;font-family: 'Cabin', sans-serif;} 
span {font-weight:bold; font-size:20px;}
input, option, select {font-size:20px;}

.color{font-size:16px;}

#content {width:600px;margin-left:auto;margin-right:auto;} 
#content h1 {text-align:center;margin-top:30px;}

#qrcode_img {padding:15px;background:#ffffff;} 
#qr_container{width:300px;margin-left:auto;margin-right:auto;}


</style>
<body><div id='content'><h1>Rounded QR Generator</h1><hr/>
<?php        
include "qrlib.php"; 
$default_data = "www.google.com";

    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';
	
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    
    error_reporting(E_ERROR | E_PARSE);
	$filename = $PNG_TEMP_DIR.'test.png';
	//$filename_pic = $PNG_TEMP_DIR.'test_pic.png';
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'H';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 4;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);


		if (isset($_REQUEST['fg']))
		{	
			$foreground_color= $_REQUEST['fg'];	
		}else
		{   $foreground_color= "000000";	
		}
		
		if (isset($_REQUEST['bg']))
		{
			$background_color= $_REQUEST['bg'];
		}else
		{     $background_color= "FFFFFF";
		}
		
		if (isset($_REQUEST['dt']))
		{
			$dot_color= $_REQUEST['dt'];
		}else
		{		$dot_color= "0000FF";
		}
		
		if (isset($_REQUEST['im']))
		{}
		
		$qr_config['fg']=$foreground_color;
		$qr_config['bg']=$background_color;
		$qr_config['dt']=$dot_color;
		
    if (isset($_REQUEST['data'])) { 
    
        //it's very important!
        if (trim($_REQUEST['data']) == '')
            die('data cannot be empty! <a href="?">back</a>');
            
        // user data
        $filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
		QRcode::png_p($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 2,false,$qr_config);
	
	} else {    
    
        //default data 
        QRcode::png_p($default_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2,false,$qr_config);
	}        
    //display generated file
    echo '<div id="qr_container"><img id="qrcode_img" src="'.$PNG_WEB_DIR.basename($filename).'" height="256" width="256" /></div><hr/>';  
	
	echo '<form action="index.php" method="post">
	<table>
	<tr><td><span>Data:</span></td><td><span>Reliability:</span></td></tr>
	<tr><td><input name="data" value="'.(isset($_REQUEST['data'])?htmlspecialchars($_REQUEST['data']):$default_data).'" /></td><td><select name="level">
            <option value="H"'.(($errorCorrectionLevel=='H')?' selected':'').'>H - Reliable and large</option>
			<option value="Q"'.(($errorCorrectionLevel=='Q')?' selected':'').'>Q</option>
            <option value="M"'.(($errorCorrectionLevel=='M')?' selected':'').'>M</option>
			<option value="L"'.(($errorCorrectionLevel=='L')?' selected':'').'>L - Smallest and least reliable</option>
        </select></td></tr>
	</table>';
	
	echo
		 '
		 <table>
		 <tr><td><span>Foreground Color:</span></td><td><span>Background Color:</span></td><td><span>Dot Color:</span></td></tr>
		 <tr><td><input class="color" name="fg" value="'.(isset($_REQUEST['fg'])?htmlspecialchars($_REQUEST['fg']):'000000').'" /></td>
		 <td><input class="color" name="bg" value="'.(isset($_REQUEST['fg'])?htmlspecialchars($_REQUEST['bg']):'FFFFFF').'" /></td>
		 <td><input class="color" name="dt" value="'.(isset($_REQUEST['fg'])?htmlspecialchars($_REQUEST['dt']):$dot_color).'" /></td></tr>
		 </table>';

   function parse_colors($string)
   {
		$colors=explode(",",$string,3);
   
		if(count($colors)<3)
		{
			for($c =0;$c<3-count($colors);$c++)
			{
				$colors[]=0;
			}
		}
   }
   
 /**
		 </select>&nbsp;<hr/><span>Foreground Color:</span>&nbsp;<input class="color" name="fg" value="'.(isset($_REQUEST['fg'])?htmlspecialchars($_REQUEST['fg']):'000000').'" />&nbsp;
		 <span>Background Color:</span>&nbsp;<input class="color" name="bg" value="'.(isset($_REQUEST['fg'])?htmlspecialchars($_REQUEST['bg']):'FFFFFF').'" />&nbsp;
		 <span>Dot Color:</span>&nbsp;<input class="color" name="dt" value="'.(isset($_REQUEST['fg'])?htmlspecialchars($_REQUEST['dt']):$dot_color).'" />&nbsp; 
		 '   
   */
 ?>
	
	<hr/>
        <input type="submit" value="Update"></form></div></body>
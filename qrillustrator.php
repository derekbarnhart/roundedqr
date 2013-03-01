<?php

class QRIllustrator 
{
	//Basic size info for the QR
	private $bitQuadHeight;
	private $bitBlockHeight; // The number of pixels in every bit block
	private $border;
	private $qr_start_x;
	private $qr_start_y;
	
	private $data;
	
	//Create the surfaces used to build the QR code
	private $finalimg;
	private $img;
	
	//Setup some standard colors
	private $black;
	private $white;
	private $red;
	private $green;
	private $blue;
	private $orange;
	private $yellow;
	private $purple;
	
	//Set up the colors to be used in the QR
	private $background_color;
	private $foreground_color;
	private $dot_color;
	private $background_image;
	private $demo_qr_matrix;
	private $grid_length;
	
		
	
	public function __construct($data)
	{	
		$this->grid_length = count($data);
		$this->setData($data);
	
		$this->bitQuadHeight = 20;
		$this->border = 0;
		$this->bitBlockHeight = $this->bitQuadHeight *2;
		$this->qr_start_x = $this->border*2.5;
		$this->qr_start_y = $this->border*2.5;
	
		$image_width = $this->grid_length * $this->bitBlockHeight +(2*$this->border);
		
		$this->finalimg = imagecreatetruecolor($image_width,$image_width);
		$this->img = imagecreatetruecolor($image_width,$image_width);
		
		$this->black = imagecolorallocate($this->img, 0, 0, 0);
		$this->white = imagecolorallocate($this->img, 255, 255, 255);
		$this->red   = imagecolorallocate($this->img, 255, 0, 0);
		$this->green = imagecolorallocate($this->img, 0, 255, 0);
		$this->blue  = imagecolorallocate($this->img, 0, 0, 255);
		$this->orange  = imagecolorallocate($this->img, 255, 144, 0);
		$this->yellow  = imagecolorallocate($this->img, 250, 238, 15);
		$this->purple  = imagecolorallocate($this->img, 140, 90, 247);
		
	}

public function set_color($color )
{
	if($color == "White")
	{	return $this->white;}
	elseif($color == "Black")
	{	return $this->black;}
	elseif($color == "Blue")
	{	return  $this->blue;}
	elseif($color == "Red")
	{	return  red;}
	elseif($color == "Green")
	{return $this->green;}
	elseif($color == "Orange")
	{	return $this->orange;}
	elseif($color == "Yellow")
	{	return  $this->yellow;}
	elseif($color == "Purple")
	{   return  $this->purple;}
	else
	{	
		$length = strlen($color);
		$remainder =$length % 3; 
		if($remainder)
		{
			return null;
		
		}else
		{
			$num_chars = $length /3;
		
			$r = substr($color,0,$num_chars);
			$g = substr($color,$num_chars,-$num_chars);
			$b = substr($color,-$num_chars);
		
			return imagecolorallocate($this->img, hexdec($r),hexdec($g),hexdec($b));
		}
	
	
	return null;}
}

public function setData($new_data)
{
	$this->demo_qr_matrix = $new_data;
	
}

public function setBackgroundColor($color)
{
	$this->background_color = $this->set_color($color);
	
	if(!isset($this->background_color))
	{
		$this->background_color= $this->white;
	}
}


public function setForegroundColor($color)
{
	$this->foreground_color = $this->set_color($color);
	
	if(!isset($this->foreground_color))
	{
		$this->foreground_color= $this->white;
	}
}

public function setDotColor($color)
{
	$this->dot_color = $this->set_color($color);
	
	if(!isset($this->dot_color))
	{
		$this->dot_color= $this->black;
	}
}


public function setBackgroundImage($image_location)
{
		$background_image = $image_location;
		$tile = imagecreatefrompng($image_location);
		imagesettile($this->finalimg, $tile);
		
		//print the tile on the final image surface
		imagefilledrectangle($this->finalimg, 0, 0, $this->qr_start_x+($this->grid_length* $this->bitBlockHeight)+$this->border, $this->qr_start_y+($this->grid_length* $this->bitBlockHeight)+$this->border, IMG_COLOR_TILED);
}


public function renderQR($filename=false)
{
	//header ("Content-type: image/png");
	//Turn on antialiasing incase anything is using it
	imageantialias ($this->img , true );
	
	//Create the background 
	imagefill($this->img,0,0,$this->background_color);

	$byte_xpos =$this->qr_start_x; 
	$byte_ypos =$this->qr_start_y;

	//$this->grid_length=count($this->demo_qr_matrix);
	
	for($y = 0; $y<$this->grid_length; $y++)
	{
		for( $x = 0; $x< $this->grid_length; $x++)
		{
			$topLeftRounded=false;
			$topRightRounded=false;
			$bottomRightRounded=false;
			$bottomLeftRouned=false;

			//Get all the locations
			$above = $this->demo_qr_matrix[$y-1][$x];
			$below = $this->demo_qr_matrix[$y+1][$x];
			$left = $this->demo_qr_matrix[$y][$x-1];
			$right = $this->demo_qr_matrix[$y][$x+1];
			
			$above_left = $this->demo_qr_matrix[$y-1][$x-1];
			$above_right = $this->demo_qr_matrix[$y-1][$x+1];;
			$below_left =  $this->demo_qr_matrix[$y+1][$x-1];;
			$below_right = $this->demo_qr_matrix[$y+1][$x+1];;
			
			
			
			
		//Figure out if this byte is ON or OFF
		if( $this->demo_qr_matrix[$y][$x]==1)
		{	//This one is on
			//Figure out upper left
			if( $left == 0  && $above == 0  )
			{
				//There is nothing to above or to the left so this should be rounded
				$this->printOnUpperLeftR($byte_xpos, $byte_ypos);
				$topLeftRounded=true;
			}else
			{
				$this->printOnNormal($byte_xpos, $byte_ypos);
			}
			
			//Figure out upper right
			if( $right == 0  && $above == 0  )
			{
				//There is nothing to above or to the left so this should be rounded
				$this->printOnUpperRightR($byte_xpos+$this->bitQuadHeight, $byte_ypos);
				$topRightRounded=true;
			}else
			{
				$this->printOnNormal($byte_xpos+$this->bitQuadHeight, $byte_ypos);
			}
			
			//Figure out lower rigt
			if( $right == 0  && $below == 0  )
			{
				//There is nothing to above or to the left so this should be rounded
				$this->printOnLowerRightR($byte_xpos+$this->bitQuadHeight, $byte_ypos+$this->bitQuadHeight);
				$bottomRightRounded=true;
			}else
			{
				$this->printOnNormal($byte_xpos+$this->bitQuadHeight, $byte_ypos+$this->bitQuadHeight);
			}
			
			//Figure out lower left
			
			if( $left == 0  && $below == 0  )
			{
				//There is nothing to below or to the left so this should be rounded
				$this->printOnLowerLeftR($byte_xpos, $byte_ypos+$this->bitQuadHeight);
				$bottomLeftRounded=true;
			}else
			{
				$this->printOnNormal($byte_xpos, $byte_ypos+$this->bitQuadHeight);
			}
		
		
		if($topLeftRounded && $topRightRounded && $bottomRightRounded && $bottomLeftRounded)
		{	//This is a solitary dot so lets color it differently to add interest
			$this->printOnRounded($byte_xpos, $byte_ypos+$this->bitQuadHeight);
		}
		
		
		}else
		{// This is an off.... throw in some rounded stuff
		
			if( $left == 1  && $above == 1  && $above_left == 1)
			{
				//There is nothing to above or to the left so this should be rounded
				$this->printOffUpperLeftR($byte_xpos, $byte_ypos);
			}else
			{
				//Do nothing, this is supposed to be blank
			}
			
			//Figure out upper right
			if( $right == 1  && $above == 1 && $above_right == 1 )
			{
				//There is nothing to above or to the left so this should be rounded
				$this->printOffUpperRightR($byte_xpos+$this->bitQuadHeight, $byte_ypos);
			}else
			{
				
			}
			
			//Figure out lower rigt
			if( $right == 1  && $below == 1  && $below_right == 1)
			{
				//There is nothing to above or to the left so this should be rounded
				$this->printOffLowerRightR($byte_xpos+$this->bitQuadHeight, $byte_ypos+$this->bitQuadHeight);
			}else
			{
				
			}
			//Figure out lower left
			if( $left == 1  && $below == 1  && $below_left == 1)
			{
				//There is nothing to below or to the left so this should be rounded
				$this->printOffLowerLeftR($byte_xpos, $byte_ypos+$this->bitQuadHeight);
			}else
			{
				
			}
		
		}
		
		$byte_xpos+=$this->bitQuadHeight*2;
		}
		$byte_xpos=$this->qr_start_x;
		
		$byte_ypos+=$this->bitQuadHeight*2;
	}

if(isset($background_image))
{
	//Set the background color to transparent
	$transparentColor = $this->background_color;
	imagecolortransparent ( $this->img, $transparentColor );
}
imagesettile($this->finalimg, $this->img);
imagefilledrectangle($this->finalimg, 0, 0, $this->qr_start_x+($this->grid_length* $this->bitBlockHeight)+$this->border, $this->qr_start_y+($this->grid_length* $this->bitBlockHeight)+$this->border, IMG_COLOR_TILED);

//Display the image		
if(isset($filename))
{
	imagepng($this->finalimg,$filename);
}else
{
	imagepng($finalimg);
}
//Clean up

imagedestroy($this->finalimg);
imagedestroy($this->img);
}


function printOnUpperLeftR( $x,  $y)
{
$adjusted_height = ($this->bitQuadHeight*2);
imagefilledarc ($this->img , $x+$this->bitQuadHeight, $y+$this->bitQuadHeight , $adjusted_height , $adjusted_height , 180 , 270 , $this->foreground_color , IMG_ARC_PIE);
}

function printOnUpperRightR( $x,  $y)
{
$adjusted_height = ($this->bitQuadHeight*2);
	imagefilledarc ($this->img , $x, $y+$this->bitQuadHeight , $adjusted_height , $adjusted_height , 270, 0 , $this->foreground_color , IMG_ARC_PIE);
}

function printOnLowerRightR( $x,  $y)
{
$adjusted_height = ($this->bitQuadHeight*2);
	imagefilledarc ($this->img , $x, $y , $adjusted_height , $adjusted_height , 0, 90 , $this->foreground_color , IMG_ARC_PIE);
}

function printOnLowerLeftR( $x,  $y)
{
	$adjusted_height = ($this->bitQuadHeight*2);
	imagefilledarc ($this->img , $x+$this->bitQuadHeight, $y , $adjusted_height , $adjusted_height , 90, 180 , $this->foreground_color , IMG_ARC_PIE);
}

function printOnRounded( $x,  $y)
{

$adjusted_height = ($this->bitQuadHeight*2);
	imagefilledellipse ( $this->img ,$x+$this->bitQuadHeight , $y , $adjusted_height, $adjusted_height, $this->dot_color);
	}

function printOnNormal( $x,  $y)
{
	imagefilledrectangle($this->img, $x, $y, $x+$this->bitQuadHeight, $y+$this->bitQuadHeight , $this->foreground_color);
}

function printOffUpperLeftR( $x,  $y)
{

$adjusted_height = ($this->bitQuadHeight*2);
	imagefilledrectangle($this->img, $x, $y, $x+$this->bitQuadHeight, $y+$this->bitQuadHeight , $this->foreground_color);
	imagefilledarc ($this->img , $x+$this->bitQuadHeight, $y+$this->bitQuadHeight , $adjusted_height , $adjusted_height , 180 , 270 , $this->background_color , IMG_ARC_PIE);
}

function printOffUpperRightR( $x,  $y)
{

$adjusted_height = ($this->bitQuadHeight*2);
	imagefilledrectangle($this->img, $x, $y, $x+$this->bitQuadHeight, $y+$this->bitQuadHeight , $this->foreground_color);
	imagefilledarc ($this->img , $x, $y+$this->bitQuadHeight , $adjusted_height , $adjusted_height , 270, 0 , $this->background_color , IMG_ARC_PIE);
}

function printOffLowerRightR( $x,  $y)
{

$adjusted_height = ($this->bitQuadHeight*2);
	imagefilledrectangle($this->img, $x, $y, $x+$this->bitQuadHeight, $y+$this->bitQuadHeight , $this->foreground_color);
	imagefilledarc ($this->img , $x, $y , $adjusted_height , $adjusted_height , 0, 90 , $this->background_color , IMG_ARC_PIE);
}

function printOffLowerLeftR( $x,  $y)
{

$adjusted_height = ($this->bitQuadHeight*2);
	imagefilledrectangle($this->img, $x, $y, $x+$this->bitQuadHeight, $y+$this->bitQuadHeight , $this->foreground_color);
	imagefilledarc ($this->img , $x+$this->bitQuadHeight, $y , $adjusted_height , $adjusted_height , 90, 180 , $this->background_color , IMG_ARC_PIE);
}
	
}
	
?>
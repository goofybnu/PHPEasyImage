<?php
include_once('bmp_functions.php');	//Required for bmp image type
class easyImage {
	var $m = array("image/bmp","image/x-windows-bmp","image/gif","image/jpeg","image/pjpeg","image/png");
	var $e = array("bm","bmp","gif","jpg","jpeg","jpe","jfif","jfif-tbnl","png","x-png");
	function load($file){
		$this->fe = strtolower(end(explode(".",$file)));
		
		if(!file_exists($file))
			return false;
			
		if(!in_array($this->fe,$this->e))
			return false;
		
		switch($this->fe){
			case 'jpg':
			case 'jpeg':
			case 'jpe':
			case 'jfif':
			case 'jfif-tbnl':
				if(!function_exists("imagecreatefromjpeg"))
					return false;

				$this->i = imagecreatefromjpeg($file);
				return $this->i;
			break;
			
			case 'png':
			case 'x-png':
				if(!function_exists("imagecreatefrompng"))
					return false;

				$this->i = imagecreatefrompng($file);
				return $this->i;
			break;
			
			case 'gif':
				if(!function_exists("imagecreatefromgif"))
					return false;

				$this->i = imagecreatefromgif($file);
				return $this->i;
			break;
			
			case 'bm':
			case 'bmp':
				if(!function_exists("imagecreatefrombmp"))
					return false;

				$this->i = imagecreatefrombmp($file);
				return $this->i;
			break;
			
			default :
				return false;
			break;
		}
	}
	function rotate($image, $degrees){
		$image = imagerotate($image, $degrees, 0);
		return $image;
	}
	function resize($image,$width,$height,$stretch=false){
		$this->originalWidth = imagesx($image);
		$this->originalHeight = imagesy($image);
		if($stretch==true){
			$this->newWidth = $width;
			$this->newHeight = $height;
		} else {
			$this->escale = min($width/$this->originalWidth,$height/$this->originalHeight);
			$this->newWidth = floor($this->escale*$this->originalWidth);
			$this->newHeight = floor($this->escale*$this->originalHeight);
		}
		if(function_exists('imagecopyresampled')){
			if(function_exists('imageCreateTrueColor')){
				$this->newImage = imagecreatetruecolor($this->newWidth,$this->newHeight); 
			} else {
				$this->newImage = imagecreate($this->newWidth,$this->newHeight);
			}
			if(!@imagecopyresampled($this->newImage,$image,0,0,0,0,$this->newWidth,$this->newHeight,$this->originalWidth,$this->originalHeight)){
				imagecopyresized($this->newImage,$image,0,0,0,0,$this->newWidth,$this->newHeight,$this->originalWidth,$this->originalHeight);
			}
		} else {
			$this->newImage = imagecreate($this->newWidth,$this->newHeight);
			imagecopyresized($this->newImage,$image,0,0,0,0,$this->newWidth,$this->newHeight,$this->originalWidth,$this->originalHeight);
		}
		return $this->newImage;
	}
	function resizeMin($image,$width,$height,$stretch=false){
		$this->originalWidth = imagesx($image);
		$this->originalHeight = imagesy($image);
		if($stretch==true){
			$this->newWidth = $width;
			$this->newHeight = $height;
		} else {
			$this->escale = max($width/$this->originalWidth,$height/$this->originalHeight);
			$this->newWidth = floor($this->escale*$this->originalWidth);
			$this->newHeight = floor($this->escale*$this->originalHeight);
		}
		if(function_exists('imagecopyresampled')){
			if(function_exists('imageCreateTrueColor')){
				$this->newImage = imagecreatetruecolor($this->newWidth,$this->newHeight); 
			} else {
				$this->newImage = imagecreate($this->newWidth,$this->newHeight);
			}
			if(!@imagecopyresampled($this->newImage,$image,0,0,0,0,$this->newWidth,$this->newHeight,$this->originalWidth,$this->originalHeight)){
				imagecopyresized($this->newImage,$image,0,0,0,0,$this->newWidth,$this->newHeight,$this->originalWidth,$this->originalHeight);
			}
		} else {
			$this->newImage = imagecreate($this->newWidth,$this->newHeight);
			imagecopyresized($this->newImage,$image,0,0,0,0,$this->newWidth,$this->newHeight,$this->originalWidth,$this->originalHeight);
		}
		return $this->newImage;
	}
	function crop($image,$width,$height,$x='center',$y='center'){
		$this->w = imagesx($image);
		$this->h = imagesy($image);
		if(!is_numeric($x)){
			switch($x){
				case 'left':
					$xi = 0;
				break;
				
				case 'right':
					$xi = $this->w - $width;
				break;
				
				case 'center':
					$xi = ($this->w - $width) / 2;
				break;
			}
		}
		if(!is_numeric($y)){
			switch($y){
				case 'top':
					$yi = 0;
				break;
				
				case 'bottom':
					$yi = $this->h - $height;
				break;
				
				case 'center':
					$yi = ($this->h - $height) / 2;
				break;
			}
		}
		
		if(function_exists('imagecopyresampled')){
			if(function_exists('imageCreateTrueColor')){
				$this->newImage = imagecreatetruecolor($width,$height);
			} else {
				$this->newImage = imagecreate($width,$height);
			}
			if(!@imagecopyresampled($this->newImage,$image,0,0,$xi,$yi,$width,$height,$width,$height)){
				imagecopyresized($this->newImage,$image,0,0,$xi,$yi,$width,$height,$width,$height);
			}
		} else {
			$this->newImage = imagecreate($width,$height);
			imagecopyresized($this->newImage,$image,0,0,$xi,$yi,$width,$height,$width,$height);
		}
		return $this->newImage;
	}
	function save($image,$file,$quality=80){
		if(!is_resource($image))
			$image = $this->load($image);
		$this->fe = strtolower(end(explode(".",$file)));
		switch($this->fe){
			case 'jpg':
			case 'jpeg':
			case 'jpe':
			case 'jfif':
			case 'jfif-tbnl':
				if(!function_exists("imagejpeg"))
					return false;

				imagejpeg($image,$file,$quality);
				return true;
			break;
			
			case 'png':
			case 'x-png':
				if(!function_exists("imagepng"))
					return false;

				imagepng($image,$file);
				return true;
			break;
			
			case 'gif':
				if(!function_exists("imagegif"))
					return false;

				imagegif($image,$file);
				return true;
			break;
			
			case 'bm':
			case 'bmp':
				if(!function_exists("imagecreatefrombmp"))
					return false;

				$this->i = imagebmp($image,$file);
				return true;
			break;
			
			default :
				return false;
			break;
		}
	}
}
?>
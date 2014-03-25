<?php
/***************************************************************************
**	Do not copy or distribute without prior permission of the developer.  **
***************************************************************************/

/*
EasyImage Class
Created By Carlos Ricardo Schmitz
E-mail: goofybnu@gmail.com
Version: 2.35
*/

require_once("bmp_functions.php");
class easyImage {
	var $m = array("image/bmp","image/x-windows-bmp","image/gif","image/jpeg","image/pjpeg","image/png");
	var $e = array("bm","bmp","gif","jpg","jpeg","jpe","jfif","jfif-tbnl","png","x-png");
	function hextorgb($color){
		return array(
			0=>hexdec(substr($color,1,2)),
			1=>hexdec(substr($color,3,2)),
			2=>hexdec(substr($color,5,2))
		);
	}
	private function getFileExtencion($file){
		$this->fe = explode(".", $file);
		$this->fe = end($this->fe);
		$this->fe = strtolower($this->fe);
		return $this->fe;
	}
	function load($file){
		$this->fe = $this->getFileExtencion($file);

		if(!file_exists($file)){
			return false;
		}

		if(!in_array($this->fe,$this->e)){
			return false;
		}

		switch($this->fe){
			case 'jpg':
			case 'jpeg':
			case 'jpe':
			case 'jfif':
			case 'jfif-tbnl':
				if(!function_exists("imagecreatefromjpeg")){
					return false;
				}
				$this->i = imagecreatefromjpeg($file);
				return $this->i;
			break;

			case 'png':
			case 'x-png':
				if(!function_exists("imagecreatefrompng")){
					return false;
				}
				$this->i = imagecreatefrompng($file);
				return $this->i;
			break;

			case 'gif':
				if(!function_exists("imagecreatefromgif")){
					return false;
				}
				$this->i = imagecreatefromgif($file);
				return $this->i;
			break;

			case 'bm':
			case 'bmp':
				if(!function_exists("imagecreatefrombmp")){
					return false;
				}
				$this->i = imagecreatefrombmp($file);
				return $this->i;
			break;

			default :
				return false;
			break;
		}
	}
	function save($image,$file,$quality=80){
		if(!is_resource($image)){
			$image = $this->load($image);
		}
		$this->fe = $this->getFileExtencion($file);
		switch($this->fe){
			case 'jpg':
			case 'jpeg':
			case 'jpe':
			case 'jfif':
			case 'jfif-tbnl':
				if(!function_exists("imagejpeg")){
					return false;
				}
				imagejpeg($image,$file,$quality);
				return true;
			break;

			case 'png':
			case 'x-png':
				if(!function_exists("imagepng")){
					return false;
				}
				imagepng($image,$file);
				return true;
			break;

			case 'gif':
				if(!function_exists("imagegif")){
					return false;
				}
				imagegif($image,$file);
				return true;
			break;

			case 'bm':
			case 'bmp':
				if(!function_exists("imagecreatefrombmp")){
					return false;
				}
				$this->i = imagebmp($image,$file);
				return true;
			break;

			default :
				return false;
			break;
		}
	}
	function resize($image,$width,$height,$stretch=false){
		/*
		if(!is_resource($image)){
			$image = $this->load($image);
		}
		*/
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
	function contrast($image,$percent){
		if(!is_resource($image))
			$image = $this->load($image);
		$this->width = imagesx($image);
		$this->height = imagesy($image);
		if(!$this->newImage = imagecreatetruecolor($this->width,$this->height))
			$this->newImage = imagecreate($this->width,$this->height);

		for($this->i=0;$this->i<$this->width;$this->i++){
			for($this->j=0;$this->j<$this->height;$this->j++){
				$this->rgb = imagecolorat($image,$this->i,$this->j);
				$this->r = ($this->rgb >> 16) & 0xFF;
				$this->g = ($this->rgb >> 8) & 0xFF;
				$this->b = $this->rgb & 0xFF;
				$this->r = ($percent/100) > 1 ? min(floor($this->r * ($percent/100)), 255) : max(ceil($this->r * ($percent/100)), 0);
				$this->g = ($percent/100) > 1 ? min(floor($this->g * ($percent/100)), 255) : max(ceil($this->g * ($percent/100)), 0);
				$this->b = ($percent/100) > 1 ? min(floor($this->b * ($percent/100)), 255) : max(ceil($this->b * ($percent/100)), 0);
				$this->color = imagecolorallocate($this->newImage,$this->r,$this->g,$this->b);
				imagesetpixel($this->newImage,$this->i,$this->j,$this->color);
			}
		}
		return $this->newImage;
	}
	function text($image,$font,$x='left',$y='bottom',$text,$pad,$str,$foreground,$background){
		if(!is_resource($image))
			$image = $this->load($image);
		$this->originalWidth = imagesx($image);
		$this->originalHeight = imagesy($image);
		if(!is_int($x)){
			switch($x){
				case 'left':
					$x = $str+$pad;
				break;

				case 'right':
					$x = $this->originalWidth-($str+$pad);
				break;

				case 'center':
					$x = $this->originalWidth/2;
				break;
			}
		}
		if(!is_int($y)){
			switch($y){
				case 'top':
					$y = $str+$pad;
				break;

				case 'bottom':
					$y = $this->originalHeight-($str+$pad);
				break;

				case 'middle':
					$y = $this->originalHeight/2;
				break;
			}
		}
		$this->textWidth = strlen($text)*imagefontwidth($font);
		$this->textHeight = imagefontheight($font);
		if($x+$this->textWidth>$this->originalWidth)
			$x -= $this->textWidth+$str;

		if($y+$this->textHeight>$this->originalHeight)
			$y -= $this->textHeight+$str;

		$fg = $this->hextorgb($foreground);
		$this->fgc = imagecolorallocate($image,$fg[0],$fg[1],$fg[2]);
		$bg = $this->hextorgb($background);
		$this->bgc = imagecolorallocate($image,$bg[0],$bg[1],$bg[2]);
		$ix = $x-$str;
		$fx = $x+$str;
		$iy = $y-$str;
		$fy = $y+$str;
		for($i=$ix;$i<=$fx;$i++){
			for($j=$iy;$j<=$fy;$j++){
				imagestring($image,$font,$i,$j,$text,$this->bgc);
			}
		}
		imagestring($image,$font,$x,$y,$text,$this->fgc);
		return $image;
	}
	function ttftext($image,$font,$size,$angle,$padding,$foreground,$text){
		if(!is_resource($image))
			$image = $this->load($image);
		if(!file_exists($font))
			return $image;
		$this->a = imagettfbbox($size,$angle,$font,"W");
		if($angle<0){
			$this->fh = abs($this->a[7]-$this->a[1]);
		} else if($angle>0) {
			$this->fh = abs($this->a[1]-$this->a[7]);
		} else {
			$this->fh = abs($this->a[7]-$this->a[1]);
		}
		$this->a = imagettfbbox($size,$angle,$font,$text);
		if($angle<0) {
			$this->w = abs($this->a[4]-$this->a[0]);
			$this->h = abs($this->a[3]-$this->a[7]);
			$this->oy = $this->fh;
			$this->ox = 0;
		} else if ($angle>0) {
			$this->w = abs($a[2]-$this->a[6]);
			$this->h = abs($a[1]-$this->a[5]);
			$this->oy = abs($a[7]-$this->a[5])+$this->fh;
			$this->ox = abs($a[0]-$this->a[6]);
		} else {
			$this->w = abs($this->a[4]-$this->a[6]);
			$this->h = abs($this->a[7]-$this->a[1]);
			$this->oy = $this->fh;
			$this->ox = 0;
		}
		$fg = $this->hextorgb($foreground);
		$bg = $this->hextorgb($background);
		$this->fgc = imagecolorallocate($image,$fg[0],$fg[1],$fg[2]);
		imagettftext($image,$size,$angle,$this->ox+$padding,$this->oy+$padding,$this->fgc,$font,$text);
		return $image;
	}
	function logo($image,$logo,$x='center',$y='center'){
		if(!is_resource($image))
			$image = $this->load($image);
		if(!is_resource($logo))
			$logo = $this->load($logo);

		$this->dst_w = imagesx($image);
		$this->dst_h = imagesy($image);
		$this->src_w = imagesx($logo);
		$this->src_h = imagesy($logo);

		imagealphablending($logo,true);
		if(!is_int($x)){
			switch($x){
				case 'left':
					$x = 0;
					break;

				case 'right':
					$x = $this->dst_w-($this->src_w);
					break;

				case 'center':
					$x = ($this->dst_w-$this->src_w)/2;
					break;
			}
		}
		if(!is_int($y)){
			switch($y){
				case 'top':
					$y = 0;
					break;

				case 'bottom':
					$y = $this->dst_h-($this->src_h);
					break;

				case 'middle':
					$y = ($this->dst_h-$this->src_h)/2;
					break;
			}
		}
		imagecopy($image,$logo,$x,$y,0,0,$this->src_w,$this->src_h);
		return $image;
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
	function roundBorder($image,$size,$color){
		$this->w = imagesx($image);
		$this->h = imagesy($image);
		$this->corner = imagecreatetruecolor($size,$size);
		$this->cor = hextorgb($color);
		$this->bg = imagecolorallocate($this->corner,$this->$cor[0],$this->$cor[1],$this->$cor[1]);
		$this->trans = imagecolorallocate($this->corner,255,0,255);
		imagefilledrectangle($this->corner,0,0,$size,$size,$this->bg);
		imagefilledellipse($this->corner,$size,$size,$size*2,$size*2,$this->trans);
		imagecolortransparent($this->corner,$this->trans);
		imagecopymerge($image,$this->corner,0,0,0,0,$size,$size,100);
		$this->rotate = imagerotate($this->corner,180,0);
		imagecolortransparent($this->rotate,$this->trans);
		imagecopymerge($image,$this->rotate,$this->w-$size,$this->h-$size,0,0,$size,$size,100);
		$this->rotate = imagerotate($this->rotate,90,0);
		imagecolortransparent($this->rotate,$this->trans);
		imagecopymerge($image,$this->rotate,$this->w-$size,0,0,0,$size,$size,100);
		$this->rotate = imagerotate($this->rotate,180,0);
		imagecolortransparent($this->rotate,$this->trans);
		imagecopymerge($image,$this->rotate,0,$this->h-$size,0,0,$size,$size,100);
		imagecolortransparent($image,$this->bg);
		return $image;
	}
	function canvas(){}
	function rotate(){}
	function flip(){}
	function colorise(){}
	function newImage(){}
	function draw(){}
}
?>
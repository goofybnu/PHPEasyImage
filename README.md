#PHPEasyImage
*Easy PHP GD Image Processing Class*

##Usage
To use PHPEasyImage just upload 'class.easyimage.php' to your server and include in your script.

```php
// Include PHPEasyImage Class
require_once('class.easyimage.php');

// Create a new object instance
$easyImage = new easyImage();

/**
 * Load an image to object, files can be 'jpg, jpeg, jpe, jfif, jfif-tbnl, png, x-png, gif, bm, bmp'
 * @param $file can be local file or URL if URL File open is active on your server
 */
$image = $easyImage->load('./sample.jpg');

/**
 * Rotate image
 * @param $image image object created by load function
 * @param $degrees integer degrees
 */
$image = $easyImage->rotate($image, '45');

/**
 * Resize image
 * @param $image image object created by load function
 * @param $width integer width
 * @param $height integer height
 * @param $stretch default falue is false to respect aspect ratio
 */
$image = $easyImage->resize($image,300,300);

/**
 * Resize to minimal width or height
 * @param $image image object created by load function
 * @param $width integer width
 * @param $height integer height
 * @param $stretch default falue is false to respect aspect ratio
 */
$image = $easyImage->resizeMin($image,300,300);

/**
 * Crop image
 * @param $image image object created by load function
 * @param $width integer width
 * @param $height integer height
 * @param $x horizontal position to start crop can be integer or 'left, center, right' default value is center
 * @param $y vertical position to start crop can be integer or 'top, center, bottom' default value is center
 */
$image = $easyImage->crop($image,150,150);

/**
 * Save image to file
 * @param $image image object created by load function
 * @param $file file with path, extencion can be 'jpg, jpeg, jpe, jfif, jfif-tbnl, png, x-png, gif, bm, bmp', extencion file will set de type of image 
 * @param $quality in case of jpeg file type you can set quality default value is 80
 */
$image = $easyImage->save($image,'./newfile.png');
```

## License
PHPEasyImage is licensed under the MIT license. (http://opensource.org/licenses/MIT)

## Contributing
Pull requests are the way to go here.

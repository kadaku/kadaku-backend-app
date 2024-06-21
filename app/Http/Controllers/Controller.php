<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	use AuthorizesRequests, ValidatesRequests;

	protected $message_bad_request = "Bad Request";
	protected $message_data_found = "Data Found";
	protected $message_data_not_found = "Data Not Found";
	protected $message_add_success = "Success create data";
	protected $message_add_failed = "Failed create data";
	protected $message_update_success = "Success update data";
	protected $message_update_failed = "Failed update data";
	protected $message_destroy_success = "Success delete data";
	protected $message_destroy_failed = "Failed delete data";


	function __construct()
	{
		date_default_timezone_set('Asia/Jakarta');
	}

	function convert_to_webp($file_path)
	{
		// Get the image type
		$imageType = exif_imagetype($file_path);

		// Create an image resource from the file
		switch ($imageType) {
			case IMAGETYPE_JPEG:
				$image = imagecreatefromjpeg($file_path);
				break;
			case IMAGETYPE_PNG:
				$image = imagecreatefrompng($file_path);
				break;
			case IMAGETYPE_GIF:
				$image = imagecreatefromgif($file_path);
				break;
			default:
				throw new \Exception('Unsupported image type');
		}

		// Start output buffering to capture the WebP data
		ob_start();
		imagewebp($image, null, 90);
		$webpImage = ob_get_contents();
		ob_end_clean();

		// Free up memory
		imagedestroy($image);

		return $webpImage;
	}
	
	function resizeImageToRatio($sourcePath, $destinationPath, $desiredRatioWidth, $desiredRatioHeight)
	{
		// Get the original image dimensions and type
		list($width, $height, $imageType) = getimagesize($sourcePath);

		// Calculate the new dimensions maintaining the desired ratio
		if ($width / $height > $desiredRatioWidth / $desiredRatioHeight) {
			$newHeight = $height;
			$newWidth = ($desiredRatioWidth / $desiredRatioHeight) * $height;
		} else {
			$newWidth = $width;
			$newHeight = ($desiredRatioHeight / $desiredRatioWidth) * $width;
		}

		// Create a new image resource
		$dstImage = imagecreatetruecolor($newWidth, $newHeight);

		// Create the source image based on the type
		switch ($imageType) {
			case IMAGETYPE_JPEG:
				$srcImage = imagecreatefromjpeg($sourcePath);
				break;
			case IMAGETYPE_PNG:
				$srcImage = imagecreatefrompng($sourcePath);
				break;
			case IMAGETYPE_GIF:
				$srcImage = imagecreatefromgif($sourcePath);
				break;
			default:
				throw new Exception('Unsupported image type');
		}

		// Resize the image
		imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

		// Save the resized image to the destination path
		switch ($imageType) {
			case IMAGETYPE_JPEG:
				imagejpeg($dstImage, $destinationPath);
				break;
			case IMAGETYPE_PNG:
				imagepng($dstImage, $destinationPath);
				break;
			case IMAGETYPE_GIF:
				imagegif($dstImage, $destinationPath);
				break;
		}

		// Free up memory
		imagedestroy($srcImage);
		imagedestroy($dstImage);
	}
}

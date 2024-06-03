<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	use AuthorizesRequests, ValidatesRequests;

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
}

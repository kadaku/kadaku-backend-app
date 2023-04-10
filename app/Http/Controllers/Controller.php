<?php

namespace App\Http\Controllers;

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
    

}

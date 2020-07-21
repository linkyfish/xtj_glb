<?php
namespace Model;
// hook model_errorcode_use.php

use App\Model;

class ErrorCodeModel extends Model
{

    // hook model_errorcode_public_start.php
    public $table = 'zx_error_code';
    public $index = 'code';
    // hook model_errorcode_public_end.php

    // hook model_errorcode_start.php



    // hook model_errorcode_end.php

}
?>
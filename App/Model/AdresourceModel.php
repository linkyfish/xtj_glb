<?php
namespace Model;
// hook model_ask_use.php

use App\Model;

class AdresourceModel extends Model
{

    // hook model_ask_public_start.php
    public $table = 'adresource';
    public $index = 'id';
    public $is_delete = 'IsDelete';
    // hook model_ask_public_end.php

    // hook model_ask_start.php

    // hook model_ask_end.php

}
?>
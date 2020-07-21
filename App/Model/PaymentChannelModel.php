<?php
namespace Model;

// hook model_payment_custom_use.php

use App\Model;

class PaymentChannelModel extends Model
{
    // hook model_payment_custom_use.php
    public $table = 'payment_channel';
    public $index = 'id';
    //public $is_delete = 'is_delete';
    // hook model_payment_custom_start.php

    public function Add(){
        $insert =[
            'channel_name'=>32132,
            'status'=>1,
            'conf'=>'{}',
            'amount_items'=>"",
        ];
     return $this->insert($insert);
    }

}


?>
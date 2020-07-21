<?php
namespace Model;

// hook model_payment_custom_use.php

use App\Model;

class PaymentCustomModel extends Model
{
    // hook model_payment_custom_use.php
    public $table = 'payment_custom';
    public $index = 'ID';
    //public $is_delete = 'is_delete';
    // hook model_payment_custom_start.php

    public function Add(){
        $insert =[
            'RankNo'=>32132,
            'UserName'=>$UserName,
            'NickName'=>123,
            'MoneyNum'=>32131,
            'Signature'=>32132131,
            'WeChatID'=>3213123,
            'Status'=>$Status,
            'head_img'=>$head_img
        ];
     return $this->insert($insert);
    }
    // hook model_payment_custom_end.php
}


?>
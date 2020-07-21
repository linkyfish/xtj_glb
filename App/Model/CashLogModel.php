<?php
namespace Model;

class CashLogModel extends \App\Model {
    //public $link='db1';
    public $table = 'cash_log';
    public $index ='LogID';


    public function sum_by_sub($cond,$agents){
        $list = $this->select($cond);
        $data=[];
        foreach ($list as $v) {
            if(empty($v['AdminID'])){
                continue;
            }
            $a = $v['AdminID'];

            $data[$a]['TotalNum']+=1;
            $data[$a]['Account'] = !empty($agents[$v['AdminID']]) ? $agents[$v['AdminID']] : '-';

            //10 待审核 20 人工打款 30 禁用 40 退回 40 杀
            //count(1) as TotalNum,AdminID,sum(case when Status=20 then 1 else 0 end ) as TotalCancel,sum(case when Status=10 then Money else 0 end ) as TotalCashNum,sum(case when Status=10 then Comm else 0 end ) as TotalFeeNum

            $v['Status']==20 && $data[$a]['TotalFeeNum']=bcadd($data[$a]['TotalFeeNum'],$v['Comm']);
            switch ($v['Status']){
                case 10:
                    break;
                case 20:
                    $data[$a]['TotalCashNum']=bcadd($data[$a]['TotalCashNum'],$v['Number']);
                    $data[$a]['TotalRenGong']=bcadd($data[$a]['TotalRenGong'],$v['Money']);
                    break;
                case 30:
                    $data[$a]['TotalCancel']=bcadd($data[$a]['TotalCancel'],$v['Number']);
                    break;
                case 40:
                    $data[$a]['TotalSys']=bcadd($data[$a]['TotalSys'],$v['Number']);
                    break;
            }
        }

//        $cond['Type']=52;
//        $gamebi = $this->Gamebilog->select($cond,[],'AdminID,SUM(Money) as Money',0,0,'AdminID','GROUP BY AdminID');

        foreach ($data as &$v){
            $v['TotalCashNum']=calculate($v['TotalCashNum']);
            $v['TotalRenGong']=calculate($v['TotalRenGong']);
            //$v['TotalShouGong']=calculate(-$gamebi[$v['Account']]['Money']);
            $v['TotalCancel']=calculate($v['TotalCancel']);
            $v['TotalSys']=calculate($v['TotalSys']);
            $v['TotalFeeNum']=calculate($v['TotalFeeNum']);
        }
        return array_values($data);
    }


}
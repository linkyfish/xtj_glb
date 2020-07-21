<?php

namespace Model;


// hook model_account_use.php

class AccountModel extends \App\Model
{
	// hook model_account_public_start.php
	public $table = 'account';
	public $index = 'id';
	// hook model_account_public_end.php

	// hook model_account_start.php

	public function read_one($cond)
	{
		$data = $this->GetWithList([['table' => $this->Usr->table . ' b', 'join' => 'left', 'and' => 'b.openid = a.account']], $cond, [], 1, 1, '*', false);
		return !empty($data[0]) ? $data[0] : [];
	}

	public function find($cond, $page, $size, $select = '*')
	{
		$data = $this->GetWithList([['table' => $this->Usr->table . ' b', 'join' => 'left', 'and' => 'b.openid = a.account']], $cond, [], $page, $size, $select, false);
		return !empty($data) ? $data : [];
	}

	public function format($v, $time, $role, $agent_id_name)
	{
		$vv['idx'] = $v['usrid'];
		$vv['Name'] = $v['realname'];
		$vv['Redme'] = $v['note'];
		empty($vv['Name']) AND $vv['Name'] = "N/A";
		empty($vv['Redme']) AND $vv['Redme'] = "N/A";
		$vv['wechatID'] = $role['ls'] ? ($v['wechat'] ? $v['wechat'] : 'N/A') : '-';

		if ($role['dh']) {
			$vv['Account'] = $v['openid'];
			$vv['Tel'] = $v['phone'] ? $v['phone'] : 'N/A';
			if (in_array($role['RoleID'], [9, 10, 11])) {
				//$vv['Account'].=$v['agent_id']. xn_json_encode($role['child_list']);
				if (!in_array($v['agent_id'], $role['child_list'])) {
					$vv['Account'] = $v['usrid'];
					$vv['Tel'] = 'N/A';
				}
			}
		} else {
			$vv['Account'] = $v['usrid'];
			$vv['Tel'] = 'N/A';
		}
		if (in_array($role['RoleID'], [9, 10, 11])) {
			$vv['Name'] = '*' . mb_substr($vv['Name'], 1);
		}  //隐藏姓氏
		$vv['PokerPump'] = calculate($v['total_commission']);
		$vv['PokerWin'] = 0;
		$vv['BankAddress'] = $role['yh'] ? ($v['bankSon'] ? $v['bankSon'] : "N/A") : '-';
		$vv['BankCardID'] = $role['yh'] ? ($v['bankNo'] ? $v['bankNo'] : "N/A") : '-';
		$vv['DisOnlineDay'] = min(99999, intval(($time - $v['offline_time']) / 3600));
		$vv['Online'] = (int)$v['online'] > 0 ? 1 : 99999;
		$vv['Status'] = $v['disable'] ? 0 : 1;
		$vv['RegGive'] = $v['reg_give'];
		$vv['RegIP'] = $v['regip'];
		$vv['Agent'] = isset($agent_id_name[$v['agent_id']]) ? $agent_id_name[$v['agent_id']] : '-';
		$vv['MoneyNum'] = $vv['Balance'] = calculate(bcadd($v['chips'], $v['bankchips']));


		$total_win = $this->UsrMoney->sum('Game_Win', ['usrid' => $v['usrid']]);
		$total_uppoints = $this->UsrMoney->sum('Sg_Ad+Sc_Ad+Agent_Ad+Cash_C_Ad', ['usrid' => $v['usrid']]);
		$total_cash = $this->UsrMoney->sum('Cash_Ad+Sg_Sub+Cash_C_Ad', ['usrid' => $v['usrid']]);
		$v['total_win']!=$total_win && $this->Usr->update(['usrid'=>$v['usrid']],['total_win'=>-$total_win]);
		$v['total_uppoints'] != $total_uppoints && $this->Usr->update(['usrid' => $v['usrid']], ['total_uppoints' => $total_uppoints]);
		$v['total_cash'] != -$total_cash && $this->Usr->update(['usrid' => $v['usrid']], ['total_cash' => -$total_cash]);

		$vv['TotalWin'] = $vv['Win'] = calculate($total_win);
		$vv['TotalScore'] = $vv['ScoreNum'] = calculate(bcadd($total_uppoints, $total_cash));
		return $vv;
	}
    public function formatAgentUser($v, $time, $role, $agent_id_name)
    {
        $vv['idx'] = $v['usrid'];
        $vv['Name'] = $v['realname'];
        $vv['Redme'] = $v['note'];
        empty($vv['Name']) AND $vv['Name'] = "N/A";
        empty($vv['Redme']) AND $vv['Redme'] = "N/A";
        $vv['wechatID'] = $role['ls'] ? ($v['wechat'] ? $v['wechat'] : 'N/A') : '-';

        if ($role['dh']) {
            $vv['Account'] = $v['openid'];
            $vv['Tel'] = $v['phone'] ? $v['phone'] : 'N/A';
            if (in_array($role['RoleID'], [9, 10, 11])) {
                //$vv['Account'].=$v['agent_id']. xn_json_encode($role['child_list']);
                if (!in_array($v['agent_id'], $role['child_list'])) {
                    $vv['Account'] = $v['usrid'];
                    $vv['Tel'] = 'N/A';
                }
            }
        } else {
            $vv['Account'] = $v['usrid'];
            $vv['Tel'] = 'N/A';
        }
        if (in_array($role['RoleID'], [9, 10, 11])) {
            $vv['Name'] = '*' . mb_substr($vv['Name'], 1);
        }  //隐藏姓氏
        $vv['PokerPump'] = calculate($v['total_commission']);
        $vv['PokerWin'] = 0;
        $vv['BankAddress'] = $role['yh'] ? ($v['bankSon'] ? $v['bankSon'] : "N/A") : '-';
        $vv['BankCardID'] = $role['yh'] ? ($v['bankNo'] ? $v['bankNo'] : "N/A") : '-';
        $vv['DisOnlineDay'] = min(99999, intval(($time - $v['offline_time']) / 3600));
        $vv['Online'] = (int)$v['online'] > 0 ? 1 : 99999;
        $vv['Status'] = $v['disable'] ? 0 : 1;
        $vv['RegGive'] = $v['reg_give'];
        $vv['RegIP'] = $v['regip'];
        //$vv['Agent'] = isset($v['parent_id']) ? $v['parent_id']: '-';
        $vv['Agent'] = isset($agent_id_name[$v['agent_id']]) ? $agent_id_name[$v['agent_id']] : '-';
        $vv['MoneyNum'] = $vv['Balance'] = calculate(bcadd($v['chips'], $v['bankchips']));

        $userAgenInfo = $this->User->read(['guid'=>$v['usrid']]);
        //return $userAgenInfo;
        $vv['Reg_User'] = $userAgenInfo['Reg_User'];
        $vv['Get_Reward'] = calculate($userAgenInfo['Get_Reward']);
        $vv['Total_Reward'] = calculate($userAgenInfo['Total_Reward']);
        $vv['Cur_Reward'] = calculate($userAgenInfo['Cur_Reward']);


        $curUsermoneyInfo = $this->MoneyTransAgent->getCurrentUserAgentMoneyInfo($v['usrid'],time());
        $vv['Reg_User'] +=$curUsermoneyInfo['Reg_User'];
        $vv['Total_Reward'] +=calculate($curUsermoneyInfo['Total_Reward']);
        $vv['Cur_Reward'] +=calculate($curUsermoneyInfo['Cur_Reward']);

        $total_win = $this->UsrAgentMoney->sum('Game_Win', ['usrid' => $v['usrid']]);
        $total_uppoints = $this->UsrAgentMoney->sum('Sg_Ad+Sc_Ad+Agent_Ad+Cash_C_Ad', ['usrid' => $v['usrid']]);
        $total_cash = $this->UsrAgentMoney->sum('Cash_Ad+Sg_Sub+Cash_C_Ad', ['usrid' => $v['usrid']]);
        $v['total_win']!=$total_win && $this->Usr->update(['usrid'=>$v['usrid']],['total_win'=>-$total_win]);
        $v['total_uppoints'] != $total_uppoints && $this->Usr->update(['usrid' => $v['usrid']], ['total_uppoints' => $total_uppoints]);
        $v['total_cash'] != -$total_cash && $this->Usr->update(['usrid' => $v['usrid']], ['total_cash' => -$total_cash]);

        $total_bet = $this->UsrAgentMoney->sum('Game_Bet+Poker_Bet', ['usrid' => $v['usrid']]);
        $vv['TotalBet'] = calculate($total_bet);
        $vv['TotalWin'] = $vv['Win'] = calculate($total_win);
        $vv['TotalScore'] = $vv['ScoreNum'] = calculate(bcadd($total_uppoints, $total_cash));
        return $vv;
    }
	private function _getAccountLevel($winChips)
	{
		$lev = 0;
		if ($winChips >= 0) {
			return $lev;
		}
		$lev = 6;
		$levRange = [
			'1' => -30000,
			'2' => -50000,
			'3' => -100000,
			'4' => -150000,
			'5' => -200000,
		];

		foreach ($levRange as $k => $v) {
			if ($winChips <= $v) {
				$lev = $k;
				break;

			}
		}
		return $lev;
	}
	// hook model_account_end.php

}

?>
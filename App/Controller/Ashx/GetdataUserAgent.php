<?php

namespace Ashx;

use Ctrl\GameController;

Class GetdataUserAgent extends GameController
{

	public function AutoComplete()
	{

		return xn_json_encode([]);
	}

	public function adminReport_GetWinMaxPlayer()
	{
		// {"success":true,"r1":[{"Account":43716,"Agent":"CF6622","C_DateTime":"2019/9/1 13:13:58","idx":43716,"ScoreNum":-30194.00,"TotalBet":1383587.2,"TotalWin":-175303.45,"MoneyNum":0.249999998486601},{"Account":19468,"Agent":"222555","C_DateTime":"2019/5/7 20:30:56","idx":19468,"ScoreNum":-119812.09,"TotalBet":555040,"TotalWin":-129816,"MoneyNum":16.2609897708899},{"Account":47700,"Agent":"168899","C_DateTime":"2019/10/2 16:49:31","idx":47700,"ScoreNum":-84842.01,"TotalBet":217755.9,"TotalWin":-84842.6,"MoneyNum":0.589999999996508},{"Account":47012,"Agent":"120333","C_DateTime":"2019/9/27 15:37:50","idx":47012,"ScoreNum":-67858.00,"TotalBet":4098799.85,"TotalWin":-68236.0700000002,"MoneyNum":378.06999995633},{"Account":44003,"Agent":"890522","C_DateTime":"2019/9/2 14:04:30","idx":44003,"ScoreNum":-44888.49,"TotalBet":1477868.63,"TotalWin":-45097.3200000001,"MoneyNum":0.8800000141091},{"Account":27054,"Agent":"152535","C_DateTime":"2019/6/2 13:13:47","idx":27054,"ScoreNum":-10851.00,"TotalBet":1211064.4,"TotalWin":-40075.6,"MoneyNum":33.8000000000065},{"Account":39370,"Agent":"222109","C_DateTime":"2019/8/11 19:06:38","idx":39370,"ScoreNum":-42606.80,"TotalBet":421536.4,"TotalWin":-36930.3,"MoneyNum":0},{"Account":35967,"Agent":"060060","C_DateTime":"2019/7/30 1:20:09","idx":35967,"ScoreNum":-19506.47,"TotalBet":205174.55,"TotalWin":-31744.02,"MoneyNum":0.0100000018428545},{"Account":13822,"Agent":"302302","C_DateTime":"2019/4/20 14:11:37","idx":13822,"ScoreNum":-25388.00,"TotalBet":501190.2,"TotalWin":-30938.1,"MoneyNum":430.299999999399},{"Account":50139,"Agent":"168369","C_DateTime":"2019/10/19 17:49:01","idx":50139,"ScoreNum":-26614.56,"TotalBet":247654.8,"TotalWin":-26614.95,"MoneyNum":0.390000000268856},{"Account":29201,"Agent":"122223","C_DateTime":"2019/6/14 20:44:08","idx":29201,"ScoreNum":-15895.29,"TotalBet":448707.2,"TotalWin":-25366.7,"MoneyNum":0.509999999185311},{"Account":14593,"Agent":"060060","C_DateTime":"2019/4/21 20:48:14","idx":14593,"ScoreNum":-9526.26,"TotalBet":1097831.15,"TotalWin":-23091.2100457287,"MoneyNum":0.0102563959901275},{"Account":52033,"Agent":"774433","C_DateTime":"2019/11/3 2:17:12","idx":52033,"ScoreNum":-23068.50,"TotalBet":767443.87,"TotalWin":-23068.7300000001,"MoneyNum":0.230000001753069},{"Account":49055,"Agent":"888181","C_DateTime":"2019/10/11 9:11:27","idx":49055,"ScoreNum":-20195.09,"TotalBet":1047094.64,"TotalWin":-20204.67,"MoneyNum":0.579999999999814},{"Account":33714,"Agent":"774433","C_DateTime":"2019/7/15 14:46:38","idx":33714,"ScoreNum":-15690.59,"TotalBet":745857.09,"TotalWin":-19298.21,"MoneyNum":0.0300000000042928},{"Account":35728,"Agent":"555888","C_DateTime":"2019/7/29 12:34:27","idx":35728,"ScoreNum":-138.00,"TotalBet":489215.9,"TotalWin":-17662.5,"MoneyNum":0.610001764325716},{"Account":45912,"Agent":"157157","C_DateTime":"2019/9/18 15:04:02","idx":45912,"ScoreNum":-16212.00,"TotalBet":54827.8,"TotalWin":-16212.55,"MoneyNum":0.550000000006548},{"Account":25122,"Agent":"578889","C_DateTime":"2019/5/23 12:26:05","idx":25122,"ScoreNum":22075.83,"TotalBet":98262.96,"TotalWin":-14513.4,"MoneyNum":0.0900000000895034},{"Account":52416,"Agent":"438438","C_DateTime":"2019/11/6 13:35:08","idx":52416,"ScoreNum":-14058.00,"TotalBet":153049.7,"TotalWin":-14058,"MoneyNum":0},{"Account":52983,"Agent":"887779","C_DateTime":"2019/11/11 17:30:43","idx":52983,"ScoreNum":-13212.00,"TotalBet":69084,"TotalWin":-13212,"MoneyNum":0},{"Account":43666,"Agent":"201900","C_DateTime":"2019/9/1 0:54:23","idx":43666,"ScoreNum":-5452.09,"TotalBet":85228.44,"TotalWin":-12784.14,"MoneyNum":0.150000002914931},{"Account":47208,"Agent":"168888","C_DateTime":"2019/9/28 20:38:04","idx":47208,"ScoreNum":-13870.27,"TotalBet":100780.39,"TotalWin":-12573.2007048607,"MoneyNum":0.030154399872754},{"Account":40069,"Agent":"SZ8888","C_DateTime":"2019/8/17 7:17:50","idx":40069,"ScoreNum":-13670.16,"TotalBet":112707.29,"TotalWin":-12142.01,"MoneyNum":0},{"Account":40047,"Agent":"211314","C_DateTime":"2019/8/17 0:37:18","idx":40047,"ScoreNum":-21971.00,"TotalBet":114587.1,"TotalWin":-11941.6,"MoneyNum":0},{"Account":24617,"Agent":"711722","C_DateTime":"2019/5/21 6:46:29","idx":24617,"ScoreNum":245787.80,"TotalBet":2116283.7,"TotalWin":-11569.45,"MoneyNum":0.429999991235675},{"Account":47458,"Agent":"181900","C_DateTime":"2019/9/30 17:55:30","idx":47458,"ScoreNum":-11518.90,"TotalBet":205544.5,"TotalWin":-11186.070003624,"MoneyNum":0.0700000000948314},{"Account":53007,"Agent":"858858","C_DateTime":"2019/11/11 21:19:40","idx":53007,"ScoreNum":-11000.00,"TotalBet":87251.4,"TotalWin":-11021,"MoneyNum":21.0000000000182},{"Account":49420,"Agent":"113728","C_DateTime":"2019/10/14 14:37:38","idx":49420,"ScoreNum":-10992.01,"TotalBet":32764.01,"TotalWin":-10992.01,"MoneyNum":0},{"Account":46144,"Agent":"abc233","C_DateTime":"2019/9/20 12:45:00","idx":46144,"ScoreNum":-2704.77,"TotalBet":956152,"TotalWin":-10405.4,"MoneyNum":0.630000002759857},{"Account":34267,"Agent":"444488","C_DateTime":"2019/7/18 20:17:50","idx":34267,"ScoreNum":4054.38,"TotalBet":190434.1,"TotalWin":-10393,"MoneyNum":0.380000000000109},{"Account":51538,"Agent":"905678","C_DateTime":"2019/10/30 21:37:28","idx":51538,"ScoreNum":-10154.00,"TotalBet":86440.29,"TotalWin":-10154.06,"MoneyNum":0.0600000003387804},{"Account":51707,"Agent":"000121","C_DateTime":"2019/10/31 14:46:21","idx":51707,"ScoreNum":-9999.00,"TotalBet":50415.64,"TotalWin":-10000.6,"MoneyNum":1.60000000041782},{"Account":45012,"Agent":"N44444","C_DateTime":"2019/9/10 8:22:10","idx":45012,"ScoreNum":-7370.00,"TotalBet":90492.1,"TotalWin":-9500.6,"MoneyNum":0.630018597072649},{"Account":45069,"Agent":"YY8848","C_DateTime":"2019/9/10 16:41:21","idx":45069,"ScoreNum":5254.00,"TotalBet":6829,"TotalWin":-9357,"MoneyNum":0},{"Account":15722,"Agent":"127546","C_DateTime":"2019/4/25 12:40:34","idx":15722,"ScoreNum":18108.91,"TotalBet":19660.89,"TotalWin":-9351.91,"MoneyNum":0.0800006678966838},{"Account":25619,"Agent":"660077","C_DateTime":"2019/5/25 18:12:10","idx":25619,"ScoreNum":24443.00,"TotalBet":708963.2,"TotalWin":-9264.40000000001,"MoneyNum":0.39999999999182},{"Account":33784,"Agent":"201900","C_DateTime":"2019/7/15 23:10:06","idx":33784,"ScoreNum":-6136.49,"TotalBet":1814.29,"TotalWin":-8959.97,"MoneyNum":0},{"Account":39825,"Agent":"111438","C_DateTime":"2019/8/14 22:19:03","idx":39825,"ScoreNum":-11699.21,"TotalBet":35217.78,"TotalWin":-8654.43,"MoneyNum":0.080000000000081},{"Account":48064,"Agent":"138328","C_DateTime":"2019/10/4 20:59:36","idx":48064,"ScoreNum":-8528.00,"TotalBet":1048353.3,"TotalWin":-8528.02,"MoneyNum":0.0199999999415924},{"Account":26848,"Agent":"777001","C_DateTime":"2019/6/1 14:13:48","idx":26848,"ScoreNum":-6508.00,"TotalBet":55404,"TotalWin":-8523.8,"MoneyNum":0.700000000000728},{"Account":46437,"Agent":"663333","C_DateTime":"2019/9/23 0:46:50","idx":46437,"ScoreNum":-8300.00,"TotalBet":16427,"TotalWin":-8300,"MoneyNum":0},{"Account":38153,"Agent":"444488","C_DateTime":"2019/8/4 18:45:31","idx":38153,"ScoreNum":-689.94,"TotalBet":94888.8,"TotalWin":-8276.2,"MoneyNum":0.0100000000002183},{"Account":50434,"Agent":"201900","C_DateTime":"2019/10/21 15:26:03","idx":50434,"ScoreNum":-8200.00,"TotalBet":74924,"TotalWin":-8200.1,"MoneyNum":0.0999999999994923},{"Account":41433,"Agent":"550088","C_DateTime":"2019/8/25 10:45:05","idx":41433,"ScoreNum":-9852.16,"TotalBet":200828.8,"TotalWin":-8060.28,"MoneyNum":0.140000000070586},{"Account":16380,"Agent":"339481","C_DateTime":"2019/4/27 11:04:58","idx":16380,"ScoreNum":332970.40,"TotalBet":4748127.8,"TotalWin":-7931.90000000002,"MoneyNum":0},{"Account":19901,"Agent":"446688","C_DateTime":"2019/5/8 7:17:32","idx":19901,"ScoreNum":19051.51,"TotalBet":316727.49,"TotalWin":-7790.71,"MoneyNum":0.440000000002556},{"Account":17470,"Agent":"920326","C_DateTime":"2019/5/1 15:38:20","idx":17470,"ScoreNum":-8900.10,"TotalBet":273229.4,"TotalWin":-7742.4,"MoneyNum":0.740000000892451},{"Account":50122,"Agent":"219219","C_DateTime":"2019/10/19 16:09:36","idx":50122,"ScoreNum":-7400.00,"TotalBet":175450.18,"TotalWin":-7400.08,"MoneyNum":0.0799999999979709},{"Account":38964,"Agent":"994466","C_DateTime":"2019/8/9 0:19:29","idx":38964,"ScoreNum":-7629.49,"TotalBet":432415.97,"TotalWin":-7258.69000190735,"MoneyNum":10.3000019073784},{"Account":52931,"Agent":"366377","C_DateTime":"2019/11/11 1:43:57","idx":52931,"ScoreNum":-7212.00,"TotalBet":58093,"TotalWin":-7212.4,"MoneyNum":0.400000000001455}],"r2":[{"Account":48676,"Agent":"201900","C_DateTime":"2019/10/8 19:07:50","idx":48676,"ScoreNum":415731.76,"TotalBet":15178284.56,"TotalWin":415731.759763965,"MoneyNum":0},{"Account":32471,"Agent":"888556","C_DateTime":"2019/7/11 15:41:58","idx":32471,"ScoreNum":545497.75,"TotalBet":8575476.5,"TotalWin":295217.299187564,"MoneyNum":0.00127215499378508},{"Account":12170,"Agent":"201900","C_DateTime":"2019/4/19 19:47:34","idx":12170,"ScoreNum":731038.41,"TotalBet":7342227.37,"TotalWin":283541.76,"MoneyNum":0},{"Account":12917,"Agent":"345678","C_DateTime":"2019/4/19 23:02:16","idx":12917,"ScoreNum":220149.88,"TotalBet":3264323.44,"TotalWin":239313.68,"MoneyNum":44},{"Account":24805,"Agent":"kfc666","C_DateTime":"2019/5/21 22:21:22","idx":24805,"ScoreNum":251836.00,"TotalBet":8502929.39,"TotalWin":189884.859999999,"MoneyNum":2.53000001936061},{"Account":18960,"Agent":"833833","C_DateTime":"2019/5/6 0:40:51","idx":18960,"ScoreNum":538312.67,"TotalBet":4073669.49,"TotalWin":188565.45,"MoneyNum":6.01000000013119},{"Account":28217,"Agent":"000900","C_DateTime":"2019/6/9 18:04:28","idx":28217,"ScoreNum":258323.96,"TotalBet":3130034.01,"TotalWin":156492.83,"MoneyNum":0.0300000019128903},{"Account":47711,"Agent":"201900","C_DateTime":"2019/10/2 17:54:23","idx":47711,"ScoreNum":152205.50,"TotalBet":8849715.7,"TotalWin":152205.39965763,"MoneyNum":0.100342411043933},{"Account":14996,"Agent":"173173","C_DateTime":"2019/4/22 20:45:31","idx":14996,"ScoreNum":209922.29,"TotalBet":9480342.68,"TotalWin":149359.749643412,"MoneyNum":0.00246064209247976},{"Account":46065,"Agent":"555677","C_DateTime":"2019/9/19 21:22:55","idx":46065,"ScoreNum":114253.00,"TotalBet":3997311,"TotalWin":129776.511327982,"MoneyNum":0.165183534620155},{"Account":29685,"Agent":"201900","C_DateTime":"2019/6/18 8:34:19","idx":29685,"ScoreNum":254293.00,"TotalBet":4063977.51,"TotalWin":127673.45,"MoneyNum":0.580000000027212},{"Account":26671,"Agent":"110077","C_DateTime":"2019/5/31 13:01:44","idx":26671,"ScoreNum":157562.96,"TotalBet":6325446.29,"TotalWin":114105.92,"MoneyNum":0.0600000000000001},{"Account":47593,"Agent":"526526","C_DateTime":"2019/10/1 20:08:24","idx":47593,"ScoreNum":108740.00,"TotalBet":4855865.26,"TotalWin":108737.42,"MoneyNum":2.5799999999291},{"Account":48795,"Agent":"201900","C_DateTime":"2019/10/9 15:41:18","idx":48795,"ScoreNum":104103.98,"TotalBet":3931309.04,"TotalWin":104103.66,"MoneyNum":0.319999999997208},{"Account":40222,"Agent":"333113","C_DateTime":"2019/8/18 14:57:13","idx":40222,"ScoreNum":126830.55,"TotalBet":7258131.6,"TotalWin":102768.699999999,"MoneyNum":1.15999999992084},{"Account":27693,"Agent":"152535","C_DateTime":"2019/6/5 21:28:20","idx":27693,"ScoreNum":174659.80,"TotalBet":2562529.9,"TotalWin":101866.4,"MoneyNum":0},{"Account":26740,"Agent":"660077","C_DateTime":"2019/5/31 19:26:53","idx":26740,"ScoreNum":445564.00,"TotalBet":4987632.3,"TotalWin":98849.8998569481,"MoneyNum":14.6303825491123},{"Account":45553,"Agent":"201900","C_DateTime":"2019/9/15 12:11:15","idx":45553,"ScoreNum":111808.00,"TotalBet":1550277.4,"TotalWin":98579.9,"MoneyNum":0.19999999997367},{"Account":39931,"Agent":"333113","C_DateTime":"2019/8/16 1:31:11","idx":39931,"ScoreNum":91936.00,"TotalBet":4155156.91,"TotalWin":98487.47,"MoneyNum":0.780000007041963},{"Account":48681,"Agent":"152535","C_DateTime":"2019/10/8 19:37:55","idx":48681,"ScoreNum":95115.86,"TotalBet":3815450.21,"TotalWin":95115.7842509651,"MoneyNum":0.0757490431112774},{"Account":48049,"Agent":"516518","C_DateTime":"2019/10/4 19:02:32","idx":48049,"ScoreNum":93877.00,"TotalBet":2396677.4,"TotalWin":93876.9,"MoneyNum":0.0999999999976353},{"Account":46444,"Agent":"599888","C_DateTime":"2019/9/23 2:59:25","idx":46444,"ScoreNum":96900.00,"TotalBet":1778452,"TotalWin":87299.3999999996,"MoneyNum":0.600000000479969},{"Account":10097,"Agent":"201928","C_DateTime":"2019/2/8 21:32:40","idx":10097,"ScoreNum":-72638.42,"TotalBet":76000,"TotalWin":79150,"MoneyNum":0},{"Account":33964,"Agent":"339993","C_DateTime":"2019/7/16 22:34:46","idx":33964,"ScoreNum":150682.80,"TotalBet":1892170.38,"TotalWin":74774.5399999999,"MoneyNum":0.0779108884034375},{"Account":50866,"Agent":"561888","C_DateTime":"2019/10/24 9:04:40","idx":50866,"ScoreNum":73978.26,"TotalBet":2449188.8,"TotalWin":73977.0999970436,"MoneyNum":1.16000000003942},{"Account":12182,"Agent":"201900","C_DateTime":"2019/4/19 19:55:09","idx":12182,"ScoreNum":212897.44,"TotalBet":2423635.8,"TotalWin":68086.5,"MoneyNum":0.180000001201606},{"Account":43808,"Agent":"967967","C_DateTime":"2019/9/2 9:31:10","idx":43808,"ScoreNum":91134.87,"TotalBet":4396795.7,"TotalWin":66836.8999999998,"MoneyNum":1.26999997636644},{"Account":46676,"Agent":"336000","C_DateTime":"2019/9/24 22:40:53","idx":46676,"ScoreNum":76803.00,"TotalBet":2731611.4,"TotalWin":65352.84,"MoneyNum":0.250000004340959},{"Account":47672,"Agent":"066688","C_DateTime":"2019/10/2 13:30:12","idx":47672,"ScoreNum":63559.99,"TotalBet":927313,"TotalWin":63554.3,"MoneyNum":5.69000000000574},{"Account":29576,"Agent":"202088","C_DateTime":"2019/6/17 16:01:56","idx":29576,"ScoreNum":345250.48,"TotalBet":3152667.04,"TotalWin":63401.9499999995,"MoneyNum":0.679999984846205},{"Account":46819,"Agent":"181900","C_DateTime":"2019/9/25 23:38:14","idx":46819,"ScoreNum":70862.07,"TotalBet":3782549.18,"TotalWin":63072.38,"MoneyNum":0.0900000000913559},{"Account":28296,"Agent":"998822","C_DateTime":"2019/6/10 6:17:44","idx":28296,"ScoreNum":189169.95,"TotalBet":835451.1,"TotalWin":62138.9499904633,"MoneyNum":0},{"Account":50255,"Agent":"888717","C_DateTime":"2019/10/20 13:05:59","idx":50255,"ScoreNum":59691.08,"TotalBet":158551.1,"TotalWin":59691.08,"MoneyNum":0},{"Account":45058,"Agent":"339993","C_DateTime":"2019/9/10 15:31:52","idx":45058,"ScoreNum":48266.35,"TotalBet":1264415.89,"TotalWin":56855.5399055386,"MoneyNum":0.0500944614632317},{"Account":50785,"Agent":"152535","C_DateTime":"2019/10/23 16:49:58","idx":50785,"ScoreNum":56151.69,"TotalBet":1873828.46,"TotalWin":56151.6588079547,"MoneyNum":0.0311188087981918},{"Account":29363,"Agent":"905678","C_DateTime":"2019/6/16 1:48:53","idx":29363,"ScoreNum":219998.00,"TotalBet":2085192.1,"TotalWin":55108.9,"MoneyNum":0.29999999999518},{"Account":33554,"Agent":"774433","C_DateTime":"2019/7/14 15:14:27","idx":33554,"ScoreNum":87590.96,"TotalBet":2439683.16,"TotalWin":53821.0248672294,"MoneyNum":0.0351327474449135},{"Account":33233,"Agent":"115549","C_DateTime":"2019/7/12 20:57:17","idx":33233,"ScoreNum":96268.20,"TotalBet":1237360.3,"TotalWin":52835.3439295292,"MoneyNum":0.0905210776491003},{"Account":29732,"Agent":"000556","C_DateTime":"2019/6/18 17:01:23","idx":29732,"ScoreNum":150590.70,"TotalBet":745463.23,"TotalWin":51898.18,"MoneyNum":0},{"Account":32337,"Agent":"191666","C_DateTime":"2019/7/10 14:02:58","idx":32337,"ScoreNum":53342.80,"TotalBet":634386.24,"TotalWin":50842.88,"MoneyNum":0},{"Account":31072,"Agent":"W00001","C_DateTime":"2019/6/28 13:21:21","idx":31072,"ScoreNum":242009.52,"TotalBet":643492.39,"TotalWin":49119.1392639065,"MoneyNum":0.00233993364192386},{"Account":47729,"Agent":"201900","C_DateTime":"2019/10/2 20:21:17","idx":47729,"ScoreNum":46533.58,"TotalBet":4002262.39,"TotalWin":46532.83,"MoneyNum":0.74999997835593},{"Account":49688,"Agent":"008811","C_DateTime":"2019/10/16 21:13:33","idx":49688,"ScoreNum":45815.98,"TotalBet":630347.62,"TotalWin":45815.55,"MoneyNum":0.429999996693368},{"Account":48305,"Agent":"858777","C_DateTime":"2019/10/5 22:37:45","idx":48305,"ScoreNum":45590.00,"TotalBet":1237789,"TotalWin":45589.8,"MoneyNum":0.200000000005076},{"Account":13329,"Agent":"666888","C_DateTime":"2019/4/20 1:46:47","idx":13329,"ScoreNum":102195.61,"TotalBet":816231,"TotalWin":45513.35,"MoneyNum":40.0700000005836},{"Account":48607,"Agent":"343536","C_DateTime":"2019/10/8 3:10:20","idx":48607,"ScoreNum":45455.87,"TotalBet":618087.7,"TotalWin":45455.45,"MoneyNum":0.419999999570791},{"Account":31152,"Agent":"100869","C_DateTime":"2019/6/28 23:26:42","idx":31152,"ScoreNum":254317.47,"TotalBet":1578959.22,"TotalWin":43471.48,"MoneyNum":0},{"Account":47563,"Agent":"333379","C_DateTime":"2019/10/1 15:41:23","idx":47563,"ScoreNum":43207.75,"TotalBet":1297089.86,"TotalWin":43207.28,"MoneyNum":0.469999999073924},{"Account":33196,"Agent":"585888","C_DateTime":"2019/7/12 17:56:51","idx":33196,"ScoreNum":82647.97,"TotalBet":1582774.94,"TotalWin":42400.04,"MoneyNum":0.0700000054150678},{"Account":46827,"Agent":"777188","C_DateTime":"2019/9/26 0:53:17","idx":46827,"ScoreNum":47348.74,"TotalBet":809944.81,"TotalWin":41134.3290664482,"MoneyNum":0.061528790955272}]}

		$sDate = $this->request->param('sDate', '');
		$eDate = $this->request->param('eDate', '');
		$cond = [];
		$player = $this->Usr->select($cond, [], 'usrid,openid,agent_id,chips,bankchips,total_uppoints,total_cash');
		$cond = ['times' => ['>=' => $sDate . ' 00:00:00', '<=' => $eDate . ' 23:59:59']];
		$data = $this->MoneyTransAgent->PlayerMaxWin($cond, $player);
		$this->response('0000', $data);
	}

	public function adminReport_report_onlinePlayer()
	{
		$sDate = $this->request->param('sDate', '');
		$eDate = $this->request->param('eDate', '');
		$data = ['r1' => [], 'r2' => [], 'r3' => [], 'r4' => []];
		$dates = $this->MoneyTransAgent->getDatesBetweenTwoDays($sDate, $eDate);
		$r1 = $this->Usr->RegDate($sDate, $eDate);
		$r2 = $this->PlayerOline->LoginDate($dates);
		$r3 = $this->MoneyTransAgent->ChipsDate($sDate, $eDate);
		$dates = array_reverse($dates);
		foreach ($dates as $v) {
			$data['r1'][] = [
				'mydate' => $v,
				'TotalNum' => isset($r1['TotalNum'][$v]) ? $r1['TotalNum'][$v] : 0,
			];
			$data['r2'][] = [
				'mydate' => $v,
				'TotalNum' => $r2[$v]['game'],
			];
			$data['r3'][] = [
				'mydate' => $v . ' 00:00:00',
				'TotalNum' => $r2[$v]['home'],
			];
			if ($this->roleInfo['czs']) {
				$data['r4'][] = [
					'mydate' => $v,
					'TotalNum' => isset($r3['TotalNum'][$v]) ? $r3['TotalNum'][$v] : 0,
				];
			}
		}

		$this->response('0000', $data);
	}

    public function GetOnlineNum_onLinePlayerList()
    {

        $page = $this->request->param('pageIndex', 1);
        $size = $this->request->param('pageSize', 20);
        $gameid = $this->request->param('GameID', 0);
//        $usertype = $this->request->param('usertype', 0);
//        $sorttype = $this->request->param('sorttype', 3);
        $child_list = [];
        $this->agent_child_list($this->default_user['id'], $child_list);
        $agent_id = arrlist_values($child_list, 'id');
        $agent_id[] = $this->default_user['id'];
        $cond['online'] = ['>' => 0];
        $data['total'] = $this->Usr->count($cond);
        !empty($gameid) && $cond['roomid']=$gameid;
        $list['results'] = $this->Usr->select($cond, [],'*', $page, $size);
        $start = ($page - 1) * $size;
        $time = time();
        $_list = [];
        $money = arrlist_key_values($this->agent_array, 'id', 'balance');
        $LatestLoginIP = $this->UserStat->CacheGet('LatestLoginIPKey_');
        //风控相关
        $OnlineUserMoneyInfo =   $this->UserStat->CacheGet('OnlineUserMoneyInfo_');

        foreach ($list['results'] as $k => $v) {
            $_list[$k] = $this->Account->formatAgentUser($v, $time, $this->roleInfo, $this->agent_id_guid);
            $_list[$k]['Rownum'] = $start + $k + 1;
            //$_list[$k]['GameID'] = $v['gameid'];

            $this->isadmin && $_list[$k]['AgentMoney'] = calculate($money[$v['agent_id']]);
            $_list[$k]['IP'] = $this->isadmin ? $v['last_login_ip'] : '-';
            $_list[$k]['less_money'] = '-';
            $_list[$k]['roomid'] = $v['roomid'];
            $_list[$k]['GameName'] = $this->Game->getNameById($v['roomid']);
            //判断是否在多人登录的IP种特殊标识
            $warring_user = 0;
            foreach ($LatestLoginIP as $item)
            {
                if($v['last_login_ip'] == $item['last_login_ip'])
                {
                    $warring_user = 1;
                }
            }
            $_list[$k]['warring_user'] = $warring_user;
            $_list[$k]['fk'] = 0;
            $_list[$k]['fk_val'] = $OnlineUserMoneyInfo['_'.$v['usrid']]['val'];
            if(isset($OnlineUserMoneyInfo['_'.$v['usrid']]))
            {
                if($OnlineUserMoneyInfo['_'.$v['usrid']]['val']>5)
                {
                    $_list[$k]['fk'] = 1;

                }
            }
            $_list[$k]['usrid'] =$v['usrid'];

        }
        $data['results'] = $_list;
        $this->response('0000', $data);
    }




	public function GetOnlineNum_GetOnlineNumByAgent()
	{

		$this->agent_child_list($this->default_user['id'], $child_list);
		$agent_id = arrlist_values($child_list, 'id');
		$agent_id[] = $this->default_user['id'];
		$cond['online'] = ['>' => 0];
		$data['Num'] = $this->Usr->count($cond);
		$this->response('0000', $data);
	}


	public function GetPlatFormReport_plat_common_1()
	{
		$agent = $this->default_user;
		$this->hspower($agent['id']);
		$child_list = [];
		$this->agent_child_list($agent['id'], $child_list);
		$kuozhan = $count = 0;

		$usernum = $this->Usr->count(['ai' => 0]);
		$data['results'] = [["TotalAgent" => $count, "TotalPlayer" => $usernum, "TotalPromo" => $kuozhan]];
		$this->response('0000', $data);
	}

	public function GetPlatFormReport_plat_common_3()
	{
		$data = $this->rankData("day");
		$this->response('0000', $data);
	}

	public function GetPlatFormReport_plat_common_4()
	{
		$data = $this->rankData("month");
		$this->response('0000', $data);
	}

	public function GetPlatFormReport_plat_common_5()
	{
		$data = $this->rankData("day", true);
		$this->PostTask('MoneyTransAgent_minute',[]);
		$this->response('0000', $data);
	}

	public function GetPlatFormReport_plat_common_6()
	{
		$data = $this->rankData("month", true);
		$this->response('0000', $data);
	}

	private function rankData($dateType = "day", $isQipai = false)
	{
		if ($isQipai == true) {
			$data['results'] = [["TotalBet" => 0, "TotalWin" => 0]];
			return $data;
		}
		//获取今天的日期
		$dateDayStart = date("Y-m-d 00:00:00");
		$dateDayEnd = date("Y-m-d 23:59:59");

		//获取当月的日期

		$BeginDate = date('Y-m-01 00:00:00');
		$dateMonthEnd = date('Y-m-t 23:59:59');


		$data['ViewTYPE'] = 1;

		if ($dateType == 'day') {
			$userTotalInfo = $this->MoneyTransAgent->stat_sum_new($dateDayStart,$dateDayEnd,$this->default_user['id']);
		} else if ($dateType == 'month') {
			$userTotalInfo = $this->MoneyTransAgent->stat_sum_new($BeginDate,$dateMonthEnd,$this->default_user['id']);
		}

		if (!$isQipai) {
			$TotalBet = $userTotalInfo['Game_Bet'];
			$TotalWin = $userTotalInfo['Game_Win'];
		} else {
			$TotalBet = $userTotalInfo['Poker_Bet'];
			$TotalWin = $userTotalInfo['Poker_Pump'];
		}
		$data['results'] = [["TotalBet" => $TotalBet, "TotalWin" => $TotalWin]];
		return $data;
	}


	public function AccountList_playerList()
	{
		$username = $this->request->param('userName', '');
		$pageIndex = $this->request->param('pageIndex', 1);
		$pageSize =1000 ;// $this->request->param('pageSize', 5000000);
		$user = $this->User->read_by_username($username);
		$this->hspower($user['id']);
		$cond = [];
		//!empty($user['id']) AND $cond = ['b.agent_id' => $user['id']];

		$result = $this->Account->GetWithList([['table' => $this->Usr->table . ' b', 'join' => 'left', 'and' => 'b.openid = a.account']], $cond, ['b.offline_time' => -1], $pageIndex, $pageSize);
		$data = ['total' => $result['total'], 'results' => []];
		$start = ($pageIndex - 1) * $pageSize;
		$ol = time();

		foreach ($result['results'] as $k => $v) {
			$vv = $this->Account->format($v, $ol, $this->roleInfo, $this->agent_id_name);
			$vv['Rownum'] = $start + $k + 1;

			$data['results'][] = $vv;
		}
		$data['agent']['reg_set'] = $user['reg_set'];
		$this->response('0000', $data);
	}


    public function AccountList_agentplayerList()
    {
        $username = $this->request->param('userName', '-1');
        $pageIndex = $this->request->param('pageIndex', 1);
        $pageSize = $this->request->param('pageSize', 25);
        $this->hspower($this->token);
        $cond['a.RoleID'] = 111;
        if(!empty($username))
        {
            $user = $this->Usr->read(['openid'=>$username]);
            if(!empty($user['usrid']))
            {
                $userAgentInfo =   $this->User->read(['guid'=>$user['usrid']]);
                if($userAgentInfo)
                {
                    $cond['a.parent_id'] = $userAgentInfo['id'];
                }
            }
            if(!isset($user['usrid']))
            {
                unset($cond['a.parent_id']);
            }
        }



        $result = $this->User->GetWithList([['table' => $this->Usr->table . ' b', 'join' => 'left', 'and' => 'b.usrid = a.guid']], $cond, ['b.offline_time' => -1], $pageIndex, $pageSize);
        $data = ['total' => $result['total'], 'results' => []];

        $start = ($pageIndex - 1) * $pageSize;
        $ol = time();

        foreach ($result['results'] as $k => $v) {
            $vv = $this->Account->formatAgentUser($v, $ol, $this->roleInfo, $this->agent_id_name);
            $vv['RegCount'] = $v['Reg_User'];
            $vv['GetMoney'] = calculate($v['Get_Reward']);
            $vv['TotalMoney'] = calculate($v['Total_Reward']);
            $vv['LeftMoney'] = calculate($v['Cur_Reward']);
            $vv['Rownum'] = $start + $k + 1;
            $data['results'][] = $vv;
        }

        if($user)
        {
            $userAgentInfo = $this->User->read(['guid'=>$user['usrid']]);
            if($userAgentInfo&&$userAgentInfo['RoleID'] ==111)
            {
                $condCur['a.RoleID'] = 111;
                $condCur['a.guid'] = $user['usrid'];
                $userResults = $this->User->GetWithList([['table' => $this->Usr->table . ' b', 'join' => 'left', 'and' => 'b.usrid = a.guid']], $condCur, ['b.offline_time' => -1], $pageIndex, $pageSize);
                $vv = $this->Account->formatAgentUser($userResults['results'][0], $ol, $this->roleInfo, $this->agent_id_name);
                $vv['RegCount'] = $vv['Reg_User'];
                $vv['Rownum'] = '-';
                $vv['GetMoney'] = calculate($vv['Get_Reward']);
                $vv['TotalMoney'] = calculate($vv['Total_Reward']);
                $vv['LeftMoney'] = calculate($vv['Cur_Reward']);
                array_unshift($data['results'],$vv);
                $data['total']+=1;
            }
        }

        $data['agent']['reg_set'] = $user['reg_set'];
        $this->response('0000', $data);
    }

	public function AccountList_agentList()
	{
		$username = $this->request->param('userName');
		$pageIndex = $this->request->param('pageIndex', 1);
		$pageSize = $this->request->param('pageSize', 25);
		$user = $this->User->read_by_username($username);
		$this->hspower($user['id']);
		$where['is_deleted'] = 0;
		$where['parent_id'] = $user['id'];
		if ($user['RoleID'] == 10) {
			$agent_list = $this->User->GetList($where, ['username' => 1], $pageIndex, $pageSize);
		} else {
			$agent_list = $this->User->GetList($where, ['id' => 1], $pageIndex, $pageSize);
		}

		$data['total'] = $agent_list['total'];
		$start = ($pageIndex - 1) * $pageSize;
		foreach ($agent_list['results'] as $k => $v) {
			$vv = $this->User->format($v, $this->roleInfo);
			$vv['Rownum'] = $start + $k + 1;
			$data['results'][] = $vv;
		}
		$this->response('0000', $data);
	}

	public function AccountList_getSubAccountList()
	{
		$results = $this->User->select(['RoleID' => $this->admin_role_arr], ["id" => -1]);
		$role_arr = arrlist_key_values($this->rolearray, 'RoleID', 'RoleName');
		$data['results'] = [];
		foreach ($results as $v) {
			if ($v['RoleID'] < 3) {
				continue;
			}
			$v['RoleName'] = $role_arr[$v['RoleID']] ? $role_arr[$v['RoleID']] : '';
			$v['UserName'] = $v['username'];
			$v['IsEnable'] = $v['status'];
			$v['RegTime'] = date('Y-m-d H:i:s', $v['register_time']);
			$data['results'][] = $v;
		}
		$this->response('0000', $data);
	}


	public function AccountList_getSubAccountList2()
	{
		$results = $this->User->GetList(['RoleID' => $this->admin_role_arr], ["id" => -1]);
		$role_arr = arrlist_key_values($this->rolearray, 'RoleID', 'RoleName');
		$data['results'] = [];
		$data['TotalAdd'] = 0.00;
		$data['TotalReduce'] = 0.00;
		foreach ($results['results'] as $v) {
			$v['RoleName'] = $role_arr[$v['RoleID']] ? $role_arr[$v['RoleID']] : '';
			$v['UserName'] = $v['username'];
			$v['IsEnable'] = $v['status'];
			$v['RegTime'] = date('Y-m-d H:i:s', $v['register_time']);
			$data['results'][] = $v;
		}
		$this->response('0000', $data);
	}


//
//    public function AdminReport()
//    {
//        $data = [];
//        $action = $this->request->param("action");
//        switch ($action) {
//            case "report_onlinePlayer":
//                $startDate = $this->request->param("sDate", "");
//                $endDate = $this->request->param("eDate", "");
//                //获取开始结束日期
//                $dates = $this->transDate($startDate, $endDate);
//                $dataList = $this->RankPlatform->select(["ymd" => $dates["where"]]);
//                if (empty($dataList)) {
//                    $data["r1"] = $data["r2"] = $data["r3"] = [];
//                }
//                foreach ($dataList as $index => $datum) {
//                    //新增玩家
//                    $data["r1"][] = ["mydate" => $datum["ymd"], "TotalNum" => empty($datum["player_add_num"]) ? "-" : $datum["player_add_num"]];
//                    //登录游戏
//                    $data["r2"][] = ["mydate" => $datum["ymd"], "TotalNum" => empty($datum["player_play_game_num"]) ? "-" : $datum["player_play_game_num"]];
//                    //登录大厅
//                    $data["r3"][] = ["mydate" => $datum["ymd"], "TotalNum" => empty($datum["player_login_num"]) ? "-" : $datum["player_login_num"]];
//                }
//                break;
//            default:
//                break;
//        }
//
//        $this->response('0000', $data);
//    }

	/**
	 * 获取管理员IP白名单列表
	 */
	public function AccountList_getAdminIpList()
	{
		$results = $this->AdminIpWhiteList->select();
		foreach ($results as &$row){
			$row['username']=isset($this->agent_id_name[$row['adminId']])?$this->agent_id_name[$row['adminId']]:'';
		}
		$data['results'] = $results;
		$this->response('0000', $data);
	}

	public function GetPlatFormReport_userIpAreaNow()
	{

	}

	public function GetPlatFormReport_userIpArea()
	{
		$this->needadmin(1);
		$ipList = $this->Logintable->select([], [], 'IP,ipregion,count(usrid) as playerCount', 0, 0, '', 'group by ipregion');
		$area = arrlist_values($ipList,'ipregion');
		$num = count($area);
		for ($i = 0; $i < $num; $i++) {
			$count = 0;
			foreach ($ipList as $item) {
				if ($item['ipregion'] == $area[$i]) {
					$count = $item['playerCount'];
				}
			}
			if ($count > 0) {
				$data[] = [
					'name' => $area[$i],
					'value' => $count,
				];
			}

		}
		$this->response('0000', ['area' => $area, 'data' => $data]);
	}


}

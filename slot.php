<?php
/* 
	Timothy S. Murphy
	Alphabet Slots v0.1
	Based on the classic 3-reel 3-line slot machines.
	Theoretical return is 96.5%
	
	PAYTABLE____
	Payline           Pays
	Three Z's  (333)  500
	Three Y's  (666)  100
	Three D's  (444)  50
	Three C's  (222)  20
	Three B's  (111)  15
	Three A's  (000)  10
	A-A-any    (00-)  5
	A-any-any  (0--)  2
	
	REELS____
	# Symbol    Reel 1   Reel 2   Reel 3
	0 A         5        2        3
	1 B         4        4        4
	2 C         3        4        4
	3 Z         1        1        1
	4 D         3        3        1
	5 X         3        5        6
	6 Y         1        1        1
	  Total     20       20       20


*/

$config_balance = isset($_GET['player_balance']) ? (intval($_GET['player_balance'])) : 100;
$config_play_lines = isset($_GET['player_spins']) ? (intval($_GET['player_spins'])) : 3;
$config_winnings = 0;
$paytable_marker = Array();

if(!($config_play_lines == 1 || $config_play_lines == 3)) {
	echo "error, only 1 or 3 lines can be played";
	die();
}

if($config_play_lines > $config_balance) {
	echo "you are broke, <a href='slot.php'>restart?</a>";
	die();
}
 
//"spin" the reels
$reel[0] = array(0,0,0,0,0,1,1,1,1,2,2,2,3,4,4,4,5,5,5,6);
$reel[1] = array(0,0,1,1,1,1,2,2,2,2,3,4,4,4,5,5,5,5,5,6);
$reel[2] = array(0,0,0,1,1,1,1,2,2,2,2,3,4,5,5,5,5,5,5,6);
shuffle($reel[0]);
shuffle($reel[1]);
shuffle($reel[2]);

//take credits
$config_balance -= $config_play_lines;

//determine outcome
for($i = 0; $i < 3; $i++) {
	$playline[$i] = array($reel[0][$i], $reel[1][$i], $reel[2][$i]);
	
	$playline_rowbg[$i] = "#eeeeee"; //unplayed lines are grey
	if(($config_play_lines == 1 && $i == 1) || $config_play_lines == 3) {
		$playline_payout[$i] = getPayout($playline[$i]);
		$playline_payout_display[$i] = "";
		$playline_rowbg[$i] = "#ffffff";
		if($playline_payout[$i] > 0) {
			$config_winnings += $playline_payout[$i];
			$playline_payout_display[$i] = "+".$playline_payout[$i];
			$paytable_marker[] = $playline_payout[$i];
			$playline_rowbg[$i] = "#ccffcc"; //winning line green
			if($playline_payout[$i] >= 10) {
				$playline_rowbg[$i] = "#99ff99"; //winning line 10+ extra green
			}
		}
	}
	
}

//pay winnings
$config_balance += $config_winnings;


function getSymbol($num) {
	$returnSym = 'X'; //default no payout value
	switch($num) {
		case 0:
			$returnSym = 'A';
			break;
		case 1:
			$returnSym = 'B';
			break;
		case 2: 
			$returnSym = 'C';
			break;
		case 3:
			$returnSym = 'Z';
			break;
		case 4:
			$returnSym = 'D';
			break;
		case 5:
			$returnSym = 'X';
			break;
		case 6:
			$returnSym = 'Y';
			break;
		default:
			echo "error, reel value out of range.";
			die();
	}
	
	return $returnSym;
}

function getPayout($pl) {
	/*
	PAYTABLE____
	Payline           Pays
	Three Z's  (333)  500
	Three Y's  (666)  100
	Three D's  (444)  50
	Three C's  (222)  20
	Three B's  (111)  15
	Three A's  (000)  10
	A-A-any    (00-)  5
	A-any-any  (0--)  2
	*/
	$returnPay = 0;
	if($pl[0] == 3 && $pl[1] == 3 && $pl[2] == 3) {
		$returnPay = 500;
	} else if($pl[0] == 6 && $pl[1] == 6 && $pl[2] == 6) {
		$returnPay = 100;
	} else if($pl[0] == 4 && $pl[1] == 4 && $pl[2] == 4) {
		$returnPay = 50;
	} else if($pl[0] == 2 && $pl[1] == 2 && $pl[2] == 2) {
		$returnPay = 20;
	} else if($pl[0] == 1 && $pl[1] == 1 && $pl[2] == 1) {
		$returnPay = 15;
	} else if($pl[0] == 0 && $pl[1] == 0 && $pl[2] == 0) {
		$returnPay = 10;
	} else if($pl[0] == 0 && $pl[1] == 0) {
		$returnPay = 5;
	} else if($pl[0] == 0) {
		$returnPay = 2;
	}
	
	return $returnPay;
}


//this function checks if the pay table amount has been payed to the player,
//and will draw and arrow next to the corresponding pay table row to show 
//player how they won.
function markPaytable($paytableamt, $ptm) {
	if(array_search($paytableamt, $ptm) !== FALSE) {
		return '<span style="background-color:#ccffcc;">&nbsp;&laquo;&nbsp;'.$paytableamt.'&nbsp;&raquo;&nbsp;</span>';
	} else {
		return $paytableamt;
	}
}

?>
<html>
<head>
	<title>Alphabet Slots</title>
</head>
<body>
	<h2>Alphabet Slots</h2>
	
	<h3>Bet</h3>
	<form method="get" action="slot.php">
		<input type="hidden" name="nocache" value="<? echo mt_rand(1, 99999); ?>">
		Balance: <input type="text" name="player_balance" value="<? echo $config_balance; ?>"><br>
		<button type="submit" name="player_spins" value="1">SPIN - 1 Line</button>
		<button type="submit" name="player_spins" value="3">SPIN - 3 Lines</button>
	</form>
	
	<h3>Spin Outcome</h3>
	<table cellpadding="10" cellspacing="4" border="0">
		<?
		for($i = 0; $i < 3; $i++) {
		?>
			<tr bgcolor="<? echo $playline_rowbg[$i]; ?>">
				<td><? echo getSymbol($playline[$i][0]); ?></td>
				<td><? echo getSymbol($playline[$i][1]); ?></td>
				<td><? echo getSymbol($playline[$i][2]); ?></td>
				<td><? echo $playline_payout_display[$i]; ?></td>
			</tr>
		<?
		}
		?>
	</table>
	
	<h3>Pay Table</h3>
	<table cellpadding="5" cellspacing="2" border="0">
		<tr><td>Payline</td><td>Pays</td></tr>
		<tr><td>Three Z's</td><td><? echo markPaytable(500, $paytable_marker); ?></td></tr>
		<tr><td>Three Y's</td><td><? echo markPaytable(100, $paytable_marker); ?></td></tr>
		<tr><td>Three D's</td><td><? echo markPaytable(50, $paytable_marker); ?></td></tr>
		<tr><td>Three C's</td><td><? echo markPaytable(20, $paytable_marker); ?></td></tr>
		<tr><td>Three B's</td><td><? echo markPaytable(15, $paytable_marker); ?></td></tr>
		<tr><td>Three A's</td><td><? echo markPaytable(10, $paytable_marker); ?></td></tr>
		<tr><td>A-A-any</td><td><? echo markPaytable(5, $paytable_marker); ?></td></tr>
		<tr><td>A-any-any</td><td><? echo markPaytable(2, $paytable_marker); ?></td></tr>
	</table>
</body>
</html>
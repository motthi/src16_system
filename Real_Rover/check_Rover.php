<?php
/**
 * Created by PhpStorm.
 * User: k1100
 * Date: 2019/10/13
 * Time: 0:54
 */

//$score_tagは競技に合わせて変更する
$score_tag = array(
    "goal5m" => "ゴールゾーン到達(5m)",
    "goal4m" => "ゴールゾーン到達(4m)",
    "goal3m" => "ゴールゾーン到達(3m)",
    "gps_goal" => "GPS使用しゴールゾーン内で停止",
    "ball" => "ボールゾーン到達",
    "ball_stop" => "ボールゾーン内で停止",
    "rock" => "岩除け",
    "bamp" => "バンプ走破",
    "complete" => "完全制覇",
    "time" => "タイムボーナス"
);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="check_Rover.css">
    <title>Real Rover Check</title>
</head>
<body>

<h1>SRC16 Real Rover<br>得点確認画面</h1>
<hr>

<h2>
    <?php
    echo "チーム名:".$_POST["team"]." ";
    if (!empty($_POST["Match"])){
        $_SESSION["Match"] = $_POST["Match"];
    }
    if ($_SESSION["Match"] == 0){
        echo "1次予選";
    }
    else if($_SESSION["Match"] == 1){
        echo "2次予選";
    }
    else if($_SESSION["Match"] == 2){
        echo "決勝";
    }
    echo "第".$_POST["try"]."走行";
    ?>
</h2>

<!-- 結果表示 -->
<table border="1" style="border-collapse: collapse">
    <tr><th>項目</th><th>得点</th></tr>
    <?php
    foreach ($_POST as $key => $val) {
        if ($key != "total" && $key != "regist" && $key != "team" && $key != "try" && $key != "Match") {
            echo "<tr><th>" . $key . "</th><th>" . $val . "</th></tr>";
            $score_total += $val;
        }
    }
    if ($score_total == $_POST["total"]){
        echo "<tr><th>合計</th><th>".$score_total."</th></tr>";
    }
    ?>
</table>

<!-- 登録ボタン -->
<p>以上で登録します。よろしいですか？</p>
<form method="POST" action="regist_Rover.php">
    <input type="hidden" value="<?php echo $_POST["team"]?>" name="team">
    <input type="hidden" value="<?php echo $_POST["try"]?>" name="try">
    <?php
    //print_r($_POST);
    foreach ($_POST as $key => $val){
        $tag = array_search($key, $score_tag);
        echo "<input type='hidden' value=".$val." name=".$tag.">";
    }
    ?>
    <input type="hidden" value="<?php echo $score_total?>" name="total">
    <input type="hidden" value="<?php echo $_SESSION["Match"]?>" name="Match">
    <input type="submit" value="登録したい" name="yes" style="width:100px;height:60px;font:10pt MSゴシック;font-weight: bold">
</form>
<br>

<!-- スコアシート画面へ戻る -->
<form method="POST" action="scoresheet_Rover.php">
    <input type="hidden" value="<?php echo $_SESSION["Match"]?>" name="Match">
    <input type="submit" value="登録したくない" name="no">
</form>
</body>
</html>

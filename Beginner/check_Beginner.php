<?php
/**
 * Created by PhpStorm.
 * User: k1100
 * Date: 2019/10/13
 * Time: 0:54
 */

//$score_tagは競技に合わせて変更する
$score_tag = array(
    'relay' => "中継ポイントで1秒停止",
    'obs1' => "障害物1を回避",
    'obs2' => "障害物2を回避",
    'complete' => "ゴールラインで完全停止",
    'relay_time' => "中継タイム",
    'complete_time' => "完全停止タイム"
);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beginner Check</title>
</head>
<body>

<h1>SRC16 Beginner<br>得点確認画面</h1>
<hr>

<p>
    <?php
    echo "チーム名:".$_POST["team"]." ";
    if (!empty($_POST["Match"])){
        $_SESSION["Match"] = $_POST["Match"];
    }
    if ($_SESSION["Match"] == 0){
        echo "予選";
    }
    else if($_SESSION["Match"] == 1){
        echo "準決勝";
    }
    else if($_SESSION["Match"] == 2){
        echo "3位決定戦";
    }
    else if($_SESSION["Match"] == 3){
        echo "決勝";
    }
    echo "第".$_POST["try"]."走行";
    ?>
</p>

<table border="1" width="100%" style="border-collapse: collapse"style="border-collapse: collapse">
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

<p>以上で登録します。よろしいですか？</p>
<form method="POST" action="regist_Beginner.php">
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
    <input type="submit" value="登録する" name="yes">
</form>
<form method="POST" action="scoresheet_Beginner.php">
    <input type="hidden" value="<?php echo $_SESSION["Match"]?>" name="Match">
    <input type="submit" value="戻る" name="no">
</form>
</body>
</html>

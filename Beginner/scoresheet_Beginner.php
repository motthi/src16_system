<?php
/**
 * Created by PhpStorm.
 * User: Masa
 * Date: 2019/10/10
 * Time: 17:52
 *
 * http://second.adg7.com/startup05.html
 * https://qiita.com/sudnonk12/items/a0d58cc0f6ff1c6e2765
 * https://qiita.com/yuiqiiii/items/faa4d2e6fe2681dffd1a
 */

session_start();

// $elementは競技に合わせて変更する
//  得点項目のプログラム表記（キー）と日本語表記、得点要素数、得点を記入する
//  例えば、ある得点項目に対して、条件により2種類の得点が存在し得る場合は、
//  得点要素数は"2"になる
$element = [
    'relay'=>["中継ポイントで1秒停止", 1, 500],
    'obs1' =>["障害物1を回避", 1, 500],
    'obs2' =>["障害物2を回避", 1, 500],
    'complete'=>["ゴールラインで完全停止", 3, 500, 1000, 1500],
    'relay_time'=>["中継タイム", 6, 50, 100, 150, 200, 250, 300],
    'complete_time'=>["完全停止タイム", 6, 100, 200, 300, 400, 500, 600]
];

//中継タイム得点の設定
for($i = 0 ; $i <= 10 ; $i++){
    $relay_time_score[$i] = 300;
}
for($i = 11 ; $i <= 12 ; $i++){
    $relay_time_score[$i] = 250;
}
for($i = 13 ; $i <= 15 ; $i++){
    $relay_time_score[$i] = 200;
}
for($i = 16 ; $i <= 20 ; $i++){
    $relay_time_score[$i] = 150;
}
for($i = 21 ; $i <= 25 ; $i++){
    $relay_time_score[$i] = 100;
}
for($i = 26 ; $i <= 30 ; $i++){
    $relay_time_score[$i] = 50;
}
for($i = 31 ; $i <= 60 ; $i++){
    $relay_time_score[$i] = 0;
}

//完全停止タイム得点の設定
for($i = 0 ; $i <= 20 ; $i++){
    $complete_time_score[$i] = 600;
}
for($i = 21 ; $i <= 22 ; $i++){
    $complete_time_score[$i] = 500;
}
for($i = 22 ; $i <= 25 ; $i++){
    $complete_time_score[$i] = 400;
}
for($i = 25 ; $i <= 30 ; $i++){
    $complete_time_score[$i] = 300;
}
for($i = 31 ; $i <= 35 ; $i++){
    $complete_time_score[$i] = 200;
}
for($i = 36 ; $i <= 45 ; $i++){
    $complete_time_score[$i] = 100;
}
for($i = 46 ; $i <= 60 ; $i++){
    $complete_time_score[$i] = 0;
}

if($_SERVER['REQUEST_METHOD']==='POST'){
    header('Location: '.$_SERVER['SCRIPT_NAME']);
}

if (isset($_POST["reset"])){
    $val1 = $_SESSION["team_name"];
    $val2 = $_SESSION["try"];
    $_SESSION = array();
    $_SESSION["team_name"] = $val1;
    $_SESSION["try"] = $val2;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="scoresheet.css">
    <title>Beginner Score Sheet</title>
</head>
<body>

<!-- チーム名およびトライ -->
<?php
    if (isset($_POST["team"])){
        $_SESSION["team_name"] = $_POST["team"];
    }
    if (isset($_POST["try"])){
        $_SESSION["try"] = $_POST["try"];
    }
    if (isset($_POST["Match"])){
        $_SESSION["Match"] = $_POST["Match"];
    }
    echo "チーム名:".$_SESSION["team_name"]."  ";
    if($_SESSION["Match"] == 0){
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
    echo "第".$_SESSION["try"]."走行";
?>

<!-- 得点表 -->
<table border="1" style="border-collapse: collapse"style="border-collapse: collapse">
    <tr><th>項目</th><th>回数</th><th>得点小計</th></tr>
    <?php
    foreach ($element as $key => $val){
        echo "<tr><th>".$element[$key][0]."</th>";

        //回数
        echo "<th><form method='POST' action=''>";
        if(!isset($_SESSION[$element[$key][0]])){
            $_SESSION[$element[$key][0]] = 0;
        }
        if (isset($_POST[$element[$key][0]])) {
            $kbn = htmlspecialchars($_POST[$element[$key][0]], ENT_QUOTES, "UTF-8");
            switch ($kbn) {
                case "+":
                    $_SESSION[$element[$key][0]]++;
                    break;
                case "-":
                    $_SESSION[$element[$key][0]]--;
                    break;
                default:
                    $_SESSION[$element[$key][0]] = 0;
            }
        }
        if($_SESSION[$element[$key][0]] < 0){
            $_SESSION[$element[$key][0]] = 0;
        }
        else if($_SESSION[$element[$key][0]] > $element[$key][1]) {
            $_SESSION[$element[$key][0]] = $element[$key][1];
        }

        if($key == "relay_time" || $key == "complete_time") {
            //秒数が選択された場合の処理
            if (!isset($_SESSION["relay_time"])){
                $_SESSION["relay_time"] = 60;
            }
            if (!isset($_SESSION["complete_time"])){
                $_SESSION["complete_time"] = 60;
            }

            if ($key == "relay_time"){
                //中継タイムが設定されたとき
                if (isset($_POST["relay_time"])) {
                    $_SESSION["relay_time"] = htmlspecialchars($_POST["relay_time"], ENT_QUOTES, "UTF-8");
                    $score[$element[$key][0]] = $relay_time_score[$_SESSION["relay_time"]];
                }
                echo "<select name='relay_time'>";
                for ($i = 0 ; $i <= 60 ; $i++){
                    echo "<option value=".$i;
                    if($_SESSION["relay_time"] == $i){
                        echo " selected";
                    }
                    echo ">".$i."</option>";
                }
                echo "</select><input type='submit' value='OK' name='time1'>";
            }
            else if($key == "complete_time"){
                //完全停止タイムが設定されたとき
                if (isset($_POST["complete_time"])) {
                    $_SESSION["complete_time"] = htmlspecialchars($_POST["complete_time"], ENT_QUOTES, "UTF-8");
                }
                echo "<select name='complete_time'>";
                for ($i = 0 ; $i <= 60 ; $i++){
                    echo "<option value=".$i;
                    if($_SESSION["complete_time"] == $i){
                        echo " selected";
                    }
                    echo ">".$i."</option>";
                }
                echo "</select><input type='submit' value='OK' name='time2'>";
            }
        }
        else{
            //通常の回数表示
            echo $_SESSION[$element[$key][0]];
            echo "<input type='submit' value='+' name=" . $element[$key][0] . ">";
            echo "<input type='submit' value='-' name=" . $element[$key][0] . ">";
        }
        echo "</form></th>";

        //得点表示
        echo "<th>";
        if ($key == 'relay_time'){
            $score[$element[$key][0]] = $relay_time_score[$_SESSION["relay_time"]];
        }
        else if ($key == "complete_time"){
            $score[$element[$key][0]] = $complete_time_score[$_SESSION["complete_time"]];
        }
        else{
            if($_SESSION[$element[$key][0]] != 0){
                $score[$element[$key][0]] = $element[$key][$_SESSION[$element[$key][0]] + 1];
            }
            else{
                $score[$element[$key][0]] = 0;
            }
        }
        echo $score[$element[$key][0]];
        echo "</th></tr>";
    }
    ?>
</table>

<!-- 合計得点 -->
<p>
    合計得点
    <?
    $score_all = 0;
    foreach ($score as $val){
        $score_all += $val;
    }
    echo $score_all;
    ?>
</p>

<!-- 登録 -->
<form method="POST" action="check_Beginner.php">
    <input type="hidden" value="<?php echo $_SESSION["team_name"]?>" name="team">
    <input type="hidden" value="<?php echo $_SESSION["try"]?>" name ="try">
    <?php
    foreach ($score as $key => $val){
        echo "<input type='hidden' value=".$val." name=".$key.">";
    }
    ?>
    <input type="hidden" value="<?php echo $_SESSION["Match"]?>" name="Match">
    <input type="hidden" value="<?php echo $score_all?>" name ="total">
    <input type="submit" value="登録" name="regist">
</form>
<form method="POST" action="Beginner.php">
    <input type="hidden" value="<?php echo $_SESSION["Match"]?>" name="Match">
    <input type="submit" value="チーム選択画面へ戻る" name="back">
</form>

<!-- リセット -->
<form method="POST" action="">
    <input type="hidden" value="<?php echo $_SESSION["Match"]?>" name="Match">
    <input type="submit" value="リセット" name="reset">
</form>

</body>
</html>
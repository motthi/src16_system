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
    'takeoff'=>["離陸", 2, 200, 300],
    'land'=>["りゅうぐうに着陸", 2, 800, 1200],
    'photo'=>["絵の写真を撮る", 2, 2000, 3000],
    'complete'=>["完全制覇", 2, 1200, 1800],
    'ball'=>["月を1周", 2, 400, 600],
    'wind'=>["風ゾーンを通過", 2, 600, 900],
    '3min'=>["3分以内にゴール", 2, 400, 600],
    'minus'=>["墜落による減点", 3, -200, -400, -600],
    'time'=>["タイム", 1, 0]
];

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
    <link rel="stylesheet" type="text/css" href="scoresheet_Air.css">
    <title>Real Air Score Sheet</title>
</head>
<body>

<!-- チーム名およびトライ -->
<h2>
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
    echo "チーム名:".$_SESSION["team_name"]." ";
    if($_SESSION["Match"] == 0){
        echo "予選";
    }else if($_SESSION["Match"] == 1){
        echo "準決勝";
    }else if($_SESSION["Match"] == 2){
        echo "3位決定戦";
    }else{
        echo "決勝";
    }
    echo "第".$_SESSION["try"]."走行";
?>
</h2>

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

        //回数表示
        if($key == "time") {
            if (!isset($_SESSION["min"])){
                $_SESSION["min"] = 5;
            }
            if (!isset($_SESSION["sec"])){
                $_SESSION["sec"] = 0;
            }
            if (isset($_POST["time"])) {
                $_SESSION["min"] = htmlspecialchars($_POST["min"], ENT_QUOTES, "UTF-8");
                $_SESSION["sec"] = htmlspecialchars($_POST["sec"], ENT_QUOTES, "UTF-8");
            }

            $score[$element[$key][0]] = $_SESSION["min"].":".$_SESSION["sec"];

            echo "<select name='min'>";
            for ($i = 0 ; $i < 6 ; $i++) {
                echo "<option value=" . $i;
                if ($_SESSION["min"] == $i) {
                    echo " selected";
                }
                echo ">" . $i . "</option>";
            }
            echo "</select>:<select name='sec'>";
            for ($i = 0 ; $i < 60 ; $i++){
                echo "<option value=".$i;
                if ($_SESSION["min"] == "5"){
                    if ($i == 0){
                        echo " selected";
                    }
                    $_SESSION["sec"] = "0";
                }else{
                    if($_SESSION["sec"] == $i){
                        echo " selected";
                    }
                }
                echo ">".$i."</option>";
            }
            echo "</select><input type='submit' value='OK' name='time'>";
        }
        else {
            echo $_SESSION[$element[$key][0]];
            echo "<input type='submit' value='+' name=" . $element[$key][0] . ">";
            echo "<input type='submit' value='-' name=" . $element[$key][0] . ">";
        }
        echo "</form></th>";

        //得点表示
        echo "<th>";
        if ($key != 'time'){
            if($_SESSION[$element[$key][0]] != 0){
                $score[$element[$key][0]] = $element[$key][$_SESSION[$element[$key][0]] + 1];
            }
            else{
                $score[$element[$key][0]] = 0;
            }
            echo $score[$element[$key][0]];
        }
        else{
            echo $score[$element[$key][0]];
        }
        echo "</th></tr>";
    }
    ?>
</table>

<!-- 合計得点 -->
<p>
    合計得点
    <?php
    $score_all = 0;
    foreach ($score as $key => $val){
        if ($key != 'タイム'){
            $score_all += $val;
        }
    }
    if ($score_all <= 0){
        $score_all = 0;
    }
    echo $score_all;
    ?>
</p>

<!-- 登録 -->
<form method="POST" action="check_Air.php">
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

<!-- チーム選択画面へ -->
<form method="POST" action="Real_Air.php">
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
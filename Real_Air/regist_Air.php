<?php
/**
 * Created by PhpStorm.
 * User: k1100
 * Date: 2019/10/13
 * Time: 14:41
 */

//$score_tag、$excel_fileは競技に合わせて変更する
//  得点項目のプログラム表記（キー）と日本語表記（値）を記入する
$score_tag = array(
    'takeoff' => "離陸",
    'land' => "りゅうぐうに着陸",
    'photo' => "絵の写真を撮る",
    'complete' => "完全制覇",
    'ball' => "月を1周",
    'wind' => "風ゾーンを通過",
    '3min' => "3分以内にゴール",
    'minus'=> "墜落による減点",
    'time' => "タイム"
);
$excel_file = '/home/agse/app/src16-system/Real_Air/SRC16 Real Air Result.xlsx';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Real Air Registration</title>
</head>
<body>

<h1>SRC16 Real Air<br>登録完了画面</h1>
<hr>

<?php
$data = [$_POST["total"]];
foreach ($score_tag as $key => $val) {
    array_push($data, $_POST["$key"]);
}

if (!empty($_POST["Match"])) {
    $_SESSION["Match"] = $_POST["Match"];
}
else {
    $_SESSION["Match"] = 0;
}
if ($_SESSION["Match"] >= 1){
    $num = 1;
}
else{
    $num = 0;
};
?>


<!-- 書き込み -->
<?php
require_once "/home/agse/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excel_file);
$row =  $spreadsheet->getSheet($num) -> getHighestRow();
$sheetData = $spreadsheet->getSheet($num)->toArray();
$exception_value = null;

//得点の登録
if($num == 0){
    for($i = 2 ; $i <= $row ; $i++){
        if($_POST["team"] == $sheetData[$i][1]){
            if($_POST["try"] == "1"){
                $spreadsheet->getSheet($num)->fromArray($data, $exception_value, "D".($i+1));
            }
            else if($_POST["try"] == "2"){
                $spreadsheet->getSheet($num)->fromArray($data, $exception_value, "N".($i+1));
            }
            else{
                echo "走行回数が異常です。";
            }
            break;
        }
    }
    if($i > $row){
        echo "チーム名が見つかりませんでした。";
    }
}
else {
    if ($_SESSION["Match"] == 1) {
        $start = 2;
        $finish = 7;
    }
    else if ($_SESSION["Match"] == 2) {
        $start = 10;
        $finish = 11;
    }
    else if ($_SESSION["Match"] == 3) {
        $start = 14;
        $finish = 15;
    }
    for($i = $start ; $i <= $finish ; $i++){
        if($_POST["team"] == $sheetData[$i][1]){
            $spreadsheet->getSheet($num)->fromArray($data, $exception_value, "D".($i+1));
            break;
        }
    }
    if($i > $finish){
        echo "チーム名が見つかりませんでした。";
    }
}

$writer = new XlsxWriter($spreadsheet);
$writer->save($excel_file);
?>

<p>下記の通り登録しました</p>
<h2>
    <?php
    echo "チーム名:".$_POST["team"];
    if($_SESSION["Match"] == 0){
        echo "予選";
    }
    else if($_SESSION["Match"] == 1){
        echo "準決勝";
    }
    else if($_SESSION["Match"] == 2){
        echo "3位決定戦";
    }
    else{
        echo "決勝";
    }
    echo "第".$_POST["try"]."走行";
    ?>
</h2>

<!-- 登録結果表示 -->
<table border="1" width="100%" style="border-collapse: collapse"style="border-collapse: collapse">
    <tr><th>項目</th><th>得点</th></tr>
    <?php
    $fp = fopen("log.txt", "a");
    fwrite($fp, $_POST["team"]."\t".$_POST["try"]."\t");

    if($_SESSION["Match"] == 0){
        fwrite($fp, "Qualifying"."\t");
    }
    else if($_SESSION["Match"] == 1){
        fwrite($fp, "Semi"."\t");
    }
    else if($_SESSION["Match"] == 2){
        fwrite($fp, "3rd"."\t");
    }
    else{
        fwrite($fp, "final"."\t");
    }

    foreach ($_POST as $key => $val) {
        if ($key != "total" && $key != "regist" && $key != "team" && $key != "try" && $key != "yes" && $key != "Match") {
            fwrite($fp, $val."\t");
            echo "<tr><th>" . $score_tag[$key] . "</th><th>" . $val . "</th></tr>";
            if ($key != 'time') {
                $score_total += $val;
            }
        }
    }
    if ($score_total <= 0){
        $score_total = 0;
    }
    if ($score_total == $_POST["total"]){
        echo "<tr><th>合計</th><th>".$score_total."</th></tr>";
        fwrite($fp, $score_total."\n");
    }
    fclose($fp);
    ?>
</table>
<br>

<!-- チーム選択画面へのリンク -->
<form method="POST" action="Real_Air.php">
    <input type="hidden" value="<?php echo $_SESSION["Match"]?>" name="Match">
    <input type="submit" value="チーム選択画面へ" name="result">
</form>
<br>

<!-- 結果画面へのリンク -->
<form method="POST" action="result_Air.php">
    <input type="submit" value="結果画面へ" name="result">
</form>

</body>
</html>

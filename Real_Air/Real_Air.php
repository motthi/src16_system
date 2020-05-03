<?php
/**
 * Created by PhpStorm.
 * User: k1100
 * Date: 2019/10/13
 * Time: 15:03
 */
//$excel_fileは競技に合わせて変更する
//  フルパスが望ましい（らしい）
$excel_file = '/home/agse/app/src16-system/Real_Air/SRC16 Real Air Result.xlsx';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Real Air</title>
</head>
<body>

<?php
if (!empty($_POST["Match"])) {
    $_SESSION["Match"] = $_POST["Match"];
} else {
    $_SESSION["Match"] = 0;
}
if ($_SESSION["Match"] >= 1){
    $num = 1;
}
else{
    $num = 0;
}
require_once "/home/agse/vendor/autoload.php";
$reader = new PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$sheet = $reader->load($excel_file);
$sheetData = $sheet->getSheet($num)->toArray();
for ($i = 2 ; $i < count($sheetData) ; $i++){
    $team[$i] = $sheetData[$i][1];
}
?>

<h1>SRC16 Real Air</h1>
<hr>
<h2>チーム選択画面</h2>

<h3>試合形式選択</h3>
<form method="POST" action="">
    <select name="Match">
        <?php
        $mt = array(0 => "予選", 1 => "準決勝", 2 => "3位決定戦", 3 => "決勝");
        for ($i = 0 ; $i <= 3 ; $i++){
            echo "<option value='".$i."'";
            if($i == $_SESSION["Match"]){
                echo " selected";
            }
            echo ">".$mt[$i]."</option>";
        }
        ?>
    </select>
    <input type="submit" value="OK" name="OK">
</form>
<br>

<!-- チーム、走行番号選択 -->
<h3>チーム選択</h3>
<p>チーム名と走行番号を選択してください。</p>
<form method="POST" action="scoresheet_Air.php">
    <p>
        チーム名
        <select name="team">
            <?php
            $i = 0;
            foreach ($team as $val) {
                if(isset($val)){
                    if ($_SESSION["Match"] == 0) {
                        echo "<option value=".$val.">".$val."</option>";
                    }
                    else if ($_SESSION["Match"] == 1) {
                        if ($i <= 5 && $i != 2) {
                            echo "<option value=".$val.">".$val."</option>";
                        }
                    }
                    else if ($_SESSION["Match"] == 2) {
                        if ($i <= 9 && $i >= 8) {
                            echo "<option value=".$val.">".$val."</option>";
                        }
                    }
                    else if ($_SESSION["Match"] == 3) {
                        if ($i <= 13 && $i >= 12) {
                            echo "<option value=".$val.">".$val."</option>";
                        }
                    }
                }
                $i++;
            }
            ?>
        </select>
    </p>
    <p>
        第
        <select name="try">
            <?php
            for ($i = 1 ; $i <= 2 ; $i++){
                echo "<option value=".$i.">".$i."</option>";
            }
            ?>
        </select>
        走行
    </p>
    <input type="hidden" value="<?php echo $_SESSION["Match"]?>" name="Match">
    <input type="submit" value="スコアシート画面へ" name="team_OK">
</form>

<!-- 結果画面へのリンク -->
<form method="POST" action="result_Air.php">
    <br>
    <input type="submit" value="結果画面へ" name="result">
</form>

</body>
</html>
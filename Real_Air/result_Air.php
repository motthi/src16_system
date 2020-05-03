<?php
/**
 * Created by PhpStorm.
 * User: k1100
 * Date: 2019/10/13
 * Time: 16:19
 * http://blog.livedoor.jp/yama_90/archives/50909121.html
 */

session_start();

//$element_numは$excel_fileは競技に合わせて変更する
//  得点項目の要素数を表し、第1走と第2走が記録されているセルを特定する
//  ただし、「合計」は含まない。
$element_num = 9;
$excel_file = '/home/agse/app/src16-system/Real_Air/SRC16 Real Air Result.xlsx';
function quickSort(&$list, $first, $last) {
    $firstPointer = $first;
    $lastPointer  = $last;
    //枢軸値を決める。配列の中央値
    $centerValue  = $list[intVal(($firstPointer + $lastPointer) / 2)];

    //並び替えができなくなるまで
    do {
        //枢軸よりも左側で値が小さい場合はポインターは進める
        while ($list[$firstPointer] < $centerValue) {
            $firstPointer++;
        }
        //枢軸よりも右側で値が大きい場合はポインターを減らす
        while ($list[$lastPointer] > $centerValue) {
            $lastPointer--;
        }
        //この操作で左側と右側の値を交換する場所は特定

        if ($firstPointer <= $lastPointer) {
            //ポインターが逆転していない時は交換可能
            $tmp                 = $list[$lastPointer];
            $list[$lastPointer]  = $list[$firstPointer];
            $list[$firstPointer] = $tmp;
            //ポインタを進めて分割する位置を指定
            $firstPointer++;
            $lastPointer--;
        }
    } while ($firstPointer <= $lastPointer);

    if ($first < $lastPointer) {
        //左側が比較可能の時
        quickSort($list, $first, $lastPointer);
    }

    if ($firstPointer < $last) {
        //右側が比較可能時
        quickSort($list, $firstPointer, $last);
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="result_Air.css">
    <title>Real Air Result</title>
</head>
<body>

<div class="img">
    <div class="mask">
        <h1 class="title"><span>SRC16 Real Air</span> <span>試合結果</span></h1>
        <hr>
        <h2 style="color:red"><span>全日程が終了しました。優勝は「もんじろう」です！</span></h2>

        <!-- 表示変更ボタン -->
        <form method="POST" action="result_Air.php">
            <span>表示したい順位を選択してください : </span>
            <span>
                <input type="submit" value="予選" name="Match">
                <input type="submit" value="決勝" name="Match">
            </span>
        </form>

        <!-- 表示順位決定 -->
        <?php
        if (!isset($_SESSION["Match"])){
            $_SESSION["Match"] = 0;
        }else{
            if (!empty($_POST["Match"])){
                $type = $_POST["Match"];
                if (strcmp($type,"予選") == 0){
                    $_SESSION["Match"] = 0;
                }
                else if(strcmp($type,"決勝") == 0){
                    $_SESSION["Match"] = 1;
                }
                else{
                    if ($type == 0){
                        $_SESSION["Match"] = 0;
                    }
                    else{
                        $_SESSION["Match"] = 1;
                    }
                }
            }
        }

        if ($_SESSION["Match"] == 0){
            echo "<h2><span>";
            echo "予選　順位</span><span>（上位4チームが決勝進出）</span>";
            echo "</h1>";
        }
        else if ($_SESSION["Match"] == 1){
            //echo "決勝トーナメント　順位";
        }
        ?>

        <!-- データの読み込み -->
        <?php
        require_once "/home/agse/vendor/autoload.php";
        use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excel_file);
            $row = $spreadsheet->getSheet($_SESSION["Match"]) -> getHighestRow();
            $sheetData = $spreadsheet->getSheet($_SESSION["Match"]) -> toArray();
        }
        catch(Exception $e){
            file_put_contents("error.txt", $e."\n\n\n", FILE_APPEND);
            //echo "<script type='text/javascript'>setTimeout(function(){location.reload();}, 10);</script>";
        }
        ?>

        <!-- データの整理 -->
        <?php
        for($i = 0 ; $i <= $row ; $i++){
            for ($j = 0 ; $j <= 20 ; $j++){
                $data[$i][$j] = $sheetData[$i][$j];
            }
            if ($i >= 2){
                $score_data[$data[$i][1]]['first'] = $data[$i][3];
                $score_data[$data[$i][1]]['second'] = $data[$i][3+$element_num+1];
            }
        }
        ?>

        <!-- 結果表 -->
        <?php
        if ($_SESSION["Match"] == 0 ){
            //予選の結果表示
            //好成績の保存
            for($i = 2 ; $i <= $row-1 ; $i++){
                if ($data[$i][3] >= $data[$i][3+$element_num+1]){
                    $rank[$i-2] = $data[$i][3];
                    $score[$data[$i][1]] = $data[$i][3];
                }else{
                    $rank[$i-2] = $data[$i][3+$element_num+1];
                    $score[$data[$i][1]] = $data[$i][3+$element_num+1];
                }
            }

            //ランク算出
            quicksort($rank, 0, count($rank)-1);

            echo "<table border='1' style='border-collapse: collapse' style='border-collapse: collapse' class='qua'>";
            echo "<tr><th>順位</th><th>チーム名</th><th>成績</th><th>第1走</th><th>第2走</th></tr>";
            for ($i = count($rank)-1 ; $i >= 2 ; $i--){
                if($rank[$i] != $rank[$i+1]){
                    foreach ($score as $key => $val){
                        if($val == $rank[$i]){
                            if(!isset($val)){
                                continue;
                            }
                            //予選表示の場合
                            if(count($rank)-$i < 4){
                                echo "<tr class='over8'>";
                            }
                            else if(count($rank)-$i == 4){
                                echo "<tr class='redLine'>";
                            }
                            else{
                                echo "<tr>";
                            }
                            echo "<th>".(count($rank)-$i)."</th><th>".$key."</th><th>".$val."</th>";
                            echo "<th>".$score_data[$key]['first']."</th><th>".$score_data[$key]['second']."</th></tr>";
                        }
                    }
                }
            }
            echo "</table>";
        }
        else{
            //決勝の結果表示
            for($i = 2 ; $i <= $row-1 ; $i++){
                $team[$i-2] = $data[$i][1];
                $score[$i-2] = $data[$i][3];
            }

            $l1 = "red";
            $l2 = "black";
            $l3 = "red";

            if ($score[4] > $score[5]){
                $l4 = "red";
                $l5 = "black";
                $l6 = "red";
            }
            else if ($score[4] < $score[5]){
                $l4 = "black";
                $l5 = "red";
                $l6 = "red";
            }
            else {
                $l4 = "black";
                $l5 = "black";
                $l6 = "black";
            }

            if ($score[12] > $score[13]){
                $l7 = "red";
                $l8 = "black";
                $l9 = "red";
                $f = $team[12];
            }
            else if ($score[12] < $score[13]){
                $l7 = "black";
                $l8 = "red";
                $l9 = "red";
                $f = $team[13];
            }
            else {
                $l7 = "black";
                $l8 = "black";
                $l9 = "black";
            }

            if ($score[8] > $score[9]){
                $l10 = "red";
                $l11 = "black";
                $l12 = "red";
                $trd = $team[8];
            }
            else if ($score[8] < $score[9]){
                $l10 = "black";
                $l11 = "red";
                $l12 = "red";
                $trd = $team[9];
            }
            else {
                $l10 = "black";
                $l11 = "black";
                $l12 = "black";
            }
            ?>
            <h2>決勝トーナメント</h2>
            <table cellpadding="0" cellspacing="0" class="qua">
                <!-- <table border='1' style='border-collapse: collapse' style='border-collapse: collapse'> -->
                <tr height="60">
                    <th width="80" rowspan="3"><?php echo $team[0];?></th><td width="150" valign="bottom"></td>
                </tr>
                <tr height="3">
                    <td bgcolor=<?php echo $l1;?>></td><td width="3" rowspan="2" bgcolor=<?php echo $l1;?>>
                </tr>
                <tr height="60">
                    <td valign="top"></td><td width="180"><span><?php echo $score[0];?></span><span>(予選:2000)</span></td>
                </tr>
                <tr height="3">
                    <td></td><td></td><td width="3" bgcolor=<?php echo $l3;?>></td><td width="3" bgcolor=<?php echo $l3;?>><td width="3" rowspan="4" bgcolor=<?php echo $l7;?>></td>
                </tr>
                <tr height="60">
                    <th rowspan="3"><?php echo $team[1];?></th><td valign="bottom"></td><td bgcolor=<?php echo $l2?> rowspan="2"></td><td><span><?php echo $score[1];?></span><span>(予選:1200)</span></td><td><?php echo $score[12];?></td>
                </tr>
                <tr height="3">
                    <td bgcolor=<?php echo $l2;?>></td><td></td><td></td>
                </tr>
                <tr height="60">
                    <td valign="top"></td><td></td><td></td><td></td><td width="150" rowspan="3" align="center"><?php echo $f;?></td>
                </tr>

                <tr height="3">
                    <td></td><td></td><td></td><td></td><td bgcolor=<?php echo $l9;?>></td><td width="150" bgcolor=<?php echo $l9;?>></td>
                </tr>
                <tr height="60">
                    <th rowspan="3"><?php echo $team[4];?></th><td valign="bottom"></td><td width="1"></td><td></td><td rowspan="4" bgcolor=<?php echo $l8;?>></td><td></td>
                </tr>
                <tr height="3">
                    <td bgcolor=<?php echo $l4;?>></td><td width="3" rowspan="2" bgcolor=<?php echo $l4;?>><td></td><td></td>
                </tr>
                <tr height="60">
                    <td valign="top"></td><td><?php echo $score[4];?></td><td><?php echo $score[13];?></td>
                </tr>
                <tr height="3">
                    <td></td><td></td><td bgcolor=<?php echo $l6;?>></td><td bgcolor=<?php echo $l6;?>></td>
                </tr>
                <tr height="60">
                    <th rowspan="3"><?php echo $team[5];?></th><td valign="bottom"></td><td bgcolor=<?php echo $l5;?>></td><td><?php echo $score[5];?></td>
                </tr>
                <tr height="3">
                    <td bgcolor=<?php echo $l5;?>></td><td bgcolor=<?php echo $l5;?>></td>
                </tr>
                <tr height="60">
                    <td valign="top"></td>
                </tr>
            </table>
            <?php
        }
        ?>

        <!-- 表示変更ボタン -->
        <form method="POST" action="">
            <span>表示したい順位を選択してください : </span>
            <span>
                <input type="submit" value="予選" name="Match">
                <input type="submit" value="決勝" name="Match">
            </span>
        </form>

    </div>
</div></body>
</html>

<!-- 自動更新
<script type="text/javascript">
    setTimeout(function(){
        location.reload();
    }, 60000);
</script>
-->

<?php
/**
 * Created by PhpStorm.
 * User: k1100
 * Date: 2019/10/13
 * Time: 16:19
 */

//$element_numは$excel_fileは競技に合わせて変更する
//  得点項目の要素数を表し、第1走と第2走が記録されているセルを特定する
//  ただし、「合計」は含まない。
$element_num = 10;
$excel_file = '/home/agse/app/src16-system/Real_Rover/SRC16 Real Rover Result.xlsx';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="result_Rover.css">
    <title>Real Rover Result</title>
</head>
<body>

<!--
<p>画面サイズ：<span id="ScrSize"></span></p>
<p>ウィンドウサイズ：<span id="WinSize"></span></p>

<script type="text/javascript">
    //画面サイズの取得
    getScreenSize();
    //ウィンドウサイズの取得
    getWindowSize();

    //画面サイズを取得する
    function getScreenSize() {
        var s = "横幅 = " + window.parent.screen.width + " / 高さ = " + window.parent.screen.height;
        document.getElementById("ScrSizeWidth").innerHTML = s;
    }

    //ウィンドウサイズを取得する
    function getWindowSize() {
        var sW,sH,s;
        sW = window.innerWidth;
        sH = window.innerHeight;

        s = "横幅 = " + sW + " / 高さ = " + sH;

        document.getElementById("WinSize").innerHTML = s;
    }
</script>
-->

<div class="img">
    <div class="mask">
        <h1 class="title" class="normal">
            <span>SRC16 Real Rover</span> <span>試合結果</span>
        </h1>
        <hr>

        <h2 style="color:red" class="normal">
            <span>全日程が終了しました。</span><span>優勝は"LEGEND"です！</span>
        </h2>

        <!-- 表示変更ボタン -->
        <form method="POST" action="result_Rover.php">
            <span>表示したい順位を選択してください :</span>
            <span>
                <input type="submit" value="1次予選" name="Match">
                <input type="submit" value="2次予選" name="Match">
                <input type="submit" value="決勝" name="Match">
            </span>
        </form>

        <!-- 表示順位決定 -->
        <?php
        if (!empty($_POST["Match"])){
            $type = $_POST["Match"];
            if (strcmp($type,"1次予選") == 0){
                $_SESSION["Match"] = 0;
            }
            else if(strcmp($type,"2次予選") == 0){
                $_SESSION["Match"] = 1;
            }
            else if(strcmp($type,"決勝") == 0){
                $_SESSION["Match"] = 2;
            }
            else{
                $_SESSION["Match"] = $type;
            }
        }
        else {
            $_SESSION["Match"] = 2;
        }

        if ($_SESSION["Match"] == 0){
            echo "<h2>";
            echo "<span>1次予選　結果</span><span>（上位4チームが2次予選進出）</span>";
            echo "</h2>";
        }
        else if ($_SESSION["Match"] == 1){
            echo "<h2>";
            echo "<span>2次予選　結果</span><span>（上位2チームが決勝進出）</span>";
            echo "</h2><h3>";
            echo "成績は、  第1走＋第2走＋1次予選走行で高い得点";
            echo "</h3>";
        }
        else if($_SESSION["Match"] == 2){
            echo "<h1>";
            echo "決勝 結果";
            echo "</h1>";
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
            echo "<script type='text/javascript'>setTimeout(function(){location.reload();}, 10);</script>";
        }

        //データの整理
        for($i = 0 ; $i <= $row ; $i++){
            for ($j = 0 ; $j <= 20 ; $j++){
                $data[$i][$j] = $sheetData[$i][$j];
            }
            if ($i >= 2){
                $score_data[$data[$i][1]]['first'] = $data[$i][3];
                $score_data[$data[$i][1]]['second'] = $data[$i][3+$element_num+1];
            }
        }

        //好成績の保存
        $score_qua1 = array(0, 0, 4314, 2600, 1150, 600);
        for ($i = 2 ; $i <= $row-1 ; $i++) {
            $rank[$i-2] = $data[$i][3] + $data[$i][3+$element_num+1];
            $score[$data[$i][1]] = $data[$i][3] + $data[$i][3+$element_num+1];
            if ($_SESSION["Match"] == 1){
                $score[$data[$i][1]] += $score_qua1[$i];
                $rank[$i-2] += $score_qua1[$i];
            }
            //$spreadsheet->getSheet($_SESSION["Match"])->getCell("C".($i+1))->setValue($score[$data[$i][1]]);
        }

        //ランク算出
        quicksort($rank, 0, count($rank)-1);
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
        //print_r($score);
        ?>

        <!-- 結果表示 -->
        <table border="1" style="border-collapse: collapse"style="border-collapse: collapse" class="qua">
            <tr><th>順位</th><th>チーム名</th><th>成績</th>
                <?php
                if ($_SESSION["Match"] != 2){
                    //決勝以外は第1走と第2走を入れる
                    echo "<th>第1走</th><th>第2走</th>";
                }
                if ($_SESSION["Match"] == 1){
                    //2次予選は1次予選の結果も参入する
                    echo "<th>1次予選分</th>";
                }
                ?>
            </tr>
            <?php
            for ($i = count($rank)-1 ; $i >= 2 ; $i--){
                if($rank[$i] != $rank[$i+1]){
                    foreach ($score as $key => $val){
                        if($val == $rank[$i]){
                            if(!isset($score_data[$key]['first'])){
                                if ($_SESSION["Match"] != 1) {
                                    continue;
                                }
                                else{
                                    if (count($rank)-$i == 5){
                                        continue;
                                    }
                                }

                            }
                            if ($_SESSION["Match"] == 0){
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
                            }
                            else if($_SESSION["Match"] == 1){
                                //順位決定戦表示の場合
                                if(count($rank)-$i < 2){
                                    echo "<tr class='over8'>";
                                }
                                else if(count($rank)-$i == 2){
                                    echo "<tr class='redLine'>";
                                }
                                else{
                                    echo "<tr>";
                                }
                            }
                            else{
                                //決勝表示の場合
                                if(count($rank)-$i < 2){
                                    echo "<tr class='final'>";
                                }
                            }
                            echo "<th>".(count($rank)-$i)."</th><th>".$key."</th><th>".$val."</th>";
                            if ($_SESSION["Match"] != 2){
                                echo "<th>".$score_data[$key]['first']."</th><th>".$score_data[$key]['second']."</th>";
                            }
                            if ($_SESSION["Match"] == 1){
                                echo "<th>";
                                echo $val - $score_data[$key]['first'] - $score_data[$key]['second'];
                                echo "</th>";
                            }
                            echo "</tr>";
                        }
                    }
                }
            }
            ?>
        </table>
        <br>

        <!-- 表示変更ボタン -->
        <form method="POST" action="result_Rover.php">
            <span>表示したい順位を選択してください :</span>
            <span>
                <input type="submit" value="1次予選" name="Match">
                <input type="submit" value="2次予選" name="Match">
                <input type="submit" value="決勝" name="Match">
            </span>
        </form>

    </div>
</div>
</body>
</html>

<!-- 自動更新
<script type="text/javascript">
    setTimeout(function(){
        location.reload();
    }, 60000);
</script>
-->
<?php
if(isset($_POST["dateiname"]) && !empty($_POST["dateiname"])){
	$dateiname = $_POST['dateiname'];
}
elseif(isset($_GET["datei"])){
    $dateiname = $_GET['datei'];
}
else{
	$dateiname = 'c:/xampp/htdocs/hob/application/modules/front/models/';
}
?>

<html>
    <head>
        <title>Blocksniffer</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
    <body>
        <table style="margin: 50px auto;">
            <tr>
                <td>Block Sniffer</td>
            </tr>
            <tr>
                <td>
                    <form method="post" action="index.php">
                        <input type="text" name="dateiname" value="<?php echo $dateiname; ?>" style="width: 800px;"><br>
                        <input type="submit" name="senden" value="auswerten">
                    </form>
                </td>
            </tr>
        </table>
    </body>
</html>

<?php
if(isset($_POST["senden"]) && !empty($_POST["senden"])){

    $myClass = 'Buchungen.php';
    $myClass = $_POST['dateiname'];

    $file = file($myClass);

    $neuArray = array();
    $docBlock = array();

    $j = 0;
    $doc = false;
    $ausgabe = '';

    for($i=0; $i < count($file); $i++){
        $row = trim($file[$i]);
        // $row =  htmlentities ($row);
		// $row = iconv('ISO-8859-1', 'UTF-8', $row);
        $ausgabe .= $row."<br>";

        if(preg_match('#^(\/\*\*)$#',$row)){
            $docBlock = array();
            $doc = true;
        }

        if($doc)
            $docBlock[] = $row;

        if(preg_match('#^(\*\/)$#',$row)){
            $neuArray[$j] = $docBlock;
            $doc = false;
            $j++;
        }
    }

    $ersteZeile = array();
    for($i = 1; $i < count($neuArray); $i++){
        if(!preg_match('#@#',$neuArray[$i][1])){
            $row = trim($neuArray[$i][1]);
            $row = substr($row,1);
            $row = trim($row);
            $row = "* + ".$row."<br>";
            $ersteZeile[] = $row;
        }
    }

    $information = '';
    for($k=0; $k < count($ersteZeile); $k++){
        $information .= $ersteZeile[$k];
    }

    $ersterBlock = '';
    for($l=0; $l < count($neuArray[0]); $l++){
        if($l < 3)
            $ersterBlock .= $neuArray[0][$l]."<br>";

        if($l == 3){
            $ersterBlock .= $information;
            $ersterBlock .= "* <br>";
        }


        if($l > 3 && preg_match("#@#", $neuArray[0][$l]))
            $ersterBlock .= $neuArray[0][$l]."<br>";
    }

    $ersterBlock .= "*/ <br>";

    echo "<hr>".htmlentities("<?php")."<br>".$ersterBlock."<hr>".$ausgabe;

}
?>
<?php
// if($_POST['inpTextKey']) $textKey = $_POST['inpTextKey'];
// if($_POST['chkSpam']) $incSpam = $_POST['chkSpam'];
// if($_POST['radLabel']) $labels = $_POST['radLabel'];
// if($_POST['inpMaxRes']) $maxRes = $_POST['inpMaxRes'];

// if($_POST['inpTextKey']) echo $_POST['inpTextKey'].'<br>';
// if($_POST['chkSpam']) echo $_POST['chkSpam'].'<br>';
// if($_POST['radLabel']) echo $_POST['radLabel'].'<br>';
// if($_POST['inpMaxRes']) echo $_POST['inpMaxRes'].'<br>';

class Miobj {
    public function __construct() {
        
    }
    public function is_connected() {
        return $this->is_connected;
    }
    public function enviar() {
        return "Hola a todos";
    }
}
$v1 = 'Juan';
$v2 = 123;
$juegos = array('mine'=>'100', 'halo'=>30);

$arr = array('saludo'=>'Hola', 12, $v1, $v2);
echo '<pre>';
print_r($arr);
echo '</pre>';

$arr['jueguitos'] = $juegos;

$arr[] = $arr;

echo '<pre>';
print_r($arr);
echo '</pre>';

$miObj = new Miobj;

if(5>3){
    echo "Mayor";
    echo "Mayor";
}else echo "Menor";

?>

<form action="hola.php" method="POST" style="border: dashed 2px salmon; padding: 5px;">
<?php if(isset($_POST['btnSend'])) echo "POST Send DECLARADO <br>" ?>
<?php // if(isset($_POST['btnList'])) echo $miObj->enviar()." POST List DECLARADO <br>" ?>

<input type="submit" value="Enviar" name="btnSend">
<input type="submit" value="Listar" name="btnList">
</form>
<?php if(isset($_POST['btnList'])) echo $miObj->enviar()." POST List DECLARADO <br>" ?>

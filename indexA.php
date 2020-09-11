<?php
class Api_gg{
    public $has_content;

    public $textKey;
    public $labels;
    public $maxRes;
    public $incSpam;
    public $conn;

    // public $sender;
    public $to;
    public $subject;
    public $body;

    public function __construct() {
        $this->include();

    }

    private function include() {
        // require __DIR__ . 'google-api-php-client-v2.7.0-PHP7.4/vendor/autoload.php';
        // include_once 'google-api-php-client-v2.7.0-PHP7.4/vendor/autoload.php';
        require 'google-api-php-client-v2.7.0-PHP7.4/vendor/autoload.php';
        include "connection.php";
        $this->conn = new Connection();
    }

    public function saveContent() {
        // if(isset($_POST['inpTextKey']) && $_POST['inpTextKey'] != "") {
        //     $this->has_content = true;
        //     echo 'Con contenido <br>';
        // }else {
        //     echo 'Sin contenido <br>';
        //     $this->has_content = false;
        // }

        
        if(isset($_POST['inpTextKey'])) $this->textKey = $_POST['inpTextKey'];
        // if(isset($_POST['chkSpam'])) $this->incSpam = $_POST['chkSpam'];
        if(isset($_POST['radLabel'])) $this->labels = $_POST['radLabel'];
        // if(isset($_POST['chkLabel'])) $this->labels = $_POST['chkLabel'];
        if(isset($_POST['inpMaxRes'])) $this->maxRes = $_POST['inpMaxRes'];
        
        
        if(isset($_POST['txtTo'])) $this->to = $_POST['txtTo'];
        if(isset($_POST['txtSubject'])) $this->subject = $_POST['txtSubject'];
        if(isset($_POST['txaBody'])) $this->body = $_POST['txaBody'];

        // echo '<pre>';
        // print_r($this->labels);
        // echo '</pre>';

    }

    public function is_register() { ///
        // $this->conn = new Connection();
        if($this->conn->is_connected()) {
            return true;
        } else {
            return false;
        }
    }

    public function goList() {
        // $conn = new Connection();

        if($this->conn->is_connected()) {
            require_once ("gmail.php");
            $gmail = new Gmail($this->conn->get_client());
            // return $gmail->listMessages();
            echo "CONECTADO CON CLIENTE";
            return $gmail->listarMens($this->textKey, $this->labels, $this->maxRes);
        } else {
            return "Primero Registrate: ".$this->conn->get_unauthenticated_data();
        }
    }

    public function goRead() {
        // $conn = new Connection();

        if($this->conn->is_connected()) {
            require_once ("gmail.php");
            $gmail = new Gmail($this->conn->get_client());
            // return $gmail->listMessages();
            echo "CONECTADO CON CLIENTE <br>";
            return $gmail->MostrarMens($this->textKey, $this->labels, $this->maxRes);
        } else {
            return "Primero Registrate: ".$conn->get_unauthenticated_data();
        }
    }

    public function goSend() {
        // $conn = new Connection();

        if($this->conn->is_connected()) {
            require_once ("gmail.php");
            $gmail = new Gmail($this->conn->get_client());
            // return $gmail->listMessages();
            echo "CONECTADO CON CLIENTE <br>";
            return $gmail->enviarMens($this->to, $this->subject, $this->body);
        } else {
            return "Primero Registrate: ".$conn->get_unauthenticated_data();
        }
    }
}
$apiGG = new Api_gg();
// $conn = new Connection();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gg-Gmail</title>
</head>
<body>
<?php if($apiGG->is_register()) {?>
    <div class="container" style="justify-content: center; align-items: center;">
            
        <div class="contForm" style="border: 1px solid salmon; display: inline-block; padding: 10px; margin: auto 10px  ;">
            <form action="index.php" method="POST">

                <?php $apiGG->saveContent(); ?>

                <p>Etiquetas de buscqueda: <br>
                    <input type="radio" name="radLabel" id="" value="INBOX" checked <?php if($apiGG->labels == "INBOX") echo "checked"; ?> >Bandeja de Entrada <br>
                    <input type="radio" name="radLabel" id="" value="DRAFT" <?php if($apiGG->labels == "DRAFT") echo "checked"; ?> >Borradores <br>
                    <input type="radio" name="radLabel" id="" value="SPAM" <?php if($apiGG->labels == "SPAM") echo "checked"; ?> >Spam <br>
                </p>

                <!-- <p>Etiquetas de buscqueda: <br>
                    <input type="checkbox" name="chkLabel[]" id="" value="INBOX" <?php //if(in_array("INBOX", $apiGG->labels)) echo "checked" ?>>Bandeja de Entrada <br>
                    <input type="checkbox" name="chkLabel[]" id="" value="DRAFT" <?php //if(in_array("DRAFT", $apiGG->labels)) echo "checked" ?>>Borradores <br>
                    <input type="checkbox" name="chkLabel[]" id="" value="SPAM" <?php //if(in_array("SPAM", $apiGG->labels)) echo "checked" ?>>Spam <br>
                </p> -->
                

                <p>Max. Resultados <br>
                    <input type="number" name="inpMaxRes" id="" placeholder='"5"' value="<?php echo $apiGG->maxRes ?>">
                </p>

                <p>Buscar palabra clave<br>
                    <input type="text" name="inpTextKey" id="" placeholder='ej. "auto"' value="<?php echo $apiGG->textKey ?>">
                </p>
                <input type="submit" value="Listar" name="btnList">
                <input type="submit" value="Mostrar" name="btnRead">
            </form>
        </div>

        <div style="border: 1px solid salmon; display: inline-block; padding: 10px; margin: auto 10px;">
            <form action="index.php" method="POST">
            <?php $apiGG->saveContent(); ?>

                <p>Para: <br>
                    <input type="email" name="txtTo" id="" value="<?php echo $apiGG->to ?>">
                </p>

                <p>Asunto: <br>
                    <input type="text" name="txtSubject" id="" value="<?php echo $apiGG->subject ?>">
                </p>

                <p>Contenido: <br>
                    <!-- <input type="text" name="txtBody" id=""><br> -->
                    <textarea name="txaBody" id="" cols="20" rows="5"><?php echo $apiGG->body ?></textarea><br>
                </p>

                <input type="submit" value="Enviar" name="btnSend">
                <!-- <input type="submit" value="Mostrar" name="btnRead"> -->
            </form>
        </div>
        
        <div class="" id="divResult" style="width: 50%;">
            <?php
            if(isset($_POST['btnList'])) $apiGG->goList();
            if(isset($_POST['btnRead'])) $apiGG->goRead();
            
            if(isset($_POST['btnSend'])) $apiGG->goSend();
            ?>
        </div>
    </div>
<?php } else echo "Primero registrate: ".$apiGG->conn->get_unauthenticated_data(); ?>
</body>
</html>
<!-- $apiGG = new Api_gg;
echo "<!DOCTYPE html><html>";
echo $apiGG->go();
echo "</html>"; -->
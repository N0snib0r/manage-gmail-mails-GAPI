<?php 

class Gmail extends Controller {
    function __construct() {
        parent::__construct();
        $this->view->mensaje = ""; //Mensaje para para mostrar an Views
    }

    function render() {
        if($this->model->isRegister()) { //Esta para cambiaar
            $this->listMessages('INBOX',null);
        } else {
            $this->view->authUrl = $this->model->getAuthData();
            // echo "NO REGISTRADO"; //TEST
            $this->view->render('gmail/authentication');
        }
    }

    function listMessages($label,$key=null) {
        // $this->model->listMsg($label,$key);

        $messages = $this->model->listMessages($label,$key);

        if(empty($messages)) {
            $this->view->mensaje = "Ningun archivo mensaje";
        }
        $this->view->messages = $messages;
        $this->view->render("gmail/index");
    }

    function readMessage($idMail) {
        $msg = $this->model->read($idMail);

        if($msg) {
            $this->view->msg = $msg;
            $this->view->render("gmail/detalle");
        } else {
            $this->view->mensaje = "Algo ocurrio mal, vuelva a intentarlo";
        }
    }

    function downloadFile($params) {
        // $this->model->download($msgId,$idAttach,$nameFile);
        $this->model->download($params);
        $this->view->render("gmail/detalle");
        // $this->view->render("gmail/detalle");
    }

    function downloadAll($id) {
        $this->model->downloadAll($id);
    }

    function searchM() {
        isset($_POST['inpSearch']) ? $key = $_POST['inpSearch'] : $key = null;
        $this->listMessages(null,$key);
        
        // $messages = $this->model->listMessages("TRASH",$key);

        // if(empty($messages)) {
        //     $this->view->mensaje = "Ningun archivo mensaje";
        // }
        // $this->view->messages = $messages;
        // $this->view->render("gmail/index");
    }


}

?>
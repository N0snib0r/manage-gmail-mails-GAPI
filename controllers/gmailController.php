<?php 

class Gmail extends Controller {
    function __construct() {
        parent::__construct();
        $this->view->mensaje = ""; //Mensaje auxiliar para mostrar en las vistas
    }

    function render() { // Funcion principal
        if($this->model->isRegister()) { // Verifica si el usuario esta registrado
            $this->listMessages('INBOX',null); // Por defecto listara los mensajes de INBOX y no buscar una palabra
        } else {
            $this->view->authUrl = $this->model->getAuthData(); // Obtiene la URL de autenticacion para el usuario
            $this->view->render('gmail/authentication');
        }
    }

    function listMessages($label,$key=null) { // Lista los correos en base a la etiqueta y el parametro de busqueda
        $messages = $this->model->listMessages($label,$key); // LLama al modelo y devuelve el listado de los mensajes

        if(empty($messages)) {
            $this->view->mensaje = "Ningun archivo mensaje";
        }
        $this->view->messages = $messages;
        $this->view->render("gmail/index");
    }

    function readMessage($idMail) { // Muestra el conteido de un correo
        $msg = $this->model->read($idMail); // Model devuelve El contenido y sus archivos adjuntos

        if($msg) {
            $this->view->msg = $msg;
            $this->view->render("gmail/detalle");
        } else {
            $this->view->mensaje = "Algo ocurrio mal, vuelva a intentarlo";
        }
    }

    function downloadFile($params) { // Descarga un archivo adjutno seleccionado
        $this->model->download($params);
        $this->view->render("gmail/detalle");
    }

    function downloadAll($id) { // Descarga todos los archivos adjuntos
        $this->model->downloadAll($id);
    }

    function searchM() { // Lista los correos en base a un parametro de busqueda
        isset($_POST['inpSearch']) ? $key = $_POST['inpSearch'] : $key = null;
        $this->listMessages(null,$key); // La busqueda sera general, no basado en un etiqueta
    }


}

?>
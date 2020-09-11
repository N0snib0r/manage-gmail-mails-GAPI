<?php 

class Gmail extends Controller { 
    function __construct() {
        parent::__construct();
        $this->view->mensaje = ""; //Mensaje para para mostrar an Views
    }

    function render() {
        if($this->model->isRegister()) { //Esta para cambiaar
            

            
            $this->view->render("gmail/index");

        } else {
            $this->view->authUrl = $this->model->getAuthData();
            // echo "NO REGISTRADO"; //TEST
            $this->view->render('gmail/authentication');
        }
    }


}

?>
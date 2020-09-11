<?php 
class GmailModel extends Model{

    function __construct() {
        parent::__construct();
        $this->client = $this->conn->getClient();
        $this->service = $this->createService();

        
    }

    function isRegister() { //Verifica si el usuario ya esta registrado
        return $this->conn->isConnected();
    }

    function createService() {
        // Inicializamos el servicio de Google Drive
        $service = new Google_Service_Drive($this->client);
        return $service;
    }




}

?>
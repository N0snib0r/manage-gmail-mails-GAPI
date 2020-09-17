<?php

use Google\Auth\Cache\Item;

// Clases auxiliares para envio de datos ordenado
include_once 'models/mensaje.php'; 
include_once 'models/adjunto.php';

class GmailModel extends Model{
    protected $service;

    function __construct() {
        parent::__construct();
        $this->userId = 'me'; // Palabra clave, hace referencia la correo del usuario registrado

        $this->client = $this->conn->getClient(); // Cliente registrado
        $this->service = $this->createService();  // Servicio principal de Gmail
    }

    function listMessages($labelId="INBOX",$q) { // Lista los correos | Por defecto lista INBOX
        $items = [];
        //Creamos las varibles necesarios
        $pageToken = null;
        $messages = array();

        //Parametros de list()
        $opt_param = array(
            'includeSpamTrash' => false, // No incluir spam en la busqueda
            'labelIds' => $labelId,      // Etiqueta donde se obtendran los correos
            'maxResults' => 5);          // Cantidad maxima de resultados e el listado

        if(!empty($q)) $opt_param['q'] = $q; // Palabra que se buscara en los correos

        //Parametros de get()
        $optParamGet = array('format' => 'metadata',   // Solo devulve metadatos
        'metadataHeaders' => array('from','subject')); // Metadatos que devolvera
        
        try {
            if($pageToken) {
                $opt_param['pageToken'] = $pageToken;
            }
            //Obtiene un listado con los ID de los mensajes
            $messagesResponse = $this->service->users_messages->listUsersMessages($this->userId, $opt_param);

            if($messagesResponse->getMessages()) {
                $messages = array_merge($messages, $messagesResponse->getMessages());
                $pageToken = $messagesResponse->getNextPageToken();

                foreach ($messages as $row) {
                    $item = new Mensaje(); // Objeto auxiliar
                    // Obtiene la metadata de cada mensaje por su ID
                    $msg = $this->service->users_messages->get($this->userId, $row->getId(), $optParamGet);

                    $item->idMsg = $row->getId(); // Guarda el ID del mensaje

                    switch ($msg->payload->headers[0]->name) { // Variantes del orden de la metadata
                        case 'Subject':
                            $item->subject = $msg->getPayload()->getHeaders()[0]->value;
                            $item->from = $msg->getPayload()->getHeaders()[1]->value;
                        break;
                        case 'From':
                            $item->from = $msg->getPayload()->getHeaders()[0]->value;
                            $item->subject = $msg->getPayload()->getHeaders()[1]->value;
                        break;
                    }

                    array_push($items, $item);
                }
                return $items; // Retorna un mensaje con los datos de todos los mensajes
            }
        } catch(Exception $e) {
            return [];
        }
    }

    function read($id) { // Obtiene el contenido de un correo
        $attachs = [];

        //Parametros para obtener metadatos
        $optParam = array('format' => 'metadata',
                            'metadataHeaders' => array('from', 'subject'));

        try {
            // Obtiene la metadata del correo
            $msgMet = $this->service->users_messages->get($this->userId,$id,$optParam);
            // Obtiene todo el contenido del correo
            $msgFull = $this->service->users_messages->get($this->userId,$id);

            // Obtiene los datos de los archivos adjuntos
            $attachParts = $msgFull->getPayload()->getParts();

            for ($i=1; $i<count($attachParts); $i++) {
                $attach = new Adjunto(); // Objeto auxiliar
                $attach->idAttach = $attachParts[$i]->body->attachmentId; // Identificador del archivo Adjunto
                $attach->nameFile = $attachParts[$i]->filename;
                $attach->idPart = $attachParts[$i]->partId;
                
                array_push($attachs, $attach);
            }
            $item = new Mensaje(); // Objeto auxiliar

            // Variantes de formatos de los correos
            //Obtiene el contenido del mensaje y lo decodifica
            if(isset($msgFull->getPayload()->parts[1]->body->data)) {
                $bdy = self::base64UrlDecode($msgFull->payload->parts[1]->body->data);
            } elseif (isset($msgFull->getPayload()->getParts()[0]->parts[1]->body->data)) {
                $bdy = self::base64UrlDecode($msgFull->getPayload()->getParts()[0]->parts[1]->body->data);
            } elseif (isset($msgFull->getPayload()->getBody()->data)) {
                $bdy = self::base64UrlDecode($msgFull->getPayload()->getBody()->data);
            }
            $item->body = $bdy;

            switch ($msgMet->payload->headers[0]->name) { // Variantes del orden de la metadata obtenida
                case 'Subject':
                    $item->subject = $msgMet->getPayload()->getHeaders()[0]->value;
                    $item->from = $msgMet->getPayload()->getHeaders()[1]->value;
                break;
                case 'From':
                    $item->subject = $msgMet->getPayload()->getHeaders()[1]->value;
                    $item->from = $msgMet->getPayload()->getHeaders()[0]->value;
                break;
            }
            $item->idMsg = $id[0];
            $item->attachments = $attachs;
            
            return $item; // Retorna un objeto con todo el contenido del correo
            
        } catch(Exception $e) {
            return [];
        }
    }

    function downloadAll($idMsg) { // Descarga todos los archivos adjuntos de un correo
        $pathDir = "downloads/"; // Ruta de destino de los archivos descargados
        
        try {
            $msg = $this->service->users_messages->get($this->userId,$idMsg);
            $attachParts = $msg->getPayload()->getParts();

            for ($i=1; $i<count($attachParts); $i++) {

                $idAttach = $attachParts[$i]->body->attachmentId;
                $nameFile = $attachParts[$i]->filename;

                //Obtiene la data del archivo adjunto
                $attachmentData = $this->service->users_messages_attachments->get($this->userId,$idMsg,$idAttach);

                $data = self::base64UrlDecode($attachmentData->data);

                $fd = fopen($pathDir.$nameFile, "w+");
                fwrite($fd, $data);
                fclose ($fd);
            }
            
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function download($param) { // Descarga solo un archivo adjunto
        $id = $param[0];       // ID del mensaje
        $idAttach = $param[1]; // ID del archivo adjunto
        $nameFile = $param[2]; // nombre del archivo
        $pathFile = "downloads/".$nameFile; // Ruta de la descarga

        // Obtiene la data del archivo adjunto
        $attachmentData = $this->service->users_messages_attachments->get($this->userId,$id,$idAttach);
        // Decodificado de base64
        $data = self::base64UrlDecode($attachmentData->data);

        try { // Creacion del archivo en el servidor
            $fd = fopen($pathFile, "w+");
            fwrite($fd, $data);
            fclose ($fd);
            exit;
        } catch(Exception $e) {
            return false;
        }
    }

    static function base64UrlDecode($data) { // Decodificado base64
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $data));
    }

    function isRegister() { // Verifica si el usuario ya esta registrado
        return $this->conn->isConnected();
    }

    function getAuthData() { //Obtiene la URL para Auth
        return $this->conn->getAuthData();
    }

    function createService() { // Inicializamos el servicio de Google Drive
        $service = new Google_Service_Gmail($this->client);
        return $service;
    }




}

?>
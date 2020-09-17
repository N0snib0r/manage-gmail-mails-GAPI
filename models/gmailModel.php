<?php

use Google\Auth\Cache\Item;

include_once 'models/mensaje.php';
include_once 'models/adjunto.php';

class GmailModel extends Model{
    protected $service;

    function __construct() {
        parent::__construct();
        $this->userId = 'me';

        $this->client = $this->conn->getClient();
        $this->service = $this->createService();
    }

    function listMessages($labelId="INBOX",$q) {
        $items = [];
        //Creamos las varibles necesarios
        $pageToken = null;
        $messages = array();

        //Parametros de list()
        $opt_param = array(
            'includeSpamTrash' => false,
            'labelIds' => $labelId,
            'maxResults' => 5);
        // if($labelId != null) $opt_param['labelIds'] = $q;
        if(!empty($q)) $opt_param['q'] = $q;

        //Parametros de get()
        $optParamGet = array('format' => 'metadata',
        'metadataHeaders' => array('from','subject'));
        
        try {
            if($pageToken) {
                $opt_param['pageToken'] = $pageToken;
            }
            $messagesResponse = $this->service->users_messages->listUsersMessages($this->userId, $opt_param);
            if($messagesResponse->getMessages()) {
                $messages = array_merge($messages, $messagesResponse->getMessages());
                $pageToken = $messagesResponse->getNextPageToken();

                foreach ($messages as $row) {
                    $item = new Mensaje();
                    $msg = $this->service->users_messages->get($this->userId, $row->getId(), $optParamGet);

                    $item->idMsg = $row->getId();

                    switch ($msg->payload->headers[0]->name) {
                        case 'Subject':
                            $item->subject = $msg->getPayload()->getHeaders()[0]->value;
                            $item->from = $msg->getPayload()->getHeaders()[1]->value;
                        break;
                        case 'From':
                            $item->from = $msg->getPayload()->getHeaders()[0]->value;
                            $item->subject = $msg->getPayload()->getHeaders()[1]->value;
                        break;
                    }
                    
                    // if($msg->payload->headers[0]->name == "subject") $item->subject = $msg->getPayload()->getHeaders()[0]->value;
                    // elseif($msg->payload->headers[1]->name == "subject") $item->subject = $msg->getPayload()->getHeaders()[1]->value;

                    // $item->from = $msg->getPayload()->getHeaders()[1]->value;

                    array_push($items, $item);
                    
                    // echo '<pre>';
                    // // print_r($msg->getPayload()->headers);
                    // print_r($msg->getLabelIds());
                    // echo '</pre>';
                }
                return $items;
            }
        } catch(Exception $e) {
            return [];
        }
    }

    function read($id) {
        $items = [];

        $pageToken = NULL;
        $messages = array();

        //Parametros para obtener metadatos
        $optParamMet = array('format' => 'metadata',
                            'metadataHeaders' => array('from', 'subject'));

        try {
            $msgMet = $this->service->users_messages->get($this->userId,$id,$optParamMet);
            $msgFull = $this->service->users_messages->get($this->userId,$id);

            $attachParts = $msgFull->getPayload()->getParts();

            for ($i=1; $i<count($attachParts); $i++) {
                $attach = new Adjunto();
                $attach->idAttach = $attachParts[$i]->body->attachmentId;
                $attach->nameFile = $attachParts[$i]->filename;
                $attach->idPart = $attachParts[$i]->partId;

                array_push($items, $attach);
            }
            
            // echo '<pre class="text-white">';
            // print_r($attachParts);
            // echo '</pre>';

            // $attachmentData = $this->service->users_messages_attachments->get($this->userId,$id,$attach);

            // $data = strtr($attachmentData->getData(), array('-' => '+', '_' => '/'));
            // $data = self::base64UrlDecode($attachmentData->data);
            // $myfile = fopen("downloads/wallpaper.asd", "w+");
            // fwrite($myfile, $data);
            // fclose($myfile);

            // $decodedData = strtr($attachmentData, array('-' => '+', '_' => '/'));
            // $attachData = self::base64UrlDecode($msgAttach->data);

            // echo '<a href="'.$attachData.'"></a>';

            $item = new Mensaje();

            // $bdy = self::base64UrlDecode($msgFull->payload->parts[1]->body->data);

            if(isset($msgFull->getPayload()->parts[1]->body->data)) {
                $bdy = self::base64UrlDecode($msgFull->payload->parts[1]->body->data);
            } elseif (isset($msgFull->getPayload()->getParts()[0]->parts[1]->body->data)) {
                $bdy = self::base64UrlDecode($msgFull->getPayload()->getParts()[0]->parts[1]->body->data);
            } elseif (isset($msgFull->getPayload()->getBody()->data)) {
                $bdy = self::base64UrlDecode($msgFull->getPayload()->getBody()->data);
            }

            $item->body = $bdy;

            switch ($msgMet->payload->headers[0]->name) {
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
            $item->attachments = $items;

            // array_push($item, $items);

            // echo '<pre class="text-white">';
            // print_r($msg->payload->parts[0]->body->data);
            // print_r($item);
            // echo '</pre>';
            
            return $item;
            
        } catch(Exception $e) {
            return [];
        }

    }

    function downloadAll($idMsg) {
        $pathDir = "downloads/";
        
        try {
            $msg = $this->service->users_messages->get($this->userId,$idMsg);
            $attachParts = $msg->getPayload()->getParts();

            for ($i=1; $i<count($attachParts); $i++) {

                $idAttach = $attachParts[$i]->body->attachmentId;
                $nameFile = $attachParts[$i]->filename;

                $attachmentData = $this->service->users_messages_attachments->get($this->userId,$idMsg,$idAttach);

                $data = self::base64UrlDecode($attachmentData->data);

                $fd = fopen($pathDir.$nameFile, "w+");
                fwrite($fd, $data);
                fclose ($fd);
                // exit;
            }
            
        } catch (Exception $e) {
            
        }
    }

    function download($param) {
        $id = $param[0]; //idMesssage
        $idAttach = $param[1];
        $nameFile = $param[2];

        $attachmentData = $this->service->users_messages_attachments->get($this->userId,$id,$idAttach);

        $pathFile = "downloads/".$nameFile;

        // echo '<pre>';
        // print_r($param);
        // echo '</pre>';

        // $data = strtr($attachmentData->getData(), array('-' => '+', '_' => '/'));
        
        $data = self::base64UrlDecode($attachmentData->data);

        try {
            $fd = fopen($pathFile, "w+");
            fwrite($fd, $data);
            fclose ($fd);
            exit;
        } catch(Exception $e) {
            return false;
        }
    }

    static function base64ToJPEG($base64_string, $content_type) { // En desuso
        $find = ["_","-"]; $replace = ["/","+"];
        $base64_string = str_replace($find,$replace,$base64_string);
        $url_str = 'data:'.$content_type.','.$base64_string;
        $base64_string = "url(".$url_str.")";
        $data = explode(',', $base64_string);
        return base64_decode( $data[ 1 ] );
    }

    static function base64UrlDecode($data) {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $data));
    }

    function isRegister() { //Verifica si el usuario ya esta registrado
        return $this->conn->isConnected();
    }

    function getAuthData() { //Obtiene la URL para Auth
        return $this->conn->getAuthData();
    }

    function createService() {
        // Inicializamos el servicio de Google Drive
        $service = new Google_Service_Gmail($this->client);
        return $service;
    }




}

?>
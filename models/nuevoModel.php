<?php 

class NuevoModel extends Model{
    protected $service;

    function __construct() {
        parent::__construct();
        $this->userId = 'me';

        $this->client = $this->conn->getClient();
        $this->service = $this->createService();
    }

    function send($content) {
        // echo '<pre>';
        // print_r($content);
        // echo '</pre>';
        $boundary = uniqid(rand(), true);
        $message = new Google_Service_Gmail_Message();
        //CREAR EL MENSAJE
        $rawMessageString = "From: <{$this->userId}>\r\n";
        $rawMessageString .= "To: <{$content['to']}>\r\n";
        $rawMessageString .= 'Subject: =?utf-8?B?' . self::base64UrlEncode($content['subject']) . "?=\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\r\n";
        $rawMessageString .= "\r\n--{$boundary}\r\n";

        $rawMessageString .= "Content-Type: text/plain; charset=utf-8\r\n"; //plain / html
        $rawMessageString .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
        // $rawMessageString .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";

        // $rawMessageString .= "{$content['body']}\r\n";
        foreach ($content['body'] as $value) {
            // $rawMessageString .= "{$content['body']}\r\n";
            $rawMessageString .= "{$value}\r\n";
        }
        $rawMessageString .= "--{$boundary}\r\n";

        if(!empty($content['attach'])) {

            foreach ($content['attach'] as $key => $file) {
                $array = explode('/', $file['tmp_name']);
                $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
                // $mimeType = finfo_file($finfo, $filePath);
                $mimeType = $file['type'];
                // $fileName = $array[sizeof($array)-1];
                $fileName = $file['name'];
                // $fileData = base64_encode(file_get_contents($file['tmp_name']));

                $rawMessageString .= "\r\n--{$boundary}\r\n";
                $rawMessageString .= 'Content-Type: '. $mimeType .'; name="'. $fileName .'";' . "\r\n";
                // $rawMessageString .= 'Content-ID: <' ."me". '>' . "\r\n";            
                $rawMessageString .= 'Content-Description: ' . $fileName . ';' . "\r\n";
                $rawMessageString .= 'Content-Disposition: attachment; filename="' . $fileName . '"; size=' . $file['size']. ';' . "\r\n";
                // $rawMessageString .= 'Content-Disposition: attachment; ';
                // $rawMessageString .= "filename=" . $fileName . "\r\n";
                $rawMessageString .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
                $rawMessageString .= chunk_split(base64_encode(file_get_contents($file['tmp_name'])), 76, "\n") . "\r\n";
                // $rawMessageString .= "$contFile\r\n";
                // $rawMessageString .= "\r\n";
                $rawMessageString .= "--{$boundary}\r\n";
            }
        }

        try {
        //Codificar el mensaje
        $rawMessage = self::base64UrlEncode($rawMessageString);
        $message->setRaw($rawMessage);

        $message = $this->service->users_messages->send($this->userId, $message);
        // print 'Mensaje con ID: ' . $message->getId() . ' enviado.';
        // return $message;
        return true;
        } catch (Exception $e) {
            return false;
        }
    }

    static function base64UrlEncode($data, $pad = null) {
        $data = str_replace(array('+', '/'), array('-', '_'), base64_encode($data));
        if(!$pad) {
            $data = rtrim($data, "=");
        }
        return $data;
    }

    function createService() {
        // Inicializamos el servicio de Google Drive
        $service = new Google_Service_Gmail($this->client);
        return $service;
    }

}

?>
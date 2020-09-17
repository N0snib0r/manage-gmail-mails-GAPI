<?php 

class NuevoModel extends Model{
    protected $service;

    function __construct() {
        parent::__construct();
        $this->userId = 'me'; // Palabra clave, hace referencia la correo del usuario registrado

        $this->client = $this->conn->getClient(); // Cliente registrado
        $this->service = $this->createService();  // Servicio principal de Gmail
    }

    function send($content) { // Envia un correo 
        $boundary = uniqid(rand(), true);
        $message = new Google_Service_Gmail_Message();
        //CREAR EL MENSAJE
        $rawMessageString = "From: <{$this->userId}>\r\n";
        $rawMessageString .= "To: <{$content['to']}>\r\n";
        $rawMessageString .= 'Subject: =?utf-8?B?' . self::base64UrlEncode($content['subject']) . "?=\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\r\n";
        $rawMessageString .= "\r\n--{$boundary}\r\n";

        $rawMessageString .= "Content-Type: text/plain; charset=utf-8\r\n"; // text/html | text/plain
        $rawMessageString .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
        foreach ($content['body'] as $value) {
            $rawMessageString .= "{$value}\r\n";
        }
        $rawMessageString .= "--{$boundary}\r\n";

        if(!empty($content['attach'])) {
            foreach ($content['attach'] as $file) {
                $mimeType = $file['type'];
                $fileName = $file['name'];

                $rawMessageString .= "\r\n--{$boundary}\r\n";
                $rawMessageString .= 'Content-Type: '. $mimeType .'; name="'. $fileName .'";' . "\r\n";
                $rawMessageString .= 'Content-Description: ' . $fileName . ';' . "\r\n";
                $rawMessageString .= 'Content-Disposition: attachment; filename="' . $fileName . '"; size=' . $file['size']. ';' . "\r\n";
                $rawMessageString .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
                $rawMessageString .= chunk_split(base64_encode(file_get_contents($file['tmp_name'])), 76, "\n") . "\r\n";
                $rawMessageString .= "--{$boundary}\r\n";
            }
        }

        try {
        //Codificar el mensaje en base64
        $rawMessage = self::base64UrlEncode($rawMessageString);
        $message->setRaw($rawMessage);
        //Enviado del correo
        $message = $this->service->users_messages->send($this->userId, $message);

        return true;
        } catch (Exception $e) {
            return false;
        }
    }

    static function base64UrlEncode($data, $pad = null) { // Codificado base64
        $data = str_replace(array('+', '/'), array('-', '_'), base64_encode($data));
        if(!$pad) {
            $data = rtrim($data, "=");
        }
        return $data;
    }

    function createService() { // Creacion del servicio
        // Inicializamos el servicio de Google Drive
        $service = new Google_Service_Gmail($this->client);
        return $service;
    }

}

?>
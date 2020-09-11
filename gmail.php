<?php
class Gmail {
    public $client;
    public function __construct($client) {
        $this->client = $client;
        $hola = '';
    }

    public static function base64url_decode($data) {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $data));
    }
    public static function base64url_encode($data, $pad = null) {
        $data = str_replace(array('+', '/'), array('-', '_'), base64_encode($data));
        if(!$pad) {
            $data = rtrim($data, "=");
        }
        return $data;
    }

    public static function createMessage($sender, $to, $subject, $messageText) {
        $message = new Google_Service_Gmail_Message();
        
        $rawMessageString = "From: <{$sender}>\r\n";
        $rawMessageString .= "To: <{$to}>\r\n";
        // $rawMessageString .= 'Subject: =?utf-8?B?' . base64url_encode($subject) . "?=\r\n";
        $rawMessageString .= 'Subject: =?utf-8?B?' . self::base64url_encode($subject) . "?=\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
        $rawMessageString .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
        $rawMessageString .= "{$messageText}\r\n";
        
        // $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
        $rawMessage = self::base64url_encode($rawMessageString);
        $message->setRaw($rawMessage);
        return $message;
    }

    public function sendMessage($service, $userId, $message) {
        $servicio = new Google_Service_Gmail_Message();///
        
        try {
            $message = $service->users_messages->send($userId, $message);
            print 'Message with ID: ' . $message->getId() . ' sent.';
            return $message;
        } catch (Exception $e) {
            print 'Ha ocurrido un error SendMe: ' . $e->getMessage();
        }
        return null;
    }

    public function createDraft($service, $user, $message) {
        $draft = new Google_Service_Gmail_Draft();
        $draft->setMessage($message);
        $service = new Google_Service_Gmail($this->client);
        
        try {
            $draft = $service->users_drafts->create($user, $draft);
            print 'Draft ID: ' . $draft->getId();
        } catch (Exception $e) {
            print 'Ha ocurrido un error CreateDraft: ' . $e->getMessage();
        }
        return $draft;
    }

    public function enviarMens($to, $subject, $messageText) {
        $service = new Google_Service_Gmail($this->client);
        $message = new Google_Service_Gmail_Message();
        $userId = "me";

        //CREAR EL MENSAJE
        $rawMessageString = "From: <{$userId}>\r\n";
        $rawMessageString .= "To: <{$to}>\r\n";
        // $rawMessageString .= 'Subject: =?utf-8?B?' . base64url_encode($subject) . "?=\r\n";
        $rawMessageString .= 'Subject: =?utf-8?B?' . self::base64url_encode($subject) . "?=\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
        $rawMessageString .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
        $rawMessageString .= "{$messageText}\r\n";

        // $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
        $rawMessage = self::base64url_encode($rawMessageString);
        $message->setRaw($rawMessage);
        // return $message;

        //ENVIAR MENSAJE
        try {
            $message = $service->users_messages->send($userId, $message);
            print 'Mensaje con ID: ' . $message->getId() . ' enviado.';
            return $message;
        } catch (Exception $e) {
            print 'Ha ocurrido un error envMens: ' . $e->getMessage();
        }
        
    }

    public function readLabels() {
        $service = new Google_Service_Gmail($this->client);
        // Print the labels in the user's account.
        $user = 'me';
        $results = $service->users_labels->listUsersLabels($user);

        $the_html = "";
        if (count($results->getLabels()) == 0) {
            // print "No labels found.\n";
            $the_html .= "<p>No hay etiquetas encontradas</p>";
        } else {
            // print "Labels:\n";
            $the_html .= "<p>Etiquetas: </p>";
            foreach ($results->getLabels() as $label) {
                // printf("- %s\n", $label->getName());
                $the_html .= "<p>". $label->getName() ."</p>";
            }
        }
        return $the_html;
    }

    public function listMessages($textKey="", $labels = "INBOX", $maxRes = 20, $incSpam = false) {
        $service = new Google_Service_Gmail($this->client);
        // Print the labels in the user's account.
        $userId = 'me';

        $pageToken = NULL;
        $messages = array();

        $opt_param = array( 'includeSpamTrash' => $incSpam,
                            'labelIds' => $labels,
                            'maxResults' => $maxRes);
        if($textKey != "") $opt_param['q'] = $textKey; 

        // $opt_param = [];
        // $opt_param['includeSpamTrash'] = false;
        // $opt_param['labelIds'] = 'INBOX';
        // $opt_param['maxResults'] = 3;
        // $opt_param['q'] = null;
        echo var_dump($opt_param).'<br>';

        // $i = 0;
        // do {
            // if($i==5) break;
            // $i++;
            try {
                if($pageToken) {
                    $opt_param['pageToken'] = $pageToken;
                }
                $messagesResponse = $service->users_messages->listUsersMessages($userId, $opt_param);
                if ($messagesResponse->getMessages()) {
                    $messages = array_merge($messages, $messagesResponse->getMessages());
                    $pageToken = $messagesResponse->getNextPageToken();

                    return $messages;
                    // $this->readMessages($messages);
                }
            } catch (Exception $e) {
                print 'Ha ocurrido un error: ' . $e->getMessage();
            }
        // } while ($pageToken);
        
        // foreach ($messages as $message) {
        //     // if($i < 10) {
        //         $i++;
        //         print $i.' Message with ID: ' . $message->getId() . '<br/>';
        //         $msg = $service->users_messages->get($userId, $message->getId());
        //         $bdy = self::base64url_decode($msg->getPayload()->parts[1]->body->data);
        //         // echo '<div>'.$bdy.'</div>';
        //         // echo '<pre>'.$bdy.'</pre>';
        //         echo '<p>'.$bdy.'</p>';
        //         // echo '<pre>'.var_export($msg->payload->parts[1]->body->data, true).'</pre>';
        //     // }
        // }
    }

    public function readMessages($textKey, $labels, $maxRes, $incSpam) {
        $service = new Google_Service_Gmail($this->client);
        $userId = 'me';
        $optParamGet = array('format' => 'metadata',
                            'metadataHeaders' => array('from'));

        // $optParamGet['format'] = 'metadata';
        // $optParamGet['metadataHeaders'] = array('subject', 'from', 'to');
        // echo '<pre>';
        // print_r($optParamGet);
        // echo '</pre>';
        // echo '<br> AQUI: '.var_dump($optParamGet);

        $idMessages =  $this->listMessages($textKey, $labels, $maxRes, $incSpam);

        try {
            foreach ($idMessages as $message) {
                print 'Mensaje con ID: ' . $message->getId() . '<br>';
                $msg = $service->users_messages->get($userId, $message->getId(), $optParamGet);
                // $bdy = self::base64url_decode($msg->getPayload()->parts[1]->body->data);
                // echo '<p>'.$bdy.'</p>';
                // echo '<pre>'.var_export($msg, true).'</pre>';
                // echo '<br>';
                echo '<pre>';
                print_r($msg->getPayload()->getHeaders()[0]->value);
                // print_r($msg->payload->headers[13]);
                // print_r($msg->getPayload()->getHeaders());
                // echo '- ';
                // print_r($msg->getPayload()->getHeaders()[21]->getValue());
                echo '</pre>';
            }
        } catch (Exception $e) {
            print 'Ha ocurrido un error: ' . $e->getMessage();
        }
    }

    public function listarMens($textKey, $labels, $maxRes) {
        //Creamos el Objeto del Servicio de Gmail
        $service = new Google_Service_Gmail($this->client);
        $userId = 'me';
        $pageToken = NULL;
        $messages = array();

        //Parametros de list()
        $opt_param = array( 'includeSpamTrash' => false,
                            'labelIds' => 'INBOX',
                            'maxResults' => 5);
        
        if($textKey != "") $opt_param['q'] = $textKey;
        if($labels != "") $opt_param['labelIds'] = $labels;
        if($maxRes != "") $opt_param['maxResults'] = $maxRes;
        // if($incSpam != null) $opt_param['includeSpamTrash'] = $maxRes;

        echo var_dump($opt_param).'<br>';
        echo '<pre>';
        print_r($opt_param);
        echo '</pre>';
        
        //Parametros de get()
        $optParamGet = array('format' => 'metadata',
                            'metadataHeaders' => array('from','subject'));

        try {
            if($pageToken) {
                $opt_param['pageToken'] = $pageToken;
            }
            $messagesResponse = $service->users_messages->listUsersMessages($userId, $opt_param);
            if ($messagesResponse->getMessages()) {
                $messages = array_merge($messages, $messagesResponse->getMessages());
                $pageToken = $messagesResponse->getNextPageToken();

                foreach ($messages as $message) {
                    print 'Mensaje con ID: ' . $message->getId() . '<br>';
                    $msg = $service->users_messages->get($userId, $message->getId(), $optParamGet);
                    // $bdy = self::base64url_decode($msg->getPayload()->parts[1]->body->data);
                    // echo '<p>'.$bdy.'</p>';
                    // echo '<pre>'.var_export($msg, true).'</pre>';
                    // echo '<br>';
                    echo '<pre>';
                    print_r($msg->getPayload()->getHeaders()[0]->value);
                    echo ' || ';
                    print_r($msg->getPayload()->getHeaders()[1]->value);
                    // print_r($msg->payload->headers[13]);
                    // print_r($msg->getPayload()->getHeaders());
                    // echo '- ';
                    // print_r($msg->getPayload()->getHeaders()[21]->getValue());
                    echo '</pre>';
                }
                // $this->readMessages($messages);
            }
        } catch (Exception $e) {
            print 'Ha ocurrido un error: ' . $e->getMessage();
        }
    }
    public function mostrarMens($textKey, $labels, $maxRes) {
        //Creamos el Objeto del Servicio de Gmail
        $service = new Google_Service_Gmail($this->client);
        $userId = 'me';
        $pageToken = NULL;
        $messages = array();

        //Parametros de list()
        $opt_param = array( 'includeSpamTrash' => false,
                            'labelIds' => 'INBOX',
                            'maxResults' => 5);
        
        if($textKey != "") $opt_param['q'] = $textKey;
        if($labels != "") $opt_param['labelIds'] = $labels;
        if($maxRes != "") $opt_param['maxResults'] = $maxRes;
        // if($incSpam != null) $opt_param['includeSpamTrash'] = $maxRes;

        echo var_dump($opt_param).'<br>';
        echo '<pre>';
        print_r($opt_param);
        echo '</pre>';
        
        //Parametros de get()
        // $optParamFull = array('format' => 'full');

        $optParamMet = array('format' => 'metadata',
                            'metadataHeaders' => array('from', 'subject'));

        try {
            if($pageToken) {
                $opt_param['pageToken'] = $pageToken;
            }
            $messagesResponse = $service->users_messages->listUsersMessages($userId, $opt_param);
            if ($messagesResponse->getMessages()) {
                $messages = array_merge($messages, $messagesResponse->getMessages());
                $pageToken = $messagesResponse->getNextPageToken();

                foreach ($messages as $message) {
                    print 'Mensaje con ID: ' . $message->getId() . '<br>';

                    $msgMet = $service->users_messages->get($userId, $message->getId(), $optParamMet);
                    $msgFull = $service->users_messages->get($userId, $message->getId());

                    //Obtiene la data del body de diferentes formas
                    if(isset($msgFull->getPayload()->parts[1]->body->data)) {
                        echo "1";
                        $bdy = self::base64url_decode($msgFull->getPayload()->parts[1]->body->data);
                    } elseif (isset($msgFull->getPayload()->getParts()[0]->parts[1]->body->data)) {
                        echo "2";
                        $bdy = self::base64url_decode($msgFull->getPayload()->getParts()[0]->parts[1]->body->data);
                    } elseif (isset($msgFull->getPayload()->getBody()->data)) {
                        echo "3";
                        $bdy = self::base64url_decode($msgFull->getPayload()->getBody()->data);
                    }

                    // echo '<pre>'.var_export($msg, true).'</pre>';
                    // echo '<br>';
                    echo '<pre>';
                    print_r($msgMet->getPayload()->getHeaders()[1]->value. " || ");
                    print_r($msgMet->getPayload()->getHeaders()[0]->value );
                    // print_r($msg->payload->headers[13]);
                    // print_r($msg->getPayload()->getHeaders());
                    // echo '- ';
                    // print_r($msgFull->getPayload()->getParts()[0]->parts[1]->body->data);
                    // print_r($msgFull->getPayload()->parts[1]->body->data);
                    print_r($msgFull->getPayload()->getBody()->data);

                    echo '</pre>';
                    echo '<pre>'.$bdy.'</pre>';
                    echo '<hr>';
                }
                // $this->readMessages($messages);
            }
        } catch (Exception $e) {
            print 'Ha ocurrido un error: ' . $e->getMessage();
        }
    }
}

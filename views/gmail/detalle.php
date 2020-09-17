<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
</head>
<body class="bg-secondary">
    
    <?php require 'views/header.php'; ?>

    <div class="container py-3">

        <!-- <div class="text-center py-3 rounded-lg w-70 mx-auto bg-light text-light"> -->
            <div><?php $this->mensaje; ?></div>

            <div class="justify-content-center text-light">
                <div class="row">
                    <div class="col col-md-auto h3">
                        <?php echo $this->msg->subject; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col font-weight-bold">
                        <?php echo $this->msg->from; ?>
                    </div>
                </div>
                <hr class="bg-light">
            </div>

            <div class="row justify-content-center bg-secondary">
                <div class="col col-lg-auto col-sm-10 bg-light rounded p-3">
                    <?php echo $this->msg->body; ?>
                    <hr class="bg-secondary my-2">
                    
                    <ul class="list-group ">

                        <li class="list-group-item text-white font-weight-bolder bg-secondary">
                            <span class="mr-auto"><?php echo count($this->msg->attachments) . " Archivos adjuntos" ?></span>
                            <a class="btn btn-outline-success ml-1 border-0" title="Descargar todos los archivos" href="<?php echo constant('URL').'gmail/downloadAll/'.$this->msg->idMsg ?>">
                                <i class="fa fa-download"></i>
                            </a>
                        </li>

                        <?php foreach ($this->msg->attachments as $key => $data) { ?>
                            <a class="list-group-item list-group-item-action" href="<?php echo constant('URL')."gmail/downloadFile/".$this->msg->idMsg."/".$data->idAttach."/".$data->nameFile ?>" target="_blank" title="Descargar"><?php echo $data->nameFile ?></a>
                            <!-- <a class="btn btn-outline-success btn-sm btn-block" href="<?php //echo constant('URL')."gmail/downloadFile/".$this->msg->idMsg."/".$data->idAttach."/".$data->nameFile ?>" target="_blank"><?php echo $data->nameFile ?></a><br> -->
                        <?php } ?>
                    </ul>

                </div>
            </div>
                
        <!-- </div> -->
        <?php 
            //TEST
            // echo "<pre>";
            // print_r($this->file);
            // echo "</pre>";
        ?>
        
    </div>

    <?php require 'views/footer.php'; ?>

</body>
</html>
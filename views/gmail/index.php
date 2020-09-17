<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
</head>
<body class="bg-light">
    
    <?php require 'views/header.php'; ?>

    <div class="container py-3">
        <div class="w-70">
            
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" colspan="4" class="py-0 rounded-top border-top-0">
                            <nav class="navbar navbar-dark bg-dark">
                                <ul class="navbar-nav">                    
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Bandeja de entrada
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="<?php echo constant('URL') ?>gmail/listMessages/INBOX">Bandeja de entrada</a>
                                            <a class="dropdown-item" href="<?php echo constant('URL') ?>gmail/listMessages/SENT">Enviados</a>
                                            <a class="dropdown-item" href="<?php echo constant('URL') ?>gmail/listMessages/SPAM">Spam</a>
                                            <a class="dropdown-item" href="<?php echo constant('URL') ?>gmail/listMessages/DRAFT">Borradores</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="<?php echo constant('URL') ?>nuevo">Redactar un correo</a>
                                            <!-- <div class="dropdown-divider"></div> -->
                                        </div>
                                </ul>
                                
                                <!-- Formulario de busqueda -->
                                <form  action="<?php echo constant('URL'); ?>gmail/searchM" method="POST">
                                    <div class="input-group">
                                        <input name="inpSearch" type="search" class="form-control bg-light border-light" placeholder="Buscar correo">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-light pb-1" type="submit">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                
                            </nav>
                        </th>
                    </tr>

                    <tr class="text-center">
                        <th scope="col">#</th>
                        <th scope="col">Remitente</th>
                        <th scope="col">Asunto</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-alumnos" class="text-center">

                    <?php
                        foreach($this->messages as $key => $row) {
                            $msg = $row;
                    ?>
                    <tr>
                        <td class="align-middle" scope="row"><?php echo $key+1 ?></td>

                        <td class="align-middle text-left"><?php echo $msg->from ?></td>
                        <td class="align-middle text-left text-truncate" style="max-width: 300px" title='<?php echo $msg->subject ?>' ><?php echo $msg->subject ?></td>
                        <!-- <td><?php //echo $file->id ?></td> -->
                        
                        <td class="navbar">
                            <li class="btn-group ml-auto mr-3">
                                <a href="<?php echo constant('URL').'gmail/readMessage/'.$msg->idMsg; ?>" class="btn btn-outline-secondary btn-sm">Abrir</a>

                                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only"></span>
                                </button>
                                <div class="dropdown-menu">
                                
                                    <a class="dropdown-item" href="<?php echo constant('URL').'gmail'.$msg->id; ?>">Opcion 1</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo constant('URL').'gmail'.$msg->id; ?>">Eliminar</a>
                                </div>
                            </li>
                        </td>
                    </tr>
                    <?php } ?>
                    
                </tbody>
            </table>
            
            <div class="center"><?php echo $this->mensaje; ?></div>
            


        </div>
        <?php
            //TEST | Ver el objeto de cada archivo
        //     echo '<pre>';
        //     print_r($this->files);
        //     echo '</pre>';
        ?>
        
    </div>

    <?php require 'views/footer.php'; ?>

</body>
</html>
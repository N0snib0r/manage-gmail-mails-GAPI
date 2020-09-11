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

            <nav class="navbar navbar-dark bg-dark rounded-top">
                <ul class="navbar-nav">                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Mi drive
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="<?php echo constant('URL') ?>gmail">Opcion 1</a>
                            <a class="dropdown-item" href="<?php echo constant('URL') ?>gmail">Obcion 2</a>
                            <div class="dropdown-divider"></div>
                            <!-- <a class="dropdown-item" href="#">Something else here</a> -->
                        </div>
                    </li>
                </ul>
            </nav>
            
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
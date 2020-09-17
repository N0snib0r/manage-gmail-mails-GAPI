<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body class="bg-light">
    <?php require 'views/header.php'; ?>
    <div class="container py-3">

        <div class="row justify-content-center">
            <div class="col col-lg-6">
                <div class="alert alert-secondary"><?php echo $this->mensaje; ?></div>
                <form action="<?php echo constant('URL') ?>nuevo/sendMail" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Para</label>
                        <input name="inpTo" type="email" class="form-control"placeholder="name@example.com" required>
                    </div>

                    <div class="form-group">
                        <label>Asunto</label>
                        <input name="inpSubject" type="text" class="form-control"placeholder="Asunto">
                    </div>

                    <div class="form-group">
                        <label>Contenido</label>
                        <textarea name="txaBody" class="form-control" rows="6"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Adjuntar</label>
                        <input name="inpFile[]" class="form-control py-1" type="file" multiple>
                    </div>

                    <input class="btn btn-outline-primary" type="submit" value="Enviar">
                </form>
            </div>
        </div>

    </div>
    <?php require 'views/footer.php'; ?>
</body>

</html>
<?php

use services\oauth\VKontakteOAuth2Service;

/* @var $oauthService VKontakteOAuth2Service */
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP-UP register</title>
    <link rel="stylesheet" href="<?php echo '/frontend/web/css/login.css' ?>">
    <link rel="stylesheet" href="<?php echo '/frontend/web/css/socials.css' ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="utf-8"/>
</head>
<body>
<div class="registration-cssave">
    <form method="post">
        <h3 class="text-center">Регистрация на сайте</h3>
        <div class="form-group">
            <input class="form-control item" type="text" name="name" placeholder="Имя" value="<?php echo $name; ?>" required>
        </div>
        <div class="form-group">
            <input class="form-control item" type="email" name="email" placeholder="Email" value="<?php echo $email; ?>" required>
        </div>
        <div class="form-group">
            <input class="form-control item" type="password" name="password" minlength="6" placeholder="Пароль"
                   value="<?php echo $password; ?>" required>
        </div>
        <div class="form-group">
            <input class="form-control item" type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
        </div>

        <div class="form-group">
            <?php
            if (!empty($errors) && is_array($errors)) {
                foreach ($errors as $error) { ?>
                    <p style="color: red"><?php echo $error; ?></p><br>
                <?php }
            } ?>
        </div>

        <div class="form-group">
            <input class="btn btn-primary btn-block create-account" type="submit" name="submit" value="Регистрация">
        </div>

        <div class="form-group">
            <a class="btn btn-primary btn-block create-account" href="<?php echo $oauthService->getLink(); ?>">
                <i class="fa fa-vk"></i>
            </a>
        </div>
    </form>

    <?php require_once ROOT .'/frontend/views/captcha/recaptcha-v3.php'; ?>

</div>
</body>
</html>
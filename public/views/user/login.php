<?php

use app\dictionaries\AuthDictionary;

?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP-UP login</title>
    <link rel="stylesheet" href="<?php echo '/../../css/login.css' ?>">
    <link rel="stylesheet" href="<?php echo '/../../css/socials.css' ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="utf-8"/>
</head>
<body>
<div class="registration-cssave">
    <form method="post">
        <h3 class="text-center">Форма входа</h3>
        <div class="form-group">
            <input class="form-control item" type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input class="form-control item" type="password" name="password" minlength="6" placeholder="Пароль"
                   required>
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
            <input class="btn btn-primary btn-block create-account" type="submit" name="submit" value="Вход в аккаунт">
        </div>

        <div class="form-group">
            <a class="btn btn-primary btn-block create-account" href="/user/login/<?php echo AuthDictionary::VK_CLIENT_NAME; ?>">
                <i class="fa fa-vk"></i>
            </a>
            <a class="btn btn-primary btn-block create-account" href="/user/login/<?php echo AuthDictionary::MAILRU_CLIENT_NAME; ?>">
                mail.ru
            </a>
        </div>
    </form>

    <?php require_once ROOT .'/public/views/captcha/recaptcha-v3.php'; ?>

</div>
</body>
</html>
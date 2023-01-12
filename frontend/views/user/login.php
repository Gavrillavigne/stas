<!DOCTYPE html>
<html>
<head>
    <title>PHP-UP login</title>
    <link rel="stylesheet" href="<?php echo '/frontend/web/css/login.css' ?>">
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
    </form>

    <?php require_once ROOT .'/frontend/views/captcha/recaptcha-v3.php'; ?>

</div>
</body>
</html>
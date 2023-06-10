<?php
/** @var $files array */
/** @var $errors array */

$styles = [
    0 => '',                // simple
    1 => 'color: red',      // secure
    2 => 'color: purple',   // expiration
    3 => 'color: green',    // secure + expiration
];
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>Personal Cabinet</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="../../frontend/web/js/share-file.js"></script>
    <meta charset="utf-8"/>
</head>
<body>
<h2>Кабинет пользователя</h2>

<div>
    <p>Список загруженных файлов:</p>
    <hr>
    <?php foreach ($files as $id => $file) {
        $url = $path = !empty($file['filePath']) ? getenv('DOMAIN') . '/' . $file['filePath'] : '';
        $title = $desc = $file['fileName'] ?? '';

        $isSecure = $file['isSecure'];
        $isExpiration = !empty($file['expirationTime']);

        if ($isSecure && $isExpiration) {
            $color = 3;
        } elseif ($isExpiration) {
            $color = 2;
        } elseif ($isSecure) {
            $color = 1;
        } else {
            $color = 0;
        }

        $url = $path = $isSecure || $isExpiration
            ? getenv('DOMAIN') . '/' . 'files/download/' . $id
            : $path;
        ?>

        <span style="<?php echo $styles[$color]; ?>"> <?php echo $file['fileName']; ?></span>
    <form id="delete-<?php echo $id; ?>" method="post">
        <input type="hidden" name="delete" value="<?php echo $id; ?>">
        <input type="submit" value="Удалить"/>

        <?php if ($isSecure || $isExpiration) { ?>
            </form>
            <form id="download-<?php echo $id; ?>" method="post">
                <input type="hidden" name="download" value="<?php echo $id; ?>">
                <input type="submit" value="Скачать"/>
            </form>
        <?php } else { ?>
            <a href="<?php echo $file['filePath']; ?>" download="<?php echo $file['fileName']; ?>">Скачать</a>
        <?php } ?>
        <!--            <a href="--><?php //echo $file['filePath']; ?><!--" target="_blank">Открыть</a>-->
        <a href="<?php echo 'cabinet/open-file/' . $file['filePath']; ?>" target="_blank">Открыть</a>
        <?php if (!$isSecure) { ?>
            </form>
        <?php } ?>
        <br>
        <div>
            <span>Поделиться файлом:</span><br>
            <a onclick='<?php echo 'Share.vkontakte("' . $url . '","' . $title . '","' . $path . '","' . $desc . '")'; ?>'>
                <i class="fa fa-vk"></i></a>
            <a onclick='<?php echo 'Share.facebook("' . $url . '","' . $title . '","' . $path . '","' . $desc . '")'; ?>'>
                <i class="fa fa-facebook"></i></a>
            <a onclick='<?php echo 'Share.mailru("' . $url . '","' . $title . '","' . $path . '","' . $desc . '")'; ?>'>
                {MailRu}</a>
            <a onclick='<?php echo 'Share.odnoklassniki("' . $url . '","' . $title . '","' . $path . '","' . $desc . '")'; ?>'>
                <i class="fa fa-odnoklassniki"></i></a>
            <a onclick='<?php echo 'Share.twitter("' . $url . '","' . $title . '","' . $path . '","' . $desc . '")'; ?>'>
                <i class="fa fa-twitter"></i></a>
        </div>
        <hr>
    <?php } ?>
</div>
<hr>
<p>Загрузить новый файл:</p>
<form id="upload" method="post" enctype="multipart/form-data">
    Выберите файл:
    <input type="file" name="filename" size="10"/><br/>
    <input type="checkbox" name="secure" value="1"/>Разрешить скачивание только владельцу<br/>
    <input type="checkbox" name="expiration" value="1"/>Разрешить скачивание в течение 10ч после загрузки<br/>
    <br/>
    <input type="submit" value="Загрузить"/>
</form>

<?php if (!empty($errors)) { ?>
    <div>
        <p style="color: red">Errors:</p>
        <?php foreach ($errors as $error) { ?>
            <p style="color: red"><?php echo $error; ?></p><br>
        <?php } ?>
    </div>
<?php } ?>

</body>
</html>
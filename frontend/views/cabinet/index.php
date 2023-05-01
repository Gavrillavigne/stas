<?php
/** @var $files array */
/** @var $errors array */
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>Personal Cabinet</title>
    <meta charset="utf-8"/>
</head>
<body>
<h2>Кабинет пользователя</h2>

<div>
    <p>Список загруженных файлов:</p>
    <?php foreach ($files as $id => $file) {
        echo $file['fileName'] ?>
        <form id="delete-<?php echo $id; ?>" method="post">
            <input type="hidden" name="delete" value="<?php echo $id; ?>">
            <input type="submit" value="Удалить"/>
            <a href="<?php echo $file['filePath']; ?>" download="<?php echo $file['fileName']; ?>">Скачать</a>
<!--            <a href="--><?php //echo $file['filePath']; ?><!--" target="_blank">Открыть</a>-->
            <a href="<?php echo 'cabinet/open-file/' . $file['filePath']; ?>" target="_blank">Открыть</a>
        </form>
        <br/>
    <?php } ?>
</div>
<hr>
<p>Загрузить новый файл:</p>
<form id="upload" method="post" enctype="multipart/form-data">
    Выберите файл:
    <input type="file" name="filename" size="10"/><br/><br/>
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
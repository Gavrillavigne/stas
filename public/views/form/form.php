<!DOCTYPE html>
<html>
<head>
    <title>Forms</title>
    <meta charset="utf-8"/>
</head>
<body>
<h2>Загрузка файла</h2>
<form id="form">
    Выберите файл: <input type="file" name="filename" size="10"/><br/><br/>
    <input type="submit" value="Загрузить"/>
</form>

<!-- Количество загруженных байт -->
<p id="uploaded"></p>

<!-- Прогресс-бар -->
<p>
    <progress max="100" value="0" id="progress"></progress>
</p>

<!-- Результат загрузки файла -->
<p id="result"></p>

<script src="../../js/ajax.js"></script>
</body>
</html>
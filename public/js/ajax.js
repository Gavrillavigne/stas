let progress = document.getElementById("progress"),
    uploaded = document.getElementById("uploaded"),
    result = document.getElementById("result"),
    maxFileSize = 30000;

document.getElementById("form").onsubmit = function (e) {
    e.preventDefault();
    let input = this.elements.filename;
    let file = input.files[0];

    // проверка на размер файла
    if (file.size >= maxFileSize) {
        result.innerHTML = 'Слишком большой размер файла';
        return false;
    }

    if (file) {
        upload(file);
    }
}

function upload(file) {
    let ajax = new XMLHttpRequest();
    ajax.upload.onprogress = function (event) {
        uploaded.innerHTML = 'Загружено ' + event.loaded + ' из ' + event.total + ' байт';
        progress.setAttribute('max', event.total);
        progress.value = event.loaded;
    }

    ajax.onload = ajax.onerror = function () {
        if (this.status == 200) {
            result.innerHTML = 'Файл успешно загружен';
        } else {
            result.innerHTML = 'Не удалось загрузить файл';
        }
    }

    let formData = new FormData();
    formData.append("filename", file);

    ajax.open("POST", "actionForm.php", true);
    ajax.send(formData);
}
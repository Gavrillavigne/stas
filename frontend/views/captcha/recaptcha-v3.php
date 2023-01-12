<script src="https://www.google.com/recaptcha/api.js?render=<?php echo getenv('RECAPTCHA3_SITE_KEY'); ?>"></script>
<script>
    grecaptcha.ready(function () {
        grecaptcha.execute('<?php echo getenv('RECAPTCHA3_SITE_KEY')?>', {action: 'submit'}).then(function (token) {
            // console.log(token);
            document.getElementById('g-recaptcha-response').value = token;
        });
    });
</script>
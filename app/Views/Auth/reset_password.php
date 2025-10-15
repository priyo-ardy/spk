<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery Request</title>
</head>

<body>
    Hai, <strong><?= $name ?></strong>
    <br>
    <p>
        Anda baru saja melakukan permintaan perubahan password pada tangal <strong><?= $date ?></strong>, klik tautan dibawah ini untuk melakukan reset password anda.
    </p>
    <p>
        <a href="<?= base_url() . 'recover-password/' . $token ?>">Reset Password</a>
    </p>
    <p>
        Apabila anda tidak melakukan permintaan perubahan password, abaikan email ini.
    </p>
    <br>
    <br>
    <p>
        Terima kasih.<br>
        <strong>System Administrator</strong><br>
        PT. Schlemmer Automotive Indonesia
    </p>

</body>

</html>
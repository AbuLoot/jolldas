<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Новые обновления на вашем аккаунте</title>
</head>
<body>
  <h1>Jolldas Cargo</h1>
  <h2>
    Уважаемый <?php echo $trackUser->name . ' ' . $trackUser->lastname; ?>,<br>
    Посылка с трек-кодом: <?php echo $trackCode; ?> поступил на склад.
  </h2>
  <h4>Дата: <?php echo date('Y-m-d'); ?></h4><br>
  <h4>Время: <?php echo date('G:i'); ?></h4>
  <p><a href="https://jolldas.kz/">www.jolldas.kz</a></p>
</body>
</html>
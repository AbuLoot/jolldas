<!DOCTYPE html>
<html>
<head>
  <title>Filemanager</title>

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
</head>
<body>
  <div style="min-width:725px;height:600px;" class="table-responsive">
    <div id="fm"></div>
  </div>

  <script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
</body>
</html>
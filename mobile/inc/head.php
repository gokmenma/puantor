<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title><?php echo $title ?? "Puantor Mobil | Puantaj Takip Uygulaması"; ?></title>

  <!-- PWA & Favicon -->
  <link rel="icon" href="http://puantor.site/static/favicon.ico" type="image/x-icon" />
  <link rel="manifest" href="manifest.json">

  <!-- CSS CDN Libraries (Consistent with Desktop) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <!-- Google Fonts (Inter Font Pairings) -->
  <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

  <!-- Mobile Custom Premium CSS -->
  <link rel="stylesheet" href="./css/mobile.css?v=<?php echo filemtime(__DIR__ . '/../css/mobile.css'); ?>" />

  <!-- jQuery (Required for shared logic/APIs) -->
  <script src="http://puantor.site/dist/js/jquery.3.7.1.min.js"></script>

  <style>
    :root {
      --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, sans-serif;
    }
  </style>
</head>

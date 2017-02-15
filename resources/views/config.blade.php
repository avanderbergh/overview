<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Overview - Config</title>
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.3.1/css/bulma.css" integrity="sha256-E3NFAdoEdCZvMUGANNJ8cM/gRzIO+9Tx9QGCCzYHquw=" crossorigin="anonymous" />
</head>
<body>
<div id="config">
<h1>Hello</h1>
</div>
@include('partials.phpvars')
<script src="{{ mix('/js/app.js') }}"></script>
<script src="https://use.fontawesome.com/4da356da52.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Student Overview</title>
        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.3.1/css/bulma.css" integrity="sha256-E3NFAdoEdCZvMUGANNJ8cM/gRzIO+9Tx9QGCCzYHquw=" crossorigin="anonymous" />
    </head>
    <body>
        <div id="app">
            <nav class="nav has-shadow">
                <div class="nav-left">
                    <a class="nav-item">
                        <h1 class="title">Overview</h1>
                    </a>
                </div>
                <div class="nav-right">
                    <div class="nav-item">
                        <p class="control has-icon">
                            <input class="input" type="text" v-model="search" placeholder="Search">
                            <span class="icon is-small">
                                <i class="fa fa-user"></i>
                            </span>
                        </p>
                    </div>
                </div>
            </nav>
            <student-list :search="search"></student-list>
        </div>
        @include('partials.phpvars')
        <script src="{{ mix('/js/app.js') }}"></script>
        <script src="https://use.fontawesome.com/4da356da52.js"></script>
    </body>
</html>
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
            <div class="modal" :class="{'is-active': downloadModal.show}">
                <div v-on:click="downloadModal.show = false" class="modal-background"></div>
                <div class="modal-card">
                    <div class="modal-card-body has-text-centered">
                        <p>The download will start automatically. Depending on the number of students and courses, the download might take a few minutes to start...</p>
                    </div>
                </div>
                <button v-on:click="downloadModal.show = false" class="modal-close"></button>
            </div>
            <nav class="nav has-shadow">
                <div class="nav-left">
                    <a class="nav-item">
                        <h1 class="title">Overview</h1>
                    </a>
                </div>
                <div class="nav-right">
                    <div class="nav-item">
                        <a href="/api/groups/{{ $realm_id }}/students/export" v-on:click="downloadModal.show = true" class="button is-primary is-outlined" download>

                        </a>
                    </div>
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
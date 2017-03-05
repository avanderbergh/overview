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
    <section class="hero is-primary is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    Overview
                </h1>
                <h2 class="subtitle">
                    Configuration
                </h2>
            </div>
        </div>
    </section>
    <section class="is-hero is-medium">
        <div class="hero-body">
            <div class="level">
                <div class="level-left">
                    <h3 class="level-item title is-3">Please enter your Schoology API Credentials</h3>
                </div>
                <div class="level-right">
                    <a href="https://{{ session('schoology')['domain'] }}/system_settings/integration/api" target="_blank" class="level-item">Request your API Keys</a>
                </div>
            </div>
            <div class="control is-horizontal">
                <div class="control-label">
                    <label class="label">Consumer Key</label>
                </div>
                <p class="control">
                    <input class="input" type="text" v-model="school.api_key" placeholder="Enter your API Key">
                </p>
                <div class="control-label">
                    <label class="label">Consumer Secret</label>
                </div>
                <p class="control">
                    <input class="input" type="text" v-model="school.api_secret" placeholder="Enter your API Secret">
                </p>
            </div>
            <div class="level">
                <div class="level-left">
                </div>
                <div class="level-right">
                    <p class="level-item">
                        <p class="control">
                            <a class="button is-primary is-large" :class="{'is-disabled': button.saveApiKeys.disabled, 'is-loading': button.saveApiKeys.loading}" v-on:click="saveApiKeys()">Save</a>
                        </p>
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section class="hero is-info">
        <div class="hero-body">
            <div class="level">
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">User Quota</p>
                        <p class="title" v-text="school.user_quota"></p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <a class="title">Update Subscription</a>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Expiry date</p>
                        <p class="title" v-text="school.valid_until"></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <pre>@{{ $data }}</pre>
</div>
@include('partials.phpvars')
<script src="{{ mix('/js/config.js') }}"></script>
<script src="https://use.fontawesome.com/4da356da52.js"></script>
</body>
</html>
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
            <p>
                To use Overview, you will need to create a new Schoology user (the Observer Account) that will need to be enrolled in all courses as an admin. Once the user is created, sign in as that user and generate new API access keys. You can do this in System Settings -> Integration. In the API Tab, click on Request API Keys. Once you've generated the API Keys, copy and paste them in the fields below.<br>
                You do <strong>not</strong> need to enroll the Observer account into any courses as the Overview app will do this automatically.<br><br>
            </p>
            <p>
                To see an overview of student course completion, first create a group and add the students as members and the supervisor / mentor as an admin . Next install the Overview app in the group (make sure to check Course Admins only). Now the course admin can open the app by opening the group and clicking on Overview in the left column menu.<br><br>
            </p>
            <div class="level">
                <div class="level-left">
                    <h3 class="level-item title is-3">Please enter your Schoology API Credentials</h3>
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
                        <p>To update your subscription, please email <a href="mailto:adriaan@engagenie.com?subject=Overview Subscription">adriaan@engagenie.com</a></p>
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
</div>
@include('partials.phpvars')
<script src="{{ mix('/js/config.js') }}"></script>
<script src="https://use.fontawesome.com/4da356da52.js"></script>
</body>
</html>
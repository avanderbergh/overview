require('./bootstrap');

const config = new Vue({
    el: '#config',
    data: {
        school: {
            'api_key': null,
            'api_secret': null,
        },
        button: {
            saveApiKeys: {
                'disabled': false,
                'loading': false,
            }
        },
    },
    mounted() {
        var that = this;
        axios.get('/api/schools')
            .then(function (result) {
                that.school = result.data
            })
    },
    methods: {
        saveApiKeys() {
            var that = this;
            this.button.saveApiKeys.disabled = true;
            this.button.saveApiKeys.loading = true;
            axios.post('/api/schools/api-keys', that.school)
                .then(function (response) {
                    that.school = response.data;
                    that.button.saveApiKeys.disabled = false;
                    that.button.saveApiKeys.loading = false;
                })
        }
    }
})
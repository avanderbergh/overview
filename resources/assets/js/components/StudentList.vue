<template>
    <div class="container" style="padding-top:20px;">
        <div class="modal" :class="{'is-active': downloadModal.show}">
            <div v-on:click="downloadModal.show = false" class="modal-background"></div>
            <div class="modal-card">
                <div class="modal-card-body has-text-centered">
                    <p>The download will start automatically. Depending on the number of students and courses, the download might take a few minutes to start...</p>
                </div>
            </div>
            <button v-on:click="downloadModal.show = false" class="modal-close"></button>
        </div>
        <div v-show="userQuotaExceededNotification.show" class="notification is-warning">
            You've exceeded your user quota! Please ask your Schoology Administrator to add more users and reload the page.
        </div>
        <nav class="level">
            <div class="level-left">
                <div class="level-item">
                    <p class="control">
                        <span class="select">
                            <select v-model="searchYear">
                                <option value="0">Graduation Year</option>
                                <option v-for="year in gradYears">{{ year }}</option>
                            </select>
                        </span>
                    </p>
                </div>
            </div>
            <div class="level-right">
                <div class="level-item" v-show="total_students > 0 && students.length == total_students">
                    <a :href="'/api/groups/' + realm_id  + '/students/export'" v-on:click="downloadModal.show = true" class="button is-primary is-outlined">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>&nbsp;Export
                    </a>
                </div>
            </div>
        </nav>
        <div class="has-text-centered" v-show="total_students == 0" style="padding-top:15em;">
            <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
        </div>
        <div class="box has-text-centered" v-show="total_students > 0 && students.length < total_students">
            <p class="subtitle is-5"><i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i> Loading Student {{ students.length+1 }} of {{ total_students }}</p>
            <progress class="progress is-primary" :value="students.length" :max="total_students"></progress>
        </div>
        <div class="columns is-multiline is-mobile">
            <student :number="filteredByGradYear.length" :student="student" v-for="student in filteredByGradYear"></student>
        </div>
        <pre>{{$data}}</pre>
    </div>
</template>
<style>
</style>
<script>
    import Student from './Student.vue'
    export default {
        props: ['search'],
        data() {
            return {
                realm_id: null,
                searchYear: 0,
                total_students: 0,
                students: [],
                userQuotaExceededNotification: {
                    show: false
                },
                downloadModal: {
                   show: false
                }
            }
        },
        mounted() {
            this.realm_id = Overview.realm_id,
            this.getStudents();
            this.listen();
        },
        methods: {
            getStudents() {
                var that = this;
                axios.get('/api/groups/'+Overview.realm_id+'/students')
                .then(function (response) {
                    if (response.data == -1) {
                        console.log('User Quota Exceeded');
                        that.userQuotaExceededNotification.show = true;
                        that.total_students = response.data;
                    } else {
                        that.total_students = response.data;
                    }
                })
            },
            listen() {
                Echo.private('students.'+Overview.realm_id+'.'+Overview.user_id)
                    .listen('GotStudentCompletions', event => {
                        this.students.push(event.enrollment);
                    });
            }
        },
        components:{
            'student': Student,
        },
        computed: {
            filteredByName() {
                var self = this
                if (!self.search) {
                    return self.students;
                } else {
                    var searchRegex = new RegExp(self.search, 'i')
                    return self.students.filter(function (student) {
                        return searchRegex.test(student.name)
                    })
                }
            },
            filteredByGradYear() {
                var self = this
                if (!self.searchYear) {
                    return self.filteredByName
                } else {
                    return self.filteredByName.filter(function (student) {
                        return student.grad_year.indexOf(self.searchYear) !== -1
                    })
                }
            },
            gradYears() {
                var years = [];
                if (this.students) {
                    this.students.forEach(function (student) {
                        if (!years.includes(student.grad_year)) {
                            years.push(student.grad_year);
                        }
                    });
                }
                return years.sort();
            }
        }
    }
</script>
<template>
    <div class="container" style="padding-top:20px;">
        <div class="has-text-centered" v-show="total_students < 1" style="padding-top:15em;">
            <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
        </div>
        <div class="box has-text-centered" v-show="total_students > 0 && students.length < total_students">
            <p class="subtitle is-5"><i class="fa fa-spinner fa-pulse fa-lg fa-fw"></i> Loading Student {{ students.length+1 }} of {{ total_students }}</p>
            <progress class="progress is-primary" :value="students.length" :max="total_students"></progress>
        </div>
        <div class="columns is-multiline is-mobile">
            <student :number="filteredStudents.length" :student="student" v-for="student in filteredStudents"></student>
        </div>
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
                total_students: 0,
                students: [],
            }
        },
        mounted() {
            this.getStudents();
            this.listen();
        },
        methods: {
            getStudents() {
                var that = this;
                axios.get('/api/groups/'+Overview.realm_id+'/students')
                .then(function (response) {
                    that.total_students = response.data;
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
            filteredStudents() {
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
        }
    }
</script>
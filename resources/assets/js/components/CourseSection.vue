<template>
    <div class="box" v-if="section.completions.total_rules > 0 || section.grades.total_grades > 0">
        <h5 class="subtitle" v-text="section.course_title + ':' + section.section_title"></h5>
        <div v-if="section.completions.total_rules">
            <strong>Rules:</strong> {{ section.completions.completed_rules }}/{{section.completions.total_rules}} ({{ completions_percent }}%)
            <progress :class="{'is-danger': completions_percent < 33, 'is-warning': completions_percent >= 33 && completions_percent < 66, 'is-primary': completions_percent > 66 && completions_percent < 99, 'is-success': completions_percent > 99 }" class="progress is-small" :value="completions_percent" max="100">{{ completions_percent }}%</progress>
        </div>
        <div v-if="section.grades.total_grades">
            <strong>Grades:</strong> {{ section.grades.completed_grades }}/{{section.grades.total_grades}} ({{ grades_percent }}%)
            <progress :class="{'is-danger': grades_percent < 33, 'is-warning': grades_percent >= 33 && grades_percent < 66, 'is-primary': grades_percent > 66 && grades_percent < 99, 'is-success': grades_percent > 99 }" class="progress is-small" :value="grades_percent" max="100">{{ grades_percent }}%</progress>
        </div>
    </div>
</template>
<style>

</style>
<script>
    export default{
        props: ['section'],
        data(){
            return{
            }
        },
        computed: {
            completions_percent() {
                var p = this.section.completions.completed_rules / this.section.completions.total_rules;
                return Math.round(p*100);
            },
            grades_percent() {
                var p = this.section.grades.completed_grades / this.section.grades.total_grades;
                return Math.round(p*100);
            },
        },
    }
</script>

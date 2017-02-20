<template>
    <div :class="{'is-12': number == 1, 'is-6': number == 2, 'is-4': number >= 3}" class="column">
        <div class="card">
            <div class="card-content">
                <div class="media">
                    <div class="media-left">
                        <figure class="image" style="height: 40px; width: 40px;">
                            <img :src="student.picture_url" alt="Image">
                        </figure>
                    </div>
                    <div class="media-content">
                        <p class="title is-4" v-text="student.name"></p>
                        <p class="subtitle is-6" v-show="student.grad_year">
                            <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                            {{ student.grad_year }}
                        </p>
                    </div>
                </div>
                <div class="content">
                    <div>
                        {{ student.completed_sections }} of {{ student.total_sections }} courses completed
                        <progress :class="{'is-danger': percent < 33, 'is-warning': percent >= 33 && percent < 66, 'is-primary': percent > 66 && percent < 99, 'is-success': percent > 99 }" class="progress is-medium" :value="student.completed_sections" :max="student.total_sections"></progress>
                    </div>
                    <div style="max-height:32em; overflow-y:auto; overflow-x:visible;">
                        <course-section v-for="section in student.sections" :section="section"></course-section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    course-section:{
        max-height:32em;
        overflow-y:scroll,
    }
</style>
<script>
    import CourseSectionComponent from './CourseSection.vue'
    export default{
        props: ['student','number'],
        data(){
            return{
            }
        },
        components:{
            'course-section': CourseSectionComponent
        },
        computed: {
            percent() {
                var p = this.student.completed_sections / this.student.total_sections;
                return Math.round(p*100);
            }
        }
    }
</script>
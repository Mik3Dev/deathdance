<template>
    <v-row>
        <v-col cols="8">
            <appointment-schedule />
        </v-col>
        <v-col v-if="user" cols="4">
            <my-appointment-list :appointments="[]" />
        </v-col>
    </v-row>
</template>

<script>
import AppointmentSchedule from '../components/AppointmentSchedule.vue'
import MyAppointmentList from '../components/MyAppointmentLists.vue'

export default {
    name: 'SchedulePage',
    components: { MyAppointmentList, AppointmentSchedule },
    computed: {
        user () {
            return this.$store.state.user
        },
    },
    async created () {
        await this.$store.dispatch('getAppointments')
    }
}
</script>

<style>

</style>

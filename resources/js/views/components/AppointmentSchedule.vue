<template>
    <v-card rounded>
        <v-card-title>
            <v-subheader class="pl-0 text-uppercase">
                Appointment Schedule
            </v-subheader>
            <v-spacer />
            <create-appointment v-if="user" />
        </v-card-title>
        <v-card-text>
            <v-calendar
                ref="calendar"
                :weekdays="weekday"
                :events="events"
                @click:event="showEvent"
            />
            <v-dialog
                v-model="showEventCard"
                max-width="600"
            >
                <appointment-card @close="showEventCard=false"/>
            </v-dialog>
        </v-card-text>
    </v-card>
</template>

<script>
import CreateAppointment from './CreateAppointment.vue'
import AppointmentCard from './AppointmentCard.vue'

export default {
    name: "AppointmentSchedule",
    components: { CreateAppointment, AppointmentCard },
    data: () => ({
        picker: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
            .toISOString()
            .substr(0, 10),
        appointmentDate: "",
        showCreateAppointment: false,
        weekday: [1, 2, 3, 4, 5],
        showEventCard: false
    }),
    computed: {
        user() {
            return this.$store.state.user;
        },
        appointments() {
            return this.$store.state.appointments;
        },
        events() {
            return this.appointments.map(appointment => {
                return {
                    name: appointment.user.name,
                    start: appointment.appointment_datetime,
                    end: appointment.end_datetime,
                    color:
                        this.user && this.user.id === appointment.user.id ? "blue" : "green",
                    appointmentId: appointment.id
                };
            });
        }
    },
    methods: {
        async showEvent(e) {
            const { appointmentId } = e.event
            await this.$store.dispatch('getAppointment', { appointmentId })
            this.showEventCard = true
        }
    }
};
</script>

<style></style>

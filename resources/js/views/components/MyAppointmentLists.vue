<template>
  <v-card rounded>
      <v-card-text>
        <v-subheader class="pl-0 text-uppercase">
            My Appointments
        </v-subheader>
          <v-list>
              <v-list-item
                v-for="appointment in myAppointments"
                :key="appointment.id"
              >
                  <v-list-item-content>
                    <v-list-item-title>
                        {{ appointment.description }}
                    </v-list-item-title>
                    <v-list-item-subtitle>
                        {{ appointment.appointment_datetime }}
                    </v-list-item-subtitle>
                  </v-list-item-content>
                  <v-list-item-action>
                      <update-appointment :appointment="appointment" />
                  </v-list-item-action>
                  <v-list-item-action>
                      <delete-appointment :appointment-id="appointment.id" />
                  </v-list-item-action>
              </v-list-item>
          </v-list>
      </v-card-text>
  </v-card>
</template>

<script>
import DeleteAppointment from './DeleteAppointment.vue'
import UpdateAppointment from './UpdateAppointment.vue'

export default {
    name: 'MyAppointmentList',
    components: { DeleteAppointment, UpdateAppointment },
    computed: {
        user () {
            return this.$store.state.user
        },
        appointments () {
            return this.$store.state.appointments
        },
        myAppointments () {
            return this.appointments.filter(appointment => appointment.user.id === this.user.id)
        }
    }
}
</script>

<style>

</style>

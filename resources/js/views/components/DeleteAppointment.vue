<template>
    <v-dialog v-model="dialog" max-width="600">
        <template #activator="{ on, attrs }">
            <v-btn
                icon
                small
                v-bind="attrs"
                v-on="on"
            >
                <v-icon>
                    mdi-delete
                </v-icon>
            </v-btn>
        </template>

        <v-card>
            <v-card-title>
                <v-subheader class="pl-0">
                    DELETE APPOINTMENT
                </v-subheader>
            </v-card-title>
            <v-card-text>
                Do you want to delete this appointment?
            </v-card-text>
            <v-card-actions>
                <v-btn
                    text
                    color="primary"
                    @click="dialog=false"
                >
                    Cancel
                </v-btn>
                <v-spacer />
                <v-btn
                    text
                    color="primary"
                    @click="submit"
                >
                    Ok
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'DeleteAppointment',
    props: {
        appointmentId: {
            type: [Number, String],
            default: ''
        }
    },
    data: () => ({
        dialog: false
    }),
    methods: {
        async submit () {
            await this.$store.dispatch('deleteAppointment', { appointmentId: this.appointmentId })
            await this.$store.dispatch('getAppointments')
            this.dialog = false
        }
    }
}
</script>

<style>

</style>

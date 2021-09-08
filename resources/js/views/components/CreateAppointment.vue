<template>
    <v-dialog v-model="dialog" max-width="600">
        <template #activator="{ on, attrs}">
            <v-btn
                small
                color="primary"
                v-bind="attrs"
                v-on="on"
            >
                Create Appointment
            </v-btn>
        </template>
        <v-card>
            <v-card-title>
                <v-subheader class="text-uppercase pl-0">
                    Create Appointment
                </v-subheader>
                <v-spacer />
                <v-btn icon @click="close">
                    <v-icon>
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form ref="form">
                    <v-row dense>

                        <v-col cols="12">
                            <v-text-field
                                v-model="appointment.description"
                                label="Description"
                                :rules="descriptionRules"
                                counter="150"
                            />
                        </v-col>
                        <v-col cols="6">
                            <v-menu
                                v-model="dateMenu"
                                ref="dateMenu"
                                min-width="auto"
                            >
                                <template #activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="appointment.date"
                                        label="Date"
                                        readonly
                                        :rules="requiredRules"
                                        v-bind="attrs"
                                        v-on="on"
                                    />
                                </template>

                                <v-date-picker
                                    v-model="appointment.date"
                                    no-title
                                    scrollable
                                    :allowed-dates="allowedDates"
                                >
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="dateMenu = false"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-spacer />
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.dateMenu.save(appointment.date)"
                                    >
                                        OK
                                    </v-btn>
                                </v-date-picker>
                            </v-menu>
                        </v-col>

                        <v-col cols="6">
                            <v-autocomplete
                                v-model="appointment.time"
                                :items="times"
                                :rules="requiredRules"
                                label="Time"
                            />
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-btn text @click="close">
                    Close
                </v-btn>
                <v-spacer />
                <v-btn text color="primary" @click="submit">
                    Create
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'CreateAppointment',
    data: () => ({
        appointment: {
            description: '',
            date: (new Date()).toISOString().substr(0, 10),
            time: ''
        },
        requiredRules: [
            v => !!v || 'This field is required'
        ],
        descriptionRules: [
            v => !!v || 'This field is requied.',
            v => v.length < 150 || 'Max. 150 Characaters.'
        ],
        times: [ '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
        dialog: false,
        dateMenu: false,
    }),
    computed: {
        datetime () {
            return `${this.appointment.date} ${this.appointment.time}`
        }
    },
    methods: {
        allowedDates (value) {
            const date = new Date(value)
            if (date.getDay() === 5 || date.getDay() === 6) {
                return false
            }
            return true
        },
        close () {
            this.$refs.form.resetValidation()
            this.appointment = {
                description: '',
                date: (new Date()).toISOString().substr(0, 10),
                time: ''
            }
            this.dialog = false
        },
        async submit () {
            if (this.$refs.form.validate()){
                await this.$store.dispatch('storeAppointment', {
                    description: this.appointment.description,
                    appointment_datetime: this.datetime
                })
                await this.$store.dispatch('getAppointments')
                this.close()
            }
        }
    }
}
</script>

<style>

</style>

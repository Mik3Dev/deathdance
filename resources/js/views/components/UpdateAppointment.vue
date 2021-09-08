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
                    mdi-pencil
                </v-icon>
            </v-btn>
        </template>

        <v-card>
            <v-card-title>
                <v-subheader class="pl-0">
                    UPDATE APPOINTMENT
                </v-subheader>
            </v-card-title>
            <v-card-text>
                <v-form ref="form">
                    <v-row dense>

                        <v-col cols="12">
                            <v-text-field
                                v-model="appointmentEditable.description"
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
                                        v-model="appointmentEditable.date"
                                        label="Date"
                                        readonly
                                        :rules="requiredRules"
                                        v-bind="attrs"
                                        v-on="on"
                                    />
                                </template>

                                <v-date-picker
                                    v-model="appointmentEditable.date"
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
                                        @click="$refs.dateMenu.save(appointmentEditable.date)"
                                    >
                                        OK
                                    </v-btn>
                                </v-date-picker>
                            </v-menu>
                        </v-col>

                        <v-col cols="6">
                            <v-autocomplete
                                v-model="appointmentEditable.time"
                                :items="times"
                                :rules="requiredRules"
                                label="Time"
                            />
                        </v-col>
                    </v-row>
                </v-form>
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
    name: 'UpdateAppointment',
    props: {
        appointment: {
            type: Object,
            default: () => {}
        }
    },
    data: () => ({
        dialog: false,
        appointmentEditable: {
            id: '',
            description: '',
            date: '',
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
        dateMenu: false
    }),
    computed: {
        datetime () {
            return `${this.appointmentEditable.date} ${this.appointmentEditable.time}`
        }
    },
    mounted () {
        this.appointmentEditable = {
            description: this.appointment.description,
            date: this.appointment.appointment_datetime.substring(0,10),
            time: this.appointment.appointment_datetime.substring(11,16)
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
        async submit () {
            await this.$store.dispatch('updateAppointment', {
                appointmentId: this.appointment.id,
                description: this.appointmentEditable.description,
                appointment_datetime: this.datetime
            })
            await this.$store.dispatch('getAppointments')
            this.dialog = false
        }
    }
}
</script>

<style>

</style>

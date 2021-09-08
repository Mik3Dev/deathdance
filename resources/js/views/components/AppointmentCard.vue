<template>
    <v-card>
        <v-card-title class="text-uppercase">
            {{ appointment.user.name }}
        </v-card-title>
        <v-card-subtitle>
            {{ appointment.user.email }}
        </v-card-subtitle>

        <v-card-text>
            <v-list>
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title>
                            {{ appointment.description }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                            {{ appointment.appointment_datetime }} - {{appointment.end_datetime }}
                        </v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-card-text>

        <v-card-actions>
            <v-spacer />
            <v-btn text color="primary" @click="$emit('close')">
                Ok
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    name: 'AppointmentCard',
    computed: {
        appointment () {
            return this.$store.state.appointment
        }
    }
};
</script>

<style></style>

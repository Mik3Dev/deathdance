<template>
    <v-app>
        <v-app-bar app>
            <v-toolbar-title>Dancing with Death - Schedule App</v-toolbar-title>
            <v-spacer />
            <button-login v-if="!user" />
            <button-register v-if="!user" />
            <button-logout v-else />
        </v-app-bar>

        <v-main>
            <v-container class="mt-4">
                <router-view />
            </v-container>
        </v-main>

        <v-footer app>
            <!-- -->
        </v-footer>
    </v-app>
</template>
<script>
import ButtonLogin from './components/ButtonLogin.vue'
import ButtonRegister from './components/ButtonRegister.vue'
import ButtonLogout from './components/ButtonLogout.vue'
import MyAppointmentList from './components/MyAppointmentLists.vue'
import AppointmentSchedule from './components/AppointmentSchedule.vue'

export default {
    name: 'App',
    components: {
        ButtonLogin,
        ButtonRegister,
        ButtonLogout,
        MyAppointmentList,
        AppointmentSchedule,
    },
    data: ()  => ({}),
    computed: {
        user () {
            return this.$store.state.user
        }
    }
};
</script>

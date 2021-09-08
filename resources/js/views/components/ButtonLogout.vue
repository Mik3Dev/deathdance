<template>
    <v-btn text @click="logout">
        Logout
    </v-btn>
</template>

<script>
export default {
    name: 'ButtonLogout',
    methods: {
        async logout () {
            this.$store.dispatch('logout')
        }
    }
}
</script>

<style>

</style>

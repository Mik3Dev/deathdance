require("./bootstrap");

import Vue from "vue";
import VueRouter from "vue-router";

import vuetify from "./plugins/vuetify";
import router from "./routes/index";
import store from "./store";
import App from "./views/app.vue";

Vue.use(VueRouter);

const app = new Vue({
    el: "#app",
    components: { App },
    vuetify,
    router,
    store
});

import store from "../store/index.js";

const routes = [
    {
        path: "/",
        component: () => import("../views/pages/SchedulePage.vue"),
        name: "home",
        beforeEnter(to, from, next) {
            next();
        }
    },
    {
        path: "/login",
        component: () => import("../views/pages/Login.vue"),
        name: "login",
        beforeEnter(to, from, next) {
            if (store.state.user) {
                next("/");
            }
            next();
        }
    },
    {
        path: "/register",
        component: () => import("../views/pages/Register.vue"),
        name: "register",
        beforeEnter(to, from, next) {
            if (store.state.user) {
                next("/");
            }
            next();
        }
    }
];

export default routes;

import VueRouter from "vue-router";
import routes from "./routes";
import store from "../store/index";

const router = new VueRouter({
    mode: "history",
    routes
});

router.beforeEach(async (to, from, next) => {
    await store.dispatch("getCurrentUser");
    next();
});

export default router;

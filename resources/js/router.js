import { createRouter, createWebHistory } from  'vue-router'
import TicketsList from "./pages/TicketsList.vue";
import TicketDetail from "./pages/TicketDetail.vue";
import Dashboard from "./pages/Dashboard.vue";

export default createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', redirect: '/tickets'},
        { path: '/tickets', component: TicketsList },
        { path: '/tickets/:id', component: TicketDetail, props: true },
        { path: '/dashboard', component: Dashboard},
    ]
})
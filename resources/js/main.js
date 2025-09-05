/*
import { createApp } from 'vue';
import router from './router';
import App from "./root/App.vue";
createApp(App).use(router).mount("#app");
*/
import { createApp } from 'vue'
import App from './root/App.vue'
import router from './router';

// Font Awesome setup
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import { faInfoCircle, faStickyNote } from '@fortawesome/free-solid-svg-icons'

library.add(faInfoCircle, faStickyNote)

const app = createApp(App)

app.use(router)

app.component('font-awesome-icon', FontAwesomeIcon)

app.mount('#app')

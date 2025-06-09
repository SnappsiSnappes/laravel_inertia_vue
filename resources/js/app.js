import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue'
import { createInertiaApp , Head, Link} from '@inertiajs/vue3'
import { ZiggyVue } from "../../vendor/tightenco/ziggy"
import Layout from './Layouts/Layout.vue';

// Настройка глобального CSRF-токена
document.addEventListener('DOMContentLoaded', () => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  if (csrfToken) {
      window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
  }
});

createInertiaApp({
  title: (title)=> `My app ${title == '' ? '':'|'} ${title}`,
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    let page = pages[`./Pages/${name}.vue`]
    page.default.layout = page.default.layout || Layout
    return page
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .component('Head',Head)
      .component('Link',Link)
      .mount(el)
  },
  progress:{
    color:"green",
    includeCSS:true,
    showSpinner:true
  },
})
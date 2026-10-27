/**
 * Vue-Inseln in Blade-Seiten, eingebunden per @vite nur dort, wo sie vorkommen.
 * Blade: <div data-vue-component="data-table" data-props="{{ json_encode($props) }}"></div>
 */
import { createApp } from 'vue';
import DataTable from './components/DataTable.vue';

const components = {
    'data-table': DataTable,
};

document.querySelectorAll('[data-vue-component]').forEach((element) => {
    const component = components[element.dataset.vueComponent];
    if (component) {
        createApp(component, JSON.parse(element.dataset.props || '{}')).mount(element);
    }
});

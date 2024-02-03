import './bootstrap';
import {createApp} from 'vue'
import App from './components/app.vue';
import ExpenseForm from './components/expense-form.vue';

const app = createApp(App);

app.component('expense-form', ExpenseForm);

app.mount('#app');

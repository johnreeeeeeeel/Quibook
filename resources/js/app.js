import axios from 'axios';
import { createApp, h } from 'vue';

// Layouts
import GuestLayout from './Layouts/GuestLayout.vue';
import AuthLayout from './Layouts/AuthLayout.vue';

// Components
import Login from './Auth/Login.vue';
import Signup from './Auth/Signup.vue';
import ResetPassword from './Auth/ResetPassword.vue';
import Index from './Pages/Index.vue';
import Home from './Pages/Home.vue';

axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    } else {
        console.error('CSRF token meta tag not found!');
    }
});

const components = {
    Login,
    Signup,
    ResetPassword,
    Index,
    Home,
};

// Mount all Vue components
document.querySelectorAll('[data-component]').forEach(el => {
    const name = el.dataset.component;
    const layout = el.dataset.layout || 'none';
    const Component = components[name];

    if (!Component) {
        console.warn(`Component "${name}" not registered`);
        return;
    }

    let app;

    if (layout === 'guest') {
        app = createApp({
            render: () => h(GuestLayout, {}, { default: () => h(Component) })
        });
    } else if (layout === 'auth') {
        app = createApp({
            render: () => h(AuthLayout, {}, { default: () => h(Component) })
        });
    } else {
        app = createApp(Component);
    }

    app.mount(el);
});
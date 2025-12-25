import { createApp, h } from 'vue';
import GuestLayout from './Layouts/GuestLayout.vue';
import AuthLayout from './Layouts/AuthLayout.vue';

// Auth
import Login from './Auth/Login.vue';
import Signup from './Auth/Signup.vue';
import ResetPassword from './Auth/ResetPassword.vue';

// Pages
import Index from './Pages/Index.vue';
import Home from './Pages/Home.vue';

const components = {
    Login,
    Signup,
    ResetPassword,
    Index,
    Home,
};

// Mount Vue
document.querySelectorAll('[data-component]').forEach(el => {
  const name = el.dataset.component;
  const layoutType = el.dataset.layout; // guest / auth / none
  const PageComponent = components[name];

  if (!PageComponent) return;

  if (layoutType === 'guest') {
    createApp({
      render: () => h(GuestLayout, {}, { default: () => h(PageComponent) })
    }).mount(el);
  } else if (layoutType === 'auth') {
    createApp({
      render: () => h(AuthLayout, {}, { default: () => h(PageComponent) })
    }).mount(el);
  } else {
    // no layout
    createApp(PageComponent).mount(el);
  }
});

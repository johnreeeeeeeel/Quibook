<template>
  <div>
    <header>
      <nav>
        <div>
          <img class="logo" src="../../../public/images/quibook_dark.png" alt="Quibook">
        </div>

        <!-- Mobile dropdown -->
        <div class="mobile-menu dropdown">
          <button class="btn" type="button" id="menuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <Icon class="icon" icon="mingcute:user-4-fill" width="38px" height="38px" />
          </button>

          <ul class="dropdown-menu" aria-labelledby="menuDropdown">
            <li><a class="dropdown-item" onclick="location.href='/profile'">Profile</a></li>
            <li><a class="dropdown-item" onclick="location.href='/settings'">Settings</a></li>
            <li><a class="dropdown-item" @click="logout">Log out</a></li>
          </ul>
        </div>

      </nav>
    </header>

    <main>
      <slot></slot>
    </main>

    <footer>
      © 2025 Flashcraft
    </footer>
  </div>
</template>

<script>
import axios from 'axios';
import { Icon } from '@iconify/vue';

export default {

  components: {
    Icon, 
  },

  methods: {
    async logout() {
      try {
        await axios.post('/logout');
      } catch (error) {
        console.error('Logout failed:', error);
      } finally {
        // Always redirect to login, even if request fails
        window.location.href = '/login';
      }
    },
  },
};
</script>

<style scoped>
header nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 80px;
  background: #F2F0EC;
  z-index: 1000;
  padding: 0 84px;
}

header nav .mobile-menu {
  display: flex;
}

header nav .mobile-menu .btn {
  padding: 0;
  outline: none;
  border: none;
}

header nav .logo {
  height: 38px;
  width: auto;
}



main {
  background-color: #F2F0EC;
  min-height: calc(100vh - 60px);
  margin-top: 60px;
  padding: 24px;
}

footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
</style>

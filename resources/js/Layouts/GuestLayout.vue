<template>
  <div>
    <header>
      <nav>
        <div>
          <img class="logo" src="../../../public/images/quibook_dark.png" alt="Quibook">
        </div>

        <!-- Desktop buttons -->
        <div class="desktop-menu">
          <button type="button" class="btn login-btn" onclick="location.href='/login'">Log in</button>
          <button type="button" class="btn download-btn" onclick="location.href='/download'">Get app</button>
          <button type="button" class="btn signup-btn" onclick="location.href='/signup'">Start to take notes</button>
        </div>

        <!-- Mobile dropdown -->
        <div class="mobile-menu dropdown">
          <button class="btn" type="button" id="menuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <Icon class="icon" icon="mingcute:menu-line" width="24px" height="24px" />
          </button>

          <ul class="dropdown-menu" aria-labelledby="menuDropdown">
            <li><a class="dropdown-item" onclick="location.href='/login'">Log in</a></li>
            <li><a class="dropdown-item" onclick="location.href='/download'">Get app</a></li>
            <li><a class="dropdown-item" onclick="location.href='/signup'">Start to take notes</a></li>
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
import { Icon } from '@iconify/vue';

export default {

  components: {
    Icon, 
  },

  methods: {
    
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

@media (max-width: 991px) {
  header nav .desktop-menu {
    display: none !important;
  }

  header nav .mobile-menu {
    display: flex !important;
  }
}

header nav .mobile-menu {
  display: none;
}

header nav .mobile-menu .btn {
  padding: 0;
}

header nav .desktop-menu {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 12px;
}

header nav .logo {
  height: 38px;
  width: auto;
}

header nav .btn {
  font-weight: 600;
  padding: 8px 18px;
  border-radius: 8px;
  outline: none;
  border: none;
}

header nav .login-btn {
  background-color: transparent;
  border: 1px solid transparent;
}

header nav .login-btn:hover,
header nav .login-btn:active {
  background-color: #f9f9f9;
  border: 1px solid transparent;
}

header nav .download-btn {
  background-color: transparent;
  border: 1px solid #222;
}

header nav .download-btn:hover,
header nav .download-btn:active {
  color: #F2F0EC;
  background-color: #222;
  border: 1px solid #222;
}

header nav .signup-btn {
  color: #F2F0EC;
  background-color: #222;
  border: 1px solid #222;
}

header nav .signup-btn:hover,
header nav .signup-btn:active {
  background-color: #00A82D;
  border: 1px solid #00A82D;
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

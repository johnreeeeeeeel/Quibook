<template>
  <div class="layout container-fluid p-0 m-0">

    <section class="login-section">

      <img class="logo" src="../../../public/images/quibook_dark.png" alt="reload">

      <p>Welcome back, login to continue.</p>

      <form @submit.prevent="submit">
        <p class="input-success" v-if="success">{{ success }}</p>
        <p class="input-error" v-if="error">{{ error }}</p>

        <label class="input-label">Email</label>
        <input type="email" v-model="email" class="form-control" placeholder="Email">

        <div v-if="step === 2">
          <label class="input-label">Token</label>
          <input type="text" v-model="token" class="form-control" placeholder="Token">

          <label class="input-label">New Password</label>
          <input type="password" v-model="password" class="form-control" placeholder="New Password">

          <label class="input-label">Confirm Password</label>
          <input type="password" v-model="password_confirmation" class="form-control" placeholder="Confirm Password">
        </div>

        <div class="action-buttons">
          <button type="submit" class="btn primary-btn" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span v-else>{{ step === 1 ? 'Send Token' : 'Reset Password' }}</span>
          </button>

          <button type="button" class="btn secondary-btn" @click="goToLogin">
            Back to Login
          </button>
        </div>
      </form>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: '',
      token: '',
      password: '',
      password_confirmation: '',
      step: 1,
      success: '',
      error: '',
      loading: false,
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
  },
  methods: {
    async submit() {
      this.error = '';
      this.success = '';
      this.loading = true;

      try {
        let res, data;

        if (this.step === 1) {
          res = await fetch('/forgot-password', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': this.csrf,
              'Accept': 'application/json'
            },
            body: JSON.stringify({ email: this.email })
          });

          data = await res.json();
          if (!res.ok) throw new Error(data.message || 'Failed to send token');

          this.success = data.message;
          this.step = 2;
        } else {
          res = await fetch('/reset-password', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': this.csrf,
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              email: this.email,
              token: this.token,
              password: this.password,
              password_confirmation: this.password_confirmation
            })
          });

          data = await res.json();
          if (!res.ok) throw new Error(data.message || 'Failed to reset password');

          this.success = data.message;
          setTimeout(() => window.location.href = '/login', 1500);
        }
      } catch (err) {
        this.error = err.message || 'Server error. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    goToLogin() {
      window.location.href = '/login';
    }
  }
};
</script>

<style scoped>
.layout {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 24px;
  background-image: url("../Images/auth_bg.png");
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
}

.layout .logo {
  height: 48px;
  width: auto;
}

.login-section {
  background-color: transparent;
  border-radius: 18px;
  width: 440px;
  padding: 38px 18px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.login-section form {
  display: flex;
  flex-direction: column;
  gap: 12px;
  width: 100%;
}

.login-section .login-tools {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
}

.login-section .login-tools div {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 6px;
  font-size: 14px;
}

.login-section .login-tools a {
  text-decoration: none;
  color: #00A82D;
  font-size: 14px;
}

.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.notice {
  text-align: center;
  color: #999;
  margin-top: 12px;
  margin-bottom: 12px;
  font-size: 14px;
}

.notice a {
  color: #00A82D;
  cursor: pointer;
}

.notice a:hover,
.notice a:active {
  text-decoration: underline;
}





.input-label {
  font-weight: 500;
  margin-bottom: -8px;
  color: #444;
}

input {
  padding: 8px 16px;
  border-radius: 12px;
  outline: none;
  box-shadow: none;
}

input:focus {
  outline: none;
  border: 1px solid #00A82D;
  box-shadow: 0 0 5px rgba(0, 168, 45, 0.5);
}

.btn {
  font-weight: 600;
  padding: 8px 18px;
  border-radius: 8px;
  outline: none;
  border: none;
}

.primary-btn {
  color: #F2F0EC;
  background-color: #222;
  border: 1px solid #222;
}

.primary-btn:hover,
.primary-btn:active {
  background-color: #00A82D;
  border: 1px solid #00A82D;
}

.secondary-btn {
  background-color: transparent;
  border: 1px solid #222;
}

.secondary-btn:hover,
.secondary-btn:active {
  color: #F2F0EC;
  background-color: #222;
  border: 1px solid #222;
}

.input-success,
.input-error {
  display: inline-block;
  padding: 10px 16px;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 500;
  position: relative;
  opacity: 0;
  transform: translateY(-10px);
  animation: fadeInUp 0.4s forwards;
  margin-top: 8px;
}

.input-success {
  color: #28a745;
  background-color: #e6f4ea;
  border: 1px solid #28a745;
}

.input-error {
  color: #dc3545;
  background-color: #fdecea;
  border: 1px solid #dc3545;
}

.input-success::before {
  content: "✔";
  margin-right: 8px;
}

.input-error::before {
  content: "✖";
  margin-right: 8px;
}

@keyframes fadeInUp {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }

  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

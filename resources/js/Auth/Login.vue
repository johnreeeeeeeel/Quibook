<template>
    <div class="layout container-fluid p-0 m-0">

        <section class="login-section">

            <img class="logo" src="../../../public/images/quibook_dark.png" alt="reload">

            <p>Welcome back, login to continue.</p>

            <form @submit.prevent="login">
                <p class="input-success" v-if="success">{{ success }}</p>
                <p class="input-error" v-if="error">{{ error }}</p>

                <label for="email" class="input-label">Email</label>
                <input type="email" id="email" name="email" v-model="email" class="form-control" autocomplete="email">

                <label for="password" class="input-label">Password</label>
                <input :type="showPassword ? 'text' : 'password'" id="password" name="password" v-model="password"
                    class="form-control" autocomplete="current-password">

                <div class="login-tools">
                    <div>
                        <input type="checkbox" id="showPass" v-model="showPassword">
                        <label for="showPass">Show Password</label>
                    </div>
                    <a href="/reset-password">Forgot Password?</a>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn primary-btn" :disabled="loginLoading">
                        <span v-if="loginLoading" class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                        <span v-else>Log in</span>
                    </button>

                    <p class="notice">
                        By clicking Log in, you accept Quibook's <a>Terms of Service</a> and <a>Privacy Policy
                        </a>.
                    </p>

                    <button type="button" class="btn secondary-btn" @click="goToSignup">
                        New to Quibook? Sign up now!
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
            password: '',
            showPassword: false,
            success: '',
            error: '',
            loginLoading: false,
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
    },
    methods: {
        async login() {
            this.success = '';
            this.error = '';
            this.loginLoading = true;

            try {
                const res = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'Accept': 'application/json' // <-- ensure Laravel returns JSON
                    },
                    body: JSON.stringify({
                        email: this.email,
                        password: this.password
                    })
                });

                const data = await res.json();

                if (!res.ok) throw new Error(data.message || 'Login failed');

                this.success = data.message;
                setTimeout(() => window.location.href = '/home', 1000);
            } catch (err) {
                this.error = err.message || 'Server error. Please try again.';
            } finally {
                this.loginLoading = false;
            }
        },

        goToSignup() {
            window.location.href = '/signup';
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
    max-width: 440px;
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

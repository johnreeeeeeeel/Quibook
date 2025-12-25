<template>
    <div class="layout container-fluid p-0 m-0">
        <section class="signup-section">

            <img class="logo" src="../../../public/images/quibook_dark.png" alt="reload">

            <p>Welcome, sign up to continue.</p>

            <form @submit.prevent="register">
                <p class="input-success" v-if="success">{{ success }}</p>
                <p class="input-error" v-if="error">{{ error }}</p>

                <label for="name" class="input-label">Full Name</label>
                <input type="text" id="name" name="name" v-model="name" class="form-control" autocomplete="name">

                <label for="email" class="input-label">Email</label>
                <input type="email" id="email" name="email" v-model="email" class="form-control" autocomplete="email">

                <label for="password" class="input-label">Password</label>
                <input type="text" id="password" name="password" v-model="password" class="form-control"
                    autocomplete="new-password">

                <label for="confirmPassword" class="input-label">Confirm Password</label>
                <input type="text" id="confirmPassword" name="confirmPassword" v-model="confirmPassword"
                    class="form-control" autocomplete="new-password">

                <div class="input-otp">
                    <input type="text" id="otp" name="otp" v-model="otp" class="form-control" placeholder="OTP">
                    <button type="button" class="btn primary-btn" @click="sendOtp" :disabled="sendOtpLoading">
                        <span v-if="sendOtpLoading" class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                        <span v-else>Send OTP</span>
                    </button>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn primary-btn" :disabled="signupLoading">
                        <span v-if="signupLoading" class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                        <span v-else>Sign up</span>
                    </button>

                    <p class="notice">
                        By clicking Sign up, you accept Quibook's <a>Terms of Service</a> and <a>Privacy Policy
                        </a>.
                    </p>

                    <button type="button" class="btn secondary-btn" @click="goToLogin">
                        Already have an account? Log in now!
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
            name: '',
            email: '',
            password: '',
            confirmPassword: '', // added confirm password
            otp: '',
            success: '',
            error: '',
            sendOtpLoading: false,
            signupLoading: false,
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
    },

    methods: {
        async sendOtp() {
            this.error = '';
            this.success = '';

            if (this.password !== this.confirmPassword) {
                this.error = "Passwords do not match";
                return;
            }

            this.sendOtpLoading = true;
            try {
                const res = await fetch('/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.name,
                        email: this.email,
                        password: this.password
                    })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed to send OTP');

                this.success = 'OTP sent to your email';
            } catch (err) {
                this.error = err.message || 'Server error. Please try again.';
            } finally {
                this.sendOtpLoading = false;
            }
        },

        async register() {
            this.error = '';
            this.success = '';

            if (this.password !== this.confirmPassword) {
                this.error = "Passwords do not match";
                return;
            }

            this.signupLoading = true;
            try {
                const res = await fetch('/verify-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ otp: this.otp })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'OTP verification failed');

                this.success = data.message;
                setTimeout(() => window.location.href = '/login', 1500);
            } catch (err) {
                this.error = err.message || 'Server error. Please try again.';
            } finally {
                this.signupLoading = false;
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

.signup-section {
    border-radius: 18px;
    background-color: #FFFFFF;
    max-width: 460px;
    padding: 38px 18px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.signup-section form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
}

.signup-section .input-otp {
    display: flex;
    flex-direction: row;
    gap: 4px;
}

.signup-section .input-otp input {
    flex: 1;
}

.signup-section .input-otp button {
    flex: 1;
}

.signup-section .action-buttons {
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

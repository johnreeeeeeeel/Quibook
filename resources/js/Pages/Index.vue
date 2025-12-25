<template>
    <div class="layout container-fluid p-0 m-0">
        <section class="hero-section">
            <h1>Write. Save. Remember.</h1>
            <p>
                Quibook makes it easy to capture your thoughts, ideas, and tasks in one place. Quickly jot down notes,
                organize them effortlessly, and always have your important ideas at your fingertips.
            </p>
            <button type="button" class="btn signup-btn" onclick="location.href='/signup'">Start to take notes</button>
            <p>Already have an account? <a href="/login">Log in</a></p>
        </section>
    </div>
</template>

<script setup>

</script>

<style scoped>
.layout {
    display: flex;
    justify-content: center;
    align-items: center;
}

.hero-section {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 24px;
    background-color: #F1EBE0;
    padding: 24px;
    border-radius: 12px;
    width: 1024px;
    text-align: center;
}

.hero-section h1 {
    font-weight: 700;
}

.hero-section p {
    font-weight: 600;
}

.hero-section .btn {
    font-weight: 600;
    padding: 8px 18px;
    border-radius: 8px;
    outline: none;
    border: none;
}

.hero-section .signup-btn {
    color: #F2F0EC;
    background-color: #222;
    border: 1px solid #222;
}

.hero-section .signup-btn:hover,
.hero-section .signup-btn:active {
    background-color: #00A82D;
    border: 1px solid #00A82D;
}

.hero-section p:nth-child(4) {
    font-weight: 500;
}

.hero-section p:nth-child(4) a {
    color: #333;
}

.hero-section p:nth-child(4) a:hover,
.hero-section p:nth-child(4) a:active {
    text-decoration: none;
    color: #00A82D;
}
</style>
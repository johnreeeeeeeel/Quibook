<template>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Index Page</h1>
            <button class="btn btn-danger" @click="logout">Logout</button>
        </div>

        <p>Welcome to the dashboard!</p>
    </div>
</template>

<script setup>
const csrf = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute('content');

const logout = async () => {
    await fetch('/logout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf
        }
    });

    // Redirect to login page
    window.location.href = '/';
};
</script>

<style scoped>
</style>

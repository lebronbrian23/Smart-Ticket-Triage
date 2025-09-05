<template>
    <div class="layout">
        <header class="layout__header">
            <h1 class="layout__title">
                <router-link to="/" class="layout__nav-link">Smart Ticket Triage</router-link>
            </h1>
            <nav class="layout__nav">
                <router-link to="/tickets" class="layout__nav-link">Tickets</router-link>
                <router-link to="/dashboard" class="layout__nav-link">Dashboard</router-link>
                <button class="layout__theme-toggle" @click="toggleTheme">{{ themeLabel }}</button>
            </nav>
        </header>
        <main class="layout__main">
            <router-view />
        </main>
    </div>
</template>

<script>
export default {
    name: 'AppRoot',
    data() {
        return { dark: false }
    },
    computed: {
        themeLabel() {
            return this.dark ? 'Light' : 'Dark'
        }
    },
    created() {
        const saved = localStorage.getItem('theme')
        this.dark = saved === 'dark'
        document.body.className = this.dark ? 'theme-dark' : 'theme-light'
    },
    methods: {
        toggleTheme() {
            this.dark = !this.dark
            document.body.className = this.dark ? 'theme-dark' : 'theme-light'
            localStorage.setItem('theme', this.dark ? 'dark' : 'light')
        }
    }
}
</script>

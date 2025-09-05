<template>
    <section class="dashboard">
        <h2 class="dashboard__title">Dashboard</h2>

        <div class="dashboard__stats-cards">
            <div class="dashboard__card dashboard__card--total">
                <p class="dashboard__card-title">Total Tickets</p>
                <p class="dashboard__card-value">{{ stats.total }}</p>
            </div>

            <div
                class="dashboard__card"
                v-for="(count, st) in stats.status"
                :key="st"
                :style="{ backgroundColor: statusColors[st] || '#eee', color: statusTextColors[st] || '#111' }"
            >
                <p class="dashboard__card-title">{{ st }}</p>
                <p class="dashboard__card-value">{{ count }}</p>
            </div>
        </div>

        <div class="dashboard__chart-container">
            <canvas id="categoryChart"></canvas>
            <div class="dashboard__legend">
                <div
                    v-for="(color, label) in legendColors"
                    :key="label"
                    class="dashboard__legend-item"
                >
                    <span class="dashboard__legend-color" :style="{ backgroundColor: color }"></span>
                    <span class="dashboard__legend-label">{{ label }}</span>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import api from '../api'
import { Chart } from 'chart.js/auto'

export default {
    name: 'Dashboard',
    data() {
        return {
            stats: { total: 0, status: {}, category: {} },
            chart: null,
            legendColors: {},
            statusColors: {
                open: '#4CAF50',
                in_progress: '#FF9800',
                closed: '#9E9E9E',
                resolved: '#F44336'
            },
            statusTextColors: {
                open: '#fff',
                in_progress: '#fff',
                closed: '#fff',
                resolved: '#fff'
            },
            fallbackColors: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
            ]
        }
    },
    async created() {
        this.stats = await api.getStats()

        // Make sure category has counts per status
        // If category is empty, default to stats.status
        if (!Object.keys(this.stats.category).length) {
            this.stats.category = { ...this.stats.status }
        }

        this.draw()
    },
    methods: {
        draw() {
            const ctx = document.getElementById('categoryChart')
            if (this.chart) this.chart.destroy()

            const labels = Object.keys(this.stats.category)
            let data = Object.values(this.stats.category)

            // Ensure all values are numbers
            data = data.map(v => Number(v) || 0)

            // If all data are 0, add dummy values to show empty slices
            const total = data.reduce((sum, val) => sum + val, 0)
            if (total === 0) {
                data = new Array(data.length).fill(1)
            }

            // Map colors based on statusColors or fallback
            const backgroundColors = labels.map((label, index) => {
                return this.statusColors[label.toLowerCase()] || this.fallbackColors[index % this.fallbackColors.length]
            })

            // Save legend colors for template
            this.legendColors = labels.reduce((acc, label, idx) => {
                acc[label] = backgroundColors[idx]
                return acc
            }, {})

            this.chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{ data, backgroundColor: backgroundColors }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    }
                }
            })
        }

    }
}
</script>

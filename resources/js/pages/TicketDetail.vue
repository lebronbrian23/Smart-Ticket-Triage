<template>
    <section v-if="ticket" class="ticket-detail">
        <div class="ticket-detail__header">
            <h2 class="ticket-detail__subject">{{ ticket.subject }}</h2>
            <small class="ticket-detail__id">#{{ ticket.id }}</small>
        </div>

        <p>{{ ticket.body }}</p>
        <p>Status: {{ ticket.status }}</p>
        <p>Status: {{ ticket.category }}</p>

        <p>Confidence: {{ formatConfidence(ticket.confidence) }}</p>
        <p>Explanation: {{ ticket.explanation || '—' }}</p>
        <p>Internal Note: {{ ticket.note || '' }}</p>


        <label class="ticket-detail__label">
            Status:
            <select v-model="status" class="ticket-detail__select">
                <option value="">—</option>
                <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
            </select>
        </label>
        <label class="ticket-detail__label">
            Category:
            <select v-model="category" class="ticket-detail__select">
                <option value="">—</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
        </label>

        <div class="ticket-detail__note">
            <label class="ticket-detail__label">
                Internal Note
                <textarea
                    v-model="note"
                    class="ticket-detail__textarea"
                    placeholder="Internal note (max 2000 chars)"
                />
            </label>
        </div>

        <div class="ticket-detail__note-actions">
            <button class="btn" @click="goBack">Cancel</button>
            <button
                class="btn btn--primary"
                @click="save({ note, category, status })"
                :disabled="saving"
            >
                <span v-if="saving" class="ticket-list__spinner"></span>
                {{ saving ? 'Saving…' : 'Save Note' }}
            </button>

            <button
                class="btn"
                @click="classify"
                :disabled="classifying"
            >
                <span v-if="classifying" class="ticket-list__spinner"></span>
                {{ classifying ? 'Classifying…' : 'Run Classification' }}
            </button>

        </div>
    </section>
</template>

<script>
import api from '../api'

export default {
    name: 'TicketDetail',
    props: { id: { type: String, required: true } },
    data() {
        return {
            ticket: null,
            note: '',
            status: '',
            category: '',
            saving: false,
            classifying: false,
            categories: ['Bug', 'Feature', 'Support', 'Other'],
            statuses: ['open', 'resolved', 'in_progress', 'closed']
        }
    },
    created() {
        this.load()
    },
    methods: {
        async load() {
            this.ticket = await api.getTicket(this.id)
            this.note = this.ticket.note || ''
        },

        async save(patch) {
            this.saving = true
            try {
                this.ticket = await api.updateTicket(this.id, patch)
            } finally {
                this.saving = false
            }
        },

        formatConfidence(confidence) {
            return confidence == null ? '-' : Number(confidence).toFixed(2)
        },

        async classify() {
            this.classifying = true
            try {
                await api.classifyTicket(this.id)
                const poll = async () => {
                    const fresh = await api.getTicket(this.id)
                    this.ticket = fresh
                    if (!fresh.explanation) {
                        await new Promise(resolve => setTimeout(resolve, 1600))
                        return poll()
                    }
                }
                await poll()
            } finally {
                this.classifying = false
            }
        },
        goBack() {
            this.$router.push('/tickets')
        }
    }
}
</script>

<template>
    <section class="ticket-list">
        <div class="ticket-list__toolbar">
            <input
                v-model="filters.search"
                class="ticket-list__search"
                placeholder="Search…"
                @input="fetchTickets(1)"
            />
            <button class="btn btn--primary" @click="openNew = true">New Ticket</button>
        </div>

        <div class="ticket-list__grid">
            <article v-for="ticket in tickets.data" :key="ticket.id" class="ticket-list__item">
                <h3 class="ticket-list__subject">
                    <router-link class="ticket-list__subject-link" :to="`/tickets/${ticket.id}`">{{ ticket.subject }}</router-link>

                    <span v-if="ticket.note" class="ticket-list__badge">
                        <font-awesome-icon :icon="['fas', 'sticky-note']" />
                    </span>

                    <span
                        v-if="ticket.explanation"
                        class="ticket-list__info"
                        :title="ticket.explanation"
                    >
                        <font-awesome-icon :icon="['fas', 'info-circle']" />
                    </span>
                </h3>

                <p class="ticket-list__body">{{ truncate(ticket.body, 111) }}</p>
                <small>Status: {{ ticket.status }} | Category: {{ ticket.category || '' }} | Confidence: {{ ticket.confidence || ''}}</small>

                <div class="ticket-list__actions">
                    <button
                        class="btn"
                        @click="classify(ticket)"
                        :disabled="loadingIds.has(ticket.id)"
                    >
                        <span v-if="loadingIds.has(ticket.id)" class="ticket-list__spinner"></span>
                        {{ loadingIds.has(ticket.id) ? 'Classifying…' : 'Classify' }}
                    </button>
                </div>
            </article>
        </div>

        <div class="ticket-list__pagination">
            <button
                class="btn"
                :disabled="!tickets.prev_page_url"
                @click="fetchTickets(tickets.current_page - 1)"
            >
                Prev
            </button>
            <span>Page {{ tickets.current_page }} / {{ tickets.last_page }}</span>
            <button
                class="btn"
                :disabled="!tickets.next_page_url"
                @click="fetchTickets(tickets.current_page + 1)"
            >
                Next
            </button>
        </div>

        <NewTicketModal
            :open="openNew"
            @close="openNew=false"
            @created="onCreated"
        />
    </section>
</template>

<script>
import api from '../api';
import NewTicketModal from "../components/NewTicketModal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import debounce from 'lodash/debounce'

export default {
    name: 'TicketsList',
    components: {FontAwesomeIcon, NewTicketModal },
    data() {
        return {
            openNew: false,
            tickets: { data: [], current_page: 1, last_page: 1 },
            filters: { search: '', per_page: 12 },
            loadingIds: new Set(),
        }
    },
    created() {
        this.fetchTickets(1);
    },
    methods: {
        fetchTickets: debounce( async function (page) {
            const params = { ...this.filters, page }
            this.tickets = await api.listTickets(params)
        }, 500),
        onCreated() {
            this.fetchTickets(1);
        },
        truncate(s, len) {
            return s.length > len ? s.slice(0, len) + '…' : s
        },
        async classify(ticket) {
            this.loadingIds.add(ticket.id);
            try {
                await api.classifyTicket(ticket.id);
                const poll = async () => {
                    const fresh = await api.getTicket(ticket.id);
                    Object.assign(ticket, fresh);
                    if (!fresh.explanation) {
                        await new Promise(resolve => setTimeout(resolve, 1600));
                        return poll()
                    }
                }
                await poll();
            } finally {
                this.loadingIds.delete(ticket.id);
            }
        }
    }
}
</script>

<template>
    <div v-if="open" class="modal">
        <div class="modal__backdrop" @click="$emit('close')"></div>
        <div class="modal__content">
            <h3 class="modal__title">New Ticket</h3>
            <form @submit.prevent="submit" class="form">
                <div class="form__group">
                    <input
                        v-model="form.subject"
                        class="form__input"
                        placeholder="Subject"
                        required
                    />
                </div>

                <div class="form__group">
                  <textarea
                      v-model="form.body"
                      class="form__textarea"
                      placeholder="Description"
                      required
                  ></textarea>
                </div>

                <div class="form__actions">
                    <button type="button" class="btn" @click="$emit('close')">Cancel</button>
                    <button type="submit" class="btn btn--primary" :disabled="!valid || loading">
                        {{ loading ? 'Saving…' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>


<script>
import api  from '../api'

export default {
    name: "NewTicketModal",
    props: {open: Boolean},
    data () {
        return { form: {subject: '', body: '', status: 'open'}, loading: false }
    },
    computed: {
        valid() { return this.form.subject.length >= 3 && this.form.subject.length >= 5 }
    },
    methods: {
        async submit () {
            if ( !this.valid) return
            this.loading = true
            try {
                const ticket = await api.createTicket(this.form)
                this.$emit('created', ticket)
                this.form = { subject: '', body: '', status: 'open' }
                this.$emit('close')
            } finally { this.loading = false }
        }
    }
}
</script>
import axios from 'axios'

const api = axios.create({
    baseURL: '/api',
    headers: { Accept: 'application/json' },
})

export default {
    listTickets(params){ return api.get('/tickets', {params}).then(response => response.data ) },
    getTicket(id){ return api.get(`/tickets/${id}`).then(response => response.data ) },
    createTicket(data) { return api.post('/tickets', data).then(response => response.data) },
    updateTicket(id, data) { return api.patch(`/tickets/${id}`, data ).then(response => response.data ) },
    classifyTicket(id) { return api.post(`/tickets/${id}/classify`).then(response => response.data ) },
    getStats() { return api.get('/stats').then(response => response.data ) },


}
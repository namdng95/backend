
require('./bootstrap');

import ChatMessages from './components/ChatMessages.vue';
import ChatForm from './components/ChatForm.vue';

Vue.component('chat-messages', ChatMessages);
Vue.component('chat-form', ChatForm);

const app = new Vue({
    el: '#app',

    data: {
        messages: []
    },

    created() {
        this.fetchMessages();

        console.log(Echo.channel('chat'))

        Echo.private('chat')
            .listen('.send.message', (e) => {
                console.log("OK")
                this.messages.push({
                    message: e.message.message,
                    user: e.user
                });
            });
    },

    methods: {
        fetchMessages() {
            axios.get('/messages').then(response => {
                this.messages = response.data;
            });
        },

        addMessage(message) {
            console.log(message)
            this.messages.push(message);

            axios.post('/messages', message).then(response => {
                console.log(response.data);
            });
        }
    }
});

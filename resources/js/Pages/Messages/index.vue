<template>
    <Head title="Messages" />
  
    <div class="max-w-4xl mx-auto my-10 p-4">
      <div class="bg-white border rounded-lg p-6 h-[600px] flex flex-col">
        <div class="flex justify-between items-center mb-4 pb-4 border-b">
          <div class="flex items-center gap-3">
            <Link :href="route('profile.edit')" class="text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
            </Link>
            <div class="flex items-center">
              <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-sm mr-3">
                <img 
                  v-if="otherUser.profile_image" 
                  :src="'/storage/' + otherUser.profile_image" 
                  class="w-10 h-10 rounded-full object-cover" 
                  :alt="otherUser.first_name"
                >
                <span v-else>{{ getInitials(otherUser.first_name, otherUser.last_name) }}</span>
              </div>
              <div>
                <h2 class="font-semibold">{{ otherUser.first_name }} {{ otherUser.last_name }}</h2>
                <p class="text-xs text-gray-500">{{ request.skill.title }}</p>
              </div>
            </div>
          </div>
          <div class="text-xs px-2 py-1 rounded" :class="getStatusClass(request.status)">
            {{ request.status }}
          </div>
        </div>
        
        <div class="flex-1 overflow-y-auto mb-4 space-y-4" ref="messagesContainer">
          <div v-for="message in messages" :key="message.id" class="flex" :class="message.sender_id === $page.props.auth.user.id ? 'justify-end' : 'justify-start'">
            <div 
              class="max-w-xs md:max-w-md rounded-lg px-4 py-2 text-sm" 
              :class="message.sender_id === $page.props.auth.user.id ? 'bg-black text-white' : 'bg-gray-100'"
            >
              <p>{{ message.content }}</p>
              <p class="text-xs mt-1 opacity-70">{{ formatDate(message.created_at) }}</p>
            </div>
          </div>
        </div>
        
        <form @submit.prevent="sendMessage" class="flex gap-2 pt-4 border-t">
          <input 
            type="text" 
            v-model="form.content" 
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black"
            placeholder="Type your message..."
            required
          >
          <button 
            type="submit" 
            class="px-4 py-2 bg-black text-white rounded-md"
            :disabled="form.processing"
          >
            Send
          </button>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import { defineComponent } from 'vue';
  import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
  
  export default defineComponent({
    components: {
      Head,
      Link,
    },
  
    props: {
      request: Object,
      messages: Array,
    },
  
    data() {
      return {
        newMessages: [],
      }
    },
  
    computed: {
      otherUser() {
        return this.request.sender_id === this.$page.props.auth.user.id 
          ? this.request.receiver 
          : this.request.sender;
      },
      
      allMessages() {
        return [...this.messages, ...this.newMessages];
      }
    },
  
    setup(props) {
      const form = useForm({
        content: '',
      });
  
      return { form };
    },
  
    mounted() {
      this.scrollToBottom();
      this.listenForMessages();
    },
  
    updated() {
      this.scrollToBottom();
    },
  
    beforeUnmount() {
      Echo.leave(`chat.${this.request.id}`);
    },
  
    methods: {
      getInitials(firstName, lastName) {
        return `${firstName.charAt(0)}${lastName.charAt(0)}`;
      },
      
      formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      },
      
      sendMessage() {
        this.form.post(route('messages.store', this.request.id), {
          onSuccess: () => {
            this.form.reset();
          }
        });
      },
      
      scrollToBottom() {
        if (this.$refs.messagesContainer) {
          this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
        }
      },
      
      listenForMessages() {
        Echo.private(`chat.${this.request.id}`)
          .listen('NewMessage', (e) => {
            this.newMessages.push(e.message);
          });
      },
      
      getStatusClass(status) {
        switch (status) {
          case 'pending':
            return 'bg-yellow-100 text-yellow-800';
          case 'accepted':
            return 'bg-green-100 text-green-800';
          case 'rejected':
            return 'bg-red-100 text-red-800';
          default:
            return '';
        }
      }
    }
  });
  </script>
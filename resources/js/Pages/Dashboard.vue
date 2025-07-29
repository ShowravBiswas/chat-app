<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    users: Array
});

const selectedUser = ref(null);
const currentUser = ref(null);
const newMessage = ref('');
const messages = ref([]);
const messageContainer = ref(null);

// Auto-scroll chat to bottom
const scrollToBottom = () => {
    nextTick(() => {
        if (messageContainer.value) {
            messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
        }
    });
};

// Select user and load chat history
const selectUser = async (user) => {
    selectedUser.value = user;
    const res = await axios.get(`/messages/${user.id}`);
    messages.value = res.data;
    scrollToBottom();
};

// Send message API call
const sendMessage = async () => {
    if (!newMessage.value.trim() || !selectedUser.value) return;

    const res = await axios.post('/messages', {
        receiver_id: selectedUser.value.id,
        message: newMessage.value,
    });

    messages.value.push(res.data);
    newMessage.value = '';
    scrollToBottom();
};

// Format timestamp for display
const formatTime = (datetime) => {
    const date = new Date(datetime);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

let echoChannel = null;

// Setup current user and listen for real-time messages
onMounted(async () => {
    const auth = await axios.get('/user');
    currentUser.value = auth.data;

    console.log('🔌 Subscribing to private channel: chat.' + currentUser.value.id);

    echoChannel = window.Echo.private(`chat.${currentUser.value.id}`)
        .listen('MessageSent', (e) => {
            console.log('📡 Real-time message received:', e.message);

            // Fix: Ensure type-safe comparison using Number()
            if (selectedUser.value && Number(e.message.sender_id) === Number(selectedUser.value.id)) {
                messages.value.push(e.message);
                scrollToBottom();
            }
        });

    scrollToBottom();
});

// Cleanup listener on unmount
onUnmounted(() => {
    if (echoChannel && currentUser.value) {
        console.log('🔌 Leaving channel: chat.' + currentUser.value.id);
        window.Echo.leave(`chat.${currentUser.value.id}`);
    }
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex border rounded-lg h-[600px] overflow-hidden">
                            <!-- Left: User List -->
                            <div class="w-1/3 border-r overflow-y-auto bg-white">
                                <div class="p-4 font-semibold border-b bg-gray-100 sticky top-0 z-10">
                                    Users
                                </div>
                                <ul>
                                    <li
                                        v-for="user in users"
                                        :key="user.id"
                                        @click="selectUser(user)"
                                        class="cursor-pointer px-4 py-2 hover:bg-gray-100"
                                        :class="{ 'bg-gray-100 font-bold': selectedUser?.id === user.id }"
                                    >
                                        {{ user.name }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Right: Chat Area -->
                            <div class="w-2/3 flex flex-col bg-gray-50">
                                <div v-if="selectedUser">
                                    <div class="p-4 border-b font-semibold bg-gray-100 sticky top-0 z-10">
                                        Chat with {{ selectedUser?.name }}
                                    </div>

                                    <div
                                        ref="messageContainer"
                                        class="flex-1 overflow-y-auto p-4 space-y-2"
                                    >
                                        <div
                                            v-for="message in messages"
                                            :key="message.id"
                                            class="flex flex-col mb-2"
                                            :class="Number(message.sender_id) === Number(currentUser.id) ? 'items-end' : 'items-start'"
                                        >
                                            <div
                                                class="px-3 py-2 rounded max-w-[70%]"
                                                :class="Number(message.sender_id) === Number(currentUser.id)
                                                    ? 'bg-blue-500 text-white'
                                                    : 'bg-gray-300 text-gray-800'"
                                            >
                                                {{ message.message }}
                                            </div>
                                            <span class="text-xs text-gray-500 mt-1">
                                                {{ formatTime(message.created_at) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex border-t p-3 bg-white">
                                        <input
                                            type="text"
                                            v-model="newMessage"
                                            @keyup.enter="sendMessage"
                                            class="flex-1 border rounded-l px-4 py-2 focus:outline-none"
                                            placeholder="Type your message..."
                                        />
                                        <button
                                            @click="sendMessage"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-r hover:bg-blue-700"
                                        >
                                            Send
                                        </button>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="text-gray-500 h-full flex items-center justify-center text-xl"
                                    style="min-height: 400px"
                                >
                                    Select a user to start chatting
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

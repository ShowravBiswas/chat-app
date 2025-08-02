<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, onUnmounted } from 'vue';
import axios from 'axios';
import { formatTime } from '@/helpers/formatTime.js';

const props = defineProps({
    users: Array,
});

const selectedUser = ref(null);
const currentUser = ref(null);
const newMessage = ref('');
const messages = ref([]);
const messageContainer = ref(null);

const scrollToBottom = () => {
    nextTick(() => {
        if (messageContainer.value) {
            messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
        }
    });
};

const selectUser = async (user) => {
    selectedUser.value = user;
    const res = await axios.get(`/messages/${user.id}`);
    messages.value = res.data;
    scrollToBottom();
};

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

let echoChannel = null;

onMounted(async () => {
    const auth = await axios.get('/user');
    currentUser.value = auth.data;

    echoChannel = window.Echo.private(`chat.${currentUser.value.id}`)
        .listen('MessageSent', (e) => {
            if (
                selectedUser.value &&
                Number(e.message.sender_id) === Number(selectedUser.value.id)
            ) {
                messages.value.push(e.message);
                scrollToBottom();
            }
        });

    scrollToBottom();
});

onUnmounted(() => {
    if (echoChannel && currentUser.value) {
        window.Echo.leave(`chat.${currentUser.value.id}`);
    }
});
</script>

<template>
    <Head title="Chat" />
    <AuthenticatedLayout>
        <div class="py-12 h-[calc(100vh-96px)]">
            <div class="h-full mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="h-full bg-white shadow-sm sm:rounded-lg">
                    <div class="flex h-full p-6 text-gray-900">
                        <!-- Left: Users -->
                        <div class="w-1/3 overflow-y-auto border-r">
                            <div class="sticky top-0 p-4 font-semibold bg-gray-100 border-b">
                                Users
                            </div>
                            <ul>
                                <li
                                    v-for="user in users"
                                    :key="user.id"
                                    @click="selectUser(user)"
                                    class="px-4 py-2 cursor-pointer hover:bg-gray-100"
                                    :class="{ 'bg-gray-100 font-bold': selectedUser?.id === user.id }"
                                >
                                    {{ user.name }}
                                </li>
                            </ul>
                        </div>

                        <!-- Right: Chat -->
                        <div class="flex flex-col w-2/3 h-full">
                            <div v-if="selectedUser" class="flex flex-col h-full">
                                <!-- Chat header -->
                                <div class="sticky top-0 p-4 font-semibold bg-gray-100 border-b">
                                    Chat with {{ selectedUser.name }}
                                </div>

                                <!-- Message container -->
                                <div
                                    ref="messageContainer"
                                    class="flex-1 p-4 space-y-2 overflow-y-auto bg-gray-50"
                                >
                                    <div
                                        v-for="message in messages"
                                        :key="message.id"
                                        class="flex flex-col"
                                        :class="Number(message.sender_id) === Number(currentUser.id)
                                            ? 'items-end'
                                            : 'items-start'"
                                    >
                                        <div
                                            class="px-3 py-2 rounded max-w-[70%]"
                                            :class="Number(message.sender_id) === Number(currentUser.id)
                                                ? 'bg-blue-500 text-white'
                                                : 'bg-gray-300 text-gray-800'"
                                        >
                                            {{ message.message }}
                                        </div>
                                        <span class="mt-1 text-xs text-gray-500">
                                            {{ formatTime(message.created_at) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Input -->
                                <div class="flex p-3 bg-white border-t">
                                    <input
                                        v-model="newMessage"
                                        @keyup.enter="sendMessage"
                                        type="text"
                                        class="flex-1 px-4 py-2 border rounded-l focus:outline-none"
                                        placeholder="Type your message..."
                                    />
                                    <button
                                        @click="sendMessage"
                                        class="px-4 py-2 text-white bg-blue-600 rounded-r hover:bg-blue-700"
                                    >
                                        Send
                                    </button>
                                </div>
                            </div>

                            <!-- No user selected -->
                            <div v-else class="flex items-center justify-center h-full text-gray-500">
                                Select a user to start chatting
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

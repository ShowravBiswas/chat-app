<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, nextTick } from 'vue';
import moment from 'moment';

const props = defineProps({
    users: Array
});

// Create 50 mock users
// const users = ref([]);
// for (let i = 1; i <= 50; i++) {
//     users.value.push({ id: i, name: `User ${i}` });
// }

const selectedUser = ref(null);

// Generate 50 mock messages
const generateMessages = () => {
    const msgs = [];
    const now = moment();
    for (let i = 0; i < 50; i++) {
        msgs.push({
            id: i + 1,
            from: i % 2 === 0 ? 'me' : 'them',
            text: `This is message #${i + 1}`,
            time: now.clone().subtract(50 - i, 'minutes').format('LT')
        });
    }
    return msgs;
};

const messages = ref([]);
const messageContainer = ref(null);

// Scroll to bottom of chat
const scrollToBottom = () => {
    nextTick(() => {
        if (messageContainer.value) {
            messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
        }
    });
};

// Select user + load messages
const selectUser = (user) => {
    selectedUser.value = user;
    messages.value = generateMessages();
    scrollToBottom();
};

onMounted(() => {
    selectedUser.value = props.users[0];
    messages.value = generateMessages();
    scrollToBottom();
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template> -->

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
                                <!-- Header -->
                                <div class="p-4 border-b font-semibold bg-gray-100 sticky top-0 z-10">
                                    Chat with {{ selectedUser?.name }}
                                </div>

                                <!-- Messages -->
                                <div
                                    ref="messageContainer"
                                    class="flex-1 overflow-y-auto p-4 space-y-2"
                                >
                                    <div
                                        v-for="message in messages"
                                        :key="message.id"
                                        class="flex flex-col"
                                        :class="message.from === 'me' ? 'items-end' : 'items-start'"
                                    >
                                        <div
                                            class="px-3 py-2 rounded max-w-[70%]"
                                            :class="message.from === 'me' ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-800'"
                                        >
                                            {{ message.text }}
                                        </div>
                                        <span class="text-xs text-gray-500 mt-1">
                                            {{ message.time }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Input -->
                                <div class="flex border-t p-3 bg-white">
                                    <input
                                        type="text"
                                        class="flex-1 border rounded-l px-4 py-2 focus:outline-none"
                                        placeholder="Type your message..."
                                    />
                                    <button class="bg-blue-600 text-white px-4 py-2 rounded-r hover:bg-blue-700">
                                        Send
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

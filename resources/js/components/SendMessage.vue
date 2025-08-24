<template>
    <div class="send-message">
        <img :src="user?.avatar || 'https://avatars.preply.com/i/logos/i/logos/avatar_t7k2mmf4dv.jpg'" class="avatar">

        <h4 class="name">Liên hệ {{ user?.full_name || 'User' }}</h4>
        <span class="desc">Giới thiệu bản thân với {{ user?.full_name }}, chia sẻ vấn đề của bạn và đặt bất kỳ câu hỏi nào</span>

        <base-input type="textarea" v-model="message" placeholder="Viết tin nhắn của bạn ở đây…"></base-input>

        <div class="send-message-actions">
            <button class="btn-xl btn-primary w-100 border-r-2" @click="sendMessage" :disabled="!message.trim()">
                Gửi tin nhắn
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, getCurrentInstance } from "vue";
import { useStore } from "vuex";

const { proxy } = getCurrentInstance();

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['messageSent', 'close']);

const message = ref(`Xin chào ${props.user?.full_name || 'User'}`);

const sendMessage = async () => {
    if (!message.value.trim()) return;

    try {
        // Gửi tin nhắn đến API
        const response = await proxy.$api.apiPost('send-message', {
            receiver_id: props.user.uid,
            content: message.value
        });

        if (response.success) {
            proxy.$notification.success('Tin nhắn đã được gửi thành công!');
            emit('messageSent', response.message);
            emit('close');
        } else {
            proxy.$notification.error('Có lỗi xảy ra khi gửi tin nhắn!');
        }
    } catch (error) {
        console.error('Error sending message:', error);
        proxy.$notification.error('Có lỗi xảy ra khi gửi tin nhắn!');
    }
}
</script>

<style scoped>
    .send-message {
        text-align: center;
    }
    .avatar {
        width: 150px;
        height: 150px;
        border-radius: 0.7rem;
        margin-top: 1rem;
    }

    .name {
        font-size: var(--font-size-heading-4);
        font-weight: 700;
        margin-top: 1rem;
    }

    .desc {
        font-size: var(--font-size-base);
        color: var(--gray-800);
        margin-bottom: 1rem;
        display: block;
    }

    .send-message-actions {
        margin-top: 1.3rem;
    }
</style>

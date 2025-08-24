<template>
<div class="chat-app">
    <div class="sidebar" v-show="!isMobile || showSidebar">
        <div class="sidebar-header">
            <div class="sidebar-header_title">
                Đoạn chat
            </div>
            <div class="sidebar-header_action">
                <button class="btn-no-bg">
                    <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" bis_size="{&quot;x&quot;:239,&quot;y&quot;:24,&quot;w&quot;:16,&quot;h&quot;:16,&quot;abs_x&quot;:634,&quot;abs_y&quot;:152}"><path d="M5 12h14" bis_size="{&quot;x&quot;:242,&quot;y&quot;:32,&quot;w&quot;:9,&quot;h&quot;:0,&quot;abs_x&quot;:637,&quot;abs_y&quot;:160}"></path><path d="M12 5v14" bis_size="{&quot;x&quot;:247,&quot;y&quot;:27,&quot;w&quot;:0,&quot;h&quot;:9,&quot;abs_x&quot;:642,&quot;abs_y&quot;:155}"></path></svg>
                </button>
                <button class="btn-no-bg">
                    <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" bis_size="{&quot;x&quot;:279,&quot;y&quot;:24,&quot;w&quot;:16,&quot;h&quot;:16,&quot;abs_x&quot;:674,&quot;abs_y&quot;:152}"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" bis_size="{&quot;x&quot;:281,&quot;y&quot;:25,&quot;w&quot;:12,&quot;h&quot;:13,&quot;abs_x&quot;:676,&quot;abs_y&quot;:153}"></path><circle cx="12" cy="12" r="3" bis_size="{&quot;x&quot;:285,&quot;y&quot;:30,&quot;w&quot;:4,&quot;h&quot;:4,&quot;abs_x&quot;:680,&quot;abs_y&quot;:158}"></circle></svg>
                </button>
            </div>
        </div>
        <div class="sidebar-search">
            <base-input v-model="search" placeholder="Tìm kiếm cuộc trò chuyện...">
                <template #icon>
                    <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" bis_size="{&quot;x&quot;:28,&quot;y&quot;:76,&quot;w&quot;:16,&quot;h&quot;:16,&quot;abs_x&quot;:680,&quot;abs_y&quot;:206}"><circle cx="11" cy="11" r="8" bis_size="{&quot;x&quot;:30,&quot;y&quot;:78,&quot;w&quot;:10,&quot;h&quot;:10,&quot;abs_x&quot;:682,&quot;abs_y&quot;:208}"></circle><path d="m21 21-4.3-4.3" bis_size="{&quot;x&quot;:39,&quot;y&quot;:87,&quot;w&quot;:2,&quot;h&quot;:2,&quot;abs_x&quot;:691,&quot;abs_y&quot;:217}"></path></svg>
                </template>
            </base-input>
        </div>
        <div class="sidebar-navs">
            <div class="sidebar-navs_item">
                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" bis_size="{&quot;x&quot;:28,&quot;y&quot;:146,&quot;w&quot;:16,&quot;h&quot;:16,&quot;abs_x&quot;:423,&quot;abs_y&quot;:274}"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" bis_size="{&quot;x&quot;:29,&quot;y&quot;:148,&quot;w&quot;:12,&quot;h&quot;:12,&quot;abs_x&quot;:424,&quot;abs_y&quot;:276}"></path></svg>
                <span>Tất cả</span>
            </div>
            <div class="sidebar-navs_item">
                <svg class="icon-md" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.528"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M11.9426 1.25C9.63423 1.24999 7.82519 1.24998 6.41371 1.43975C4.96897 1.63399 3.82895 2.03933 2.93414 2.93414C2.03933 3.82895 1.63399 4.96897 1.43975 6.41371C1.24998 7.82519 1.24999 9.63423 1.25 11.9426V12.0574C1.24999 14.3658 1.24998 16.1748 1.43975 17.5863C1.63399 19.031 2.03933 20.1711 2.93414 21.0659C3.82895 21.9607 4.96897 22.366 6.41371 22.5603C7.82519 22.75 9.63423 22.75 11.9426 22.75H12.0574C14.3658 22.75 16.1748 22.75 17.5863 22.5603C19.031 22.366 20.1711 21.9607 21.0659 21.0659C21.9607 20.1711 22.366 19.031 22.5603 17.5863C22.75 16.1748 22.75 14.3658 22.75 12.0574V10.5C22.75 10.0858 22.4142 9.75 22 9.75C21.5858 9.75 21.25 10.0858 21.25 10.5V12C21.25 14.3782 21.2484 16.0864 21.0736 17.3864C20.9018 18.6648 20.5749 19.4355 20.0052 20.0052C19.4355 20.5749 18.6648 20.9018 17.3864 21.0736C16.0864 21.2484 14.3782 21.25 12 21.25C9.62178 21.25 7.91356 21.2484 6.61358 21.0736C5.33517 20.9018 4.56445 20.5749 3.9948 20.0052C3.42514 19.4355 3.09825 18.6648 2.92637 17.3864C2.75159 16.0864 2.75 14.3782 2.75 12C2.75 9.62178 2.75159 7.91356 2.92637 6.61358C3.09825 5.33517 3.42514 4.56445 3.9948 3.9948C4.56445 3.42514 5.33517 3.09825 6.61358 2.92637C7.91356 2.75159 9.62178 2.75 12 2.75H13.5C13.9142 2.75 14.25 2.41421 14.25 2C14.25 1.58579 13.9142 1.25 13.5 1.25H11.9426Z" fill="#1C274C"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M19 1.25C16.9289 1.25 15.25 2.92893 15.25 5C15.25 7.07107 16.9289 8.75 19 8.75C21.0711 8.75 22.75 7.07107 22.75 5C22.75 2.92893 21.0711 1.25 19 1.25ZM16.75 5C16.75 3.75736 17.7574 2.75 19 2.75C20.2426 2.75 21.25 3.75736 21.25 5C21.25 6.24264 20.2426 7.25 19 7.25C17.7574 7.25 16.75 6.24264 16.75 5Z" fill="#1C274C"></path> <path d="M6.25 14C6.25 13.5858 6.58579 13.25 7 13.25H16C16.4142 13.25 16.75 13.5858 16.75 14C16.75 14.4142 16.4142 14.75 16 14.75H7C6.58579 14.75 6.25 14.4142 6.25 14Z" fill="#1C274C"></path> <path d="M7 16.75C6.58579 16.75 6.25 17.0858 6.25 17.5C6.25 17.9142 6.58579 18.25 7 18.25H13C13.4142 18.25 13.75 17.9142 13.75 17.5C13.75 17.0858 13.4142 16.75 13 16.75H7Z" fill="#1C274C"></path> </g></svg>
                <span>Chưa đọc</span>
            </div>
            <div class="sidebar-navs_item">
                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" bis_size="{&quot;x&quot;:235,&quot;y&quot;:146,&quot;w&quot;:16,&quot;h&quot;:16,&quot;abs_x&quot;:630,&quot;abs_y&quot;:274}"><rect width="20" height="5" x="2" y="3" rx="1" bis_size="{&quot;x&quot;:236,&quot;y&quot;:148,&quot;w&quot;:13,&quot;h&quot;:3,&quot;abs_x&quot;:631,&quot;abs_y&quot;:276}"></rect><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" bis_size="{&quot;x&quot;:237,&quot;y&quot;:152,&quot;w&quot;:10,&quot;h&quot;:8,&quot;abs_x&quot;:632,&quot;abs_y&quot;:280}"></path><path d="M10 12h4" bis_size="{&quot;x&quot;:241,&quot;y&quot;:154,&quot;w&quot;:2,&quot;h&quot;:0,&quot;abs_x&quot;:636,&quot;abs_y&quot;:282}"></path></svg>
                <span>Lưu trữ</span>
            </div>
        </div>
        <div class="conversation-list">
            <div v-for="conv in filteredConversations" :key="conv.id" :class="['conversation-item', {selected: selectedConversation && selectedConversation.id === conv.id}]" @click="selectConversation(conv)">
                <div class="avatar-container">
                    <img :src="conv.avatar || defaultAvatar" class="avatar" />
                    <div v-if="conv.is_online" class="online-indicator"></div>
                </div>
                <div class="conv-info">
                    <div class="conv-name-row">
                        <span class="conv-name">{{ conv.name }}</span>
                        <span class="conv-date">{{ $helper.formatRelativeTime(conv.last_message.created_at) }}</span>
                    </div>
                    <div class="conv-last">{{ conv.last_message.content }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="chat-main" v-show="!isMobile || !showSidebar">
        <div class="chat-header">
            <div class="header-info">
                <button v-if="isMobile && !showSidebar" class="btn-back" @click="backToSidebar">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <img v-if="selectedConversation?.avatar" :src="selectedConversation?.avatar" class="header-avatar" />
                <div v-else class="header-avatar">{{ $helper.getFirstCharacterOfLastName(selectedConversation?.name) }}</div>

                <div class="header-info-content">
                    <div class="header-name">{{ selectedConversation?.name || '' }}</div>
                    <div class="header-status">
                        <span v-if="selectedConversation?.is_online" class="status-online">Đang hoạt động</span>
                        <span v-else-if="selectedConversation?.last_activity" class="status-offline">{{ $helper.formatRelativeTime(selectedConversation.last_activity) }}</span>
                        <span v-else class="status-offline">Hoạt động gần đây</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="message-list chat-bg">
            <div v-for="msg in selectedConversation?.messages || []" :key="msg.id" class="message-row" :class="{me: msg.is_sent}">
                <template v-if="!msg.is_sent">
                    <img class="msg-avatar" :src="selectedConversation.avatar || defaultAvatar" />
                    <div class="msg-bubble">
                        <div class="msg-content">{{ msg.content }}</div>
                        <div class="msg-time">{{ $helper.formatTime(msg.created_at) }}</div>
                    </div>
                </template>
                <template v-else>
                    <div class="msg-bubble me">
                        <div class="msg-content">{{ msg.content }}</div>
                        <div class="msg-time">{{ $helper.formatTime(msg.created_at) }}</div>
                    </div>
                </template>
            </div>
        </div>
        <div class="message-input-bar">
            <input type="text" v-model="newMessage" @keyup.enter="sendMessage" placeholder="Nhập tin nhắn..." />
            <div class="icon-send" @click="sendMessage">
                <svg class="icon-lg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M20.33 3.66996C20.1408 3.48213 19.9035 3.35008 19.6442 3.28833C19.3849 3.22659 19.1135 3.23753 18.86 3.31996L4.23 8.19996C3.95867 8.28593 3.71891 8.45039 3.54099 8.67255C3.36307 8.89471 3.25498 9.16462 3.23037 9.44818C3.20576 9.73174 3.26573 10.0162 3.40271 10.2657C3.5397 10.5152 3.74754 10.7185 4 10.85L10.07 13.85L13.07 19.94C13.1906 20.1783 13.3751 20.3785 13.6029 20.518C13.8307 20.6575 14.0929 20.7309 14.36 20.73H14.46C14.7461 20.7089 15.0192 20.6023 15.2439 20.4239C15.4686 20.2456 15.6345 20.0038 15.72 19.73L20.67 5.13996C20.7584 4.88789 20.7734 4.6159 20.7132 4.35565C20.653 4.09541 20.5201 3.85762 20.33 3.66996ZM4.85 9.57996L17.62 5.31996L10.53 12.41L4.85 9.57996ZM14.43 19.15L11.59 13.47L18.68 6.37996L14.43 19.15Z" fill="#ffffff"></path> </g></svg>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
    onUnmounted,
    getCurrentInstance,
    watch
} from 'vue';
import { useStore } from 'vuex'

const store = useStore();
const {
    proxy
} = getCurrentInstance();

const defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=eee&color=888&size=64';
const search = ref('');
const conversations = ref([]);
const selectedConversation = ref(null);
const newMessage = ref('');
const currentUserUid = ref(store.state.userData.uid);

// Mobile sidebar/chat detail state
const showSidebar = ref(window.innerWidth <= 600 ? true : false);
const isMobile = ref(window.innerWidth <= 600);

function handleResize() {
    isMobile.value = window.innerWidth <= 600;
    if (isMobile.value) {
        // Nếu đang ở mobile, mặc định chỉ hiện sidebar
        if (!showSidebar.value && !selectedConversation.value) {
            showSidebar.value = true;
        }
    } else {
        // Desktop: luôn hiện cả 2
        showSidebar.value = false;
    }
}

window.addEventListener('resize', handleResize);

const filteredConversations = computed(() => {
    if (!search.value) return conversations.value;
    return conversations.value.filter(c => c.name.toLowerCase().includes(search.value.toLowerCase()));
});

function selectConversation(conv) {
    selectedConversation.value = null;
    getMessageDetail(conv.other_uid).then(data => {
        selectedConversation.value = {
            ...conv,
            ...data,
        };
        // Nếu là mobile, ẩn sidebar, hiện chat detail
        if (isMobile.value) {
            showSidebar.value = false;
        }
    });
}

function backToSidebar() {
    selectedConversation.value = null;
    if (isMobile.value) {
        showSidebar.value = true;
    }
}

const sendMessage = async () => {
    if (!newMessage.value.trim() || !selectedConversation.value) return;
    try {
        // Gửi tin nhắn qua API
        const response = await proxy.$api.apiPost('send-message', {
            receiver_id: selectedConversation.value.other_uid,
            content: newMessage.value,
        });
        const msg = response.message;

        selectedConversation.value.messages.push({
            ...msg,
            is_sent: true,
        });

        conversations.value.find(c => c.id === selectedConversation.value.id).last_message = response.last_message;

        newMessage.value = '';
    } catch (e) {
        console.error('Send message error:', e);
    }
};

const listenForMessages = () => {
    const channelName = `chat.${currentUserUid.value}`;
    console.log('[Echo] Listening to:', channelName);

    if (window.Echo) {
        window.Echo.private(channelName)
        .listen('MessageSent', (e) => {
            if (
                selectedConversation.value &&
                e.message.sender_id === selectedConversation.value.other_uid
            ) {
                selectedConversation.value.messages.push({
                    ...e.message,
                    is_sent: false,
                });
                conversations.value.find(c => c.id === selectedConversation.value.id).last_message = e.last_message;
            }
        });
    }
};

const listenForPresence = () => {
    if (window.Echo) {
        window.Echo.join('chat.presence')
        .here((users) => {
            updateOnlineStatus(users);
        })
        .joining((user) => {
            console.log('User joined:', user);
            updateUserOnlineStatus(user.id, true);
        })
        .leaving((user) => {
            console.log('User left:', user);
            updateUserOnlineStatus(user.id, false);
        });
    }
};

const updateOnlineStatus = (users) => {
    // Update online status for all users in the channel
    users.forEach(user => {
        updateUserOnlineStatus(user.id, true);
    });
};

const updateUserOnlineStatus = (userId, is_online) => {
    // Update the selected conversation's online status
    if (selectedConversation.value && selectedConversation.value.other_uid == userId) {
        selectedConversation.value.is_online = is_online;
    }

    // Update conversation list online status
    const conversation = conversations.value.find(c => c.other_uid == userId);
    if (conversation) {
        conversation.is_online = is_online;
    }
};

// Gọi listenForMessages mỗi khi chọn hội thoại mới
watch(selectedConversation, (val) => {
    if (val) listenForMessages();
});


const getAllContactedUsers = async () => {
    const response = await proxy.$api.apiGet('contacted-users');
    return response.data;
};

const getMessageDetail = (other_uid) => {
    return proxy.$api.apiGet(`messages/${other_uid}`);
};

onMounted(async () => {
    const data = await getAllContactedUsers();
    conversations.value = (data || []).map(conv => {
        return {
            ...conv,
            name: conv.other_user.full_name,
            avatar: conv.other_user.avatar || defaultAvatar,
            last_message: conv.last_message,
            archived: false,
            other_uid: conv.other_user.uid,
        };
    });
    if (conversations.value.length > 0) {
        selectConversation(conversations.value[0]);
    }

    // Start listening for presence events
    listenForPresence();

    store.dispatch('updateHiddenFooter', true);
});

onUnmounted(() => {
    store.dispatch('updateHiddenFooter', false);
});
</script>

<style scoped>
.chat-app {
    display: flex;
    height: calc(100vh - (var(--height-header) + 1px));
    background: #f7f8fa;
    font-family: 'Inter', Arial, sans-serif;
}

.sidebar {
    width: 380px;
    background: #fff;
    border-right: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 1.3rem;
    padding: 1rem 0;
}

.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1rem;
}

.sidebar-header_title {
    font-size: var(--font-size-heading-4);
    font-weight: 900;
    background-image: linear-gradient(to right, var(--color-primary), var(--color-primary-light));
    color: transparent;
    -webkit-background-clip: text;
    background-clip: text;
}

.sidebar-header_action {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.sidebar-navs {
    display: flex;
    align-items: center;
    justify-content: space-between;
    /* gap: 1rem; */
    padding: 0.6rem 1rem;
    border-top: 1px solid var(--gray-100);
    border-bottom: 1px solid var(--gray-100);
}

.sidebar-navs_item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    background: white;
    border-radius: 2rem;
    color: black;
    cursor: pointer;
}

.sidebar-navs_item:hover, .sidebar-navs_item.selected {
    background: #eff6ff;
    color: #2563eb;
}

.sidebar-search {
    padding: 0 1rem;
    align-content: center;
    /* border-bottom: 1px solid #f0f0f0; */
}

.sidebar-search input {
    width: 100%;
    padding: 0.7rem 1rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #f7f8fa;
    font-size: 1rem;
    outline: none;
}

.conversation-list {
    overflow-y: auto;
    padding: 0 1rem;
    display: grid;
    gap: 0.2rem;
}

.conversation-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    cursor: pointer;
    background: transparent;
    border-left: 3px solid transparent;
    transition: background 0.2s;
    padding: 0.7rem 0.8rem;
    border-radius: 0.6rem;
}

.conversation-item.selected {
    background: var(--gray-50);
}
.conversation-item:hover {
    background: var(--gray-50);
}

.avatar-container {
    position: relative;
}

.avatar {
    width: 3.4rem;
    height: 3.4rem;
    border-radius: 50%;
    object-fit: cover;
    background: #eee;
    border: 2px solid white;
}

.online-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 0.9rem;
    height: 0.9rem;
    background: #10b981;
    border-radius: 50%;
    border: 2px solid white;
}

.conv-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.conv-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.conv-name {
    font-weight: 700;
    color: #222;
    font-size: 1rem;
}

.conv-last {
    color: #888;
    font-size: 0.95rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 200px;
}

.conv-date {
    color: #bbb;
    font-size: 0.85rem;
    min-width: 50px;
    text-align: right;
}

.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #f7f8fa;
}

.chat-header {
    padding: 0 1.5rem;
    height: 5rem;
    align-content: center;
    background: #fff;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.header-info {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.header-info-content {
    display: flex;
    flex-direction: column;
    line-height: 1;
    gap: 0.4rem;
}

.header-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    background: #eee;
    border: 2px solid white;
}

.header-name {
    font-weight: 600;
    font-size: 1.1rem;
    color: #222;
}

.header-status {
    color: #bbb;
    font-size: 0.8rem;
}

.status-online {
    color: #10b981;
    font-weight: 500;
}

.status-offline {
    color: #bbb;
}

.header-actions {
    display: flex;
    gap: 1.2rem;
    color: #bbb;
    font-size: 1.3rem;
}

.chat-bg {
    background: #f7f8fa;
}

.message-list {
    flex: 1;
    overflow-y: auto;
    padding: 2rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}

.message-row {
    display: flex;
    align-items: flex-end;
    gap: 0.7rem;
    margin-bottom: 0.2rem;
}

.message-row.me {
    justify-content: flex-end;
}

.msg-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    object-fit: cover;
    background: #eee;
    border: 2px solid white;
}

.msg-bubble {
    background: #fff;
    border-radius: 16px;
    padding: 0.9rem 1.3rem;
    min-width: 60px;
    max-width: 420px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    font-size: 1rem;
}

.msg-bubble.me {
    background: var(--blue-600);
    color: #fff;
    align-items: flex-end;
}

.msg-content {
    margin-bottom: 0.2rem;
    word-break: break-word;
}

.msg-time {
    font-size: 0.85rem;
    align-self: flex-end;
    margin-top: 0.1rem;
}

.message-input-bar {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.1rem 2rem;
    border-top: 1px solid #f0f0f0;
    background: #fff;
}

.message-input-bar input {
    flex: 1;
    padding: 0.9rem 1.2rem;
    border: 1px solid #e5e7eb;
    border-radius: 2rem;
    font-size: 1rem;
    background: white;
    outline: none;
}

.icon-attach,
.icon-send,
.icon-notification,
.icon-more {
    width: 2.5rem;
    height: 2.5rem;
    display: inline-block;
    border: 1px solid var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: black;
    cursor: pointer;
}

.icon-attach:hover,
.icon-send:hover,
.icon-notification:hover,
.icon-more:hover {
    background: var(--gray-100);
}

.icon-attach,
.icon-send {
    width: 3rem;
    height: 3rem;
}

.icon-send {
    background: var(--blue-500);
    color: #fff;
}

.btn-back {
    background: none;
    border: none;
    padding: 0.2rem 0.5rem;
    cursor: pointer;
    color: #2563eb;
    display: flex;
    align-items: center;
}

@media (max-width: 900px) {
}

@media (max-width: 600px) {
    .sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid #eee;
    }

    .message-input-bar {
        padding: 0.8rem 1rem;
        gap: 0.5rem;
    }

    .header-actions {
        gap: 0.5rem;
    }

    .btn-back {
        background: none;
        border: none;
        padding: 0.2rem 0.5rem;
        cursor: pointer;
        color: #2563eb;
        display: flex;
        align-items: center;
    }

    .chat-header {
        padding: 0 0.5rem;
    }
}
</style>

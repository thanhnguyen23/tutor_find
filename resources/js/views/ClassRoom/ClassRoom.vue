<script setup>
import { ref, computed, onMounted, onBeforeUnmount, getCurrentInstance, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useStore } from 'vuex'
import Peer from 'simple-peer'

const {proxy} = getCurrentInstance()
const route = useRoute()
const store = useStore()
const channel = ref(null)
const peer = ref(null)

const isLoading = ref(true);
const localStream = ref(null)
const screenStream = ref(null)
const remoteStreamPresent = ref(false)
const micEnabled = ref(true)
const camEnabled = ref(true)
const isScreenSharing = ref(false)
const connectionStatus = ref('Chưa kết nối...')
const participantsCount = ref(1)
const hasStarted = ref(false)
const classroom = ref(null)
const uid = computed(() => store.state.userData?.uid || '')
const userName = computed(() => store.state.userData?.full_name || 'Guest')

const localVideo = ref(null)
const remoteVideo = ref(null)
const targetUid = ref('')
const peerName = ref('')
const lastSentSdp = ref('')
const canAccessClassroom = ref(true)

const statusText = computed(() => connectionStatus.value)
const selfLabel = computed(() => userName.value)
const peerLabel = computed(() => peerName.value)
const remoteMuted = computed(() => false)

const preferredConstraints = {
    video: {
        width: {
            ideal: 640
        },
        height: {
            ideal: 360
        },
        frameRate: {
            ideal: 24
        }
    },
    audio: true
}

const iceServers = [{
    urls: 'stun:stun.l.google.com:19302'
}]

// Fetch classroom data by route param id
async function loadClassroom() {
    try {
        const id = route.params.id
        const res = await proxy.$api.apiGet(`classrooms/${id}`)
        if (res?.success) {
            classroom.value = res.data

            // Kiểm tra status classroom
            if (res.data.status === 'ended') {
                proxy.$notification?.error('Lớp học đã kết thúc.')

                redirectCantAccessClassroom();
                return false
            }

            // Kiểm tra hết hạn hoặc thời gian trước khi cho phép tham gia
            if (res.time_info && !res.time_info.can_start) {
                const message = res.time_info.time_status_text
                proxy.$notification?.error(message)
                redirectCantAccessClassroom();
                return false
            }
        } else {
            classroom.value = { id }
        }
        return true
    } catch (e) {
        classroom.value = { id: route.params.id }
        proxy.$notification?.error('Không thể tải thông tin lớp học')
        redirectCantAccessClassroom();
        return false
    }
}

// ===== Methods =====
async function initEcho() {
    if (!window.Echo) {
        connectionStatus.value = 'Chưa sẵn sàng'
        return
    }

    channel.value = window.Echo.join(`classroom.${classroom.value.id}`)
    .here((users) => {
        participantsCount.value = users.length
        connectionStatus.value = 'Đã kết nối'
        const other = (users || []).find(u => u.uid !== uid.value)

        if (other) {
            targetUid.value = other.uid
            peerName.value = other.name || ''
        }
        if (users.length >= 2) createPeer(true)
    })
    .joining((user) => {
        participantsCount.value += 1
        if (user.uid !== uid.value) {
            targetUid.value = user.uid
            peerName.value = user.name || ''
        }
    })
    .leaving(() => {
        participantsCount.value = Math.max(0, participantsCount.value - 1)
        remoteStreamPresent.value = false;
    })

    window.Echo.private(`private-webrtc.${uid.value}`)
        .listen('.webrtc.signal', (e) => {
            if (e.from !== uid.value) {
                if (!peer.value) {
                    createPeer(false)
                } else if (peer.value.destroyed) {
                    createPeer(false)
                }
                if (peer.value && !peer.value.destroyed) {
                    peer.value.signal(e.signal)
                }
            }
        })
}

async function startMedia() {
    try {
        let constraints = preferredConstraints

        if (uid.value === 'f6f01db2-0bb4-4fcb-8164-e2915c286033') {
            const devices = await navigator.mediaDevices.enumerateDevices()
            const videoDevices = devices.filter(d => d.kind === 'videoinput')
            const obsCamera = videoDevices.find(d => d.label.includes('OBS Virtual Camera'))

            if (obsCamera) {
                constraints.video.deviceId = { exact: obsCamera.deviceId }
            }
        }

        const stream = await navigator.mediaDevices.getUserMedia(constraints)
        localStream.value = stream
        localVideo.value.srcObject = stream

        if (participantsCount.value >= 2) {
            createPeer(true)
        }
    } catch (e) {
        connectionStatus.value = 'Không thể truy cập media'
    }
}

async function toggleScreenShare() {
    if (!peer.value) return
    if (!isScreenSharing.value) {
        try {
            screenStream.value = await navigator.mediaDevices.getDisplayMedia({ video: true })
            const screenTrack = screenStream.value.getVideoTracks()[0]
            const sender = peer.value._pc.getSenders().find(s => s.track?.kind === 'video')
            await sender.replaceTrack(screenTrack)
            localVideo.value.srcObject = screenStream.value
            isScreenSharing.value = true
            screenTrack.onended = () => toggleScreenShare()
        } catch (e) {
            console.error('Share screen error:', e)
        }
    } else {
        const camTrack = localStream.value.getVideoTracks()[0]
        const sender = peer.value._pc.getSenders().find(s => s.track?.kind === 'video')
        await sender.replaceTrack(camTrack)

        screenStream.value?.getTracks().forEach(t => t.stop())
        localVideo.value.srcObject = localStream.value
        isScreenSharing.value = false
    }
}

function createPeer(initiator) {
    if (!localStream.value || peer.value) return

    peer.value = new Peer({
        initiator,
        trickle: false,
        stream: localStream.value,
        config: {
            iceServers
        },
        reconnectTimer: 3000
    })

    peer.value.on('signal', async (data) => {
        try {
            if (data?.sdp) {
                if (lastSentSdp.value && lastSentSdp.value === data.sdp) return
                lastSentSdp.value = data.sdp
            }
            await proxy.$api.apiPost('webrtc/signal', {
                classroom_id: classroom.value.id,
                from: uid.value,
                to: targetUid.value || '*',
                signal: data
            })
        } catch (e) {
            console.error('signal send error', e)
        }
    })

    peer.value.on('connect', () => {
        if (!hasStarted.value) {
            hasStarted.value = true;
        }
    })

    peer.value.on('stream', (remote) => {
        remoteVideo.value.srcObject = remote
        remoteStreamPresent.value = true
    })

    peer.value.on('close', () => {
        connectionStatus.value = 'Mất kết nối'
        destroyPeer()
    })

    peer.value.on('error', () => {
        connectionStatus.value = 'Lỗi kết nối'
        destroyPeer()
    })
}

async function endSession() {
    await cleanup();
    connectionStatus.value = 'Đã kết thúc';
}

function toggleMic() {
    micEnabled.value = !micEnabled.value
    localStream.value?.getAudioTracks().forEach(t => (t.enabled = micEnabled.value))
}

function toggleCamera() {
    camEnabled.value = !camEnabled.value
    localStream.value?.getVideoTracks().forEach(t => (t.enabled = camEnabled.value))
}

function destroyPeer() {
    if (peer.value) {
        try {
            peer.value.destroy()
        } catch {}
        peer.value = null
    }
}

async function cleanup() {
    destroyPeer()

    localStream.value?.getTracks().forEach(t => t.stop())
    screenStream.value?.getTracks().forEach(t => t.stop())

    if (window.Echo) {
        window.Echo.leave(`classroom.${classroom.value?.id}`)
        window.Echo.leave(`presence-classroom.${classroom.value?.id}`)
        window.Echo.leave(`private-webrtc.${uid.value}`)
    }

    if (hasStarted.value && participantsCount.value == 0) {
        proxy.$api.apiPost(`classrooms/${classroom.value.id}/end`, {
            participants_count: participantsCount.value
        }).catch(() => {})
    }
}

const redirectCantAccessClassroom = () => {
    canAccessClassroom.value = false

    setTimeout(() => {
        proxy.$router.push({ name: 'classroom-manager' })
    }, 3000)
}

// ===== Computed =====
const isLocalVideoOn = computed(() => {
    const track = localStream.value?.getVideoTracks?.()[0]
    return !!(track && track.enabled)
})

// ===== Lifecycle =====
onMounted(async () => {
    const canAccess = await loadClassroom();

    // Chỉ khởi tạo media và echo nếu được phép truy cập
    if (canAccess) {
        await startMedia();
        await initEcho();
    }
})


onBeforeUnmount(async () => {
    await cleanup();
})
</script>

<template>
<div class="webrtc-room">
    <!-- Access denied message -->
    <div v-if="!canAccessClassroom" class="access-denied">
        <div class="access-denied-content">
            <svg class="access-denied-icon" xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="m4.9 4.9 14.2 14.2"></path>
            </svg>
            <h2>Không thể truy cập lớp học</h2>
            <p v-if="classroom?.status === 'ended'">Lớp học đã kết thúc.</p>
            <p v-else>Lớp học chưa đến giờ bắt đầu.</p>
            <p>Bạn sẽ được chuyển về trang quản lý lớp học trong giây lát...</p>
            <router-link to="/classroom-manager" class="btn-md btn-primary">
                Quay lại trang quản lý
            </router-link>
        </div>
    </div>

    <!-- Main classroom interface -->
    <div v-else>
        <!-- Top status bar -->
        <div class="room-status">
        <div class="status-left">
            <span class="dot-online"></span>
            <span class="status-text">{{ statusText }}</span>
        </div>
        <div class="status-right">
            <span class="participants-chip">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                {{ participantsCount }} người tham gia
            </span>
            <div class="icon-btn" title="Cài đặt">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </div>
        </div>
    </div>

    <!-- Videos grid -->
    <div class="videos">
        <div class="video-tile">
            <video ref="localVideo" autoplay playsinline muted></video>
            <div v-if="!isLocalVideoOn" class="video-overlay">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5">
                    <path d="M16 10l4.553-2.276A2 2 0 0 1 22 9.528v4.944a2 2 0 0 1-3.447 1.804L16 14" />
                    <rect x="2" y="6" width="14" height="12" rx="2" /></svg>
                <div>Camera đã tắt</div>
            </div>
            <span class="badge mute" v-if="!micEnabled" title="Mic tắt">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md">
                    <line x1="2" x2="22" y1="2" y2="22"></line>
                    <path d="M18.89 13.23A7.12 7.12 0 0 0 19 12v-2"></path>
                    <path d="M5 10v2a7 7 0 0 0 12 5"></path>
                    <path d="M15 9.34V5a3 3 0 0 0-5.68-1.33"></path>
                    <path d="M9 9v3a3 3 0 0 0 5.12 2.12"></path>
                    <line x1="12" x2="12" y1="19" y2="22"></line>
                </svg>
            </span>
            <div class="video-caption">{{ selfLabel }}</div>
        </div>
        <div class="video-tile">
            <video ref="remoteVideo" autoplay playsinline></video>
            <div v-if="!remoteStreamPresent" class="video-overlay">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5">
                    <path d="M16 10l4.553-2.276A2 2 0 0 1 22 9.528v4.944a2 2 0 0 1-3.447 1.804L16 14" />
                    <rect x="2" y="6" width="14" height="12" rx="2" /></svg>
                <div>Camera đã tắt</div>
            </div>
            <span class="badge mute" v-if="remoteMuted" title="Mic tắt">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md">
                    <line x1="2" x2="22" y1="2" y2="22"></line>
                    <path d="M18.89 13.23A7.12 7.12 0 0 0 19 12v-2"></path>
                    <path d="M5 10v2a7 7 0 0 0 12 5"></path>
                    <path d="M15 9.34V5a3 3 0 0 0-5.68-1.33"></path>
                    <path d="M9 9v3a3 3 0 0 0 5.12 2.12"></path>
                    <line x1="12" x2="12" y1="19" y2="22"></line>
                </svg>
            </span>
            <div class="video-caption">{{ peerLabel }}</div>
        </div>
    </div>

    <!-- Bottom controls -->
    <div class="control-bar">
        <button class="btn-lg btn-primary" @click="toggleCamera">
            <span v-if="camEnabled">
                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"></path>
                    <rect x="2" y="6" width="14" height="12" rx="2"></rect>
                </svg>
            </span>
            <span v-else>
                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.66 6H14a2 2 0 0 1 2 2v2.5l5.248-3.062A.5.5 0 0 1 22 7.87v8.196"></path>
                    <path d="M16 16a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h2"></path>
                    <path d="m2 2 20 20"></path>
                </svg>
            </span>
            {{ camEnabled?'Bật camera' : 'Bật camera' }}
        </button>
        <button class="btn-lg btn-primary" @click="toggleMic">
            <span v-if="micEnabled">
                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                    <line x1="12" x2="12" y1="19" y2="22"></line>
                </svg>
            </span>
            <span v-else>
                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="2" x2="22" y1="2" y2="22"></line>
                    <path d="M18.89 13.23A7.12 7.12 0 0 0 19 12v-2"></path>
                    <path d="M5 10v2a7 7 0 0 0 12 5"></path>
                    <path d="M15 9.34V5a3 3 0 0 0-5.68-1.33"></path>
                    <path d="M9 9v3a3 3 0 0 0 5.12 2.12"></path>
                    <line x1="12" x2="12" y1="19" y2="22"></line>
                </svg>
            </span>
            {{ micEnabled?'Bật mic' : 'Bật mic' }}
        </button>
        <button class="btn-lg btn-primary" @click="toggleScreenShare">
            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="14" x="2" y="3" rx="2"></rect>
                <line x1="8" x2="16" y1="21" y2="21"></line>
                <line x1="12" x2="12" y1="17" y2="21"></line>
            </svg> Chia sẻ màn hình
        </button>
        <button class="btn-lg btn-primary" @click="endSession">
            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
            </svg> Kết thúc
        </button>
    </div>
    </div>
</div>
</template>

<style scoped>
.webrtc-room {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.access-denied {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    padding: 2rem;
}

.access-denied-content {
    text-align: center;
    max-width: 500px;
    padding: 2rem;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
}

.access-denied-icon {
    color: #dc2626;
    margin-bottom: 1rem;
}

.access-denied-content h2 {
    color: #991b1b;
    margin-bottom: 1rem;
    font-size: 1.5rem;
    font-weight: 600;
}

.access-denied-content p {
    color: #7f1d1d;
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.access-denied-content button {
    margin-top: 1.5rem;
}

.room-status {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff7ed;
    border: 1px solid #fee2e2;
    border-radius: 10px;
    padding: 1.1rem 1rem;
}

.status-left {
    display: flex;
    gap: 10px;
    align-items: center;
}

.status-right {
    display: flex;
    gap: 10px;
    align-items: center;
}

.dot-online {
    width: 10px;
    height: 10px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
}

.status-text {
    font-weight: 600;
}

.participants-chip {
    display: inline-flex;
    gap: 6px;
    align-items: center;
    background: #fef3c7;
    color: #92400e;
    border-radius: 999px;
    padding: 2px 10px;
    font-weight: 600;
}

.icon-btn {
    cursor: pointer;
}

.controls {
    display: flex;
    gap: 8px;
    align-items: center;
}

.videos {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.video-tile {
    position: relative;
    background: #fefefe;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    padding: 10px;
}

video {
    width: 100%;
    background: #000;
    border-radius: 8px;
    height: 100%;
}

.video-overlay {
    position: absolute;
    inset: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    gap: 8px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.3));
    border-radius: 8px;
    border: 1px dashed #e5e7eb;
}

.badge.mute {
    position: absolute;
    right: 16px;
    top: 10px;
    background: #ef4444;
    color: white;
    padding: 4px 6px;
    border-radius: 6px;
    font-size: 12px;
}

.video-caption {
    position: absolute;
    left: 16px;
    bottom: 14px;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 13px;
}

.control-bar {
    display: flex;
    gap: 12px;
    align-items: center;
    justify-content: center;
    padding: 12px 0;
}

.btn.control.on {
    background: #fee2e2;
    color: #b91c1c;
}

.btn.control.off {
    background: #fee2e2;
    color: #b91c1c;
}

.btn.share {
    background: #f59e0b;
    color: white;
}

.btn.danger {
    background: #ef4444;
    color: white;
}
</style>

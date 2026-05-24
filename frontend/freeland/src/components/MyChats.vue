<template>
  <div class="my-chats-page">
    <SEOHead
      title="Мои чаты — FreeLand"
      description="Личная переписка с заказчиками и исполнителями на FreeLand. Сообщения защищены и доступны только участникам диалога."
      :noindex="true"
    />
    <div class="dynamic-background">
      <div class="gradient-sphere sphere-1"></div>
      <div class="gradient-sphere sphere-2"></div>
      <div class="gradient-sphere sphere-3"></div>
      <div class="grid-overlay"></div>
      <div class="noise-overlay"></div>
    </div>

    <HeaderMenu />

    <div class="container">
      <div class="page-header ios-glass">
        <h1 class="page-title">Мои чаты</h1>
        <p class="page-subtitle">Переписка с заказчиками и фрилансерами</p>
      </div>

      <div v-if="loadingUser" class="loading-block ios-glass">Загрузка пользователя...</div>
      <div v-else-if="!user" class="loading-block ios-glass">Нужно авторизоваться</div>

      <div v-else class="chat-layout">
        <aside class="chat-list ios-glass">
          <div class="chat-list-title">Диалоги</div>
          <div v-if="chatsLoading" class="chat-list-empty">Загрузка чатов...</div>
          <div v-else-if="chats.length === 0" class="chat-list-empty">Чатов пока нет</div>

          <button
            v-for="chat in chats"
            :key="chat.id"
            class="chat-list-item"
            :class="{ active: activeChat && activeChat.id === chat.id }"
            @click="selectChat(chat)"
          >
            <div class="chat-project">{{ chat.project?.title || 'Проект' }}</div>
            <div class="chat-partner">{{ getPartner(chat)?.full_name || getPartner(chat)?.login || 'Пользователь' }}</div>
          </button>
        </aside>

        <section class="chat-content ios-glass">
          <template v-if="activeChat">
            <div class="chat-head">
              <h3>{{ activeChat.project?.title || 'Чат' }}</h3>
              <span>{{ getPartner(activeChat)?.full_name || getPartner(activeChat)?.login }}</span>
            </div>

            <div class="messages" ref="messagesContainer">
              <div v-if="messagesLoading" class="chat-list-empty">Загрузка сообщений...</div>
              <div v-else-if="messages.length === 0" class="chat-list-empty">Нет сообщений</div>
              <div
                v-else
                v-for="message in messages"
                :key="message.id"
                class="message"
                :class="{ mine: message.author_id === user.id }"
              >
                <div v-if="message.text" class="message-text">{{ message.text }}</div>
                <a
                  v-if="message.attachment_url && !messageIsImage(message)"
                  :href="message.attachment_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="message-file-link"
                >
                  📎 {{ message.attachment_name || 'Скачать файл' }}
                </a>
                <a
                  v-if="message.attachment_url && messageIsImage(message)"
                  :href="message.attachment_url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="message-image-wrap"
                >
                  <img :src="message.attachment_url" class="message-image" alt="" />
                </a>
              </div>
            </div>

            <div v-if="pendingAttachmentName" class="attachment-pill">
              <span>{{ pendingAttachmentName }}</span>
              <button type="button" class="attachment-remove" @click="clearAttachment">×</button>
            </div>

            <div class="send-row">
              <input
                ref="chatFileInput"
                type="file"
                class="chat-file-input"
                @change="onChatFile"
              />
              <button type="button" class="attach-btn" @click="$refs.chatFileInput.click()">Файл</button>
              <textarea
                v-model="newMessage"
                class="chat-input"
                rows="2"
                placeholder="Текст сообщения (необязательно, если есть файл)..."
                @keydown.enter.prevent="sendMessage"
              />
              <button
                class="send-btn"
                :disabled="sending || (!newMessage.trim() && !pendingFile)"
                @click="sendMessage"
              >
                {{ sending ? 'Отправка...' : 'Отправить' }}
              </button>
            </div>
          </template>

          <div v-else class="chat-list-empty">Выберите чат слева</div>
        </section>
      </div>
    </div>

    <FooterApp />
  </div>
</template>

<script>
import HeaderMenu from '@/elements/HeaderMenu.vue'
import FooterApp from '@/elements/FooterApp.vue'
import SEOHead from '@/elements/SEOHead.vue'

export default {
  name: 'MyChats',
  components: { HeaderMenu, FooterApp, SEOHead },
  data() {
    return {
      apiBaseUrl: '',
      user: null,
      loadingUser: true,
      chats: [],
      chatsLoading: false,
      activeChat: null,
      activeChatToken: null,
      activeChatForbidden: false,
      messages: [],
      messagesLoading: false,
      newMessage: '',
      sending: false,
      chatPollingInterval: null,
      pendingFile: null,
      pendingAttachmentName: ''
    }
  },
  created() {
    this.fetchUser()
  },
  mounted() {
    this.chatPollingInterval = setInterval(() => {
      if (this.activeChat?.id) {
        this.refreshActiveChatByToken()
      }
    }, 2500)

  },
  beforeUnmount() {
    if (this.chatPollingInterval) {
      clearInterval(this.chatPollingInterval)
    }
  },
  methods: {
    async fetchJsonWithAuth(url, token, options = {}) {
      const response = await fetch(url, {
        ...options,
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token}`,
          ...(options.headers || {})
        }
      })

      let data = null
      try {
        data = await response.json()
      } catch (error) {
        data = null
      }

      return { response, data }
    },

    async fetchUser() {
      const token = localStorage.getItem('token')
      if (!token) {
        this.user = null
        this.loadingUser = false
        return
      }

      try {
        const response = await fetch(`${this.apiBaseUrl}/api/user`, {
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`
          }
        })
        const data = await response.json()
        if (response.ok && data.success) {
          this.user = data.user
          await this.fetchChats()
        } else {
          this.user = null
        }
      } catch (error) {
        console.error('MyChats - Ошибка пользователя:', error)
        this.user = null
      } finally {
        this.loadingUser = false
      }
    },
    async fetchChats() {
      const token = localStorage.getItem('token')
      if (!token) return

      this.chatsLoading = true
      try {
        let payload = []
        let endpointUsed = '/api/chats'

        const primary = await this.fetchJsonWithAuth(`${this.apiBaseUrl}/api/chats`, token)
        if (primary.response.ok) {
          if (Array.isArray(primary.data)) {
            payload = primary.data
          } else if (primary.data?.success && Array.isArray(primary.data?.chats)) {
            payload = primary.data.chats
          } else {
            payload = []
          }
        } else if (primary.response.status === 404) {
          endpointUsed = '/api/my-chats'
          const fallback = await this.fetchJsonWithAuth(`${this.apiBaseUrl}/api/my-chats`, token)
          if (fallback.response.ok) {
            if (Array.isArray(fallback.data)) {
              payload = fallback.data
            } else if (fallback.data?.success && Array.isArray(fallback.data?.chats)) {
              payload = fallback.data.chats
            }
          } else {
            throw new Error(`Ошибка ${fallback.response.status} при загрузке ${endpointUsed}`)
          }
        } else {
          throw new Error(`Ошибка ${primary.response.status} при загрузке ${endpointUsed}`)
        }

        const activeChatId = this.activeChat?.id || null
        this.chats = payload

        if (activeChatId) {
          const freshActiveChat = this.chats.find(chat => chat.id === activeChatId)
          if (freshActiveChat) {
            this.activeChat = { ...this.activeChat, ...freshActiveChat }
          }
        } else if (this.chats.length > 0) {
          const projectId = Number(this.$route.query.project_id)
          const userId = Number(this.$route.query.user_id)

          let initialChat = null
          if (projectId) {
            initialChat = this.chats.find(chat => Number(chat.project_id) === projectId) || null
          } else if (userId) {
            initialChat = this.chats.find(
              chat => Number(chat.author_id) === userId || Number(chat.interlocutor_id) === userId
            ) || null
          }

          await this.selectChat(initialChat || this.chats[0])
        }
      } catch (error) {
        console.error('MyChats - Ошибка чатов:', error)
        this.chats = []
      } finally {
        this.chatsLoading = false
      }
    },
    getPartner(chat) {
      if (!chat || !this.user) return null
      return chat.author_id === this.user.id ? chat.interlocutor : chat.author
    },
    async selectChat(chat) {
      this.activeChat = chat
      this.activeChatForbidden = false
      await this.fetchChat(chat.id, true)
    },
    async fetchChat(chatId, forceMessages = false) {
      const token = localStorage.getItem('token')
      if (!token || !chatId) return

      if (forceMessages) {
        this.messagesLoading = true
      }

      try {
        const response = await fetch(`${this.apiBaseUrl}/api/chats/${chatId}`, {
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`
          }
        })
        let data = null
        try {
          data = await response.json()
        } catch (error) {
          data = null
        }

        if (!response.ok) {
          const error = new Error(data?.message || 'Ошибка загрузки чата')
          error.status = response.status
          throw error
        }

        this.activeChat = data
        this.activeChatForbidden = false
        const tokenChanged = this.activeChatToken !== data.token

        if (forceMessages || tokenChanged) {
          this.messages = Array.isArray(data.messages) ? data.messages : []
          this.activeChatToken = data.token

          this.$nextTick(() => {
            const container = this.$refs.messagesContainer
            if (container) {
              container.scrollTop = container.scrollHeight
            }
          })
        }
      } catch (error) {
        console.error('MyChats - Ошибка чата:', error)
        if (error?.status === 403) {
          this.activeChatForbidden = true
          this.activeChat = null
          this.activeChatToken = null
          this.messages = []
        }
      } finally {
        this.messagesLoading = false
      }
    },
    async refreshActiveChatByToken() {
      if (this.activeChatForbidden || !this.activeChat?.id) {
        return
      }
      await this.fetchChat(this.activeChat.id, false)
    },
    messageIsImage(message) {
      const n = (message.attachment_name || message.attachment_url || '').toLowerCase()
      return /\.(jpe?g|png|gif|webp)$/.test(n)
    },
    onChatFile(e) {
      const f = e.target.files && e.target.files[0]
      this.pendingFile = f || null
      this.pendingAttachmentName = f ? f.name : ''
    },
    clearAttachment() {
      this.pendingFile = null
      this.pendingAttachmentName = ''
      if (this.$refs.chatFileInput) this.$refs.chatFileInput.value = ''
    },
    async sendMessage() {
      const text = this.newMessage.trim()
      const file = this.pendingFile
      if (!this.activeChat?.id || (!text && !file)) return
      const token = localStorage.getItem('token')
      if (!token) return

      this.sending = true
      try {
        let response
        if (file) {
          const fd = new FormData()
          if (text) fd.append('text', text)
          fd.append('file', file)
          response = await fetch(`${this.apiBaseUrl}/api/chats/${this.activeChat.id}/message`, {
            method: 'POST',
            headers: {
              Accept: 'application/json',
              Authorization: `Bearer ${token}`
            },
            body: fd
          })
        } else {
          response = await fetch(`${this.apiBaseUrl}/api/chats/${this.activeChat.id}/message`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Accept: 'application/json',
              Authorization: `Bearer ${token}`
            },
            body: JSON.stringify({ text })
          })
        }

        const data = await response.json()
        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Ошибка отправки сообщения')
        }

        this.newMessage = ''
        this.clearAttachment()
        await this.fetchChat(this.activeChat.id, true)
      } catch (error) {
        console.error('MyChats - Ошибка отправки:', error)
      } finally {
        this.sending = false
      }
    }
  }
}
</script>

<style scoped>
.my-chats-page {
  min-height: 100vh;
  position: relative;

  display: flex;
  flex-direction: column;

  padding-top: 40px;
}
.container {
  flex: 1 0 auto;

  max-width: 1520px;
  margin: 0 auto;
  padding: 0 24px 64px;

  position: relative;
  z-index: 20;
  width: 100%;

  display: flex;
  flex-direction: column;
}
.page-header { padding: 24px; margin-bottom: 20px; text-align: center; }
.page-title { color: #fff; margin: 0; }
.page-subtitle { color: #f0f8ff; opacity: 0.85; margin-top: 8px; }
.loading-block { padding: 24px; text-align: center; color: #fff; }
.chat-layout { display: grid; grid-template-columns: minmax(320px, 400px) minmax(0, 1fr); gap: 20px; min-height: 74vh; align-items: stretch; }
.chat-list, .chat-content { padding: 18px; min-width: 0; min-height: 74vh; }
.chat-content { display: flex; flex-direction: column; }
.chat-list-title { color: #fff; font-weight: 700; margin-bottom: 12px; }
.chat-list-empty { color: #a8d1ff; opacity: 0.9; padding: 16px 0; }
.chat-list-item { width: 100%; text-align: left; border: 1px solid rgba(168,209,255,0.2); background: rgba(10,77,140,0.2); color: #fff; border-radius: 12px; padding: 10px; margin-bottom: 10px; cursor: pointer; }
.chat-list-item.active { border-color: rgba(168,209,255,0.6); background: rgba(10,77,140,0.4); }
.chat-project { font-weight: 600; }
.chat-partner { opacity: 0.8; font-size: 0.9rem; margin-top: 2px; }
.chat-head { border-bottom: 1px solid rgba(168,209,255,0.2); padding-bottom: 12px; margin-bottom: 12px; color: #fff; }
.chat-head h3 { margin: 0 0 4px; }
.messages { min-height: 420px; height: auto; max-height: none; flex: 1; overflow-y: auto; padding-right: 8px; display: flex; flex-direction: column; align-items: flex-start; gap: 0; }
.message { display: block; max-width: 80%; margin: 6px 0; padding: 10px 12px; border-radius: 14px; background: rgba(10,77,140,0.3); color: #fff; }
.message.mine { background: rgba(52,152,219,0.45); align-self: flex-end; }
.message-text { white-space: pre-wrap; word-break: break-word; }
.message-file-link { display: inline-block; margin-top: 6px; color: #a8d1ff; text-decoration: underline; font-size: 0.95rem; }
.message-image-wrap { display: block; margin-top: 8px; max-width: 260px; }
.message-image { width: 100%; border-radius: 10px; display: block; border: 1px solid rgba(168,209,255,0.25); }
.attachment-pill { display: inline-flex; align-items: center; gap: 8px; margin-top: 10px; padding: 6px 12px; border-radius: 9999px; background: rgba(10,77,140,0.35); color: #fff; font-size: 0.9rem; }
.attachment-remove { border: none; background: transparent; color: #f0f8ff; cursor: pointer; font-size: 1.2rem; line-height: 1; padding: 0 4px; }
.send-row { display: flex; gap: 10px; margin-top: 14px; align-items: flex-end; flex-wrap: wrap; }
.chat-file-input { display: none; }
.attach-btn { flex-shrink: 0; border: 1px solid rgba(168,209,255,0.35); background: rgba(10,77,140,0.35); color: #e8f4ff; border-radius: 9999px; padding: 12px 16px; cursor: pointer; font-weight: 600; font-size: 0.9rem; }
.attach-btn:hover { background: rgba(10,77,140,0.5); }
.chat-input { flex: 1; min-width: 160px; border-radius: 16px; border: 1px solid rgba(168,209,255,0.3); background: rgba(10,77,140,0.2); color: #fff; padding: 12px 14px; resize: none; min-height: 50px; }
.send-btn { border: 1px solid rgba(168,209,255,0.4); background: rgba(10,77,140,0.6); color: #fff; border-radius: 9999px; padding: 12px 20px; cursor: pointer; font-weight: 600; }
.send-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.dynamic-background {
  position: fixed;
  inset: 0;
  z-index: 1;
  overflow: hidden;
  pointer-events: none;
}

:deep(footer) {
  margin-top: auto;
  flex-shrink: 0;
}

.gradient-sphere {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.25;
}

.sphere-1 { width: 460px; height: 460px; background: #1a6bb3; top: -140px; left: -80px; }
.sphere-2 { width: 520px; height: 520px; background: #0a4d8c; top: 25%; right: -180px; }
.sphere-3 { width: 420px; height: 420px; background: #2a7bc3; bottom: -140px; left: 30%; }

.grid-overlay {
  position: absolute;
  inset: 0;
  background-image: linear-gradient(rgba(168, 209, 255, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(168, 209, 255, 0.05) 1px, transparent 1px);
  background-size: 40px 40px;
}

.noise-overlay {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at center, rgba(255, 255, 255, 0.02), transparent 60%);
}

@media (max-width: 900px) {
  .chat-layout { grid-template-columns: 1fr; }
  .chat-list, .chat-content { min-height: auto; }
}
</style>

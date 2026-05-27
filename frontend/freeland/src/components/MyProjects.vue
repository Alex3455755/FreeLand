<!-- resources/js/components/MyProjects.vue -->
<template>
  <div class="my-projects-page">
    <SEOHead
      title="Мои проекты — FreeLand"
      description="Управление вашими проектами и заявками на бирже FreeLand: отслеживание статусов, оплата и общение с исполнителями."
      :noindex="true"
    />
    <!-- Динамический фон -->
    <div class="dynamic-background">
      <div class="gradient-sphere sphere-1"></div>
      <div class="gradient-sphere sphere-2"></div>
      <div class="gradient-sphere sphere-3"></div>

      <div class="light-spot spot-1"></div>
      <div class="light-spot spot-2"></div>
      <div class="light-spot spot-3"></div>
      <div class="light-spot spot-4"></div>

      <div class="grid-overlay"></div>
      <div class="noise-overlay"></div>
    </div>

    <HeaderMenu />

    <div class="container">
      <div class="page-header ios-glass">
        <h1 class="page-title">Мои проекты</h1>
        <p class="page-subtitle">
          Управление вашими проектами и заявками
        </p>
      </div>

      <!-- FILTERS -->
      <div class="filters-container ios-glass">
        <div class="filters-grid">

          <div class="filter-group">
            <label class="filter-label">Поиск</label>
            <input
              v-model="filters.search"
              type="text"
              class="filter-input ios-glass"
              placeholder="Название проекта..."
            >
          </div>

          <div class="filter-group">
            <label class="filter-label">Статус</label>
            <select
              v-model="filters.status"
              class="filter-input ios-glass"
            >
              <option value="">Все статусы</option>
              <option value="open">Открыт</option>
              <option value="in_progress">В работе</option>
              <option value="completed">Завершен</option>
              <option value="cancelled">Отменен</option>
            </select>
          </div>

          <div class="filter-group">
            <label class="filter-label">Категория</label>
            <select
              v-model="filters.category"
              class="filter-input ios-glass"
            >
              <option value="">Все категории</option>

              <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="filter-group">
            <label class="filter-label">Сортировка</label>

            <select
              v-model="filters.sort"
              class="filter-input ios-glass"
            >
              <option value="newest">Сначала новые</option>
              <option value="oldest">Сначала старые</option>
              <option value="budget_desc">Бюджет ↓</option>
              <option value="budget_asc">Бюджет ↑</option>
              <option value="deadline">По дедлайну</option>
            </select>
          </div>

        </div>

        <div class="filters-actions">
          <button
            class="action-button clear-button"
            @click="resetFilters"
          >
            Сбросить фильтры
          </button>

          <div class="projects-count">
            Найдено проектов:
            <span>{{ filteredProjects.length }}</span>
          </div>
        </div>
      </div>

      <!-- LOADING USER -->
      <div v-if="userLoading" class="loading-state">
        <div class="loader ios-glass">
          <div class="loader-spinner"></div>
          <p>Загрузка...</p>
        </div>
      </div>

      <template v-else-if="user">

        <!-- LOADING PROJECTS -->
        <div v-if="projectsLoading" class="loading-state">
          <div class="loader ios-glass">
            <div class="loader-spinner"></div>
            <p>Загрузка проектов...</p>
          </div>
        </div>

        <!-- PROJECTS -->
        <div
          v-else-if="filteredProjects.length > 0"
          class="projects-list"
        >
          <div
            v-for="project in filteredProjects"
            :key="project.id"
            class="project-card ios-glass"
          >
            <div class="project-header">
              <h3 class="project-title">
                {{ project.title }}
              </h3>

              <div
                class="project-status"
                :class="project.status"
              >
                {{ getStatusText(project.status) }}
              </div>
            </div>

            <p class="project-description">
              {{ truncateDescription(project.description) }}
            </p>

            <!-- APPLICATIONS -->
            <div
              v-if="canManageApplications(project)"
              class="applications-section ios-glass"
            >
              <h4 class="applications-title">
                Заявки фрилансеров
              </h4>

              <div
                v-if="getProjectApplications(project.id).length === 0"
                class="applications-empty"
              >
                Пока нет заявок
              </div>

              <div
                v-for="application in getProjectApplications(project.id)"
                :key="application.id"
                class="application-item"
              >
                <div class="application-main">
                  <div class="partner-avatar">
                    <img
                      :src="userAvatar(application.user)"
                      :alt="'Аватар ' + (application.user?.full_name || application.user?.login || 'фрилансера')"
                      loading="lazy"
                    >
                  </div>

                  <div class="application-user">
                    <div class="application-user-name">
                      {{
                        application.user?.full_name ||
                        application.user?.login ||
                        'Фрилансер'
                      }}
                    </div>

                    <div
                      class="application-status"
                      :class="application.status"
                    >
                      {{
                        getApplicationStatusText(application.status)
                      }}
                    </div>
                  </div>
                </div>

                <div
                  v-if="application.status === 'pending'"
                  class="application-actions"
                >
                  <button
                    class="action-button approve-button"
                    :disabled="isApplicationUpdating(application.id)"
                    @click="acceptApplication(application)"
                  >
                    {{
                      isApplicationUpdating(application.id)
                        ? 'Обработка...'
                        : 'Принять'
                    }}
                  </button>

                  <button
                    class="action-button reject-button"
                    :disabled="isApplicationUpdating(application.id)"
                    @click="rejectApplication(application)"
                  >
                    {{
                      isApplicationUpdating(application.id)
                        ? 'Обработка...'
                        : 'Отклонить'
                    }}
                  </button>
                </div>
              </div>
            </div>

            <!-- META -->
            <div class="project-meta">

              <div class="meta-item">
                <span class="meta-value">
                  {{ formatBudget(project.budget) }}
                </span>
              </div>

              <div
                v-if="project.has_escrow_hold"
                class="meta-item escrow-hint"
              >
                <span class="meta-value">
                  Средства зарезервированы
                </span>
              </div>

              <div
                class="meta-item"
                v-if="project.deadline"
                :class="{ 'deadline-overdue': isProjectOverdue(project) }"
              >
                <span class="meta-value">
                  {{ formatDate(project.deadline) }}
                  <span v-if="isProjectOverdue(project)" class="overdue-badge">Просрочен</span>
                </span>
              </div>

              <div class="meta-item">
                <span class="meta-value">
                  {{ getCategoryName(project.category_id) }}
                </span>
              </div>
            </div>

            <!-- FOOTER -->
            <div class="project-footer">

              <div class="partner-info">

                <template v-if="isCustomer">
                  <span class="partner-label">
                    Исполнитель:
                  </span>

                  <div
                    v-if="project.freelancer"
                    class="partner-details"
                  >
                    <div class="partner-avatar">
                      <img
                        :src="userAvatar(project.freelancer)"
                        :alt="'Аватар исполнителя ' + (project.freelancer?.full_name || project.freelancer?.login || '')"
                        loading="lazy"
                      >
                    </div>

                    <span class="partner-name">
                      {{
                        project.freelancer.full_name ||
                        project.freelancer.login
                      }}
                    </span>
                  </div>

                  <span
                    v-else
                    class="no-partner"
                  >
                    Исполнитель не назначен
                  </span>
                </template>

                <template v-else-if="isFreelancer">

                  <span class="partner-label">
                    Заказчик:
                  </span>

                  <div
                    v-if="project.customer"
                    class="partner-details"
                  >
                    <div class="partner-avatar">
                      <img
                        :src="userAvatar(project.customer)"
                        :alt="'Аватар заказчика ' + (project.customer?.full_name || project.customer?.login || '')"
                        loading="lazy"
                      >
                    </div>

                    <span class="partner-name">
                      {{
                        project.customer.full_name ||
                        project.customer.login
                      }}
                    </span>
                  </div>

                </template>
              </div>

              <div class="project-actions">

                <button
                  v-if="canPayProject(project) && isProjectOverdue(project)"
                  @click="payProject(project, true)"
                  class="action-button pay-button partial-pay"
                  :disabled="payingProjectIds.includes(project.id)"
                >
                  {{
                    payingProjectIds.includes(project.id)
                      ? 'Оплата...'
                      : 'Оплатить 75%'
                  }}
                </button>

                <button
                  v-if="canPayProject(project) && !isProjectOverdue(project)"
                  @click="payProject(project, false)"
                  class="action-button pay-button"
                  :disabled="payingProjectIds.includes(project.id)"
                >
                  {{
                    payingProjectIds.includes(project.id)
                      ? 'Оплата...'
                      : 'Оплатить'
                  }}
                </button>

                <button
                  v-if="canPayProject(project) && isProjectOverdue(project)"
                  @click="payProject(project, false)"
                  class="action-button pay-button full-pay-overdue"
                  :disabled="payingProjectIds.includes(project.id)"
                >
                  Оплатить полностью
                </button>

                <button
                  v-if="canCancelProject(project)"
                  type="button"
                  @click="cancelProject(project)"
                  class="action-button reject-button"
                  :disabled="cancellingProjectIds.includes(project.id)"
                >
                  {{
                    cancellingProjectIds.includes(project.id)
                      ? 'Отмена...'
                      : 'Отменить заказ'
                  }}
                </button>

                <button
                  v-if="
                    getProjectFreelancerId(project) &&
                    (
                      isCustomer ||
                      (
                        isFreelancer &&
                        getProjectFreelancerId(project) === user.id
                      )
                    )
                  "
                  @click="goToMyChats(project.id)"
                  class="action-button chat-button"
                >
                  Чат
                </button>

                <button
                  v-if="
                    isCustomer &&
                    project.status === 'open' &&
                    project.customer_id === user.id
                  "
                  @click="editProject(project)"
                  class="action-button edit-button"
                >
                  Редактировать
                </button>

                <button
                  v-if="isFreelancer && getProjectFreelancerId(project) === user.id"
                  @click="openComplaint(project)"
                  class="action-button complaint-button"
                >
                  Пожаловаться
                </button>

                <button
                  @click="viewProject(project.id)"
                  class="action-button view-button"
                >
                  Просмотр
                </button>

              </div>
            </div>
          </div>
        </div>

        <!-- EMPTY -->
        <div
          v-else
          class="empty-state ios-glass"
        >
          <h3 class="empty-title">
            Проекты не найдены
          </h3>

          <p class="empty-text">
            Попробуйте изменить фильтры
          </p>

          <button
            @click="resetFilters"
            class="action-button primary"
          >
            Сбросить фильтры
          </button>
        </div>

      </template>

      <!-- NO AUTH -->
      <div
        v-else
        class="empty-state ios-glass"
      >
        <h3 class="empty-title">
          Требуется авторизация
        </h3>

        <p class="empty-text">
          Войдите в систему, чтобы просматривать свои проекты
        </p>

        <button
          @click="goToLogin"
          class="action-button primary"
        >
          Войти
        </button>
      </div>

    </div>

    <div
      v-if="showEditModal && editingProject"
      class="modal-overlay"
      @click.self="closeEditModal"
    >
      <div class="modal-content ios-glass">
        <div class="modal-header">
          <h3 class="modal-title">Редактировать проект</h3>
          <button type="button" class="modal-close" @click="closeEditModal">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        </div>

        <div class="modal-body">
          <form @submit.prevent="saveProjectEdit">
            <div class="form-group">
              <label>Название</label>
              <input v-model="editingProject.title" type="text" class="form-input ios-glass" required>
            </div>

            <div class="form-group">
              <label>Описание</label>
              <textarea v-model="editingProject.description" class="form-input ios-glass" rows="3"></textarea>
            </div>

            <div class="form-group">
              <label>Бюджет</label>
              <input v-model.number="editingProject.budget" type="number" min="0" class="form-input ios-glass">
            </div>

            <div class="form-group">
              <label>Дедлайн</label>
              <input v-model="editingProject.deadline" type="date" class="form-input ios-glass" :min="today">
            </div>

            <div class="form-group">
              <label>Категория</label>
              <select v-model="editingProject.category_id" class="form-input ios-glass">
                <option value="">Выберите категорию</option>
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
            </div>

            <p class="edit-hint">
              Сумма заказа уже зарезервирована при создании. При оплате 75% средства берутся из резерва: часть — исполнителю, остаток — на ваш баланс.
            </p>

            <div class="modal-actions">
              <button type="button" class="cancel-button" @click="closeEditModal">
                Отмена
              </button>
              <button type="submit" class="save-button" :disabled="projectUpdating">
                {{ projectUpdating ? 'Сохранение…' : 'Сохранить' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Модалка жалобы на заказчика -->
    <div
      v-if="showComplaintModal && complaintProject"
      class="modal-overlay"
      @click.self="closeComplaint"
    >
      <div class="modal-content ios-glass">
        <div class="modal-header">
          <h3 class="modal-title">Пожаловаться на заказчика</h3>
          <button type="button" class="modal-close" @click="closeComplaint">✕</button>
        </div>

        <div class="modal-body">
          <form @submit.prevent="submitComplaint">
            <p class="complaint-project">
              Проект: <strong>{{ complaintProject.title }}</strong>
            </p>

            <div class="form-group">
              <label>Опишите проблему</label>
              <textarea
                v-model="complaintText"
                class="form-input ios-glass"
                rows="5"
                placeholder="Что произошло с заказчиком по этому проекту..."
                required
              ></textarea>
            </div>

            <p class="edit-hint">
              Жалоба будет отправлена администрации. Если её примут, с вами свяжется администратор в чате.
            </p>

            <div class="modal-actions">
              <button type="button" class="cancel-button" @click="closeComplaint">
                Отмена
              </button>
              <button type="submit" class="save-button" :disabled="complaintSubmitting">
                {{ complaintSubmitting ? 'Отправка…' : 'Отправить жалобу' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <FooterApp />
  </div>
</template>

<script>
import FooterApp from '@/elements/FooterApp.vue'
import HeaderMenu from '@/elements/HeaderMenu.vue'
import SEOHead from '@/elements/SEOHead.vue'
import { avatarSrc } from '@/utils/avatar'
import { isCustomerRole } from '@/utils/roles'

export default {
  name: 'MyProjects',

  components: {
    HeaderMenu,
    FooterApp,
    SEOHead
  },

  data() {
    return {
      user: null,
      userLoading: true,

      projects: [],
      projectsLoading: false,

      categories: [],

      applications: [],
      applicationsLoading: false,

      updatingApplicationIds: [],
      payingProjectIds: [],
      cancellingProjectIds: [],

      showEditModal: false,

      editingProject: null,
      projectUpdating: false,

      showComplaintModal: false,
      complaintProject: null,
      complaintText: '',
      complaintSubmitting: false,

      apiBaseUrl: '',

      filters: {
        search: '',
        status: '',
        category: '',
        sort: 'newest'
      }
    }
  },

  computed: {

    isCustomer() {
      return this.user &&
        (
          this.user.role === 'customer' ||
          this.user.role === 'заказчик'
        )
    },

    isFreelancer() {
      return this.user &&
        (
          this.user.role === 'freelancer' ||
          this.user.role === 'фрилансер'
        )
    },

    today() {
      const today = new Date()

      const yyyy = today.getFullYear()
      const mm = String(today.getMonth() + 1).padStart(2, '0')
      const dd = String(today.getDate()).padStart(2, '0')

      return `${yyyy}-${mm}-${dd}`
    },

    filteredProjects() {

      let result = [...this.projects]

      // SEARCH
      if (this.filters.search) {

        const search = this.filters.search.toLowerCase()

        result = result.filter(project => {
          return (
            project.title?.toLowerCase().includes(search) ||
            project.description?.toLowerCase().includes(search)
          )
        })
      }

      // STATUS
      if (this.filters.status) {

        result = result.filter(project => {
          return project.status === this.filters.status
        })
      }

      // CATEGORY
      if (this.filters.category) {

        result = result.filter(project => {
          return String(project.category_id) === String(this.filters.category)
        })
      }

      // SORT
      switch (this.filters.sort) {

        case 'oldest':
          result.sort((a, b) => {
            return new Date(a.created_at) - new Date(b.created_at)
          })
          break

        case 'budget_desc':
          result.sort((a, b) => {
            return Number(b.budget || 0) - Number(a.budget || 0)
          })
          break

        case 'budget_asc':
          result.sort((a, b) => {
            return Number(a.budget || 0) - Number(b.budget || 0)
          })
          break

        case 'deadline':
          result.sort((a, b) => {

            if (!a.deadline) return 1
            if (!b.deadline) return -1

            return new Date(a.deadline) - new Date(b.deadline)
          })
          break

        default:
          result.sort((a, b) => {
            return new Date(b.created_at) - new Date(a.created_at)
          })
      }

      return result
    }
  },

  created() {
    this.fetchUser()
    this.fetchCategories()
  },

  methods: {

    resetFilters() {

      this.filters = {
        search: '',
        status: '',
        category: '',
        sort: 'newest'
      }
    },

    async fetchUser() {

      const token = localStorage.getItem('token')

      if (!token) {
        this.user = null
        this.userLoading = false
        return
      }

      try {

        const response = await fetch(
          `${this.apiBaseUrl}/api/user`,
          {
            headers: {
              Accept: 'application/json',
              Authorization: `Bearer ${token}`
            }
          }
        )

        const data = await response.json()

        if (response.ok && data.success) {

          this.user = data.user

          await this.fetchMyProjects()

        } else {

          localStorage.removeItem('token')
          localStorage.removeItem('user')

          this.user = null
        }

      } catch (error) {

        console.error(error)

        this.user = null

      } finally {

        this.userLoading = false
      }
    },

    async fetchMyProjects() {

      const token = localStorage.getItem('token')

      if (!token || !this.user) return

      this.projectsLoading = true

      try {

        const response = await fetch(
          `${this.apiBaseUrl}/api/my-projects/${this.user.id}`,
          {
            headers: {
              Accept: 'application/json',
              Authorization: `Bearer ${token}`
            }
          }
        )

        const data = await response.json()

        if (data.success && data.projects) {
          this.projects = data.projects
        } else if (Array.isArray(data)) {
          this.projects = data
        } else {
          this.projects = []
        }

        if (isCustomerRole(this.user?.role)) {
          await this.fetchApplications()
        }

      } catch (error) {

        console.error(error)

      } finally {

        this.projectsLoading = false
      }
    },

    async fetchApplications() {
      const token = localStorage.getItem('token')
      if (!token || !this.user) return

      this.applicationsLoading = true

      try {
        const response = await fetch(`${this.apiBaseUrl}/api/applications`, {
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`
          }
        })

        const data = await response.json()
        const list = Array.isArray(data) ? data : (data.applications || [])
        const projectIds = new Set(this.projects.map(p => p.id))
        this.applications = list.filter(app => projectIds.has(app.project_id))
      } catch (error) {
        console.error('Ошибка загрузки заявок:', error)
        this.applications = []
      } finally {
        this.applicationsLoading = false
      }
    },

    editProject(project) {
      this.editingProject = {
        id: project.id,
        title: project.title || '',
        description: project.description || '',
        budget: project.budget ?? '',
        deadline: project.deadline ? String(project.deadline).slice(0, 10) : '',
        category_id: project.category_id || ''
      }
      this.showEditModal = true
      document.body.style.overflow = 'hidden'
    },

    closeEditModal() {
      this.showEditModal = false
      this.editingProject = null
      this.projectUpdating = false
      document.body.style.overflow = ''
    },

    openComplaint(project) {
      this.complaintProject = project
      this.complaintText = ''
      this.showComplaintModal = true
      document.body.style.overflow = 'hidden'
    },

    closeComplaint() {
      this.showComplaintModal = false
      this.complaintProject = null
      this.complaintText = ''
      this.complaintSubmitting = false
      document.body.style.overflow = ''
    },

    async submitComplaint() {
      const token = localStorage.getItem('token')
      if (!token || !this.complaintProject) {
        this.goToLogin()
        return
      }

      const text = String(this.complaintText || '').trim()
      if (text.length < 5) {
        alert('Опишите суть жалобы подробнее (минимум 5 символов)')
        return
      }

      this.complaintSubmitting = true

      try {
        const response = await fetch(`${this.apiBaseUrl}/api/complaints`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            Authorization: `Bearer ${token}`
          },
          body: JSON.stringify({
            project_id: this.complaintProject.id,
            text
          })
        })

        const data = await response.json()

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Не удалось отправить жалобу')
        }

        alert(data.message || 'Жалоба отправлена администрации')
        this.closeComplaint()
      } catch (error) {
        alert(error.message || 'Ошибка отправки жалобы')
      } finally {
        this.complaintSubmitting = false
      }
    },

    async saveProjectEdit() {
      const token = localStorage.getItem('token')
      if (!token || !this.editingProject) {
        this.goToLogin()
        return
      }

      const title = String(this.editingProject.title || '').trim()
      if (!title) {
        alert('Введите название проекта')
        return
      }

      this.projectUpdating = true

      try {
        const response = await fetch(`${this.apiBaseUrl}/api/projects/edit`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            Authorization: `Bearer ${token}`
          },
          body: JSON.stringify({
            id: this.editingProject.id,
            title,
            description: this.editingProject.description,
            budget: this.editingProject.budget,
            deadline: this.editingProject.deadline || null,
            category_id: this.editingProject.category_id || null
          })
        })

        const data = await response.json()

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Не удалось сохранить проект')
        }

        this.closeEditModal()
        await this.fetchMyProjects()
      } catch (error) {
        alert(error.message || 'Ошибка сохранения проекта')
      } finally {
        this.projectUpdating = false
      }
    },

    async acceptApplication(application) {
      const token = localStorage.getItem('token')
      if (!token) {
        this.goToLogin()
        return
      }

      this.updatingApplicationIds.push(application.id)

      try {
        const response = await fetch(
          `${this.apiBaseUrl}/api/applications/${application.id}/accept`,
          {
            method: 'PATCH',
            headers: {
              Accept: 'application/json',
              Authorization: `Bearer ${token}`
            }
          }
        )

        const data = await response.json()

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Не удалось принять заявку')
        }

        await this.fetchMyProjects()
      } catch (error) {
        alert(error.message || 'Ошибка при принятии заявки')
      } finally {
        this.updatingApplicationIds = this.updatingApplicationIds.filter(
          id => id !== application.id
        )
      }
    },

    async rejectApplication(application) {
      const token = localStorage.getItem('token')
      if (!token) {
        this.goToLogin()
        return
      }

      this.updatingApplicationIds.push(application.id)

      try {
        const response = await fetch(
          `${this.apiBaseUrl}/api/applications/${application.id}/reject`,
          {
            method: 'PATCH',
            headers: {
              Accept: 'application/json',
              Authorization: `Bearer ${token}`
            }
          }
        )

        const data = await response.json()

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Не удалось отклонить заявку')
        }

        await this.fetchApplications()
      } catch (error) {
        alert(error.message || 'Ошибка при отклонении заявки')
      } finally {
        this.updatingApplicationIds = this.updatingApplicationIds.filter(
          id => id !== application.id
        )
      }
    },

    async fetchCategories() {

      try {

        const response = await fetch(
          `${this.apiBaseUrl}/api/categories`
        )

        const data = await response.json()

        this.categories = data

      } catch (error) {

        console.error(error)
      }
    },

    formatBudget(budget) {

      if (!budget) return 'Не указан'

      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 0
      }).format(budget)
    },

    formatDate(date) {

      if (!date) return 'Не указан'

      return new Date(date).toLocaleDateString(
        'ru-RU',
        {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        }
      )
    },

    getStatusText(status) {

      const statuses = {
        open: 'Открыт',
        in_progress: 'В работе',
        completed: 'Завершен',
        cancelled: 'Отменен'
      }

      return statuses[status] || status
    },

    getCategoryName(categoryId) {

      if (!categoryId) return 'Без категории'

      const category = this.categories.find(
        item => item.id === categoryId
      )

      return category ? category.name : 'Без категории'
    },

    truncateDescription(description) {

      if (!description) return 'Нет описания'

      return description.length > 150
        ? description.substring(0, 150) + '...'
        : description
    },

    userAvatar(user) {
      return avatarSrc(user, this.apiBaseUrl)
    },

    viewProject(id) {
      this.$router.push(`/projects/${id}`)
    },

    goToProjects() {
      this.$router.push('/projects')
    },

    goToMyChats(projectId = null) {

      if (projectId) {
        this.$router.push(`/my-chats?project_id=${projectId}`)
        return
      }

      this.$router.push('/my-chats')
    },

    goToLogin() {
      this.$router.push('/login')
    },

    canManageApplications(project) {
      return Boolean(
        this.isCustomer &&
        this.user &&
        project?.customer_id === this.user.id
      )
    },

    getProjectApplications(projectId) {

      return this.applications.filter(item => {
        return item.project_id === projectId
      })
    },

    getApplicationStatusText(status) {

      const statuses = {
        pending: 'На рассмотрении',
        accepted: 'Принята',
        rejected: 'Отклонена'
      }

      return statuses[status] || status
    },

    isApplicationUpdating(applicationId) {
      return this.updatingApplicationIds.includes(applicationId)
    },

    getProjectFreelancerId(project) {

      if (!project) return null

      return Number(
        project.freelancer_id ||
        project.freelancer?.id ||
        0
      ) || null
    },

    canPayProject(project) {

      const freelancerId = this.getProjectFreelancerId(project)

      return Boolean(
        this.isCustomer &&
        this.user &&
        project &&
        project.customer_id === this.user.id &&
        freelancerId &&
        Number(project.budget) > 0 &&
        !project.has_paid_transfer
      )
    },

    canCancelProject(project) {

      return Boolean(
        this.isCustomer &&
        this.user &&
        project &&
        project.customer_id === this.user.id &&
        project.has_escrow_hold &&
        !project.has_paid_transfer
      )
    },

    isProjectOverdue(project) {
      if (!project) return false
      if (project.is_overdue === true) return true
      if (!project.deadline) return false
      return String(project.deadline).slice(0, 10) < this.today
    },

    async payProject(project, partialOverdue = false) {
      const token = localStorage.getItem('token')
      if (!token) {
        this.goToLogin()
        return
      }

      const overdue = this.isProjectOverdue(project)
      let confirmText = 'Перевести исполнителю полную сумму по проекту?'
      if (partialOverdue && overdue) {
        const amount75 = Math.round(Number(project.budget) * 0.75)
        const refund25 = Math.round(Number(project.budget) * 0.25)
        confirmText = `Оплатить 75% (${this.formatBudget(amount75)})? ${this.formatBudget(refund25)} вернутся на ваш баланс.`
      }
      if (!window.confirm(confirmText)) return

      this.payingProjectIds.push(project.id)

      try {
        const response = await fetch(
          `${this.apiBaseUrl}/api/projects/${project.id}/pay`,
          {
            method: 'POST',
            headers: {
              Accept: 'application/json',
              'Content-Type': 'application/json',
              Authorization: `Bearer ${token}`
            },
            body: JSON.stringify({
              partial_overdue: Boolean(partialOverdue && overdue)
            })
          }
        )

        const data = await response.json()

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Не удалось выполнить оплату')
        }

        alert(data.message || 'Оплата выполнена')
        await this.fetchMyProjects()
      } catch (error) {
        alert(error.message || 'Ошибка оплаты')
      } finally {
        this.payingProjectIds = this.payingProjectIds.filter(
          id => id !== project.id
        )
      }
    },

    async cancelProject(project) {
      const token = localStorage.getItem('token')
      if (!token) {
        this.goToLogin()
        return
      }

      if (!window.confirm('Отменить заказ? Зарезервированные средства вернутся на баланс.')) {
        return
      }

      this.cancellingProjectIds.push(project.id)

      try {
        const response = await fetch(
          `${this.apiBaseUrl}/api/projects/${project.id}/cancel`,
          {
            method: 'POST',
            headers: {
              Accept: 'application/json',
              Authorization: `Bearer ${token}`
            }
          }
        )

        const data = await response.json()

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Не удалось отменить заказ')
        }

        alert(data.message || 'Заказ отменён')
        await this.fetchMyProjects()
      } catch (error) {
        alert(error.message || 'Ошибка отмены')
      } finally {
        this.cancellingProjectIds = this.cancellingProjectIds.filter(
          id => id !== project.id
        )
      }
    }
  }
}
</script>

<style scoped>
.my-projects-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  position: relative;
}

.container {
  flex: 1;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;

  padding: 40px 20px 40px;
  box-sizing: border-box;

  position: relative;
  z-index: 20;
}

/* Футер */
.footer {
  margin-top: auto;
  flex-shrink: 0;
}

.page-header {
  padding: 40px;
  margin-bottom: 30px;
  text-align: center;
}

.page-title {
  font-size: 2.8rem;
  font-weight: 700;
  color: #FFFFFF;
}

.page-subtitle {
  color: #F0F8FF;
  opacity: 0.8;
}

/* FILTERS */

.filters-container {
  margin-bottom: 30px;
  padding: 25px;
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.filter-label {
  color: #F0F8FF;
  font-size: 0.95rem;
}

.filter-input {
  width: 100%;
  padding: 14px 18px;
  border-radius: 16px;
  border: 1px solid rgba(168, 209, 255, 0.2);
  background: rgba(10, 77, 140, 0.2);
  color: #FFFFFF;
  font-size: 0.95rem;
}

.filter-input:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.5);
}

.filters-actions {
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  flex-wrap: wrap;
}

.projects-count {
  color: #F0F8FF;
  opacity: 0.9;
}

.projects-count span {
  color: #A8D1FF;
  font-weight: 700;
}

.clear-button {
  background: rgba(231, 76, 60, 0.2);
  border: 1px solid rgba(231, 76, 60, 0.4);
  color: #ffb3aa;
}

.clear-button:hover {
  background: rgba(231, 76, 60, 0.35);
}

/* PROJECTS */

.projects-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.project-card {
  padding: 30px;
}

.project-header {
  display: flex;
  justify-content: space-between;
  gap: 15px;
  margin-bottom: 15px;
}

.project-title {
  color: #FFFFFF;
  font-size: 1.5rem;
}

.project-description {
  color: #F0F8FF;
  line-height: 1.6;
  margin-bottom: 20px;
}

.project-meta {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 15px;
  margin-bottom: 20px;
}

.meta-value {
  color: #FFFFFF;
}

.project-footer {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}

.partner-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.partner-details {
  display: flex;
  align-items: center;
  gap: 10px;
}

.partner-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  overflow: hidden;
}

.partner-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.partner-name {
  color: #FFFFFF;
}

.project-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.action-button {
  padding: 10px 18px;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  transition: 0.3s;
}

.view-button {
  background: rgba(168, 209, 255, 0.12);
  color: #FFFFFF;
}

.chat-button {
  background: rgba(52, 152, 219, 0.2);
  color: #3498db;
}

.edit-button {
  background: rgba(241, 196, 15, 0.2);
  color: #f1c40f;
}

.pay-button {
  background: rgba(46, 204, 113, 0.2);
  color: #2ecc71;
}

.pay-button.partial-pay {
  background: rgba(230, 126, 34, 0.2);
  color: #e67e22;
}

.pay-button.full-pay-overdue {
  background: rgba(46, 204, 113, 0.12);
  color: #a8f0c8;
  font-size: 0.88rem;
}

.deadline-overdue .meta-value {
  color: #ff8f80;
}

.overdue-badge {
  display: inline-block;
  margin-left: 8px;
  padding: 2px 8px;
  border-radius: 999px;
  background: rgba(231, 76, 60, 0.25);
  color: #ff8f80;
  font-size: 0.8rem;
}

.reject-button {
  background: rgba(231, 76, 60, 0.2);
  color: #ff8f80;
}

.project-status {
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 0.85rem;
}

.project-status.open {
  background: rgba(46, 204, 113, 0.2);
  color: #2ecc71;
}

.project-status.in_progress {
  background: rgba(241, 196, 15, 0.2);
  color: #f1c40f;
}

.project-status.completed {
  background: rgba(52, 152, 219, 0.2);
  color: #3498db;
}

.project-status.cancelled {
  background: rgba(231, 76, 60, 0.2);
  color: #ff8f80;
}

.loading-state {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 300px;
}

.loader {
  padding: 40px;
}

.loader-spinner {
  width: 50px;
  height: 50px;
  border: 3px solid rgba(255,255,255,0.1);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.empty-state {
  padding: 60px;
  text-align: center;
}

.empty-title {
  color: #FFFFFF;
  margin-bottom: 10px;
}

.empty-text {
  color: #F0F8FF;
  opacity: 0.8;
  margin-bottom: 20px;
}



.page-header {
  padding: 40px;
  margin-bottom: 30px;
  text-align: center;
}

.page-title {
  font-size: 2.8rem;
  font-weight: 700;
  margin-bottom: 10px;
  color: #FFFFFF;
  text-shadow: 0 2px 20px rgba(168, 209, 255, 0.3);
}

.page-subtitle {
  font-size: 1.2rem;
  color: #F0F8FF;
  opacity: 0.9;
}

.tabs-container {
  display: flex;
  gap: 10px;
  padding: 10px;
  margin-bottom: 30px;
  border-radius: 100px;
  background: rgba(10, 77, 140, 0.2);
}

.tab-button {
  flex: 1;
  padding: 16px 30px;
  border: none;
  border-radius: 100px;
  font-size: 1.1rem;
  font-weight: 600;
  color: #F0F8FF;
  background: transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.tab-button:hover {
  background: rgba(168, 209, 255, 0.1);
}

.tab-button.active {
  background: rgba(168, 209, 255, 0.2);
  box-shadow: 0 4px 15px rgba(168, 209, 255, 0.2);
  color: #FFFFFF;
}

.projects-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.project-card {
  padding: 30px;
  transition: all 0.3s ease;
}

.project-card:hover {
  transform: translateY(-2px);
  border-color: rgba(168, 209, 255, 0.4);
  box-shadow: 0 20px 40px rgba(8, 51, 88, 0.6);
}

.project-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  flex-wrap: wrap;
  gap: 15px;
}

.project-title {
  font-size: 1.6rem;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0;
}

.project-status {
  padding: 6px 16px;
  border-radius: 9999px;
  font-size: 0.9rem;
  font-weight: 600;
  text-transform: uppercase;
  background: rgba(10, 77, 140, 0.3);
  border: 1px solid rgba(168, 209, 255, 0.2);
  color: #F0F8FF;
}

.project-status.open {
  background: rgba(39, 174, 96, 0.2);
  border-color: rgba(39, 174, 96, 0.4);
  color: #2ecc71;
}

.project-status.in_progress {
  background: rgba(241, 196, 15, 0.2);
  border-color: rgba(241, 196, 15, 0.4);
  color: #f1c40f;
}

.project-status.completed {
  background: rgba(52, 152, 219, 0.2);
  border-color: rgba(52, 152, 219, 0.4);
  color: #3498db;
}

.project-description {
  color: #F0F8FF;
  line-height: 1.6;
  margin-bottom: 20px;
  font-size: 1rem;
}

.applications-section {
  margin-bottom: 20px;
  padding: 16px;
  border: 1px solid rgba(168, 209, 255, 0.16);
}

.applications-title {
  margin: 0 0 12px;
  color: #FFFFFF;
  font-size: 1rem;
}

.applications-empty {
  color: #A8D1FF;
  opacity: 0.8;
}

.application-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  padding: 10px 0;
  border-top: 1px solid rgba(168, 209, 255, 0.12);
}

.application-item:first-of-type {
  border-top: none;
}

.application-main {
  display: flex;
  align-items: center;
  gap: 10px;
}

.application-user-name {
  color: #FFFFFF;
  font-weight: 600;
}

.application-status {
  margin-top: 4px;
  font-size: 0.85rem;
  color: #A8D1FF;
}

.application-status.accepted {
  color: #2ecc71;
}

.application-status.rejected {
  color: #e67e22;
}

.application-actions {
  display: flex;
  gap: 8px;
}

.action-button.approve-button {
  background: rgba(39, 174, 96, 0.2);
  border: 1px solid rgba(39, 174, 96, 0.4);
  color: #2ecc71;
}

.action-button.approve-button:hover {
  background: rgba(39, 174, 96, 0.35);
  color: #FFFFFF;
}

.action-button.reject-button {
  background: rgba(231, 76, 60, 0.2);
  border: 1px solid rgba(231, 76, 60, 0.4);
  color: #ff8f80;
}

.action-button.reject-button:hover {
  background: rgba(231, 76, 60, 0.35);
  color: #FFFFFF;
}

.project-meta {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 15px;
  margin-bottom: 20px;
  padding: 15px 0;
  border-top: 1px solid rgba(168, 209, 255, 0.1);
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.meta-icon {
  font-size: 1.2rem;
}

.meta-value {
  color: #FFFFFF;
  font-size: 1rem;
}

.project-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

.partner-info {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.partner-label {
  color: #A8D1FF;
  font-size: 0.9rem;
}

.partner-details {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(10, 77, 140, 0.3);
  padding: 5px 12px;
  border-radius: 20px;
}

.partner-avatar {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 0.9rem;
  color: #FFFFFF;
  border: 2px solid rgba(168, 209, 255, 0.3);
  overflow: hidden;
  flex-shrink: 0;
}

.partner-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  display: block;
}

.partner-avatar.large {
  width: 50px;
  height: 50px;
  font-size: 1.2rem;
}

.partner-avatar.small {
  width: 30px;
  height: 30px;
  font-size: 0.9rem;
}

.partner-name {
  color: #FFFFFF;
  font-weight: 500;
}

.no-partner {
  color: #F0F8FF;
  opacity: 0.6;
  font-style: italic;
}

.project-actions {
  display: flex;
  gap: 10px;
}

.action-button {
  padding: 10px 20px;
  border: none;
  border-radius: 9999px;
  font-size: 0.95rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 5px;
}

.action-button.chat-button {
  background: rgba(52, 152, 219, 0.2);
  border: 1px solid rgba(52, 152, 219, 0.4);
  color: #3498db;
}

.action-button.chat-button:hover {
  background: rgba(52, 152, 219, 0.4);
  color: #FFFFFF;
}

.action-button.pay-button {
  background: rgba(46, 204, 113, 0.2);
  border: 1px solid rgba(46, 204, 113, 0.4);
  color: #2ecc71;
}

.action-button.pay-button:hover {
  background: rgba(46, 204, 113, 0.35);
  color: #FFFFFF;
}

.action-button.edit-button {
  background: rgba(241, 196, 15, 0.2);
  border: 1px solid rgba(241, 196, 15, 0.4);
  color: #f1c40f;
}

.action-button.edit-button:hover {
  background: rgba(241, 196, 15, 0.4);
  color: #FFFFFF;
}

.action-button.view-button {
  background: rgba(168, 209, 255, 0.1);
  border: 1px solid rgba(168, 209, 255, 0.3);
  color: #F0F8FF;
}

.action-button.view-button:hover {
  background: rgba(168, 209, 255, 0.2);
}

.action-button.primary {
  background: rgba(10, 77, 140, 0.6);
  border: 1px solid rgba(168, 209, 255, 0.4);
  color: #FFFFFF;
  padding: 15px 40px;
  font-size: 1.1rem;
}

.action-button.primary:hover {
  background: rgba(10, 77, 140, 0.8);
  transform: translateY(-2px);
}

.chats-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.chat-card {
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.chat-card:hover {
  transform: translateY(-2px);
  border-color: rgba(168, 209, 255, 0.4);
}

.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  flex-wrap: wrap;
  gap: 10px;
}

.chat-project-title {
  font-size: 1.3rem;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0;
}

.chat-project-status {
  padding: 4px 12px;
  border-radius: 9999px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
  background: rgba(10, 77, 140, 0.3);
  border: 1px solid rgba(168, 209, 255, 0.2);
  color: #F0F8FF;
}

.chat-project-status.open {
  background: rgba(39, 174, 96, 0.2);
  border-color: rgba(39, 174, 96, 0.4);
  color: #2ecc71;
}

.chat-project-status.in_progress {
  background: rgba(241, 196, 15, 0.2);
  border-color: rgba(241, 196, 15, 0.4);
  color: #f1c40f;
}

.chat-partner {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
  padding: 10px;
  background: rgba(10, 77, 140, 0.2);
  border-radius: 15px;
}

.partner-info {
  flex: 1;
}

.partner-name {
  font-size: 1.2rem;
  font-weight: 600;
  color: #FFFFFF;
  margin-bottom: 4px;
}

.partner-role {
  font-size: 0.9rem;
  color: #A8D1FF;
}

.chat-last-message {
  background: rgba(255, 255, 255, 0.05);
  padding: 12px;
  border-radius: 12px;
  margin-bottom: 15px;
}

.chat-last-message.empty {
  opacity: 0.6;
}

.message-time {
  font-size: 0.8rem;
  color: #A8D1FF;
  display: block;
  margin-bottom: 4px;
}

.message-preview {
  color: #F0F8FF;
  font-size: 0.95rem;
  margin: 0;
  opacity: 0.9;
}

.chat-actions {
  display: flex;
  justify-content: flex-end;
}

.chat-open {
  background: transparent;
  border: 1px solid rgba(168, 209, 255, 0.3);
  color: #A8D1FF;
  padding: 8px 16px;
  border-radius: 9999px;
  font-size: 0.9rem;
}

.chat-modal {
  width: 100%;
  max-width: 800px;
  height: 80vh;
  display: flex;
  flex-direction: column;
  background: rgba(10, 25, 45, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 28px;
  overflow: hidden;
}

.chat-modal-header {
  padding: 20px 30px;
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chat-modal-project h3 {
  font-size: 1.3rem;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0 0 10px 0;
}

.chat-modal-partner {
  display: flex;
  align-items: center;
  gap: 10px;
  color: #F0F8FF;
}

.partner-role-badge {
  background: rgba(168, 209, 255, 0.2);
  padding: 2px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
  color: #A8D1FF;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 30px;
  display: flex;
  flex-direction: column;
}

.messages-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.message-item {
  display: flex;
  gap: 15px;
  max-width: 70%;
}

.message-item.my-message {
  align-self: flex-end;
  flex-direction: row-reverse;
}

.message-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: #FFFFFF;
  flex-shrink: 0;
}

.message-content {
  background: rgba(10, 77, 140, 0.2);
  padding: 12px 16px;
  border-radius: 18px;
  border: 1px solid rgba(168, 209, 255, 0.1);
}

.message-item.my-message .message-content {
  background: rgba(52, 152, 219, 0.3);
}

.message-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 5px;
  gap: 15px;
}

.message-author {
  font-weight: 600;
  color: #FFFFFF;
}

.message-time {
  font-size: 0.8rem;
  color: #A8D1FF;
}

.message-text {
  color: #F0F8FF;
  line-height: 1.5;
  margin: 0;
  word-break: break-word;
}

.no-messages {
  text-align: center;
  color: #F0F8FF;
  opacity: 0.6;
  padding: 40px;
}

.chat-input-area {
  padding: 20px 30px;
  border-top: 1px solid rgba(168, 209, 255, 0.1);
  display: flex;
  gap: 15px;
  background: rgba(10, 25, 45, 0.8);
}

.chat-input {
  flex: 1;
  padding: 12px 18px;
  border-radius: 20px;
  border: 1px solid rgba(168, 209, 255, 0.2);
  background: rgba(10, 77, 140, 0.2);
  color: #FFFFFF;
  font-size: 1rem;
  resize: none;
  font-family: inherit;
}

.chat-input:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.4);
}

.send-button {
  padding: 0 30px;
  border-radius: 9999px;
  border: 1px solid rgba(168, 209, 255, 0.3);
  background: rgba(10, 77, 140, 0.6);
  color: #FFFFFF;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 120px;
}

.send-button:hover:not(:disabled) {
  background: rgba(10, 77, 140, 0.8);
  border-color: rgba(168, 209, 255, 0.6);
}

.send-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.modal-content {
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  background: rgba(10, 77, 140, 0.3);
  animation: modalFadeIn 0.3s ease;
}

.modal-header {
  flex-shrink: 0;
}

.modal-body {
  flex: 1;
  min-height: 0;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 30px;
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #FFFFFF;
  margin: 0;
}

.modal-close {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  color: #FFFFFF;
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  background: rgba(231, 76, 60, 0.3);
  border-color: rgba(231, 76, 60, 0.4);
  transform: rotate(90deg);
}

.modal-body {
  padding: 30px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #A8D1FF;
  font-size: 0.95rem;
}

.form-input {
  width: 100%;
  padding: 12px 16px;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 12px;
  color: #FFFFFF;
  font-size: 1rem;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.4);
  background: rgba(10, 77, 140, 0.25);
}

.form-input::placeholder {
  color: rgba(168, 209, 255, 0.4);
}

textarea.form-input {
  resize: vertical;
  min-height: 80px;
}

.edit-hint {
  margin: 0 0 8px;
  padding: 12px 14px;
  border-radius: 12px;
  background: rgba(52, 152, 219, 0.12);
  border: 1px solid rgba(52, 152, 219, 0.25);
  color: #d9ecff;
  font-size: 0.88rem;
  line-height: 1.45;
}

.action-button.complaint-button {
  background: rgba(230, 126, 34, 0.2);
  border: 1px solid rgba(230, 126, 34, 0.4);
  color: #e67e22;
}

.action-button.complaint-button:hover {
  background: rgba(230, 126, 34, 0.35);
  color: #FFFFFF;
}

.complaint-project {
  margin: 0 0 18px;
  color: #F0F8FF;
  font-size: 0.95rem;
}

.complaint-project strong {
  color: #FFFFFF;
}

.modal-actions {
  display: flex;
  gap: 15px;
  margin-top: 30px;
  justify-content: flex-end;
}

.cancel-button,
.save-button {
  padding: 12px 30px;
  border-radius: 9999px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(168, 209, 255, 0.3);
}

.cancel-button {
  background: rgba(149, 165, 166, 0.2);
  color: #95a5a6;
}

.cancel-button:hover {
  background: rgba(149, 165, 166, 0.3);
  transform: translateY(-2px);
}

.save-button {
  background: linear-gradient(135deg, #0A4D8C, #1A6BB3);
  color: #FFFFFF;
}

.save-button:hover:not(:disabled) {
  background: linear-gradient(135deg, #1A6BB3, #2A7FC9);
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(8, 51, 88, 0.4);
}

.save-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.loading-state {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
}

.loader {
  padding: 40px 60px;
  text-align: center;
}

.loader-spinner {
  width: 50px;
  height: 50px;
  border: 3px solid rgba(168, 209, 255, 0.1);
  border-top-color: #F0F8FF;
  border-radius: 50%;
  margin: 0 auto 20px;
  animation: spin 1s linear infinite;
}

.loader p {
  color: #F0F8FF;
  font-size: 1.1rem;
}

.loader-sm {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.empty-state {
  padding: 80px 40px;
  text-align: center;
  max-width: 500px;
  margin: 60px auto;
}

.empty-icon {
  font-size: 5rem;
  margin-bottom: 20px;
  filter: drop-shadow(0 10px 20px rgba(168, 209, 255, 0.2));
}

.empty-title {
  font-size: 2rem;
  font-weight: 600;
  margin-bottom: 15px;
  color: #FFFFFF;
}

.empty-text {
  color: #F0F8FF;
  font-size: 1.1rem;
  margin-bottom: 30px;
  opacity: 0.8;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(8, 51, 88, 0.8);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.button-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #FFFFFF;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-right: 8px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 1024px) {
  .page-title {
    font-size: 2.4rem;
  }
  
  .project-card {
    padding: 25px;
  }
}

@media (max-width: 768px) {
  .page-header {
    padding: 30px 20px;
  }
  
  .page-title {
    font-size: 2rem;
  }
  
  .tabs-container {
    border-radius: 50px;
  }
  
  .tab-button {
    padding: 12px 20px;
    font-size: 1rem;
  }
  
  .project-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .project-meta {
    grid-template-columns: 1fr;
  }
  
  .project-footer {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .project-actions {
    width: 100%;
    flex-wrap: wrap;
  }
  
  .action-button {
    flex: 1;
    justify-content: center;
  }
  
  .chat-modal {
    height: 90vh;
  }
  
  .message-item {
    max-width: 85%;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }

  .modal-actions {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .page-header {
    padding: 20px 15px;
  }
  
  .page-title {
    font-size: 1.8rem;
  }
  
  .project-card {
    padding: 20px;
  }
  
  .chat-modal-header {
    padding: 15px 20px;
  }
  
  .chat-messages {
    padding: 20px 15px;
  }
  
  .chat-input-area {
    padding: 15px 20px;
    flex-direction: column;
  }
  
  .send-button {
    padding: 12px;
    width: 100%;
  }
  
  .message-item {
    max-width: 95%;
  }
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 768px) {

  .filters-grid {
    grid-template-columns: 1fr;
  }

  .project-header {
    flex-direction: column;
  }

  .project-footer {
    flex-direction: column;
  }

  .project-actions {
    width: 100%;
  }

  .action-button {
    flex: 1;
  }
}

/* Модальное окно редактирования — как в админ-панели */
.project-edit-modal.modal-content {
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  overflow-y: auto;
  background: rgba(10, 77, 140, 0.3);
  border-radius: 24px;
}

.project-edit-modal .modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 30px;
  border-bottom: 1px solid rgba(168, 209, 255, 0.1);
}

.project-edit-modal .modal-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #ffffff;
  margin: 0;
}

.project-edit-modal .modal-close {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  color: #ffffff;
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.project-edit-modal .modal-close:hover {
  background: rgba(231, 76, 60, 0.3);
  border-color: rgba(231, 76, 60, 0.4);
  transform: rotate(90deg);
}

.project-edit-modal .modal-body {
  padding: 30px;
}

.project-edit-modal .form-group {
  margin-bottom: 20px;
}

.project-edit-modal .form-group label {
  display: block;
  margin-bottom: 8px;
  color: #a8d1ff;
  font-size: 0.95rem;
}

.project-edit-modal .form-input {
  width: 100%;
  padding: 12px 16px;
  background: rgba(10, 77, 140, 0.2);
  border: 1px solid rgba(168, 209, 255, 0.2);
  border-radius: 12px;
  color: #ffffff;
  font-size: 1rem;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.project-edit-modal .form-input:focus {
  outline: none;
  border-color: rgba(168, 209, 255, 0.4);
  background: rgba(10, 77, 140, 0.25);
}

.project-edit-modal textarea.form-input {
  resize: vertical;
  min-height: 80px;
}

.project-edit-modal .edit-hint {
  margin: 0 0 8px;
  padding: 12px 14px;
  border-radius: 12px;
  background: rgba(52, 152, 219, 0.12);
  border: 1px solid rgba(52, 152, 219, 0.25);
  color: #d9ecff;
  font-size: 0.88rem;
  line-height: 1.45;
}

.project-edit-modal .modal-actions {
  display: flex;
  gap: 15px;
  margin-top: 24px;
  justify-content: flex-end;
  flex-wrap: wrap;
}

.project-edit-modal .cancel-button,
.project-edit-modal .save-button {
  padding: 12px 30px;
  border-radius: 9999px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(168, 209, 255, 0.3);
}

.project-edit-modal .cancel-button {
  background: rgba(149, 165, 166, 0.2);
  color: #95a5a6;
}

.project-edit-modal .cancel-button:hover {
  background: rgba(149, 165, 166, 0.3);
  transform: translateY(-2px);
}

.project-edit-modal .save-button {
  background: linear-gradient(135deg, #0a4d8c, #1a6bb3);
  color: #ffffff;
}

.project-edit-modal .save-button:hover:not(:disabled) {
  background: linear-gradient(135deg, #1a6bb3, #2a7fc9);
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(8, 51, 88, 0.4);
}

.project-edit-modal .save-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .project-edit-modal .modal-actions {
    flex-direction: column;
  }

  .project-edit-modal .cancel-button,
  .project-edit-modal .save-button {
    width: 100%;
  }
}
</style>
<template>
  <div class="home-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2>Biblioteca</h2>
        <p>Sistema de gerenciamento</p>
      </div>
      
      <nav class="sidebar-nav">
        <a href="#" class="nav-item" :class="{ active: activeMenu === 'autores' }" @click.prevent="setActiveMenu('autores')">
          <i class="fas fa-users"></i>
          Autores
        </a>
        <a href="#" class="nav-item" :class="{ active: activeMenu === 'editoras' }" @click.prevent="setActiveMenu('editoras')">
          <i class="fas fa-building"></i>
          Editoras
        </a>
        <a href="#" class="nav-item" :class="{ active: activeMenu === 'livros' }" @click.prevent="setActiveMenu('livros')">
          <i class="fas fa-book"></i>
          Livros
        </a>
      </nav>

      <div class="sidebar-footer">
        <button @click="handleLogout" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i>
          Sair
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header class="content-header">
        <h1>{{ getPageTitle }}</h1>
        <div class="user-menu">
          <span class="user-name">Bem-vindo(a)</span>
          <button v-if="activeMenu" class="add-button" @click="openModal()">
            <i class="fas fa-plus"></i>
            Adicionar {{ getButtonText }}
          </button>
        </div>
      </header>

      <div class="content-body">
        <!-- Welcome Message -->
        <div class="welcome-message" v-if="!activeMenu">
          <h2>Bem-vindo ao Sistema de Gerenciamento da Biblioteca</h2>
          <p>Selecione uma opção no menu lateral para começar</p>
        </div>

        <!-- Dynamic Content Based on Active Menu -->
        <div v-else>
          <!-- Search Bar -->
          <div class="search-bar">
            <i class="fas fa-search"></i>
            <input 
              type="text" 
              v-model="searchQuery" 
              :placeholder="'Pesquisar ' + getButtonText + '...'"
            >
          </div>

          <!-- Data Table -->
          <div class="table-container">
            <table v-if="filteredItems.length > 0">
              <thead>
                <tr>
                  <th v-for="column in currentColumns" :key="column.key">{{ column.label }}</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in filteredItems" :key="item.id">
                  <td v-for="column in currentColumns" :key="column.key">
                    {{ column.format ? column.format(item[column.key]) : item[column.key] }}
                  </td>
                  <td class="actions-cell">
                    <button class="action-btn edit-btn" @click="openModal(item)">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete-btn" @click="confirmDelete(item)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-else class="no-results">
              <i class="fas fa-search"></i>
              <p>Nenhum resultado encontrado</p>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Form Modal -->
    <div class="modal" v-if="showModal" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ getModalTitle }}</h2>
          <button class="close-btn" @click="closeModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveItem">
            <div class="form-group">
              <label for="name">Nome</label>
              <input 
                type="text" 
                id="name" 
                v-model.trim="formData.name"
                required
                class="form-control"
                :class="{ 'is-invalid': formErrors.name }"
                placeholder="Digite o nome"
              >
              <div v-if="formErrors.name" class="invalid-feedback">
                {{ formErrors.name[0] }}
              </div>
            </div>
            <div class="form-group" v-if="activeMenu === 'autores' || activeMenu === 'editoras'">
              <label for="email">Email</label>
              <input 
                type="email" 
                id="email" 
                v-model.trim="formData.email"
                required
                class="form-control"
                :class="{ 'is-invalid': formErrors.email }"
                placeholder="Digite o email"
              >
              <div v-if="formErrors.email" class="invalid-feedback">
                {{ formErrors.email[0] }}
              </div>
            </div>
            <div class="form-group" v-if="activeMenu === 'autores' || activeMenu === 'editoras'">
              <label for="phone">Telefone</label>
              <input 
                type="tel" 
                id="phone" 
                v-model.trim="formData.phone"
                required
                class="form-control"
                :class="{ 'is-invalid': formErrors.phone }"
                placeholder="Digite o telefone"
                @input="formatPhone($event, 'formData')"
              >
              <div v-if="formErrors.phone" class="invalid-feedback">
                {{ formErrors.phone[0] }}
              </div>
            </div>
            <div class="form-group" v-if="activeMenu === 'editoras'">
              <label for="address">Endereço</label>
              <input 
                type="text" 
                id="address" 
                v-model="formData.address"
                required
                placeholder="Digite o endereço"
              >
            </div>
            <div class="form-group" v-if="activeMenu === 'livros'">
              <label for="title">Título</label>
              <input 
                type="text" 
                id="title" 
                v-model.trim="formData.title"
                required
                class="form-control"
                :class="{ 'is-invalid': formErrors.title }"
                placeholder="Digite o título"
              >
              <div v-if="formErrors.title" class="invalid-feedback">
                {{ formErrors.title[0] }}
              </div>
            </div>
            <div class="form-group" v-if="activeMenu === 'livros'">
              <label for="description">Descrição</label>
              <textarea 
                id="description" 
                v-model.trim="formData.description"
                required
                class="form-control"
                :class="{ 'is-invalid': formErrors.description }"
                rows="3"
                placeholder="Digite a descrição"
              ></textarea>
              <div v-if="formErrors.description" class="invalid-feedback">
                {{ formErrors.description[0] }}
              </div>
            </div>
            <div class="form-group" v-if="activeMenu === 'livros'">
              <label for="author">Autor</label>
              <select 
                id="author" 
                v-model="formData.author_id"
                required
                class="form-control"
                :class="{ 'is-invalid': formErrors.author_id }"
              >
                <option value="">Selecione um autor</option>
                <option v-for="author in authors" :key="author.id" :value="author.id">
                  {{ author.name }}
                </option>
              </select>
              <div v-if="formErrors.author_id" class="invalid-feedback">
                {{ formErrors.author_id[0] }}
              </div>
            </div>
            <div class="form-group" v-if="activeMenu === 'livros'">
              <label for="publisher">Editora</label>
              <select 
                id="publisher" 
                v-model="formData.publisher_id"
                required
                class="form-control"
                :class="{ 'is-invalid': formErrors.publisher_id }"
              >
                <option value="">Selecione uma editora</option>
                <option v-for="publisher in editoras" :key="publisher.id" :value="publisher.id">
                  {{ publisher.name }}
                </option>
              </select>
              <div v-if="formErrors.publisher_id" class="invalid-feedback">
                {{ formErrors.publisher_id[0] }}
              </div>
            </div>
            <div class="form-group" v-if="activeMenu === 'livros'">
              <label for="isbn">ISBN</label>
              <input 
                type="text" 
                id="isbn" 
                v-model.trim="formData.isbn"
                required
                class="form-control"
                :class="{ 'is-invalid': formErrors.isbn }"
                placeholder="Digite o ISBN"
              >
              <div v-if="formErrors.isbn" class="invalid-feedback">
                {{ formErrors.isbn[0] }}
              </div>
            </div>
            <div class="form-group" v-if="activeMenu === 'livros'">
              <label for="publication_year">Ano de Publicação</label>
              <input 
                type="number" 
                id="publication_year" 
                v-model.number="formData.publication_year"
                required
                class="form-control"
                :class="{ 'is-invalid': formErrors.publication_year }"
                :max="currentYear"
                min="1800"
                placeholder="Digite o ano de publicação"
              >
              <div v-if="formErrors.publication_year" class="invalid-feedback">
                {{ formErrors.publication_year[0] }}
              </div>
            </div>
            <div class="form-group" v-if="activeMenu === 'livros'">
              <label for="pages">Número de Páginas</label>
              <input 
                type="number" 
                id="pages" 
                v-model.number="formData.pages"
                required
                class="form-control"
                :class="{ 'is-invalid': formErrors.pages }"
                min="1"
                placeholder="Digite o número de páginas"
              >
              <div v-if="formErrors.pages" class="invalid-feedback">
                {{ formErrors.pages[0] }}
              </div>
            </div>
            <div class="form-group" v-if="activeMenu === 'livros'">
              <label for="language">Idioma</label>
              <input 
                type="text" 
                id="language" 
                v-model.trim="formData.language"
                required
                class="form-control"
                :class="{ 'is-invalid': formErrors.language }"
                placeholder="Digite o idioma"
              >
              <div v-if="formErrors.language" class="invalid-feedback">
                {{ formErrors.language[0] }}
              </div>
            </div>
            <div class="form-actions">
              <button type="button" class="cancel-btn" @click="closeModal">Cancelar</button>
              <button type="submit" class="save-btn">
                {{ isEditing ? 'Salvar' : 'Adicionar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" v-if="showDeleteModal" @click.self="closeDeleteModal">
      <div class="modal-content delete-modal">
        <div class="modal-header">
          <h2>Confirmar Exclusão</h2>
          <button class="close-btn" @click="closeDeleteModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>Tem certeza que deseja excluir este item?</p>
          <div class="form-actions">
            <button class="cancel-btn" @click="closeDeleteModal">Cancelar</button>
            <button class="delete-btn" @click="deleteItem">Excluir</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

      
<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
  setup() {
    const router = useRouter()
    const activeMenu = ref('autores')
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const searchQuery = ref('')
    const authors = ref([])
    const editoras = ref([])
    const books = ref([])
    const itemToDelete = ref(null)
    const formData = ref({
      name: '',
      email: '',
      phone: '',
      address: '',
      title: '',
      description: '',
      author_id: '',
      publisher_id: '',
      isbn: '',
      publication_year: '',
      pages: '',
      language: ''
    })
    const formErrors = ref({})

    const getPageTitle = computed(() => {
      switch (activeMenu.value) {
        case 'autores':
          return 'Gerenciar Autores'
        case 'editoras':
          return 'Gerenciar Editoras'
        case 'livros':
          return 'Gerenciar Livros'
        default:
          return 'Bem-vindo'
      }
    })

    const getButtonText = computed(() => {
      switch (activeMenu.value) {
        case 'autores':
          return 'Autor'
        case 'editoras':
          return 'Editora'
        case 'livros':
          return 'Livro'
        default:
          return ''
      }
    })

    const currentColumns = computed(() => {
      switch (activeMenu.value) {
        case 'autores':
          return [
            { key: 'name', label: 'Nome' },
            { key: 'email', label: 'Email' },
            { key: 'phone', label: 'Telefone', format: (phone) => formatPhone(phone) }
          ]
        case 'editoras':
          return [
            { key: 'name', label: 'Nome' },
            { key: 'email', label: 'Email' },
            { key: 'phone', label: 'Telefone', format: (phone) => formatPhone(phone) },
            { key: 'address', label: 'Endereço' }
          ]
        case 'livros':
          return [
            { key: 'title', label: 'Título' },
            { key: 'author_name', label: 'Autor' },
            { key: 'publisher_name', label: 'Editora' },
            { key: 'isbn', label: 'ISBN', format: (isbn) => formatISBN(isbn) },
            { key: 'publication_year', label: 'Ano' },
            { key: 'pages', label: 'Páginas' }
          ]
        default:
          return []
      }
    })

    const getActiveItems = computed(() => {
      switch (activeMenu.value) {
        case 'autores':
          return authors.value
        case 'editoras':
          return editoras.value
        case 'livros':
          return books.value
        default:
          return []
      }
    })

    const filteredItems = computed(() => {
      const query = searchQuery.value.toLowerCase().trim()
      if (!query) return getActiveItems.value

      return getActiveItems.value.filter(item => {
        if (activeMenu.value === 'autores') {
          return (
            item.name?.toLowerCase().includes(query) ||
            item.email?.toLowerCase().includes(query) ||
            item.phone?.toLowerCase().includes(query)
          )
        } else if (activeMenu.value === 'editoras') {
          return (
            item.name?.toLowerCase().includes(query) ||
            item.email?.toLowerCase().includes(query) ||
            item.phone?.toLowerCase().includes(query) ||
            item.address?.toLowerCase().includes(query)
          )
        } else if (activeMenu.value === 'livros') {
          return (
            item.title?.toLowerCase().includes(query) ||
            item.description?.toLowerCase().includes(query) ||
            item.isbn?.toLowerCase().includes(query) ||
            item.language?.toLowerCase().includes(query)
          )
        }
        return true
      })
    })

    const setActiveMenu = (menu) => {
      activeMenu.value = menu
      fetchItems()
    }

    const getAxiosConfig = () => {
      const token = localStorage.getItem('token')
      return {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      }
    }

    const fetchItems = async () => {
      try {
        const config = getAxiosConfig()
        let response

        if (activeMenu.value === 'autores') {
          response = await axios.get('/api/authors', config)
          authors.value = response.data
        } else if (activeMenu.value === 'editoras') {
          response = await axios.get('/api/publishers', config)
          editoras.value = response.data
        } else if (activeMenu.value === 'livros') {
          response = await axios.get('/api/books', config)
          const booksData = response.data
          
          // Get authors and publishers for books
          const authorsResponse = await axios.get('/api/authors', config)
          const publishersResponse = await axios.get('/api/publishers', config)
          
          const authorsMap = new Map(authorsResponse.data.map(author => [author.id, author.name]))
          const publishersMap = new Map(publishersResponse.data.map(publisher => [publisher.id, publisher.name]))
          
          books.value = booksData.map(book => ({
            ...book,
            author_name: authorsMap.get(book.author_id),
            publisher_name: publishersMap.get(book.publisher_id)
          }))
        }
      } catch (error) {
        console.error('Error fetching items:', error)
        if (error.response?.status === 401) {
          handleLogout()
        }
      }
    }

    const openModal = (item = null) => {
      if (item) {
        formData.value = { ...item }
      } else {
        resetForm()
      }
      showModal.value = true
    }

    const closeModal = () => {
      showModal.value = false
      resetForm()
    }

    const resetForm = () => {
      formData.value = {
        name: '',
        email: '',
        phone: '',
        address: '',
        title: '',
        description: '',
        author_id: '',
        publisher_id: '',
        isbn: '',
        publication_year: '',
        pages: '',
        language: ''
      }
      formErrors.value = {}
    }

    const getModalTitle = computed(() => {
      const action = formData.value.id ? 'Editar' : 'Adicionar'
      return `${action} ${getButtonText.value}`
    })

    const saveItem = async () => {
      try {
        const config = getAxiosConfig()
        let endpoint
        let data

        if (activeMenu.value === 'autores') {
          endpoint = '/api/authors'
          data = {
            name: formData.value.name,
            email: formData.value.email,
            phone: formData.value.phone
          }
        } else if (activeMenu.value === 'editoras') {
          endpoint = '/api/publishers'
          data = {
            name: formData.value.name,
            email: formData.value.email,
            phone: formData.value.phone,
            address: formData.value.address
          }
        } else if (activeMenu.value === 'livros') {
          endpoint = '/api/books'
          data = {
            title: formData.value.title,
            description: formData.value.description,
            author_id: formData.value.author_id,
            publisher_id: formData.value.publisher_id,
            isbn: formData.value.isbn,
            publication_year: formData.value.publication_year,
            pages: formData.value.pages,
            language: formData.value.language
          }
        }

        if (formData.value.id) {
          await axios.put(`${endpoint}/${formData.value.id}`, data, config)
        } else {
          await axios.post(endpoint, data, config)
        }

        closeModal()
        fetchItems()
      } catch (error) {
        console.error('Error saving item:', error)
        if (error.response?.data?.errors) {
          formErrors.value = error.response.data.errors
        }
      }
    }

    const confirmDelete = (item) => {
      itemToDelete.value = item
      showDeleteModal.value = true
    }

    const closeDeleteModal = () => {
      showDeleteModal.value = false
      itemToDelete.value = null
    }

    const deleteItem = async () => {
      if (!itemToDelete.value) return

      try {
        const config = getAxiosConfig()
        let endpoint

        if (activeMenu.value === 'autores') {
          endpoint = `/api/authors/${itemToDelete.value.id}`
        } else if (activeMenu.value === 'editoras') {
          endpoint = `/api/publishers/${itemToDelete.value.id}`
        } else if (activeMenu.value === 'livros') {
          endpoint = `/api/books/${itemToDelete.value.id}`
        }

        await axios.delete(endpoint, config)
        closeDeleteModal()
        fetchItems()
      } catch (error) {
        console.error('Error deleting item:', error)
      }
    }

    const formatPhone = (phone) => {
      if (!phone) return ''
      
      // Remove all non-numeric characters
      const numbers = phone.replace(/\D/g, '')
      
      // Format according to length
      if (numbers.length === 11) {
        // Mobile phone: (XX) XXXXX-XXXX
        return `(${numbers.slice(0,2)}) ${numbers.slice(2,7)}-${numbers.slice(7)}`
      } else if (numbers.length === 10) {
        // Landline: (XX) XXXX-XXXX
        return `(${numbers.slice(0,2)}) ${numbers.slice(2,6)}-${numbers.slice(6)}`
      }
      
      return phone
    }

    const formatISBN = (isbn) => {
      if (!isbn) return ''
      
      // Remove all non-numeric characters and hyphens
      const cleaned = isbn.replace(/[^\dX-]/g, '')
      
      // If it's ISBN-13
      if (cleaned.length === 13) {
        return `${cleaned.slice(0,3)}-${cleaned.slice(3,4)}-${cleaned.slice(4,6)}-${cleaned.slice(6,12)}-${cleaned.slice(12)}`
      }
      
      // If it's ISBN-10
      if (cleaned.length === 10) {
        return `${cleaned.slice(0,1)}-${cleaned.slice(1,3)}-${cleaned.slice(3,9)}-${cleaned.slice(9)}`
      }
      
      return isbn
    }

    const handleLogout = () => {
      localStorage.removeItem('token')
      router.push('/login')
    }

    // Initialize
    fetchItems()

    return {
      activeMenu,
      showModal,
      showDeleteModal,
      searchQuery,
      authors,
      editoras,
      books,
      itemToDelete,
      formData,
      formErrors,
      getPageTitle,
      getButtonText,
      currentColumns,
      getActiveItems,
      filteredItems,
      setActiveMenu,
      openModal,
      closeModal,
      resetForm,
      getModalTitle,
      saveItem,
      confirmDelete,
      closeDeleteModal,
      deleteItem,
      formatPhone,
      formatISBN,
      handleLogout,
      currentYear: new Date().getFullYear()
    }
  }
}
</script>

<style scoped>
.home-container {
  display: flex;
  height: 100vh;
  background-color: #f5f6fa;
}

/* Sidebar */
.sidebar {
  width: 250px;
  background-color: #2c3e50;
  color: white;
  padding: 1rem;
  display: flex;
  flex-direction: column;
}

.sidebar-header {
  padding: 1rem 0;
  border-bottom: 1px solid #34495e;
}

.sidebar-header h2 {
  margin: 0;
  font-size: 1.5rem;
}

.sidebar-header p {
  margin: 0.5rem 0 0;
  font-size: 0.9rem;
  color: #bdc3c7;
}

.sidebar-nav {
  flex: 1;
  margin-top: 2rem;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 0.8rem 1rem;
  color: #ecf0f1;
  text-decoration: none;
  border-radius: 8px;
  margin-bottom: 0.5rem;
  transition: all 0.3s ease;
}

.nav-item i {
  margin-right: 1rem;
  width: 20px;
  text-align: center;
}

.nav-item:hover, .nav-item.active {
  background-color: #34495e;
  color: #3498db;
}

.sidebar-footer {
  padding-top: 1rem;
  border-top: 1px solid #34495e;
}

.logout-btn {
  width: 100%;
  padding: 0.8rem;
  background-color: transparent;
  border: 1px solid #e74c3c;
  color: #e74c3c;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.logout-btn i {
  margin-right: 0.5rem;
}

.logout-btn:hover {
  background-color: #e74c3c;
  color: white;
}

/* Main Content */
.main-content {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
}

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.content-header h1 {
  margin: 0;
  font-size: 2rem;
  color: #2c3e50;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-name {
  color: #7f8c8d;
}

.add-button {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.8rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.add-button:hover {
  background-color: #2980b9;
}

/* Search Bar */
.search-bar {
  position: relative;
  margin-bottom: 2rem;
}

.search-bar i {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #95a5a6;
}

.search-bar input {
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 1px solid #dcdde1;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.search-bar input:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

/* Table */
.table-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #dcdde1;
}

th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #2c3e50;
}

tr:hover {
  background-color: #f8f9fa;
}

.actions-cell {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  padding: 0.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.edit-btn {
  background-color: #f1c40f;
  color: white;
}

.edit-btn:hover {
  background-color: #f39c12;
}

.delete-btn {
  background-color: #e74c3c;
  color: white;
}

.delete-btn:hover {
  background-color: #c0392b;
}

/* Modal */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #dcdde1;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
  color: #2c3e50;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #95a5a6;
  cursor: pointer;
  padding: 0.5rem;
}

.close-btn:hover {
  color: #7f8c8d;
}

.modal-body {
  padding: 1.5rem;
}

/* Form */
.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #2c3e50;
  font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #dcdde1;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #3498db;
  box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.cancel-btn {
  padding: 0.8rem 1.5rem;
  border: 1px solid #95a5a6;
  background-color: white;
  color: #95a5a6;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.cancel-btn:hover {
  background-color: #95a5a6;
  color: white;
}

.save-btn {
  padding: 0.8rem 1.5rem;
  border: none;
  background-color: #3498db;
  color: white;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.save-btn:hover {
  background-color: #2980b9;
}

/* Delete Modal */
.delete-modal {
  max-width: 400px;
}

.delete-modal .modal-body {
  text-align: center;
}

.delete-modal p {
  margin: 1rem 0 2rem;
  color: #7f8c8d;
}

/* Welcome Message */
.welcome-message {
  text-align: center;
  padding: 3rem;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.welcome-message h2 {
  color: #2c3e50;
  margin-bottom: 1rem;
}

.welcome-message p {
  color: #7f8c8d;
}

/* No Results */
.no-results {
  text-align: center;
  padding: 3rem;
  color: #7f8c8d;
}

.no-results i {
  font-size: 3rem;
  margin-bottom: 1rem;
}

@media (max-width: 768px) {
  .home-container {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
    height: auto;
  }

  .main-content {
    padding: 1rem;
  }

  .content-header {
    flex-direction: column;
    gap: 1rem;
  }

  .actions-cell {
    flex-direction: column;
  }

  .action-btn {
    width: 100%;
  }
}

</style>
<template>
    <div class="layout container-fluid p-0 m-0">
        <!-- Header Toolbar -->
        <section class="tool-section">
            <button class="btn" type="button" @click="toggleSidebar">
                <Icon class="icon" icon="mingcute:menu-line" width="24" height="24" />
            </button>
            <div class="search">
                <input type="text" class="form-control" placeholder="Search Quill" v-model="searchQuery"
                    @input="filterContent" />
            </div>
        </section>

        <!-- Main Content -->
        <section class="note-section">
            <!-- Sidebar -->
            <aside class="desktop-aside" v-show="sidebarVisible">
                <ul>
                    <li>
                        <a href="#" :class="{ active: activeSection === 'notes' }" @click.prevent="setSection('notes')">
                            <Icon icon="mingcute:notebook-line" width="24" height="24" />
                            Notes
                        </a>
                    </li>
                    <li>
                        <a href="#" :class="{ active: activeSection === 'reminders' }"
                            @click.prevent="setSection('reminders')">
                            <Icon icon="mingcute:notification-line" width="24" height="24" />
                            Reminders
                        </a>
                    </li>
                    <li>
                        <a href="#" :class="{ active: activeSection === 'todos' }" @click.prevent="setSection('todos')">
                            <Icon icon="mingcute:to-do-line" width="24" height="24" />
                            To Do
                        </a>
                    </li>
                    <li>
                        <a href="#" :class="{ active: activeSection === 'checklists' }"
                            @click.prevent="setSection('checklists')">
                            <Icon icon="mingcute:checkbox-line" width="24" height="24" />
                            Check List
                        </a>
                    </li>

                    <hr>

                    <h6>Tags</h6>
                    <li>
                        <a href="#">
                            <Icon icon="mingcute:add-line" width="24" height="24" />
                            Create new tag
                        </a>
                    </li>

                    <hr>

                    <li>
                        <a href="#">
                            <Icon icon="mingcute:archive-line" width="24" height="24" />
                            Archive
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <Icon icon="mingcute:delete-3-line" width="24" height="24" />
                            Trash
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <Icon icon="mingcute:question-line" width="24" height="24" />
                            Help & Feedback
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- Main Content Area -->
            <div class="notes p-4">
                <!-- Loading -->
                <div v-if="loading" class="text-center my-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Notes -->
                <div v-else-if="activeSection === 'notes'">
                    <div v-if="filteredNotes.length === 0" class="text-center my-5 text-muted">
                        <Icon icon="mingcute:notebook-line" width="80" height="80" class="mb-3 opacity-50" />
                        <p>No notes found.</p>
                    </div>
                    <div v-else class="row">
                        <div v-for="note in filteredNotes" :key="note.note_id" class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm note-card">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ note.title }}</h5>
                                    <p class="card-text flex-grow-1" v-if="note.content">
                                        {{ truncate(note.content, 120) }}
                                    </p>
                                    <p v-else class="card-text text-muted fst-italic">No content</p>
                                    <small class="text-muted mb-3">
                                        ID: {{ note.note_id }}<br>
                                        {{ formatDate(note.created_at) }}
                                    </small>
                                    <div class="mt-auto d-flex gap-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm flex-fill"
                                            data-bs-toggle="modal" data-bs-target="#noteModal"
                                            @click="openEditModal(note)">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm flex-fill"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            @click="setDeleteCandidate(note.note_id, note.title, 'note')">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reminders -->
                <div v-else-if="activeSection === 'reminders'">
                    <div v-if="reminders.length === 0" class="text-center my-5 text-muted">
                        <Icon icon="mingcute:notification-line" width="80" height="80" class="mb-3 opacity-50" />
                        <p>No reminders yet.</p>
                    </div>
                    <div v-else>
                        <div v-for="reminder in reminders" :key="reminder.reminder_id" class="card mb-3 shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>{{ reminder.title }}</h5>
                                    <p class="text-muted mb-1">{{ reminder.description || 'No description' }}</p>
                                    <small>Remind at: {{ formatDate(reminder.remind_at) }}</small>
                                    <span class="badge ms-2"
                                        :class="reminder.is_completed ? 'bg-success' : 'bg-warning'">
                                        {{ reminder.is_completed ? 'Completed' : 'Pending' }}
                                    </span>
                                </div>
                                <button class="btn btn-outline-danger btn-sm"
                                    @click="deleteReminder(reminder.reminder_id)">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- To Do -->
                <div v-else-if="activeSection === 'todos'">
                    <div v-if="todos.length === 0" class="text-center my-5 text-muted">
                        <Icon icon="mingcute:to-do-line" width="80" height="80" class="mb-3 opacity-50" />
                        <p>No tasks yet.</p>
                    </div>
                    <div v-else>
                        <div v-for="todo in todos" :key="todo.todo_id"
                            class="d-flex align-items-center mb-3 p-3 border rounded shadow-sm">
                            <input type="checkbox" class="me-3" :checked="todo.is_completed"
                                @change="toggleTodo(todo.todo_id)" />
                            <div class="flex-grow-1">
                                <h6 :class="{ 'text-decoration-line-through text-muted': todo.is_completed }">{{
                                    todo.task }}</h6>
                                <small v-if="todo.due_date" class="text-muted">Due: {{ formatDate(todo.due_date)
                                    }}</small>
                            </div>
                            <button class="btn btn-outline-danger btn-sm"
                                @click="deleteTodo(todo.todo_id)">Delete</button>
                        </div>
                    </div>
                </div>

                <!-- Checklists -->
                <div v-else-if="activeSection === 'checklists'">
                    <div v-if="checklists.length === 0" class="text-center my-5 text-muted">
                        <Icon icon="mingcute:checkbox-line" width="80" height="80" class="mb-3 opacity-50" />
                        <p>No checklists yet.</p>
                    </div>
                    <div v-else>
                        <div v-for="checklist in checklists" :key="checklist.checklist_id" class="card mb-4 shadow-sm">
                            <div class="card-header">
                                <h5>{{ checklist.title }}</h5>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li v-for="item in checklist.items" :key="item.item_id"
                                    class="list-group-item d-flex align-items-center">
                                    <input type="checkbox" class="me-3" :checked="item.is_checked"
                                        @change="toggleChecklistItem(item.item_id)" />
                                    <span :class="{ 'text-decoration-line-through text-muted': item.is_checked }">{{
                                        item.text }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Floating Create Menu -->
        <div class="dropup create-dropdown">
            <button type="button" class="btn add-btn" data-bs-toggle="dropdown">
                <Icon icon="mingcute:add-line" width="28" height="28" />
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#noteModal"
                        @click.prevent="openCreateModal">
                        <Icon icon="mingcute:notebook-line" width="24" height="24" /> Note
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#reminderModal">
                        <Icon icon="mingcute:notification-line" width="24" height="24" /> Reminder
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#todoModal">
                        <Icon icon="mingcute:to-do-line" width="24" height="24" /> To Do
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#checklistModal">
                        <Icon icon="mingcute:checkbox-line" width="24" height="24" /> Check List
                    </a>
                </li>
            </ul>
        </div>

        <!-- Note Modal (Create/Edit) -->
        <div class="modal fade" id="noteModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form @submit.prevent="editingNote ? updateNote() : createNote()">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ editingNote ? 'Edit Note' : 'Create New Note' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Title *</label>
                                <input v-model="form.title" type="text" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <textarea v-model="form.content" class="form-control" rows="6"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" :disabled="saving">
                                {{ saving ? 'Saving...' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- To Do Modal -->
        <div class="modal fade" id="todoModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form @submit.prevent="createTodo">
                        <div class="modal-header">
                            <h5 class="modal-title">Create New To Do</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Task *</label>
                                <input v-model="todoForm.task" type="text" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Due Date (optional)</label>
                                <input v-model="todoForm.due_date" type="date" class="form-control" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" :disabled="saving">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reminder Modal -->
        <div class="modal fade" id="reminderModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form @submit.prevent="createReminder">
                        <div class="modal-header">
                            <h5 class="modal-title">Create New Reminder</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Title *</label>
                                <input v-model="reminderForm.title" type="text" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea v-model="reminderForm.description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Remind At *</label>
                                <input v-model="reminderForm.remind_at" type="datetime-local" class="form-control"
                                    required />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" :disabled="saving">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Checklist Modal (Create + Add Items) -->
        <div class="modal fade" id="checklistModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form @submit.prevent="createChecklist">
                        <div class="modal-header">
                            <h5 class="modal-title">Create New Checklist</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Title *</label>
                                <input v-model="checklistForm.title" type="text" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Items (one per line)</label>
                                <textarea v-model="checklistForm.itemsText" class="form-control" rows="6"
                                    placeholder="Buy milk&#10;Call mom&#10;Finish report"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" :disabled="saving">Create Checklist</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Permanently delete "<strong>{{ deleteCandidateTitle }}</strong>"?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" @click="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div v-for="toast in toasts" :key="toast.id" class="toast" :class="toast.type" role="alert"
                data-bs-autohide="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body">{{ toast.message }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Icon } from '@iconify/vue';

export default {
    components: { Icon },
    data() {
        return {
            activeSection: 'notes',
            searchQuery: '',
            notes: [], reminders: [], todos: [], checklists: [],
            filteredNotes: [],
            loading: false,
            saving: false,
            deleting: false,
            editingNote: null,
            deleteCandidateId: null,
            deleteCandidateTitle: '',
            deleteType: 'note',
            form: { title: '', content: '' },
            toasts: [],
            toastCounter: 0,
            sidebarVisible: true,
            isMobile: false,

            todoForm: {
      task: '',
      due_date: ''
    },
    reminderForm: {
      title: '',
      description: '',
      remind_at: ''
    },
    checklistForm: {
      title: '',
      itemsText: ''
    },
        };
    },

    watch: {
        notes: function () {
            this.filteredNotes = this.notes;
        },
        searchQuery: function () {
            this.filterContent();
        }
    },

    mounted() {
        this.checkMobile();
        window.addEventListener('resize', this.checkMobile);
        this.setSection('notes');
    },

    beforeUnmount() {
        window.removeEventListener('resize', this.checkMobile);
    },

    methods: {
        checkMobile() {
            this.isMobile = window.innerWidth < 768;
            this.sidebarVisible = !this.isMobile;
        },

        toggleSidebar() {
            this.sidebarVisible = !this.sidebarVisible;
        },

        setSection(section) {
            this.activeSection = section;
            this.loading = true;
            if (section === 'notes') this.fetchNotes();
            if (section === 'reminders') this.fetchReminders();
            if (section === 'todos') this.fetchTodos();
            if (section === 'checklists') this.fetchChecklists();
        },

        filterContent() {
            if (this.activeSection === 'notes') {
                this.filteredNotes = this.notes.filter(note =>
                    note.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (note.content && note.content.toLowerCase().includes(this.searchQuery.toLowerCase()))
                );
            }
        },

        async fetchNotes() {
            try {
                const res = await axios.get('/api/notes');
                this.notes = res.data;
                this.filteredNotes = this.notes;
            } catch (err) {
                this.showToast('Failed to load notes', 'bg-danger');
            } finally {
                this.loading = false;
            }
        },

        async fetchReminders() {
            try {
                const res = await axios.get('/api/reminders');
                this.reminders = res.data;
            } catch (err) {
                this.showToast('Failed to load reminders', 'bg-danger');
            } finally {
                this.loading = false;
            }
        },

        async fetchTodos() {
            try {
                const res = await axios.get('/api/todos');
                this.todos = res.data;
            } catch (err) {
                this.showToast('Failed to load todos', 'bg-danger');
            } finally {
                this.loading = false;
            }
        },

        async fetchChecklists() {
            try {
                const res = await axios.get('/api/checklists');
                this.checklists = res.data;
            } catch (err) {
                this.showToast('Failed to load checklists', 'bg-danger');
            } finally {
                this.loading = false;
            }
        },

        resetTodoForm() {
  this.todoForm = { task: '', due_date: '' };
},

resetReminderForm() {
  this.reminderForm = { title: '', description: '', remind_at: '' };
},

resetChecklistForm() {
  this.checklistForm = { title: '', itemsText: '' };
},

async createTodo() {
  this.saving = true;
  try {
    const res = await axios.post('/api/todos', this.todoForm);
    this.todos.unshift(res.data);
    this.closeModal('todoModal');
    this.resetTodoForm();
    this.showToast('To Do created!', 'bg-success');
  } catch (err) {
    this.showToast('Failed to create To Do', 'bg-danger');
  } finally {
    this.saving = false;
  }
},

async createReminder() {
  this.saving = true;
  try {
    const res = await axios.post('/api/reminders', this.reminderForm);
    this.reminders.unshift(res.data);
    this.closeModal('reminderModal');
    this.resetReminderForm();
    this.showToast('Reminder created!', 'bg-success');
  } catch (err) {
    this.showToast('Failed to create reminder', 'bg-danger');
  } finally {
    this.saving = false;
  }
},

async createChecklist() {
  this.saving = true;
  try {
    // First create the checklist
    const checklistRes = await axios.post('/api/checklists', {
      title: this.checklistForm.title
    });

    const checklist = checklistRes.data;

    // Then add items if any
    if (this.checklistForm.itemsText.trim()) {
      const items = this.checklistForm.itemsText
        .split('\n')
        .map(line => line.trim())
        .filter(line => line.length > 0);

      for (const text of items) {
        await axios.post(`/api/checklists/${checklist.checklist_id}/items`, { text });
      }

      // Refresh items
      checklist.items = items.map((text, i) => ({
        item_id: `temp-${i}`,
        text,
        is_checked: false
      }));
    }

    this.checklists.unshift(checklist);
    this.closeModal('checklistModal');
    this.resetChecklistForm();
    this.showToast('Checklist created!', 'bg-success');
  } catch (err) {
    this.showToast('Failed to create checklist', 'bg-danger');
  } finally {
    this.saving = false;
  }
},

// Fix toggleTodo to properly toggle
async toggleTodo(id) {
  try {
    const todo = this.todos.find(t => t.todo_id === id);
    if (!todo) return;

    const res = await axios.put(`/api/todos/${id}`, {
      task: todo.task,
      due_date: todo.due_date,
      is_completed: !todo.is_completed
    });

    todo.is_completed = res.data.is_completed;
  } catch (err) {
    this.showToast('Failed to update task', 'bg-danger');
  }
},

        openCreateModal() {
            this.editingNote = null;
            this.resetForm();
        },

        openEditModal(note) {
            this.editingNote = note;
            this.form = { title: note.title, content: note.content || '' };
        },

        async createNote() {
            this.saving = true;
            try {
                const res = await axios.post('/api/notes', this.form);
                this.notes.unshift(res.data);
                this.filteredNotes = this.notes;
                this.closeModal('noteModal');
                this.showToast('Note created!', 'bg-success');
            } catch (err) {
                this.showToast('Failed to create note', 'bg-danger');
            } finally {
                this.saving = false;
            }
        },

        async updateNote() {
            this.saving = true;
            try {
                const res = await axios.put(`/api/notes/${this.editingNote.note_id}`, this.form);
                const idx = this.notes.findIndex(n => n.note_id === this.editingNote.note_id);
                if (idx !== -1) this.notes[idx] = res.data;
                this.filteredNotes = this.notes;
                this.closeModal('noteModal');
                this.showToast('Note updated!', 'bg-success');
            } catch (err) {
                this.showToast('Failed to update note', 'bg-danger');
            } finally {
                this.saving = false;
            }
        },

        setDeleteCandidate(id, title, type = 'note') {
            this.deleteCandidateId = id;
            this.deleteCandidateTitle = title;
            this.deleteType = type;
        },

        async confirmDelete() {
            this.deleting = true;
            try {
                if (this.deleteType === 'note') {
                    await axios.delete(`/api/notes/${this.deleteCandidateId}`);
                    this.notes = this.notes.filter(n => n.note_id !== this.deleteCandidateId);
                    this.filteredNotes = this.notes;
                }
                this.closeModal('deleteModal');
                this.showToast('Deleted successfully', 'bg-danger');
            } catch (err) {
                this.showToast('Delete failed', 'bg-danger');
            } finally {
                this.deleting = false;
            }
        },

        async deleteReminder(id) {
            if (!confirm('Delete reminder?')) return;
            try {
                await axios.delete(`/api/reminders/${id}`);
                this.reminders = this.reminders.filter(r => r.reminder_id !== id);
                this.showToast('Reminder deleted', 'bg-danger');
            } catch (err) {
                this.showToast('Failed to delete', 'bg-danger');
            }
        },

        async deleteTodo(id) {
            if (!confirm('Delete task?')) return;
            try {
                await axios.delete(`/api/todos/${id}`);
                this.todos = this.todos.filter(t => t.todo_id !== id);
                this.showToast('Task deleted', 'bg-danger');
            } catch (err) {
                this.showToast('Failed to delete', 'bg-danger');
            }
        },

        async toggleTodo(id) {
            try {
                await axios.patch(`/api/todos/${id}`, { is_completed: true }); // simple toggle
                const todo = this.todos.find(t => t.todo_id === id);
                if (todo) todo.is_completed = !todo.is_completed;
            } catch (err) {
                this.showToast('Failed to update', 'bg-danger');
            }
        },

        async toggleChecklistItem(id) {
            try {
                await axios.patch(`/api/checklist-items/${id}`);
                // Find and toggle
                for (const cl of this.checklists) {
                    const item = cl.items.find(i => i.item_id === id);
                    if (item) item.is_checked = !item.is_checked;
                }
            } catch (err) {
                this.showToast('Failed to update', 'bg-danger');
            }
        },

        resetForm() {
            this.editingNote = null;
            this.form = { title: '', content: '' };
        },

        closeModal(id) {
            const modal = bootstrap.Modal.getInstance(document.getElementById(id));
            if (modal) modal.hide();
        },

        showToast(message, type = 'bg-success') {
            const id = ++this.toastCounter;
            this.toasts.push({ id, message, type });
            this.$nextTick(() => {
                const el = document.querySelector(`.toast-container .toast:last-child`);
                if (el) new bootstrap.Toast(el).show();
            });
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        truncate(text, len) {
            return text.length > len ? text.slice(0, len) + '...' : text;
        },
    },
};
</script>

<style scoped>
.layout {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 18px;
}

.tool-section {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding: 8px;
    gap: 12px;
    background-color: #FFFFFF;
    border-radius: 12px;
}

.tool-section .btn {
    border: none;
    outline: none;
}

.tool-section .search {
    flex: 1;
}

.tool-section .search input {
    padding: 8px 16px;
    border-radius: 12px;
    outline: none;
    box-shadow: none;
}

.tool-section .search input:focus {
    outline: none;
    border: 1px solid #00A82D;
    box-shadow: 0 0 5px rgba(0, 168, 45, 0.5);
}

.note-section {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 12px;
    min-height: calc(100vh - 180px);
}

.desktop-aside {
    width: 280px;
    background-color: #FFFFFF;
    border-radius: 12px;
    padding: 12px;
}

.desktop-aside h6 {
    margin-left: 12px;
    font-weight: 600;
    color: #666;
}

.desktop-aside ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.desktop-aside ul li {
    transition: all 0.2s ease;
    cursor: pointer;
    color: #222;
    font-weight: 600;
}

.desktop-aside ul li a {
    text-decoration: none;
    padding: 10px 12px;
    border-radius: 8px;
    transition: all 0.2s ease;
    color: #222;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
}

.desktop-aside ul li a:hover,
.desktop-aside ul li a.active {
    background-color: #222;
    color: #F2F0EC;
}

.notes {
    flex: 1;
    border-radius: 12px;
    background-color: #FFFFFF;
}

.notes .loading,
.notes .empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    margin: auto;
}


.note-card {
    transition: all 0.3s ease;
    border: none;
}

.note-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15) !important;
}

.create-dropdown {
    background-color: transparent;
}

.create-dropdown .add-btn {
    width: 58px;
    height: 58px;
    font-size: 18px;
    border-radius: 50%;
    background-color: #00A82D;
    position: fixed;
    bottom: 24px;
    right: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    outline: none;
    border: none;
}

.create-dropdown .add-btn:hover {
    background-color: #222;
    color: #F2F0EC;
    transform: scale(1.05);
}

.create-dropdown ul {
    list-style: none;
    padding: 0;
    outline: none;
    border: none;
    background-color: transparent;
}

.create-dropdown ul li {
    cursor: pointer;
    background-color: #00A82D;
    border-radius: 99px;
    margin-top: 8px;
    margin-bottom: 8px;
}

.create-dropdown ul li a {
    text-decoration: none;
    border-radius: 99px;
    padding: 12px;
    transition: all 0.2s ease;
    color: #F2F0EC;
    font-weight: 600;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
}

.create-dropdown ul li a:hover {
    background-color: #222;
}
</style>
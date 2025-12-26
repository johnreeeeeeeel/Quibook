<template>
    <div class="layout container-fluid p-0 m-0">

        <section class="tool-section">
            <button class="btn" type="button" @click="toggleSidebar">
                <Icon class="icon" icon="mingcute:menu-line" width="24px" height="24px" />
            </button>

            <div class="search">
                <input type="text" class="form-control" placeholder="Search Quill" />
            </div>
        </section>

        <section class="note-section">
            <aside class="desktop-aside" v-show="sidebarVisible">
                <ul>
                    <li><a href="">
                            <Icon class="icon" icon="mingcute:notebook-line" width="24px" height="24px" />
                            Notes
                        </a></li>
                    <li><a href="">
                            <Icon class="icon" icon="mingcute:notification-line" width="24px" height="24px" />
                            Reminders
                        </a></li>
                    <li><a href="">
                            <Icon class="icon" icon="mingcute:to-do-line" width="24px" height="24px" />
                            To Do
                        </a></li>

                    <hr>

                    <h6>Tags</h6>
                    <li><a href="">
                            <Icon class="icon" icon="mingcute:add-line" width="24px" height="24px" />
                            Create new tag
                        </a></li>

                    <hr>

                    <li><a href="">
                            <Icon class="icon" icon="mingcute:archive-line" width="24px" height="24px" />
                            Archive
                        </a></li>
                    <li><a href="">
                            <Icon class="icon" icon="mingcute:delete-3-line" width="24px" height="24px" />
                            Trash
                        </a></li>
                    <li><a href="">
                            <Icon class="icon" icon="mingcute:question-line" width="24px" height="24px" />
                            Help & Feedback
                        </a></li>
                </ul>
            </aside>

            <div class="notes">

                <!-- Loading -->
                <div v-if="loading" class="loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="notes.length === 0 && !loading" class="empty">
                    No notes yet. Click the <strong>+</strong> button to create one!
                </div>

                <div v-else>
                    <div v-for="note in notes" :key="note.note_id" class="col-md-6 col-lg-4 mb-4">
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
                                        data-bs-toggle="modal" data-bs-target="#noteModal" @click="openEditModal(note)">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm flex-fill"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        @click="setDeleteCandidate(note.note_id, note.title)">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Floating Add Button -->
        <button type="button" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#noteModal" @click="openCreateModal">
            <Icon class="icon" icon="mingcute:add-line" width="24px" height="24px" />
        </button>

        <!-- Create / Edit Modal -->
        <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form @submit.prevent="editingNote ? updateNote() : createNote()">
                        <div class="modal-header">
                            <h5 class="modal-title" id="noteModalLabel">
                                {{ editingNote ? 'Edit Note' : 'Create New Note' }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input v-model="form.title" type="text" class="form-control"
                                    :class="{ 'is-invalid': errors.title }" placeholder="Enter note title" required />
                                <div v-if="errors.title" class="invalid-feedback">
                                    {{ errors.title[0] }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content (optional)</label>
                                <textarea v-model="form.content" class="form-control" rows="6"
                                    placeholder="Write your note here..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="saving">
                                <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                                {{ saving ? 'Saving...' : (editingNote ? 'Update' : 'Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to <strong>permanently delete</strong> the note:<br>
                        <em>"{{ deleteCandidateTitle }}"</em>?<br>
                        <small class="text-muted">This action cannot be undone.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-danger" @click="confirmDelete" :disabled="deleting">
                            <span v-if="deleting" class="spinner-border spinner-border-sm me-2"></span>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Container (Top Right) -->
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
            <div v-for="toast in toasts" :key="toast.id" class="toast align-items-center text-white border-0"
                :class="toast.type" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true"
                data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ toast.message }}
                    </div>
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
    components: {
        Icon,
    },

    name: 'NotesPage',

    data() {
        return {
            notes: [],
            loading: true,
            saving: false,
            deleting: false,
            errors: {},
            editingNote: null,
            deleteCandidateId: null,
            deleteCandidateTitle: '',
            form: {
                title: '',
                content: '',
            },
            toasts: [],
            toastCounter: 0,

            sidebarVisible: true,
            isMobile: false,
        };
    },

    mounted() {
        this.checkMobile();
        window.addEventListener('resize', this.checkMobile);

        this.fetchNotes();
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

        async fetchNotes() {
            this.loading = true;
            try {
                const response = await axios.get('/api/notes');
                this.notes = response.data;
            } catch (error) {
                console.error('Failed to load notes:', error);
                this.showToast('Failed to load notes', 'bg-danger');
            } finally {
                this.loading = false;
            }
        },

        openCreateModal() {
            this.editingNote = null;
            this.resetForm();
        },

        openEditModal(note) {
            this.editingNote = note;
            this.form = {
                title: note.title,
                content: note.content || '',
            };
        },

        async createNote() {
            if (!this.form.title.trim()) return;

            this.saving = true;
            this.errors = {};

            try {
                const response = await axios.post('/api/notes', this.form);
                this.notes.unshift(response.data);
                this.resetForm();
                bootstrap.Modal.getInstance(document.getElementById('noteModal')).hide();
                this.showToast('Note created successfully!', 'bg-success');
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                } else {
                    this.showToast('Failed to create note', 'bg-danger');
                }
            } finally {
                this.saving = false;
            }
        },

        async updateNote() {
            this.saving = true;
            this.errors = {};

            try {
                const response = await axios.put(`/api/notes/${this.editingNote.note_id}`, this.form);
                const index = this.notes.findIndex(n => n.note_id === this.editingNote.note_id);
                if (index !== -1) this.notes[index] = response.data;

                this.resetForm();
                bootstrap.Modal.getInstance(document.getElementById('noteModal')).hide();
                this.showToast('Note updated successfully!', 'bg-success');
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors || {};
                } else {
                    this.showToast('Failed to update note', 'bg-danger');
                }
            } finally {
                this.saving = false;
            }
        },

        setDeleteCandidate(noteId, title) {
            this.deleteCandidateId = noteId;
            this.deleteCandidateTitle = title;
        },

        async confirmDelete() {
            this.deleting = true;
            try {
                await axios.delete(`/api/notes/${this.deleteCandidateId}`);
                this.notes = this.notes.filter(n => n.note_id !== this.deleteCandidateId);
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                this.showToast('Note deleted permanently', 'bg-danger');
            } catch (error) {
                this.showToast('Failed to delete note', 'bg-danger');
            } finally {
                this.deleting = false;
            }
        },

        resetForm() {
            this.editingNote = null;
            this.form = { title: '', content: '' };
            this.errors = {};
        },

        showToast(message, type = 'bg-success') {
            const id = ++this.toastCounter;
            this.toasts.push({ id, message, type });

            this.$nextTick(() => {
                const toastEl = document.querySelector(`.toast-container .toast:nth-last-child(1)`);
                if (toastEl) {
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();

                    toastEl.addEventListener('hidden.bs.toast', () => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    });
                }
            });
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        },

        truncate(text, length) {
            return text.length > length ? text.substring(0, length) + '...' : text;
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

.add-btn {
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
}

.add-btn:hover {
    background-color: #222;
    color: #F2F0EC;
    transform: scale(1.05);
}


</style>
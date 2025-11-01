class TodoApp {
    constructor() {
        // Inisialisasi data aplikasi
        this.tasks = [];
        this.categories = [];
        this.currentFilter = 'all';
        this.currentCategory = '';
        this.searchQuery = '';
        this.currentPage = 1;
        this.tasksPerPage = 10;
        
        // Cleanup tracking untuk memory leaks
        this.eventListeners = [];
        this.keyboardHandler = null;
        
        // Config untuk file upload (akan di-load dari API)
        this.fileUploadConfig = null;
        
        this.init();
    }

    async init() {
        this.setupEventListeners();
        this.setupKeyboardShortcuts();
        this.setupCSRFToken();
        this.loadCategories();
        this.loadTasks();
    }

    setupKeyboardShortcuts() {
        if (this.keyboardHandler) {
            document.removeEventListener('keydown', this.keyboardHandler);
        }
        
        // Create handler that can be cleaned up later
        this.keyboardHandler = (e) => {
            // Ctrl/Cmd + Enter untuk submit form yang aktif
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                const activeForm = document.querySelector('form:not(.hidden)');
                if (activeForm) {
                    activeForm.dispatchEvent(new Event('submit'));
                }
            }
            
            // Escape untuk close modal
            if (e.key === 'Escape') {
                e.preventDefault();
                this.closeAllModals();
                this.closeQuickAddModal();
            }
            
            // Ctrl/Cmd + N untuk new task (jika tidak ada modal terbuka)
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                if (!this.isAnyModalOpen()) {
                    e.preventDefault();
                    document.getElementById('taskTitle').focus();
                }
            }
            
            // F5 atau Ctrl/Cmd + R untuk refresh data
            if (e.key === 'F5' || ((e.ctrlKey || e.metaKey) && e.key === 'r')) {
                if (!this.isAnyModalOpen()) {
                    e.preventDefault();
                    this.loadTasks();
                    this.showAlert('🔄 Data berhasil diperbarui!', 'success');
                }
            }
        };
        
        document.addEventListener('keydown', this.keyboardHandler);
    }
    
    // Helper methods untuk keyboard shortcuts
    closeAllModals() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('uploadModal').classList.add('hidden');
        this.clearFileSelection();
    }
    
    isAnyModalOpen() {
        return !document.getElementById('editModal').classList.contains('hidden') ||
               !document.getElementById('uploadModal').classList.contains('hidden') ||
               !document.getElementById('quickAddModal').classList.contains('hidden');
    }

    setupCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.csrfToken = token.getAttribute('content');
        }
    }

    setupEventListeners() {
        // Event listener untuk form tambah tugas baru
        document.getElementById('taskForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.addTask();
        });

        // Event listener untuk fitur pencarian real-time
        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.searchQuery = e.target.value.toLowerCase();
            this.renderTasks();
        });

        // Filter buttons
        document.getElementById('filterAll').addEventListener('click', () => this.setFilter('all'));
        document.getElementById('filterActive').addEventListener('click', () => this.setFilter('active'));
        document.getElementById('filterCompleted').addEventListener('click', () => this.setFilter('completed'));

        // Category filter
        document.getElementById('categoryFilter').addEventListener('change', (e) => {
            this.currentCategory = e.target.value;
            this.renderTasks();
        });

        // Modal edit
        document.getElementById('cancelEdit').addEventListener('click', () => this.closeEditModal());
        document.getElementById('editTaskForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.updateTask();
        });

        // Close modals when clicking outside
        document.getElementById('editModal').addEventListener('click', (e) => {
            if (e.target.id === 'editModal') {
                this.closeEditModal();
            }
        });

        // Pagination
        document.getElementById('prevPage').addEventListener('click', () => this.changePage(-1));
        document.getElementById('nextPage').addEventListener('click', () => this.changePage(1));

        // Quick Add Modal
        const quickAddBtn = document.getElementById('quickAddBtn');
        const quickAddModal = document.getElementById('quickAddModal');
        const closeQuickAdd = document.getElementById('closeQuickAdd');
        const cancelQuickAdd = document.getElementById('cancelQuickAdd');
        const quickAddForm = document.getElementById('quickAddForm');

        if (quickAddBtn) {
            quickAddBtn.addEventListener('click', () => this.openQuickAddModal());
        }

        if (closeQuickAdd) {
            closeQuickAdd.addEventListener('click', () => this.closeQuickAddModal());
        }

        if (cancelQuickAdd) {
            cancelQuickAdd.addEventListener('click', () => this.closeQuickAddModal());
        }

        if (quickAddForm) {
            quickAddForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitQuickAdd();
            });
        }

        // Close modal when clicking outside
        if (quickAddModal) {
            quickAddModal.addEventListener('click', (e) => {
                if (e.target === quickAddModal) {
                    this.closeQuickAddModal();
                }
            });
        }
    }

    async makeRequest(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken || '',
                'X-Requested-With': 'XMLHttpRequest',
            },
        };

        // Handle FormData (for file uploads)
        if (options.body instanceof FormData) {
            delete defaultOptions.headers['Content-Type'];
        }

        const finalOptions = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, finalOptions);
            
            // Handle network errors
            if (!response.ok) {
                let errorMessage = 'Terjadi kesalahan';
                
                try {
                    const data = await response.json();
                    errorMessage = data.message || errorMessage;
                    
                    // Handle validation errors
                    if (data.errors) {
                        const validationErrors = Object.values(data.errors).flat();
                        errorMessage = validationErrors.join('\n');
                    }
                } catch (parseError) {
                    // If response is not JSON, use status text
                    errorMessage = `HTTP ${response.status}: ${response.statusText}`;
                }
                
                throw new Error(errorMessage);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Request error:', error);
            
            // Better error messages for common scenarios
            let userMessage = error.message;
            if (error.message.includes('NetworkError') || error.message.includes('fetch')) {
                userMessage = '❌ Koneksi internet bermasalah. Silakan coba lagi.';
            } else if (error.message.includes('500')) {
                userMessage = '❌ Terjadi kesalahan server. Silakan coba lagi.';
            } else if (error.message.includes('422')) {
                userMessage = '❌ Data yang dimasukkan tidak valid.';
            }
            
            this.showAlert(userMessage, 'error');
            throw error;
        }
    }

    async loadFileUploadConfig() {
        console.warn('File upload feature is deprecated and will be removed in future versions');
        return;
    }

    async loadCategories() {
        try {
            const response = await this.makeRequest('/api/categories');
            this.categories = response.data || [];
            this.populateCategoryOptions();
            this.updateStats();
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    populateCategoryOptions() {
        const categorySelect = document.getElementById('taskCategory');
        const editCategorySelect = document.getElementById('editTaskCategory');
        const categoryFilter = document.getElementById('categoryFilter');
        
        // Clear existing options (except first one)
        [categorySelect, editCategorySelect, categoryFilter].forEach(select => {
            if (select) {
                while (select.children.length > 1) {
                    select.removeChild(select.lastChild);
                }
            }
        });

        // Add category options
        this.categories.forEach(category => {
            [categorySelect, editCategorySelect, categoryFilter].forEach(select => {
                if (select) {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    select.appendChild(option);
                }
            });
        });
    }

    async loadTasks() {
        this.showLoading(true);
        try {
            const response = await this.makeRequest('/api/tasks');
            this.tasks = response.data || [];
            this.renderTasks();
            this.updateStats();
        } catch (error) {
            console.error('Error loading tasks:', error);
            this.showAlert('Gagal memuat tugas', 'error');
        } finally {
            this.showLoading(false);
        }
    }

    async addTask() {
        const form = document.getElementById('taskForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        const formData = new FormData(form);
        const taskData = {
            title: formData.get('title').trim(),
            description: formData.get('description').trim(),
            category_id: formData.get('category_id') || null
        };

        if (!taskData.title) {
            this.showAlert('❌ Judul tugas harus diisi', 'error');
            document.getElementById('taskTitle').focus();
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Menyimpan...';

        try {
            const response = await this.makeRequest('/api/tasks', {
                method: 'POST',
                body: JSON.stringify(taskData)
            });

            this.tasks.unshift(response.data);
            form.reset();
            this.renderTasks();
            this.updateStats();
            this.showAlert('✅ Tugas berhasil ditambahkan!', 'success');
        } catch (error) {
            console.error('Error adding task:', error);
            this.showAlert('❌ Gagal menambahkan tugas. Silakan coba lagi.', 'error');
        } finally {
            // Reset loading state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    async toggleTask(taskId) {
        try {
            const response = await this.makeRequest(`/api/tasks/${taskId}/toggle`, {
                method: 'PATCH'
            });

            const taskIndex = this.tasks.findIndex(task => task.id == taskId);
            if (taskIndex !== -1) {
                this.tasks[taskIndex] = response.data;
                this.renderTasks();
                this.updateStats();
                
                const status = response.data.completed ? 'selesai' : 'aktif';
                this.showAlert(`Tugas berhasil ditandai ${status}`, 'success');
            }
        } catch (error) {
            console.error('Error toggling task:', error);
        }
    }

    async deleteTask(taskId) {
        const task = this.tasks.find(t => t.id == taskId);
        if (!task) return;

        // Create custom confirmation modal
        const confirmed = await this.showConfirmDialog(
            '🗑️ Hapus Tugas', 
            `Apakah Anda yakin ingin menghapus tugas "${task.title}"? Tindakan ini tidak dapat dibatalkan.`
        );
        
        if (!confirmed) return;

        try {
            this.showLoading(true, 'Menghapus tugas...');
            
            await this.makeRequest(`/api/tasks/${taskId}`, {
                method: 'DELETE'
            });

            this.tasks = this.tasks.filter(task => task.id != taskId);
            this.renderTasks();
            this.updateStats();
            this.showAlert('✅ Tugas berhasil dihapus!', 'success');
        } catch (error) {
            console.error('Error deleting task:', error);
            this.showAlert('❌ Gagal menghapus tugas. Silakan coba lagi.', 'error');
        } finally {
            this.showLoading(false);
        }
    }

    openEditModal(taskId) {
        const task = this.tasks.find(t => t.id == taskId);
        if (!task) return;

        document.getElementById('editTaskId').value = task.id;
        document.getElementById('editTaskTitle').value = task.title;
        document.getElementById('editTaskDescription').value = task.description || '';
        document.getElementById('editTaskCategory').value = task.category_id || '';
        document.getElementById('editModal').classList.remove('hidden');
    }

    closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    async updateTask() {
        const taskId = document.getElementById('editTaskId').value;
        const title = document.getElementById('editTaskTitle').value.trim();
        const description = document.getElementById('editTaskDescription').value.trim();
        const categoryId = document.getElementById('editTaskCategory').value || null;
        const form = document.getElementById('editTaskForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        if (!title) {
            this.showAlert('❌ Judul tugas harus diisi', 'error');
            document.getElementById('editTaskTitle').focus();
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Menyimpan...';

        try {
            const response = await this.makeRequest(`/api/tasks/${taskId}`, {
                method: 'PUT',
                body: JSON.stringify({ 
                    title, 
                    description, 
                    category_id: categoryId 
                })
            });

            const taskIndex = this.tasks.findIndex(task => task.id == taskId);
            if (taskIndex !== -1) {
                this.tasks[taskIndex] = response.data;
                this.renderTasks();
                this.closeEditModal();
                this.showAlert('✅ Tugas berhasil diperbarui!', 'success');
            }
        } catch (error) {
            console.error('Error updating task:', error);
            this.showAlert('❌ Gagal memperbarui tugas. Silakan coba lagi.', 'error');
        } finally {
            // Reset loading state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    openUploadModal(taskId) {
        document.getElementById('uploadTaskId').value = taskId;
        document.getElementById('attachmentFile').value = '';
        this.clearFileSelection();
        document.getElementById('uploadModal').classList.remove('hidden');
        
        // Setup drag & drop functionality
        this.setupDragAndDrop();
    }

    closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        this.clearFileSelection();
    }

    async uploadAttachment() {
        const taskId = document.getElementById('uploadTaskId').value;
        const fileInput = document.getElementById('attachmentFile');
        const file = fileInput.files[0];
        const form = document.getElementById('uploadForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        if (!file) {
            this.showAlert('❌ Pilih file terlebih dahulu', 'error');
            fileInput.focus();
            return;
        }

        // Use dynamic config for validation instead of hardcoded values
        if (this.fileUploadConfig) {
            const maxSizeBytes = this.fileUploadConfig.max_size_bytes;
            if (file.size > maxSizeBytes) {
                const maxSizeMB = Math.round(this.fileUploadConfig.max_size / 1024);
                this.showAlert(`❌ Ukuran file terlalu besar. Maksimal ${maxSizeMB}MB. File Anda: ${this.formatFileSize(file.size)}`, 'error');
                return;
            }

            const allowedExtensions = this.fileUploadConfig.allowed_extensions;
            const extension = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(extension)) {
                const allowedList = allowedExtensions.join(', ').toUpperCase();
                this.showAlert(`❌ Format file tidak didukung. Gunakan: ${allowedList}`, 'error');
                return;
            }
        } else {
            // Fallback jika config belum load
            this.showAlert('❌ Konfigurasi upload belum dimuat. Silakan refresh halaman.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('attachment', file);

        // Show loading state with file info
        submitBtn.disabled = true;
        submitBtn.innerHTML = '📤 Mengupload...';

        try {
            const response = await this.makeRequest(`/api/tasks/${taskId}/upload`, {
                method: 'POST',
                body: formData
            });

            const taskIndex = this.tasks.findIndex(task => task.id == taskId);
            if (taskIndex !== -1) {
                this.tasks[taskIndex] = response.data;
                this.renderTasks();
                this.closeUploadModal();
                
                // Show success with file info
                const isImage = this.isImage(file.name);
                const iconType = isImage ? '🖼️' : '📎';
                this.showAlert(`${iconType} File "${file.name}" berhasil diupload! ${isImage ? 'Klik Preview untuk melihat gambar.' : ''}`, 'success');
            }
        } catch (error) {
            console.error('Error uploading file:', error);
            this.showAlert('❌ Gagal mengupload file. Silakan coba lagi.', 'error');
        } finally {
            // Reset loading state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    setFilter(filter) {
        this.currentFilter = filter;
        this.currentPage = 1;
        
        // Update button styles
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-500', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });
        
        const activeBtn = document.getElementById(`filter${filter.charAt(0).toUpperCase() + filter.slice(1)}`);
        if (activeBtn) {
            activeBtn.classList.add('active', 'bg-blue-500', 'text-white');
            activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
        }
        
        this.renderTasks();
    }

    getFilteredTasks() {
        let filtered = this.tasks;

        // Filter by status
        switch (this.currentFilter) {
            case 'active':
                filtered = filtered.filter(task => !task.completed);
                break;
            case 'completed':
                filtered = filtered.filter(task => task.completed);
                break;
        }

        // Filter by category
        if (this.currentCategory) {
            filtered = filtered.filter(task => task.category_id == this.currentCategory);
        }

        // Filter by search query
        if (this.searchQuery) {
            filtered = filtered.filter(task => 
                task.title.toLowerCase().includes(this.searchQuery) ||
                (task.description && task.description.toLowerCase().includes(this.searchQuery))
            );
        }

        return filtered;
    }

    changePage(direction) {
        const filteredTasks = this.getFilteredTasks();
        const totalPages = Math.ceil(filteredTasks.length / this.tasksPerPage);
        
        this.currentPage += direction;
        if (this.currentPage < 1) this.currentPage = 1;
        if (this.currentPage > totalPages) this.currentPage = totalPages;
        
        this.renderTasks();
    }

    renderTasks() {
        const tasksList = document.getElementById('tasksList');
        const emptyState = document.getElementById('emptyState');
        const pagination = document.getElementById('pagination');
        const filteredTasks = this.getFilteredTasks();

        if (filteredTasks.length === 0) {
            tasksList.innerHTML = '';
            emptyState.classList.remove('hidden');
            pagination.classList.add('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        
        // Pagination calculations
        const startIndex = (this.currentPage - 1) * this.tasksPerPage;
        const endIndex = startIndex + this.tasksPerPage;
        const paginatedTasks = filteredTasks.slice(startIndex, endIndex);
        const totalPages = Math.ceil(filteredTasks.length / this.tasksPerPage);

        // Update pagination info
        if (filteredTasks.length > this.tasksPerPage) {
            pagination.classList.remove('hidden');
            document.getElementById('showingFrom').textContent = startIndex + 1;
            document.getElementById('showingTo').textContent = Math.min(endIndex, filteredTasks.length);
            document.getElementById('totalRecords').textContent = filteredTasks.length;
            document.getElementById('currentPage').textContent = this.currentPage;
            
            document.getElementById('prevPage').disabled = this.currentPage <= 1;
            document.getElementById('nextPage').disabled = this.currentPage >= totalPages;
        } else {
            pagination.classList.add('hidden');
        }
        
        tasksList.innerHTML = paginatedTasks.map(task => this.renderTaskItem(task)).join('');
    }

    renderTaskItem(task) {
        const category = task.category ? this.categories.find(c => c.id == task.category.id) : null;
        const categoryBadge = category ? 
            `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-white shadow-soft" style="background: linear-gradient(135deg, ${category.color} 0%, ${this.adjustColor(category.color, -20)} 100%)">
                <span class="w-2 h-2 bg-white/30 rounded-full mr-2"></span>
                ${category.name}
            </span>` : '';
        
        const priorityBadge = this.getPriorityBadge(task);
        
        return `
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-soft border border-neutral-200/50 overflow-hidden hover:shadow-medium transition-all duration-300 transform hover:scale-[1.01] animate-fade-in ${task.completed ? 'opacity-75' : ''}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4 flex-1">
                            <!-- Custom Checkbox -->
                            <label class="flex items-center cursor-pointer group">
                                <div class="relative">
                                <input type="checkbox" 
                                       ${task.completed ? 'checked' : ''} 
                                       onchange="todoApp.toggleTask(${task.id})"
                                           class="sr-only peer">
                                    <div class="w-6 h-6 border-2 border-neutral-300 rounded-lg peer-checked:bg-gradient-to-r peer-checked:from-success-500 peer-checked:to-success-600 peer-checked:border-success-500 transition-all duration-300 peer-hover:scale-110 shadow-soft peer-checked:shadow-medium">
                                        <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div class="absolute inset-0 rounded-lg bg-success-400/20 scale-0 peer-checked:scale-100 transition-transform duration-300"></div>
                                </div>
                            </label>

                            <div class="flex-1 min-w-0">
                                <!-- Task Title -->
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-neutral-800 leading-tight ${task.completed ? 'line-through text-neutral-500' : ''} transition-all duration-300">
                                        ${this.escapeHtml(task.title)}
                                    </h3>
                                </div>

                                <!-- Badges Row -->
                                <div class="flex items-center gap-2 mb-3 flex-wrap">
                                ${categoryBadge}
                                    ${priorityBadge}
                                </div>

                                <!-- Description -->
                                ${task.description ? `<p class="text-neutral-600 leading-relaxed mb-4 ${task.completed ? 'line-through text-neutral-400' : ''} transition-all duration-300">${this.escapeHtml(task.description)}</p>` : ''}

                                <!-- Metadata -->
                                <div class="flex items-center justify-between text-xs text-neutral-500">
                                    <div class="flex items-center gap-4">
                                        <span class="inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            ${this.formatDate(task.created_at)}
                                        </span>
                                        ${task.updated_at !== task.created_at ? `<span class="inline-flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>${this.formatDate(task.updated_at)}</span>` : ''}
                                </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-1">
                            <button onclick="todoApp.openEditModal(${task.id})" 
                                                class="p-2 text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200 hover:shadow-soft transform hover:scale-110"
                                    title="Edit tugas">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                            </button>
                            <!-- REMOVED: Upload button (file upload feature deprecated) -->
                            <button onclick="todoApp.deleteTask(${task.id})" 
                                                class="p-2 text-danger-600 hover:bg-danger-50 rounded-lg transition-all duration-200 hover:shadow-soft transform hover:scale-110"
                                    title="Hapus tugas">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                            </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    updateStats() {
        const total = this.tasks.length;
        const completed = this.tasks.filter(task => task.completed).length;
        const active = total - completed;
        const totalCategories = this.categories.length;

        // Update statistics cards
        const totalTasksEl = document.getElementById('totalTasks');
        const activeTasksEl = document.getElementById('activeTasks');
        const completedTasksEl = document.getElementById('completedTasks');
        const totalCategoriesEl = document.getElementById('totalCategories');

        if (totalTasksEl) totalTasksEl.textContent = total;
        if (activeTasksEl) activeTasksEl.textContent = active;
        if (completedTasksEl) completedTasksEl.textContent = completed;
        if (totalCategoriesEl) totalCategoriesEl.textContent = totalCategories;

        // Update hero stats
        this.updateHeroStats();
    }

    showLoading(show) {
        const loading = document.getElementById('loading');
        if (show) {
            loading.classList.remove('hidden');
        } else {
            loading.classList.add('hidden');
        }
    }

    showAlert(message, type = 'success') {
        const alert = document.getElementById('alert');
        const alertMessage = document.getElementById('alertMessage');
        
        alertMessage.textContent = message;
        
        // Set alert color based on type
        if (type === 'error') {
            alert.querySelector('div').className = 'bg-red-500 text-white px-6 py-3 rounded-md shadow-lg';
        } else {
            alert.querySelector('div').className = 'bg-green-500 text-white px-6 py-3 rounded-md shadow-lg';
        }
        
        alert.classList.remove('hidden');
        
        // Auto hide after 4 seconds
        setTimeout(() => {
            alert.classList.add('hidden');
        }, 4000);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Method untuk mendapatkan informasi file yang diupload
    getFileInfo(filePath) {
        const fileName = filePath.split('/').pop();
        const extension = fileName.split('.').pop().toUpperCase();
        return `${fileName} (${extension})`;
    }

    // Mengecek apakah file adalah gambar untuk fitur preview
    isImage(filePath) {
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        const extension = filePath.split('.').pop().toLowerCase();
        return imageExtensions.includes(extension);
    }

    // Method untuk mendapatkan icon file berdasarkan ekstensi
    getFileIcon(filePath) {
        const extension = filePath.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': '📄',
            'doc': '📝',
            'docx': '📝', 
            'txt': '📄',
            'jpg': '🖼️',
            'jpeg': '🖼️',
            'png': '🖼️',
            'gif': '🖼️',
            'webp': '🖼️'
        };
        return iconMap[extension] || '📎';
    }

    // Method untuk format ukuran file
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // FIXED: Method untuk mendapatkan priority badge dari task.priority field
    getPriorityBadge(task) {
        // Use actual priority field from database instead of task age
        const priorityMap = {
            'urgent': {
                label: 'Mendesak',
                color: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                icon: `<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>`
            },
            'high': {
                label: 'Tinggi',
                color: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                icon: `<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>`
            },
            'medium': {
                label: 'Sedang',
                color: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                icon: `<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
                </svg>`
            },
            'low': {
                label: 'Rendah',
                color: 'bg-gray-100 text-gray-700 dark:bg-gray-600 dark:text-gray-300',
                icon: `<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>`
            }
        };

        const priority = task.priority || 'medium';
        const priorityInfo = priorityMap[priority] || priorityMap['medium'];

        return `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${priorityInfo.color}">
            ${priorityInfo.icon}
            ${priorityInfo.label}
        </span>`;
    }

    // Utility method untuk adjust color brightness
    adjustColor(color, amount) {
        // Simple color adjustment for gradients
        const usePound = color[0] === '#';
        const col = usePound ? color.slice(1) : color;

        const num = parseInt(col, 16);
        let r = (num >> 16) + amount;
        let g = (num >> 8 & 0x00FF) + amount;
        let b = (num & 0x0000FF) + amount;

        r = r > 255 ? 255 : r < 0 ? 0 : r;
        g = g > 255 ? 255 : g < 0 ? 0 : g;
        b = b > 255 ? 255 : b < 0 ? 0 : b;

        return (usePound ? '#' : '') + (r << 16 | g << 8 | b).toString(16);
    }

    // Method untuk menangani file selection dengan preview untuk gambar (dynamic config)
    handleFileSelect(file) {
        if (!file) return;

        // Check jika config sudah loaded
        if (!this.fileUploadConfig) {
            this.showAlert('❌ Konfigurasi upload belum dimuat. Silakan refresh halaman.', 'error');
            return;
        }

        // Validate file size menggunakan config dari backend
        const maxSizeBytes = this.fileUploadConfig.max_size_bytes;
        if (file.size > maxSizeBytes) {
            const maxSizeMB = Math.round(this.fileUploadConfig.max_size / 1024);
            this.showAlert(`❌ Ukuran file terlalu besar. Maksimal ${maxSizeMB}MB.`, 'error');
            return;
        }

        // Validate file type menggunakan config dari backend
        const allowedExtensions = this.fileUploadConfig.allowed_extensions;
        const extension = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(extension)) {
            const allowedList = allowedExtensions.join(', ').toUpperCase();
            this.showAlert(`❌ Format file tidak didukung. Gunakan: ${allowedList}`, 'error');
            return;
        }

        // Show file preview
        this.showFilePreview(file);
        
        // If it's an image, show thumbnail preview
        if (this.isImage(file.name)) {
            this.showImageThumbnail(file);
        }
    }

    // Method untuk menampilkan preview file
    showFilePreview(file) {
        document.getElementById('dropZoneContent').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        
        document.getElementById('fileIcon').textContent = this.getFileIcon(file.name);
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = this.formatFileSize(file.size);
        
        // Update drop zone style
        const dropZone = document.getElementById('dropZone');
        dropZone.classList.remove('border-gray-300');
        dropZone.classList.add('border-green-400', 'bg-green-50');
    }

    // Method untuk clear file selection
    clearFileSelection() {
        document.getElementById('attachmentFile').value = '';
        document.getElementById('dropZoneContent').classList.remove('hidden');
        document.getElementById('filePreview').classList.add('hidden');
        
        // Remove thumbnail preview if exists
        const thumbnailPreview = document.getElementById('thumbnailPreview');
        if (thumbnailPreview) {
            thumbnailPreview.remove();
        }
        
        // Reset drop zone style
        const dropZone = document.getElementById('dropZone');
        dropZone.classList.remove('border-green-400', 'bg-green-50');
        dropZone.classList.add('border-gray-300');
    }

    // Method untuk setup drag & drop functionality
    setupDragAndDrop() {
        const dropZone = document.getElementById('dropZone');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, this.preventDefaults, false);
            document.body.addEventListener(eventName, this.preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => this.highlightDropZone(dropZone), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => this.unhighlightDropZone(dropZone), false);
        });

        dropZone.addEventListener('drop', (e) => this.handleDrop(e), false);
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    highlightDropZone(dropZone) {
        dropZone.classList.add('border-blue-500', 'bg-blue-50');
    }

    unhighlightDropZone(dropZone) {
        dropZone.classList.remove('border-blue-500', 'bg-blue-50');
    }

    handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            const file = files[0];
            document.getElementById('attachmentFile').files = files;
            this.handleFileSelect(file);
        }
    }

    // Method untuk menampilkan thumbnail gambar sebelum upload
    showImageThumbnail(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const existingThumbnail = document.getElementById('thumbnailPreview');
            if (existingThumbnail) {
                existingThumbnail.remove();
            }

            const thumbnailDiv = document.createElement('div');
            thumbnailDiv.id = 'thumbnailPreview';
            thumbnailDiv.className = 'mt-3 p-2 border border-blue-200 rounded-lg bg-blue-50';
            thumbnailDiv.innerHTML = `
                <p class="text-xs text-gray-600 mb-2">Preview:</p>
                <img src="${e.target.result}" alt="Thumbnail" class="max-w-full max-h-24 mx-auto rounded">
            `;

            const filePreview = document.getElementById('filePreview');
            filePreview.appendChild(thumbnailDiv);
        };
        reader.readAsDataURL(file);
    }

    async downloadFile(taskId) {
        try {
            const response = await fetch(`/api/tasks/${taskId}/download`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${window.csrfToken}`,
                    'X-CSRF-TOKEN': window.csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = ''; // Filename will be set by server
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                this.showAlert('File berhasil didownload', 'success');
            } else {
                throw new Error('Download gagal');
            }
        } catch (error) {
            console.error('Error downloading file:', error);
            this.showAlert('Gagal mendownload file', 'error');
        }
    }

    previewImage(taskId, taskTitle) {
        // Create image preview modal dengan loading state
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg max-w-4xl max-h-screen p-6 m-4 overflow-auto modal">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">${this.escapeHtml(taskTitle)} - Preview</h3>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                            class="text-gray-500 hover:text-gray-700 text-xl">
                        ✕
                    </button>
                </div>
                <div class="text-center">
                    <div id="imageLoading" class="mb-4">
                        <div class="inline-flex items-center">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
                            <span class="text-gray-600">Memuat gambar...</span>
                        </div>
                    </div>
                    <img id="previewImage" src="/api/tasks/${taskId}/preview" alt="Preview" 
                         class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg hidden"
                         onload="this.classList.remove('hidden'); document.getElementById('imageLoading').classList.add('hidden')"
                         onerror="this.parentElement.innerHTML='<div class=\\'text-red-500\\'>❌ Gagal memuat gambar</div>'">
                </div>
                <div class="mt-4 text-center">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition duration-200">
                        Tutup
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Close modal on background click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });

        // Close modal on Escape key
        const escapeHandler = (e) => {
            if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', escapeHandler);
            }
        };
        document.addEventListener('keydown', escapeHandler);
    }

    // Utility method untuk escape HTML (XSS protection)
    escapeHtml(text) {
        if (typeof text !== 'string') {
            return text;
        }
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Method untuk menampilkan loading state
    showLoading(show, message = 'Memuat...') {
        let loadingEl = document.getElementById('loadingOverlay');
        
        if (show) {
            if (!loadingEl) {
                loadingEl = document.createElement('div');
                loadingEl.id = 'loadingOverlay';
                loadingEl.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
                loadingEl.innerHTML = `
                    <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                        <span class="text-gray-700">${message}</span>
                    </div>
                `;
                document.body.appendChild(loadingEl);
            }
            loadingEl.classList.remove('hidden');
        } else {
            if (loadingEl) {
                loadingEl.classList.add('hidden');
                setTimeout(() => loadingEl.remove(), 300);
            }
        }
    }

    // Method untuk menampilkan dialog konfirmasi custom
    showConfirmDialog(title, message) {
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
            modal.innerHTML = `
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">${title}</h3>
                        <p class="text-gray-600 mb-6">${message}</p>
                        <div class="flex justify-end space-x-3">
                            <button id="confirmCancel" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-200">
                                ❌ Batal
                            </button>
                            <button id="confirmOk" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-200">
                                ✅ Ya, Hapus
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            const cancelBtn = modal.querySelector('#confirmCancel');
            const okBtn = modal.querySelector('#confirmOk');
            
            const cleanup = () => {
                modal.remove();
            };
            
            cancelBtn.addEventListener('click', () => {
                cleanup();
                resolve(false);
            });
            
            okBtn.addEventListener('click', () => {
                cleanup();
                resolve(true);
            });
            
            // Close on background click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    cleanup();
                    resolve(false);
                }
            });
        });
    }

    // Method untuk menampilkan alert notification dengan modern design
    showAlert(message, type = 'success') {
        const alert = document.getElementById('alert');
        const alertMessage = document.getElementById('alertMessage');

        if (!alert || !alertMessage) return;

        alertMessage.textContent = message;

        // Update alert styling based on type
        const alertDiv = alert.querySelector('div');
        const iconDiv = alert.querySelector('.flex-shrink-0');
        
        const typeStyles = {
            'success': {
                bg: 'bg-success-100 border-success-200',
                icon: 'text-success-600',
                iconSvg: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`
            },
            'error': {
                bg: 'bg-danger-100 border-danger-200',
                icon: 'text-danger-600',
                iconSvg: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`
            },
            'warning': {
                bg: 'bg-warning-100 border-warning-200',
                icon: 'text-warning-600',
                iconSvg: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>`
            },
            'info': {
                bg: 'bg-primary-100 border-primary-200',
                icon: 'text-primary-600',
                iconSvg: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>`
            }
        };

        const style = typeStyles[type] || typeStyles.success;
        alertDiv.className = `flex items-start space-x-3 p-4 text-neutral-800 rounded-2xl shadow-large border ${style.bg} max-w-md animate-slide-up`;
        iconDiv.className = `flex-shrink-0 p-1 rounded-lg bg-white shadow-soft ${style.icon}`;
        iconDiv.innerHTML = style.iconSvg;

        alert.classList.remove('hidden');

        // Auto hide after 4 seconds
        setTimeout(() => {
            alert.classList.add('hidden');
        }, 4000);
    }

    // Method untuk update hero stats di header
    updateHeroStats() {
        const heroActiveTasks = document.getElementById('heroActiveTasks');
        const heroCompletedTasks = document.getElementById('heroCompletedTasks');

        if (heroActiveTasks && heroCompletedTasks) {
            const total = this.tasks.length;
            const completed = this.tasks.filter(task => task.completed).length;
            const active = total - completed;

            heroActiveTasks.textContent = active;
            heroCompletedTasks.textContent = completed;
        }
    }

    // Method untuk membuka quick add modal
    openQuickAddModal() {
        const modal = document.getElementById('quickAddModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.getElementById('quickTitle').focus();

            // Load categories for quick add
            this.populateQuickAddCategories();
        }
    }

    // Method untuk menutup quick add modal
    closeQuickAddModal() {
        const modal = document.getElementById('quickAddModal');
        const form = document.getElementById('quickAddForm');

        if (modal) {
            modal.classList.add('hidden');
        }

        if (form) {
            form.reset();
        }
    }

    // Method untuk populate categories di quick add modal
    populateQuickAddCategories() {
        const quickCategorySelect = document.getElementById('quickCategory');

        if (!quickCategorySelect) return;

        // Clear existing options (except first one)
        while (quickCategorySelect.children.length > 1) {
            quickCategorySelect.removeChild(quickCategorySelect.lastChild);
        }

        // Add category options
        this.categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            quickCategorySelect.appendChild(option);
        });
    }

    // Method untuk submit quick add form
    async submitQuickAdd() {
        const title = document.getElementById('quickTitle').value.trim();
        const description = document.getElementById('quickDescription').value.trim();
        const categoryId = document.getElementById('quickCategory').value || null;
        const submitBtn = document.getElementById('quickAddForm').querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        if (!title) {
            this.showAlert('❌ Judul tugas harus diisi', 'error');
            document.getElementById('quickTitle').focus();
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                <span>Menyimpan...</span>
            </div>
        `;
        
        try {
            const response = await this.makeRequest('/api/tasks', {
                method: 'POST',
                body: JSON.stringify({
                    title,
                    description,
                    category_id: categoryId
                })
            });

            this.tasks.unshift(response.data);
            this.renderTasks();
            this.updateStats();
            this.closeQuickAddModal();
            this.showAlert('✨ Tugas berhasil ditambahkan!', 'success');
        } catch (error) {
            console.error('Error adding task:', error);
            this.showAlert('❌ Gagal menambahkan tugas. Silakan coba lagi.', 'error');
        } finally {
            // Reset loading state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }
    
    /**
     * Cleanup method untuk menghindari memory leaks
     * Panggil method ini saat TodoApp tidak digunakan lagi
     */
    cleanup() {
        // Remove keyboard event listener
        if (this.keyboardHandler) {
            document.removeEventListener('keydown', this.keyboardHandler);
            this.keyboardHandler = null;
        }
        
        // Clear any intervals  
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
        
        // Remove all tracked event listeners
        this.eventListeners.forEach(({ element, event, handler }) => {
            element.removeEventListener(event, handler);
        });
        this.eventListeners = [];
        
        // Clear data arrays
        this.tasks = [];
        this.categories = [];
    }
    /**
     * ADDED: Cleanup method to prevent memory leaks
     * Call this when navigating away or destroying the app instance
     * Especially important for SPAs (Single Page Applications)
     */
    cleanup() {
        // Remove keyboard handler
        if (this.keyboardHandler) {
            document.removeEventListener('keydown', this.keyboardHandler);
            this.keyboardHandler = null;
        }
        
        // Clear data arrays to free memory
        this.tasks = [];
        this.categories = [];
        this.filteredTasks = [];
        
        // Reset state
        this.currentFilter = 'all';
        this.currentCategory = '';
        this.searchQuery = '';
        this.currentPage = 1;
        
        console.log('TodoApp cleanup completed - all event listeners removed and data cleared');
    }
}

// Inisialisasi aplikasi ketika halaman selesai dimuat
document.addEventListener('DOMContentLoaded', () => {
    window.todoApp = new TodoApp();
    
    // Optional: Auto-cleanup on page unload (for traditional multi-page apps)
    window.addEventListener('beforeunload', () => {
        if (window.todoApp && typeof window.todoApp.cleanup === 'function') {
            window.todoApp.cleanup();
        }
    });
});

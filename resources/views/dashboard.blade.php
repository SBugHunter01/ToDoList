<x-app-layout title="Dashboard - {{ config('app.name', 'TodoApp') }}">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            <i class="fas fa-tachometer-alt mr-3 text-blue-600"></i>
                            Dashboard
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Kelola tugas Anda dengan mudah dan efisien
                        </p>
                    </div>
                    <div class="mt-4 lg:mt-0">
                        <button id="addTaskBtn"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Tugas
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="statsContainer">
                <!-- Stats will be loaded via AJAX -->
            </div>

            <!-- Progress Tracking & Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Progress Overview -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-chart-line mr-2 text-primary-600"></i>
                            Progress Overview
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="progressChart" class="space-y-4">
                            <!-- Progress chart will be loaded via AJAX -->
                        </div>
                    </div>
                </div>

                <!-- Task Distribution -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-chart-pie mr-2 text-secondary-600"></i>
                            Task Distribution
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="distributionChart" class="space-y-4">
                            <!-- Distribution chart will be loaded via AJAX -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Tasks List -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700 space-y-4">
                            <!-- Header Row -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    <i class="fas fa-tasks mr-2 text-blue-600"></i>
                                    Daftar Tugas
                                </h2>
                            </div>

                            <!-- Filters Row - Mobile First -->
                            <div class="space-y-3">
                                <!-- Search Bar (Full Width on Mobile) -->
                                <div class="relative w-full sm:w-auto">
                                    <input type="text"
                                           id="searchInput"
                                           placeholder="Cari tugas..."
                                           class="w-full px-3 py-2 pl-9 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>

                                <!-- Filter Controls Grid -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-2">
                                    <!-- Status Filter -->
                                    <select id="filterStatus" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <option value="">Semua Status</option>
                                        <option value="pending">Menunggu</option>
                                        <option value="in_progress">Dalam Proses</option>
                                        <option value="completed">Selesai</option>
                                        <option value="cancelled">Dibatalkan</option>
                                    </select>

                                    <!-- Priority Filter -->
                                    <select id="filterPriority" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <option value="">Semua Prioritas</option>
                                        <option value="low">Rendah</option>
                                        <option value="medium">Sedang</option>
                                        <option value="high">Tinggi</option>
                                        <option value="urgent">Mendesak</option>
                                    </select>

                                    <!-- Category Filter -->
                                    <select id="filterCategory" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <option value="">Semua Kategori</option>
                                    </select>

                                    <!-- Sort By -->
                                    <select id="sortBy" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <option value="created_at">Terbaru</option>
                                        <option value="updated_at">Update Terakhir</option>
                                        <option value="priority">Prioritas</option>
                                        <option value="status">Status</option>
                                        <option value="category">Kategori</option>
                                    </select>

                                    <!-- Sort Direction -->
                                    <select id="sortDirection" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                        <option value="desc">⬇ Descending</option>
                                        <option value="asc">⬆ Ascending</option>
                                    </select>
                                </div>

                                <!-- Clear Filters Button (Full Width on Mobile) -->
                                <button id="clearFiltersBtn"
                                        class="w-full sm:w-auto px-4 py-2 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium transition-all hover:shadow-md">
                                    <i class="fas fa-times mr-2"></i>Reset Semua Filter
                                </button>
                            </div>
                        </div>

                        <!-- Tasks Container -->
                        <div id="tasksContainer" class="max-h-96 lg:max-h-[600px] xl:max-h-[700px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent hover:scrollbar-thumb-gray-400 dark:hover:scrollbar-thumb-gray-500 transition-colors">
                            <!-- Tasks will be loaded via AJAX -->
                            <div class="text-center py-12">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                                <p class="mt-4 text-gray-600 dark:text-gray-400">Memuat tugas...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Stats -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-chart-pie mr-2 text-green-600"></i>
                            Ringkasan Hari Ini
                        </h3>
                        <div id="quickStats">
                            <!-- Quick stats will be loaded via AJAX -->
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-tags mr-2 text-purple-600"></i>
                            Kategori
                        </h3>
                        <div id="categoriesList" class="space-y-2">
                            <!-- Categories will be loaded via AJAX -->
                        </div>
                        <button id="addCategoryBtn" class="mt-4 w-full px-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambah Kategori
                        </button>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-history mr-2 text-orange-600"></i>
                            Aktivitas Terbaru
                        </h3>
                        <div id="recentActivity" class="space-y-3">
                            <!-- Recent activity will be loaded via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Task Modal -->
    <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 id="taskModalTitle" class="text-xl font-semibold text-gray-900 dark:text-white">
                            Tambah Tugas Baru
                        </h3>
                        <button id="closeTaskModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <form id="taskForm" class="p-6 space-y-6">
                    @csrf
                    <input type="hidden" id="taskId" name="task_id">

                        <!-- Title -->
                        <div>
                            <label for="taskTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-heading mr-2 text-blue-600"></i>Judul Tugas
                            </label>
                            <input type="text"
                                   id="taskTitle"
                                   name="title"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                   placeholder="Masukkan judul tugas"
                                   required>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="taskStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-info-circle mr-2 text-green-600"></i>Status
                            </label>
                            <select id="taskStatus"
                                    name="status"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="pending">Menunggu</option>
                                <option value="in_progress">Dalam Proses</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="taskPriority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-exclamation-triangle mr-2 text-orange-600"></i>Prioritas
                            </label>
                            <select id="taskPriority"
                                    name="priority"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="low">Rendah</option>
                                <option value="medium">Sedang</option>
                                <option value="high">Tinggi</option>
                                <option value="urgent">Mendesak</option>
                            </select>
                        </div>

                    <!-- Description -->
                    <div>
                        <label for="taskDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-align-left mr-2 text-green-600"></i>Deskripsi (Opsional)
                        </label>
                        <textarea id="taskDescription"
                                  name="description"
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                  placeholder="Masukkan deskripsi tugas"></textarea>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="taskCategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-tag mr-2 text-purple-600"></i>Kategori
                        </label>
                        <select id="taskCategory"
                                name="category_id"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Pilih kategori</option>
                        </select>
                    </div>


                    <!-- Buttons -->
                    <div class="flex space-x-4 pt-4">
                        <button type="button"
                                id="cancelTaskBtn"
                                class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                id="saveTaskBtn"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Tambah Kategori Baru
                        </h3>
                        <button id="closeCategoryModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <form id="categoryForm" class="p-6 space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="categoryName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-tag mr-2 text-blue-600"></i>Nama Kategori
                        </label>
                        <input type="text"
                               id="categoryName"
                               name="name"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="Masukkan nama kategori"
                               required>
                    </div>

                    <!-- Color -->
                    <div>
                        <label for="categoryColor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-palette mr-2 text-purple-600"></i>Warna
                        </label>
                        <div class="flex space-x-2">
                            <input type="color"
                                   id="categoryColor"
                                   name="color"
                                   value="#3B82F6"
                                   class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded-lg">
                            <input type="text"
                                   id="categoryColorText"
                                   class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                   placeholder="#3B82F6"
                                   readonly>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="categoryDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-align-left mr-2 text-green-600"></i>Deskripsi (Opsional)
                        </label>
                        <textarea id="categoryDescription"
                                  name="description"
                                  rows="2"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                  placeholder="Masukkan deskripsi kategori"></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-4 pt-4">
                        <button type="button"
                                id="cancelCategoryBtn"
                                class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                id="saveCategoryBtn"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        Konfirmasi Hapus
                    </h3>
                </div>

                <div class="p-6">
                    <p class="text-gray-700 dark:text-gray-300 mb-6">
                        Apakah Anda yakin ingin menghapus <span id="deleteItemName" class="font-semibold"></span>?
                        Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="flex space-x-4">
                        <button type="button"
                                id="cancelDeleteBtn"
                                class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Batal
                        </button>
                        <button type="button"
                                id="confirmDeleteBtn"
                                class="flex-1 px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-white dark:bg-gray-800 border-l-4 border-blue-500 rounded-lg shadow-lg p-4 max-w-sm">
            <div class="flex items-center">
                <i id="notificationIcon" class="fas fa-check-circle text-blue-500 mr-3"></i>
                <p id="notificationMessage" class="text-gray-900 dark:text-white"></p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Initialize dashboard when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboard();
            setupEventListeners();
        });

        // Load dashboard data
        function loadDashboard() {
            loadStats();
            loadTasks();
            loadCategories();
            loadQuickStats();
            loadRecentActivity();
        }

        // Setup event listeners
        // Utility function for debouncing
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

    // Clear all filters and reset sorting
    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterPriority').value = '';
        document.getElementById('filterCategory').value = '';
        document.getElementById('sortBy').value = 'created_at';
        document.getElementById('sortDirection').value = 'desc';
        loadTasks();
    }

        function setupEventListeners() {
            // Add task button
            document.getElementById('addTaskBtn').addEventListener('click', () => openTaskModal());

            // Task modal
            document.getElementById('closeTaskModal').addEventListener('click', () => closeTaskModal());
            document.getElementById('cancelTaskBtn').addEventListener('click', () => closeTaskModal());
            document.getElementById('taskForm').addEventListener('submit', handleTaskSubmit);

            // Category modal
            document.getElementById('addCategoryBtn').addEventListener('click', () => openCategoryModal());
            document.getElementById('closeCategoryModal').addEventListener('click', () => closeCategoryModal());
            document.getElementById('cancelCategoryBtn').addEventListener('click', () => closeCategoryModal());
            document.getElementById('categoryForm').addEventListener('submit', handleCategorySubmit);

            // Color picker
            document.getElementById('categoryColor').addEventListener('input', function() {
                document.getElementById('categoryColorText').value = this.value;
            });

        // Filter and sort event listeners
        document.getElementById('searchInput').addEventListener('input', debounce(loadTasks, 300));
        document.getElementById('filterStatus').addEventListener('change', loadTasks);
        document.getElementById('filterPriority').addEventListener('change', loadTasks);
        document.getElementById('filterCategory').addEventListener('change', loadTasks);
        document.getElementById('sortBy').addEventListener('change', loadTasks);
        document.getElementById('sortDirection').addEventListener('change', loadTasks);
        document.getElementById('clearFiltersBtn').addEventListener('click', clearFilters);

            // Delete modal
            document.getElementById('cancelDeleteBtn').addEventListener('click', () => closeDeleteModal());
            document.getElementById('confirmDeleteBtn').addEventListener('click', () => confirmDelete());

            // Dark mode listener
            document.addEventListener('dark-mode-toggle', function(event) {
                // Reload data to apply dark mode classes
                setTimeout(() => {
                    loadDashboard();
                }, 100);
            });
        }

        // Task functions
        function openTaskModal(task = null) {
            const modal = document.getElementById('taskModal');
            const form = document.getElementById('taskForm');
            const title = document.getElementById('taskModalTitle');

                if (task) {
                    title.textContent = 'Edit Tugas';
                    document.getElementById('taskId').value = task.id;
                    document.getElementById('taskTitle').value = task.title;
                    document.getElementById('taskDescription').value = task.description || '';
                    document.getElementById('taskStatus').value = task.status || 'pending';
                    document.getElementById('taskPriority').value = task.priority || 'medium';
                    document.getElementById('taskCategory').value = task.category_id || '';
                } else {
                    title.textContent = 'Tambah Tugas Baru';
                    form.reset();
                    document.getElementById('taskId').value = '';
                    document.getElementById('taskStatus').value = 'pending';
                    document.getElementById('taskPriority').value = 'medium';
                }

            loadCategoriesForSelect();
            modal.classList.remove('hidden');
        }

        function closeTaskModal() {
            document.getElementById('taskModal').classList.add('hidden');
        }

        // Utility function for button state management
        function setButtonLoading(button, loading = true, text = null) {
            if (loading) {
                button.disabled = true;
                button.dataset.originalText = button.innerHTML;
                button.innerHTML = text || '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            } else {
                button.disabled = false;
                button.innerHTML = button.dataset.originalText || button.innerHTML;
                delete button.dataset.originalText;
            }
        }

        // Utility functions for task HTML generation
        function createTaskPriorityStripe(task) {
            const priorityInfo = getPriorityInfo(task.priority);
            return `<div class="h-1 w-full ${priorityInfo.stripe_class} rounded-t-2xl"></div>`;
        }

        function createTaskBadges(task) {
            const statusInfo = getStatusInfo(task.status);
            const priorityInfo = getPriorityInfo(task.priority);

            return `
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="badge ${statusInfo.badge_class}">${statusInfo.label}</span>
                    <span class="badge ${priorityInfo.badge_class}">${priorityInfo.label}</span>
                    ${task.category ? `<span class="badge badge-info" style="background-color: ${task.category.color}15; color: ${task.category.color};">${task.category.name}</span>` : ''}
                </div>
            `;
        }

        function createTaskContent(task) {
            const isCompleted = task.status === 'completed';
            const completedClass = isCompleted ? 'line-through opacity-60' : '';

            return `
                <div class="mb-3">
                    <h3 class="font-semibold text-lg ${completedClass} text-gray-900 dark:text-white">${task.title}</h3>
                    ${task.description ? `<p class="text-gray-600 dark:text-gray-400 mt-1 ${completedClass}">${task.description}</p>` : ''}
                </div>
            `;
        }

        function createTaskActions(task) {
            const statusInfo = getStatusInfo(task.status);
            const canAdvance = statusInfo.can_advance;
            const canToggle = statusInfo.can_toggle;

            return `
                <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex items-center space-x-2">
                        <button onclick="toggleTask(${task.id})"
                                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors ${canToggle ? '' : 'opacity-50 cursor-not-allowed'}"
                                ${!canToggle ? 'disabled' : ''}
                                title="${canToggle ? 'Toggle completion status' : 'Cannot toggle this status'}">
                            <i class="fas ${task.status === 'completed' ? 'fa-undo' : 'fa-check'} text-green-600"></i>
                        </button>
                        ${canAdvance ? `
                            <button onclick="advanceTaskStatus(${task.id})"
                                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                    title="Advance to next status">
                                <i class="fas fa-arrow-right text-blue-600"></i>
                            </button>
                        ` : ''}
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="openEditModal(${task.id})"
                                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                title="Edit task">
                            <i class="fas fa-edit text-blue-600"></i>
                        </button>
                        <button onclick="deleteTask(${task.id}, '${task.title.replace(/'/g, "\\'")}')"
                                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                title="Delete task">
                            <i class="fas fa-trash text-red-600"></i>
                        </button>
                    </div>
                </div>
            `;
        }

        async function handleTaskSubmit(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const taskId = formData.get('task_id');
            const isEdit = !!taskId;

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            setButtonLoading(submitBtn, true);

            try {
                const url = isEdit ? `/tasks/${taskId}` : '/tasks';
                const method = isEdit ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    },
                    body: formData
                });

                let result;
                try {
                    result = await response.json();
                } catch (parseError) {
                    throw new Error('Server response tidak valid');
                }

                if (response.ok && result.success) {
                    showNotification(isEdit ? 'Tugas berhasil diperbarui' : 'Tugas berhasil dibuat', 'success');
                    closeTaskModal();
                    await Promise.all([loadTasks(), loadStats(), loadQuickStats()]);
                } else {
                    // Handle validation errors
                    if (result.errors && typeof result.errors === 'object') {
                        const errorMessages = Object.values(result.errors).flat();
                        showNotification('Error Validasi: ' + errorMessages.join(', '), 'error');
                    } else {
                        showNotification(result.message || 'Terjadi kesalahan saat menyimpan tugas', 'error');
                    }
                }
            } catch (error) {
                console.error('Task submit error:', error);
                if (error.name === 'TypeError' && error.message.includes('fetch')) {
                    showNotification('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.', 'error');
                } else {
                    showNotification(error.message || 'Terjadi kesalahan jaringan', 'error');
                }
            } finally {
                // Always reset button state, regardless of error type
                setButtonLoading(submitBtn, false);
            }
        }

        // Category functions
        function openCategoryModal() {
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }

        async function handleCategorySubmit(e) {
            e.preventDefault();

            const formData = new FormData(e.target);

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

            try {
                const response = await fetch('/categories', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    },
                    body: formData
                });

                let result;
                try {
                    result = await response.json();
                } catch (parseError) {
                    throw new Error('Server response tidak valid');
                }

                if (response.ok && result.success) {
                    showNotification('Kategori berhasil ditambahkan', 'success');
                    closeCategoryModal();
                    loadCategories();
                    loadTasks(); // Refresh tasks to show new category option
                } else {
                    // Handle validation errors
                    if (result.errors && typeof result.errors === 'object') {
                        const errorMessages = Object.values(result.errors).flat();
                        showNotification('Error Validasi: ' + errorMessages.join(', '), 'error');
                    } else {
                        showNotification(result.message || 'Terjadi kesalahan saat menambahkan kategori', 'error');
                    }
                }
            } catch (error) {
                console.error('Category submit error:', error);
                if (error.name === 'TypeError' && error.message.includes('fetch')) {
                    showNotification('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.', 'error');
                } else {
                    showNotification(error.message || 'Terjadi kesalahan jaringan', 'error');
                }
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        // Delete functions
        let deleteCallback = null;

        function openDeleteModal(itemName, callback) {
            document.getElementById('deleteItemName').textContent = itemName;
            deleteCallback = callback;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            deleteCallback = null;
        }

        async function confirmDelete() {
            if (deleteCallback) {
                await deleteCallback();
            }
            closeDeleteModal();
        }

        // Load functions
        async function loadStats() {
            try {
                // Calculate stats from tasks data
                const response = await fetch('/tasks');
                const result = await response.json();

                if (result.success) {
                    const tasks = result.data;
                    const total = tasks.length;
                    const completed = tasks.filter(t => t.completed).length;
                    const pending = total - completed;

                    const statsHtml = `
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100">Total Tugas</p>
                                    <p class="text-2xl font-bold">${total}</p>
                                </div>
                                <i class="fas fa-tasks text-3xl opacity-80"></i>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100">Selesai</p>
                                    <p class="text-2xl font-bold">${completed}</p>
                                </div>
                                <i class="fas fa-check-circle text-3xl opacity-80"></i>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-orange-100">Belum Selesai</p>
                                    <p class="text-2xl font-bold">${pending}</p>
                                </div>
                                <i class="fas fa-clock text-3xl opacity-80"></i>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100">Persentase</p>
                                    <p class="text-2xl font-bold">${total > 0 ? Math.round((completed / total) * 100) : 0}%</p>
                                </div>
                                <i class="fas fa-percentage text-3xl opacity-80"></i>
                            </div>
                        </div>
                    `;

                    document.getElementById('statsContainer').innerHTML = statsHtml;
                    updateProgressCharts(tasks);
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        function updateProgressCharts(tasks) {
            const total = tasks.length;
            const completed = tasks.filter(t => t.completed).length;
            const pending = tasks.filter(t => t.status === 'pending').length;
            const inProgress = tasks.filter(t => t.status === 'in_progress').length;
            const cancelled = tasks.filter(t => t.status === 'cancelled').length;
            const completionRate = total > 0 ? Math.round((completed / total) * 100) : 0;

            // Priority distribution
            const priorities = {
                low: tasks.filter(t => t.priority === 'low').length,
                medium: tasks.filter(t => t.priority === 'medium').length,
                high: tasks.filter(t => t.priority === 'high').length,
                urgent: tasks.filter(t => t.priority === 'urgent').length,
            };

            // Progress Overview Chart
            const progressHtml = `
                <div class="space-y-4">
                    <!-- Completion Progress Bar -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Completion Progress</span>
                            <span class="font-semibold text-gray-900 dark:text-white">${completionRate}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-success-500 to-success-600 h-3 rounded-full transition-all duration-1000 ease-out"
                                 style="width: ${completionRate}%"></div>
                        </div>
                    </div>

                    <!-- Priority Distribution -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Priority Distribution</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-danger-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Urgent</span>
                                </div>
                                <span class="text-sm font-semibold">${priorities.urgent}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-warning-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">High</span>
                                </div>
                                <span class="text-sm font-semibold">${priorities.high}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-info-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Medium</span>
                                </div>
                                <span class="text-sm font-semibold">${priorities.medium}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-secondary-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Low</span>
                                </div>
                                <span class="text-sm font-semibold">${priorities.low}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Task Distribution Chart
            const distributionHtml = `
                <div class="space-y-4">
                    <!-- Status Distribution -->
                    <div class="space-y-3">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Status Distribution</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-secondary-50 dark:bg-secondary-900/20 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 bg-secondary-500 rounded-full"></div>
                                    <span class="text-sm font-medium">Pending</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold">${pending}</span>
                                    <div class="w-16 bg-secondary-200 dark:bg-secondary-700 rounded-full h-2">
                                        <div class="bg-secondary-500 h-2 rounded-full" style="width: ${total > 0 ? (pending / total) * 100 : 0}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-info-50 dark:bg-info-900/20 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 bg-info-500 rounded-full"></div>
                                    <span class="text-sm font-medium">In Progress</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold">${inProgress}</span>
                                    <div class="w-16 bg-info-200 dark:bg-info-700 rounded-full h-2">
                                        <div class="bg-info-500 h-2 rounded-full" style="width: ${total > 0 ? (inProgress / total) * 100 : 0}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-success-50 dark:bg-success-900/20 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 bg-success-500 rounded-full"></div>
                                    <span class="text-sm font-medium">Completed</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold">${completed}</span>
                                    <div class="w-16 bg-success-200 dark:bg-success-700 rounded-full h-2">
                                        <div class="bg-success-500 h-2 rounded-full" style="width: ${total > 0 ? (completed / total) * 100 : 0}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-danger-50 dark:bg-danger-900/20 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 bg-danger-500 rounded-full"></div>
                                    <span class="text-sm font-medium">Cancelled</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold">${cancelled}</span>
                                    <div class="w-16 bg-danger-200 dark:bg-danger-700 rounded-full h-2">
                                        <div class="bg-danger-500 h-2 rounded-full" style="width: ${total > 0 ? (cancelled / total) * 100 : 0}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Stats -->
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="p-3 bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-lg">
                                <div class="text-2xl font-bold text-primary-600">${total}</div>
                                <div class="text-xs text-primary-600/80">Total Tasks</div>
                            </div>
                            <div class="p-3 bg-gradient-to-br from-success-50 to-success-100 dark:from-success-900/20 dark:to-success-800/20 rounded-lg">
                                <div class="text-2xl font-bold text-success-600">${completionRate}%</div>
                                <div class="text-xs text-success-600/80">Completed</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('progressChart').innerHTML = progressHtml;
            document.getElementById('distributionChart').innerHTML = distributionHtml;
        }

        async function loadTasks(page = 1) {
            try {
                const searchQuery = document.getElementById('searchInput').value.trim();
                const statusFilter = document.getElementById('filterStatus').value;
                const priorityFilter = document.getElementById('filterPriority').value;
                const categoryFilter = document.getElementById('filterCategory').value;
                const sortBy = document.getElementById('sortBy').value;
                const sortDirection = document.getElementById('sortDirection').value;

                let url = '/tasks';
                const params = new URLSearchParams();

                // Add pagination
                params.append('page', page);
                params.append('per_page', 20); // Load 20 items per page

                // Add sorting
                params.append('sort_by', sortBy);
                params.append('sort_direction', sortDirection);

                if (searchQuery) {
                    params.append('search', searchQuery);
                }
                if (statusFilter) {
                    params.append('status', statusFilter);
                }
                if (priorityFilter) {
                    params.append('priority', priorityFilter);
                }
                if (categoryFilter) {
                    params.append('category_id', categoryFilter);
                }

                const response = await fetch(url + '?' + params.toString());
                const result = await response.json();

                if (result.success) {
                    const tasksHtml = result.data.length > 0
                        ? '<div class="p-6 space-y-4">' + result.data.map(task => createTaskHtml(task)).join('') + '</div>'
                        : '<div class="text-center py-12"><i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i><p class="text-gray-500 dark:text-gray-400">Belum ada tugas</p></div>';

                    document.getElementById('tasksContainer').innerHTML = tasksHtml;

                    // Add pagination controls if needed
                    const meta = result.meta;
                    if (meta.last_page > 1) {
                        const paginationHtml = createPaginationHtml(meta);
                        document.getElementById('tasksContainer').insertAdjacentHTML('afterend', paginationHtml);
                    }
                }
            } catch (error) {
                console.error('Error loading tasks:', error);
                document.getElementById('tasksContainer').innerHTML =
                    '<div class="p-6"><div class="text-center py-12"><i class="fas fa-exclamation-triangle text-6xl text-red-300 dark:text-red-600 mb-4"></i><p class="text-red-500 dark:text-red-400">Gagal memuat tugas</p></div></div>';
            }
        }

        function createPaginationHtml(meta) {
            const { current_page, last_page, total } = meta;
            let html = '<div class="flex items-center justify-between mt-6 px-6">';

            // Previous button
            if (current_page > 1) {
                html += `<button onclick="loadTasks(${current_page - 1})" class="btn-secondary btn-sm">
                    <i class="fas fa-chevron-left mr-2"></i>Previous
                </button>`;
            } else {
                html += '<div></div>'; // Spacer
            }

            // Page info
            html += `<div class="text-sm text-gray-600 dark:text-gray-400">
                Page ${current_page} of ${last_page} (${total} total tasks)
            </div>`;

            // Next button
            if (current_page < last_page) {
                html += `<button onclick="loadTasks(${current_page + 1})" class="btn-secondary btn-sm">
                    Next<i class="fas fa-chevron-right ml-2"></i>
                </button>`;
            } else {
                html += '<div></div>'; // Spacer
            }

            html += '</div>';
            return html;
        }

            function createTaskHtml(task) {
                const statusInfo = getStatusInfo(task.status);
                const priorityInfo = getPriorityInfo(task.priority);
                const completedClass = task.completed ? 'line-through opacity-60' : '';
                const isCompleted = task.completed;

                // Enhanced category badge with modern styling
                const categoryBadge = task.category
                    ? `<span class="badge" style="background-color: ${task.category.color}15; color: ${task.category.color}; border: 1px solid ${task.category.color}30;">
                           <i class="fas fa-tag mr-1 text-xs"></i>${task.category.name}
                       </span>`
                    : '';

                // Priority indicator with enhanced styling
                const priorityIndicator = `<span class="badge ${priorityInfo.badge_class}">
                    ${priorityInfo.icon} ${priorityInfo.label}
                </span>`;

                // Status indicator with modern design
                const statusIndicator = `<span class="badge ${statusInfo.badge_class}">
                    ${statusInfo.icon} ${statusInfo.label}
                </span>`;

                return `
                    <div class="card card-hover animate-fade-in-up group relative overflow-hidden ${isCompleted ? 'opacity-75' : ''}" style="animation-delay: ${Math.random() * 0.3}s;">
                        <!-- Subtle background gradient for completed tasks -->
                        ${isCompleted ? '<div class="absolute inset-0 bg-gradient-to-r from-success-50 to-success-100 dark:from-success-900/10 dark:to-success-800/10 opacity-50"></div>' : ''}

                        <div class="relative">
                            <!-- Priority indicator stripe -->
                            <div class="absolute top-0 left-0 w-full h-1 ${priorityInfo.stripe_class}"></div>

                            <div class="p-6">
                                <!-- Header with status and priority -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-2 flex-wrap gap-2">
                                        ${statusIndicator}
                                        ${priorityIndicator}
                                        ${categoryBadge}
                                    </div>

                                    <!-- Action buttons -->
                                    <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
                                        <button onclick="openTaskModal(${JSON.stringify(task).replace(/"/g, '&quot;')})"
                                                class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-200 transform hover:scale-110"
                                                title="Edit Task">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button onclick="deleteTask(${task.id}, '${task.title.replace(/'/g, "\\'")}')"
                                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200 transform hover:scale-110"
                                                title="Delete Task">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Task Content -->
                                <div class="space-y-3">
                                    <!-- Title with click to edit -->
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white ${completedClass} cursor-pointer hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200 leading-tight"
                                        onclick="openTaskModal(${JSON.stringify(task).replace(/"/g, '&quot;')})">
                                        ${task.title}
                                    </h3>

                                    <!-- Description -->
                                    ${task.description ? `
                                        <p class="text-gray-600 dark:text-gray-300 ${completedClass} leading-relaxed text-sm">
                                            ${task.description}
                                        </p>
                                    ` : ''}

                                    <!-- Task Metadata -->
                                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                ${new Date(task.created_at).toLocaleDateString('id-ID', {
                                                    day: 'numeric',
                                                    month: 'short',
                                                    year: 'numeric'
                                                })}
                                            </span>
                                            ${task.updated_at !== task.created_at ? `
                                                <span class="flex items-center">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Updated ${new Date(task.updated_at).toLocaleDateString('id-ID', {
                                                        day: 'numeric',
                                                        month: 'short'
                                                    })}
                                                </span>
                                            ` : ''}
                                        </div>

                                        <!-- Completion Toggle Button -->
                                        <button onclick="toggleTask(${task.id})"
                                                class="relative w-8 h-8 rounded-full ${statusInfo.button_class} flex items-center justify-center transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2 ${statusInfo.focus_ring} group/btn"
                                                title="${statusInfo.next_label}">
                                            <i class="fas ${statusInfo.icon_class} text-sm transition-all duration-200 ${statusInfo.icon_animation}"></i>

                                            <!-- Ripple effect -->
                                            <span class="absolute inset-0 rounded-full ${statusInfo.ripple_class} opacity-0 group-hover/btn:opacity-20 transition-opacity duration-300"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Hover effect overlay -->
                            <div class="absolute inset-0 bg-gradient-to-r from-primary-500/5 to-primary-600/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        </div>
                    </div>
                `;
            }

            function getStatusInfo(status) {
                const statusMap = {
                    'pending': {
                        label: 'Menunggu',
                        icon: '<i class="fas fa-clock mr-1"></i>',
                        badge_class: 'badge-secondary',
                        button_class: 'bg-secondary-100 hover:bg-secondary-200 border border-secondary-300 dark:bg-secondary-800 dark:border-secondary-600 dark:hover:bg-secondary-700',
                        focus_ring: 'focus:ring-secondary-500',
                        icon_class: 'fa-clock text-secondary-600 dark:text-secondary-400',
                        icon_animation: '',
                        ripple_class: 'bg-secondary-500',
                        next_label: 'Mulai Kerja'
                    },
                    'in_progress': {
                        label: 'Dalam Proses',
                        icon: '<i class="fas fa-play-circle mr-1"></i>',
                        badge_class: 'badge-info',
                        button_class: 'bg-info-100 hover:bg-info-200 border border-info-300 dark:bg-info-900/30 dark:border-info-700 dark:hover:bg-info-800/50',
                        focus_ring: 'focus:ring-info-500',
                        icon_class: 'fa-play-circle text-info-600 dark:text-info-400',
                        icon_animation: 'animate-pulse',
                        ripple_class: 'bg-info-500',
                        next_label: 'Selesai'
                    },
                    'completed': {
                        label: 'Selesai',
                        icon: '<i class="fas fa-check-circle mr-1"></i>',
                        badge_class: 'badge-success',
                        button_class: 'bg-success-100 hover:bg-success-200 border border-success-300 dark:bg-success-900/30 dark:border-success-700 dark:hover:bg-success-800/50',
                        focus_ring: 'focus:ring-success-500',
                        icon_class: 'fa-check-circle text-success-600 dark:text-success-400',
                        icon_animation: 'animate-bounce-in',
                        ripple_class: 'bg-success-500',
                        next_label: 'Reset'
                    },
                    'cancelled': {
                        label: 'Dibatalkan',
                        icon: '<i class="fas fa-times-circle mr-1"></i>',
                        badge_class: 'badge-danger',
                        button_class: 'bg-danger-100 hover:bg-danger-200 border border-danger-300 dark:bg-danger-900/30 dark:border-danger-700 dark:hover:bg-danger-800/50',
                        focus_ring: 'focus:ring-danger-500',
                        icon_class: 'fa-times-circle text-danger-600 dark:text-danger-400',
                        icon_animation: '',
                        ripple_class: 'bg-danger-500',
                        next_label: 'Reset'
                    }
                };
                return statusMap[status] || statusMap['pending'];
            }

            function getPriorityInfo(priority) {
                const priorityMap = {
                    'low': {
                        label: 'Rendah',
                        icon: '<i class="fas fa-arrow-down mr-1"></i>',
                        badge_class: 'badge-secondary',
                        stripe_class: 'bg-secondary-400 dark:bg-secondary-600',
                        level: 1
                    },
                    'medium': {
                        label: 'Sedang',
                        icon: '<i class="fas fa-minus mr-1"></i>',
                        badge_class: 'badge-warning',
                        stripe_class: 'bg-warning-400 dark:bg-warning-600',
                        level: 2
                    },
                    'high': {
                        label: 'Tinggi',
                        icon: '<i class="fas fa-arrow-up mr-1"></i>',
                        badge_class: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                        stripe_class: 'bg-orange-400 dark:bg-orange-600',
                        level: 3
                    },
                    'urgent': {
                        label: 'Mendesak',
                        icon: '<i class="fas fa-exclamation-triangle mr-1"></i>',
                        badge_class: 'badge-danger',
                        stripe_class: 'bg-danger-500 dark:bg-danger-600',
                        level: 4
                    }
                };
                return priorityMap[priority] || priorityMap['medium'];
            }

        async function loadCategories() {
            try {
                const response = await fetch('/categories');
                const result = await response.json();

                if (result.success) {
                    const categoriesHtml = result.data.length > 0
                        ? result.data.map(category => `
                            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 rounded-full" style="background-color: ${category.color};"></div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">${category.name}</span>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">${category.tasks_count || 0} tugas</span>
                            </div>
                        `).join('')
                        : '<p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada kategori</p>';

                    document.getElementById('categoriesList').innerHTML = categoriesHtml;

                    // Update category filter
                    const filterSelect = document.getElementById('filterCategory');
                    filterSelect.innerHTML = '<option value="">Semua Kategori</option>' +
                        result.data.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');

                    // Update task category select
                    const taskSelect = document.getElementById('taskCategory');
                    taskSelect.innerHTML = '<option value="">Pilih kategori</option>' +
                        result.data.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        function loadCategoriesForSelect() {
            // Categories already loaded in loadCategories
        }

        async function loadQuickStats() {
            try {
                const response = await fetch('/tasks');
                const result = await response.json();

                if (result.success) {
                    const tasks = result.data;
                    const today = new Date().toDateString();
                    const todayTasks = tasks.filter(t => new Date(t.created_at).toDateString() === today).length;
                    const completedToday = tasks.filter(t => t.completed && new Date(t.updated_at).toDateString() === today).length;

                    const quickStatsHtml = `
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">${todayTasks}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tugas hari ini</p>
                        </div>
                        <div class="text-center mt-4">
                            <p class="text-2xl font-bold text-green-600">${completedToday}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Selesai hari ini</p>
                        </div>
                    `;

                    document.getElementById('quickStats').innerHTML = quickStatsHtml;
                }
            } catch (error) {
                console.error('Error loading quick stats:', error);
            }
        }

        async function loadRecentActivity() {
            try {
                const response = await fetch('/tasks');
                const result = await response.json();

                if (result.success) {
                    const recentTasks = result.data.slice(0, 5);
                    const activityHtml = recentTasks.length > 0
                        ? recentTasks.map(task => `
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                    <i class="fas fa-plus text-blue-600 dark:text-blue-400 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        Tugas baru: <span class="font-medium">${task.title}</span>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        ${new Date(task.created_at).toLocaleDateString('id-ID')}
                                    </p>
                                </div>
                            </div>
                        `).join('')
                        : '<p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada aktivitas</p>';

                    document.getElementById('recentActivity').innerHTML = activityHtml;
                }
            } catch (error) {
                console.error('Error loading recent activity:', error);
            }
        }

        // Task actions
        async function toggleTask(taskId) {
            try {
                const response = await fetch(`/tasks/${taskId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    await Promise.all([loadTasks(), loadStats(), loadQuickStats()]);
                    showNotification('Status tugas berhasil diubah', 'success');
                } else {
                    showNotification(result.message || 'Gagal mengubah status tugas', 'error');
                }
            } catch (error) {
                console.error('Toggle task error:', error);
                if (error.name === 'TypeError' && error.message.includes('fetch')) {
                    showNotification('Tidak dapat terhubung ke server', 'error');
                } else {
                    showNotification('Terjadi kesalahan saat mengubah status tugas', 'error');
                }
            }
        }

        function deleteTask(taskId, taskTitle) {
            openDeleteModal(`tugas "${taskTitle}"`, async () => {
                try {
                    const response = await fetch(`/tasks/${taskId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': window.Laravel.csrfToken
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        await Promise.all([loadTasks(), loadStats(), loadQuickStats()]);
                        showNotification('Tugas berhasil dihapus', 'success');
                    } else {
                        showNotification(result.message || 'Gagal menghapus tugas', 'error');
                    }
                } catch (error) {
                    console.error('Delete task error:', error);
                    if (error.name === 'TypeError' && error.message.includes('fetch')) {
                        showNotification('Tidak dapat terhubung ke server', 'error');
                    } else {
                        showNotification('Terjadi kesalahan saat menghapus tugas', 'error');
                    }
                }
            });
        }

        // Notification
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const icon = document.getElementById('notificationIcon');
            const messageEl = document.getElementById('notificationMessage');

            // Set icon and colors based on type
            if (type === 'success') {
                icon.className = 'fas fa-check-circle text-green-500 mr-3';
                notification.className = notification.className.replace('border-blue-500', 'border-green-500');
            } else if (type === 'error') {
                icon.className = 'fas fa-exclamation-circle text-red-500 mr-3';
                notification.className = notification.className.replace('border-blue-500', 'border-red-500');
            }

            messageEl.textContent = message;
            notification.classList.remove('hidden');

            // Hide after 3 seconds
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        // Initialize FAB animation
        initFAB();
    </script>

    <!-- Floating Action Button -->
    <div id="fab" class="fixed bottom-6 right-6 z-90 group opacity-0 scale-0 transform transition-all duration-500">
        <!-- Main FAB Button -->
        <button onclick="openTaskModal()"
                class="w-14 h-14 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white rounded-full shadow-xl hover:shadow-2xl transform hover:scale-110 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-primary-500/50 animate-float"
                aria-label="Tambah Tugas Baru">
            <i class="fas fa-plus text-xl transition-transform duration-300 group-hover:rotate-45"></i>
        </button>

        <!-- FAB Tooltip -->
        <div class="absolute bottom-full right-0 mb-3 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 pointer-events-none whitespace-nowrap">
            Tambah Tugas Baru
            <div class="absolute top-full right-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
        </div>

        <!-- FAB Ripple Effect -->
        <div class="absolute inset-0 rounded-full bg-primary-500/20 animate-ping"></div>
        <div class="absolute inset-0 rounded-full bg-primary-500/10 animate-pulse"></div>
    </div>

    <!-- FAB JavaScript -->
    <script>
        function initFAB() {
            const fab = document.getElementById('fab');

            // FAB visibility based on scroll position
            let lastScrollTop = 0;
            let fabVisible = true;

            window.addEventListener('scroll', () => {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    // Scrolling down - hide FAB
                    if (fabVisible) {
                        fab.style.transform = 'translateY(100px) scale(0.8)';
                        fab.style.opacity = '0';
                        fabVisible = false;
                    }
                } else {
                    // Scrolling up - show FAB
                    if (!fabVisible) {
                        fab.style.transform = 'translateY(0) scale(1)';
                        fab.style.opacity = '1';
                        fabVisible = true;
                    }
                }

                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            });

            // FAB entrance animation
            setTimeout(() => {
                fab.style.transform = 'scale(1)';
                fab.style.opacity = '1';
            }, 500);
        }
    </script>
</x-app-layout>

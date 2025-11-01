class AdminDashboard {
    constructor() {
        this.users = [];
        this.stats = {};
        this.filteredUsers = [];
        this.currentAction = null;
        this.currentUserId = null;
        this.refreshInterval = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupCSRFToken();
        this.loadAllData();
        this.startAutoRefresh();
    }

    setupCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.csrfToken = token.getAttribute('content');
        }
    }

    setupEventListeners() {
        // Refresh button
        document.getElementById('refreshUsers').addEventListener('click', () => {
            this.loadUsers();
        });

        // Export button
        document.getElementById('exportUsers').addEventListener('click', () => {
            this.exportUsersData();
        });

        // User search
        document.getElementById('userSearch').addEventListener('input', (e) => {
            this.filterUsers(e.target.value);
        });

        // Modal confirmation
        document.getElementById('confirmCancel').addEventListener('click', () => {
            this.closeConfirmModal();
        });

        document.getElementById('confirmAction').addEventListener('click', () => {
            this.executeAction();
        });

        // Close modal when clicking outside
        document.getElementById('confirmModal').addEventListener('click', (e) => {
            if (e.target.id === 'confirmModal') {
                this.closeConfirmModal();
            }
        });
    }

    async makeRequest(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken || '',
                'X-Requested-With': 'XMLHttpRequest',
            },
        };

        const finalOptions = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, finalOptions);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
            
            return data;
        } catch (error) {
            console.error('Request error:', error);
            this.showAlert('Terjadi kesalahan: ' + this.escapeHtml(error.message || 'Unknown error'), 'error');
            throw error;
        }
    }

    async loadAllData() {
        this.showLoading(true);
        try {
            await Promise.all([
                this.loadStats(),
                this.loadUsers()
            ]);
            this.updateLastRefreshTime();
        } catch (error) {
            console.error('Error loading data:', error);
            this.showAlert('Gagal memuat data admin', 'error');
        } finally {
            this.showLoading(false);
        }
    }

    async loadStats() {
        try {
            const response = await this.makeRequest('/admin/stats');
            this.stats = response.data;
            this.updateStatsDisplay();
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    async loadUsers() {
        try {
            const response = await this.makeRequest('/admin/users');
            this.users = response.data;
            this.filteredUsers = [...this.users];
            this.renderUsers();
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    updateStatsDisplay() {
        const { users, tasks, categories } = this.stats;

        // Update main stats cards
        const statsContainer = document.getElementById('statsContainer');
        const completionRate = tasks.total > 0 ? Math.round((tasks.completed / tasks.total) * 100) : 0;

        statsContainer.innerHTML = `
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total Pengguna</p>
                        <p class="text-3xl font-bold">${users.total}</p>
                        <p class="text-blue-200 text-xs mt-1">+${users.recent} minggu ini</p>
                    </div>
                    <i class="fas fa-users text-4xl opacity-80"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Total Tugas</p>
                        <p class="text-3xl font-bold">${tasks.total}</p>
                        <p class="text-green-200 text-xs mt-1">+${tasks.recent} minggu ini</p>
                    </div>
                    <i class="fas fa-tasks text-4xl opacity-80"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Kategori</p>
                        <p class="text-3xl font-bold">${categories.total}</p>
                    </div>
                    <i class="fas fa-tags text-4xl opacity-80"></i>
                </div>
            </div>
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm">Tingkat Penyelesaian</p>
                        <p class="text-3xl font-bold">${completionRate}%</p>
                    </div>
                    <i class="fas fa-chart-line text-4xl opacity-80"></i>
                </div>
            </div>
        `;

        // Update detailed user stats
        const userStatsContainer = document.getElementById('userStats');
        userStatsContainer.innerHTML = `
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">User Biasa</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pengguna reguler</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">${users.regular_users}</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Admin</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Administrator sistem</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">${users.admins}</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-tasks text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">User Aktif</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Memiliki tugas</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">${this.users.filter(user => user.tasks_count > 0).length}</span>
            </div>
        `;

        // Update detailed task stats
        const taskStatsContainer = document.getElementById('taskStats');
        taskStatsContainer.innerHTML = `
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Selesai</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tugas yang sudah diselesaikan</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">${tasks.completed}</span>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Belum Selesai</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tugas yang masih pending</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">${tasks.pending}</span>
            </div>
            <!-- Attachment stats removed since attachments are no longer supported -->
        `;

        // Render most used categories
        this.renderMostUsedCategories();

        // Render recent activities
        this.renderRecentActivities();
    }

    renderMostUsedCategories() {
        const container = document.getElementById('mostUsedCategories');
        const categories = this.stats.categories.most_used;

        if (!categories || categories.length === 0) {
            container.innerHTML = '<div class="text-center py-8"><i class="fas fa-tags text-4xl text-gray-300 dark:text-gray-600 mb-2"></i><p class="text-gray-500 dark:text-gray-400">Belum ada kategori yang digunakan</p></div>';
            return;
        }

        const maxCount = Math.max(...categories.map(c => c.count));
        
        container.innerHTML = categories.map(category => {
            const percentage = maxCount > 0 ? (category.count / maxCount) * 100 : 0;
            // FIXED: Escape HTML to prevent XSS attacks
            const safeName = this.escapeHtml(category.name);
            const safeColor = this.escapeHtml(category.color);
            return `
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color: ${safeColor}20;">
                                <div class="w-3 h-3 rounded-full" style="background-color: ${safeColor};"></div>
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">${safeName}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">${category.count} tugas</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-500" style="width: ${percentage}%; background-color: ${safeColor};"></div>
                    </div>
                </div>
            `;
        }).join('');
    }

    renderRecentActivities() {
        const container = document.getElementById('recentActivities');
        const activities = this.stats.recent_activities;

        if (!activities || activities.length === 0) {
            container.innerHTML = '<div class="text-center py-8"><i class="fas fa-history text-4xl text-gray-300 dark:text-gray-600 mb-2"></i><p class="text-gray-500 dark:text-gray-400">Belum ada aktivitas terbaru</p></div>';
            return;
        }

        container.innerHTML = activities.map(activity => {
            const icon = activity.type === 'task_created' ? 'fas fa-plus-circle' : 'fas fa-user-plus';
            const iconColor = activity.type === 'task_created' ? 'text-blue-600 dark:text-blue-400' : 'text-green-600 dark:text-green-400';
            const bgColor = activity.type === 'task_created' ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-green-50 dark:bg-green-900/20';
            
            // FIXED: Escape HTML to prevent XSS attacks
            const safeMessage = this.escapeHtml(activity.message);
            const safeTime = this.escapeHtml(activity.time);
            
            return `
                <div class="flex items-start space-x-4 p-4 ${bgColor} rounded-lg">
                    <div class="w-10 h-10 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center shadow-sm">
                        <i class="${icon} ${iconColor}"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900 dark:text-white font-medium">${safeMessage}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${safeTime}</p>
                        ${activity.category ? `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-2" style="background-color: ${this.escapeHtml(activity.category.color)}20; color: ${this.escapeHtml(activity.category.color)};">${this.escapeHtml(activity.category.name)}</span>` : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    filterUsers(searchTerm) {
        const term = searchTerm.toLowerCase();
        this.filteredUsers = this.users.filter(user => 
            user.name.toLowerCase().includes(term) || 
            user.email.toLowerCase().includes(term) ||
            user.role.toLowerCase().includes(term)
        );
        this.renderUsers();
    }

    renderUsers() {
        const tbody = document.getElementById('usersTableBody');
        
        if (this.filteredUsers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada user yang ditemukan
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = this.filteredUsers.map(user => {
            const roleColor = user.role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800';
            const isCurrentUser = user.id === parseInt(window.authUserId || 0);
            
            return `
                <tr class="hover:bg-gray-50 fade-in">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">${user.name.charAt(0).toUpperCase()}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    ${user.name} ${isCurrentUser ? '(You)' : ''}
                                </div>
                                <div class="text-sm text-gray-500">${user.email}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${roleColor}">
                            ${user.role}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${user.tasks_count}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${user.created_at}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            ${!isCurrentUser ? `
                                <button onclick="adminDashboard.showRoleChangeModal(${user.id}, '${user.role}')" 
                                        class="text-blue-600 hover:text-blue-900 transition">
                                    ${user.role === 'admin' ? '👤 Make User' : '🔧 Make Admin'}
                                </button>
                                <button onclick="adminDashboard.showDeleteModal(${user.id}, '${user.name}')" 
                                        class="text-red-600 hover:text-red-900 transition">
                                    🗑️ Delete
                                </button>
                            ` : '<span class="text-gray-400">Current User</span>'}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    showRoleChangeModal(userId, currentRole) {
        const user = this.users.find(u => u.id === userId);
        const newRole = currentRole === 'admin' ? 'user' : 'admin';
        const roleText = newRole === 'admin' ? 'Admin' : 'User biasa';
        
        document.getElementById('confirmTitle').textContent = 'Ubah Role User';
        document.getElementById('confirmMessage').textContent = 
            `Apakah Anda yakin ingin mengubah role ${user.name} menjadi ${roleText}?`;
        
        this.currentAction = 'changeRole';
        this.currentUserId = userId;
        this.currentRole = newRole;
        
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    showDeleteModal(userId, userName) {
        document.getElementById('confirmTitle').textContent = 'Hapus User';
        document.getElementById('confirmMessage').textContent = 
            `Apakah Anda yakin ingin menghapus user ${userName}? Tindakan ini tidak dapat dibatalkan.`;
        
        this.currentAction = 'deleteUser';
        this.currentUserId = userId;
        
        const confirmButton = document.getElementById('confirmAction');
        confirmButton.textContent = 'Ya, Hapus';
        confirmButton.className = 'px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition';
        
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
        this.currentAction = null;
        this.currentUserId = null;
        this.currentRole = null;
        
        // Reset confirm button
        const confirmButton = document.getElementById('confirmAction');
        confirmButton.textContent = 'Ya, Lanjutkan';
        confirmButton.className = 'px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition';
    }

    async executeAction() {
        if (!this.currentAction || !this.currentUserId) return;

        try {
            if (this.currentAction === 'changeRole') {
                await this.changeUserRole(this.currentUserId, this.currentRole);
            } else if (this.currentAction === 'deleteUser') {
                await this.deleteUser(this.currentUserId);
            }
            
            this.closeConfirmModal();
        } catch (error) {
            console.error('Error executing action:', error);
        }
    }

    async changeUserRole(userId, newRole) {
        const user = this.users.find(u => u.id == userId);
        if (!user) return;

        try {
            this.showLoading(true, 'Mengubah role user...');
            
            const response = await this.makeRequest(`/admin/users/${userId}/role`, {
                method: 'PATCH', // Fixed: Changed from PUT to PATCH to match route definition
                body: JSON.stringify({ role: newRole })
            });

            this.showAlert(`✅ Role ${user.name} berhasil diubah menjadi ${newRole}!`, 'success');
            await this.loadUsers();
            await this.loadStats();
        } catch (error) {
            console.error('Error changing user role:', error);
            this.showAlert('❌ Gagal mengubah role user. Silakan coba lagi.', 'error');
        } finally {
            this.showLoading(false);
        }
    }

    async deleteUser(userId) {
        const user = this.users.find(u => u.id == userId);
        if (!user) return;

        // Cegah admin menghapus dirinya sendiri atau admin terakhir
        if (user.role === 'admin') {
            // Count total admin users
            const adminCount = this.users.filter(u => u.role === 'admin').length;
            
            if (adminCount <= 1) {
                this.showAlert('❌ Tidak dapat menghapus admin terakhir!', 'error');
                return;
            }
        }

        try {
            this.showLoading(true, 'Menghapus user...');
            
            const response = await this.makeRequest(`/admin/users/${userId}`, {
                method: 'DELETE'
            });

            this.showAlert(`✅ User "${user.name}" berhasil dihapus!`, 'success');
            await this.loadUsers();
            await this.loadStats();
        } catch (error) {
            console.error('Error deleting user:', error);
            this.showAlert('❌ Gagal menghapus user. Silakan coba lagi.', 'error');
        } finally {
            this.showLoading(false);
        }
    }

    showLoading(show) {
        const loading = document.getElementById('adminLoading');
        if (show) {
            loading.classList.remove('hidden');
        } else {
            loading.classList.add('hidden');
        }
    }

    showAlert(message, type = 'success') {
        const alert = document.getElementById('adminAlert');
        const alertMessage = document.getElementById('adminAlertMessage');
        
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

    // Method untuk auto-refresh data setiap 30 detik
    startAutoRefresh() {
        // Clear existing interval untuk menghindari multiple intervals
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
        
        // Auto refresh setiap 30 detik, tapi hanya jika tidak ada modal yang terbuka
        this.refreshInterval = setInterval(() => {
            // Cek jika tidak ada modal yang terbuka dan user tidak sedang mengetik
            const isModalOpen = !document.getElementById('confirmModal').classList.contains('hidden');
            const isUserTyping = document.activeElement && document.activeElement.tagName === 'INPUT';
            
            if (!isModalOpen && !isUserTyping) {
                this.loadAllData();
            }
        }, 30000);

        // Stop auto refresh ketika user tidak aktif
        // Remove existing visibility listener jika ada
        if (this.visibilityHandler) {
            document.removeEventListener('visibilitychange', this.visibilityHandler);
        }
        
        this.visibilityHandler = () => {
            if (document.visibilityState === 'hidden') {
                if (this.refreshInterval) {
                    clearInterval(this.refreshInterval);
                    this.refreshInterval = null;
                }
            } else if (document.visibilityState === 'visible') {
                // Refresh data setelah user kembali ke tab
                this.loadAllData();
                this.startAutoRefresh();
            }
        };
        
        document.addEventListener('visibilitychange', this.visibilityHandler);
    }

    // Method untuk menampilkan loading state
    showLoading(show, message = 'Memuat...') {
        let loadingEl = document.getElementById('adminLoadingOverlay');
        
        if (show) {
            if (!loadingEl) {
                loadingEl = document.createElement('div');
                loadingEl.id = 'adminLoadingOverlay';
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
                setTimeout(() => {
                    if (loadingEl.parentNode) {
                        loadingEl.remove();
                    }
                }, 300);
            }
        }
    }

    // Method untuk konfirmasi action yang lebih baik
    showConfirmDialog(title, message, actionText = 'Ya', cancelText = 'Batal') {
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
            modal.innerHTML = `
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">${title}</h3>
                        <p class="text-gray-600 mb-6">${message}</p>
                        <div class="flex justify-end space-x-3">
                            <button id="customConfirmCancel" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-200">
                                ${cancelText}
                            </button>
                            <button id="customConfirmOk" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200">
                                ${actionText}
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            const cancelBtn = modal.querySelector('#customConfirmCancel');
            const okBtn = modal.querySelector('#customConfirmOk');
            
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

    // Method untuk update timestamp terakhir refresh
    updateLastRefreshTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
        const dateString = now.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });

        const lastUpdatedEl = document.getElementById('lastUpdated');
        if (lastUpdatedEl) {
            lastUpdatedEl.textContent = `${dateString}, ${timeString}`;
        }
        
        // Update status indicator
        const statusEl = document.getElementById('statusIndicator');
        if (statusEl) {
            statusEl.className = 'w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse';
        }
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

    // Method untuk export data users
    exportUsersData() {
        const csvContent = "data:text/csv;charset=utf-8," 
            + "Name,Email,Role,Created At\n"
            + this.users.map(user => 
                `"${user.name}","${user.email}","${user.role}","${new Date(user.created_at).toLocaleDateString()}"`
            ).join("\n");

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `users_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showAlert('✅ Data pengguna berhasil diexport!', 'success');
    }
    
    /**
     * Cleanup method untuk menghindari memory leaks
     */
    cleanup() {
        // Clear auto refresh interval
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
        
        // Remove visibility change handler
        if (this.visibilityHandler) {
            document.removeEventListener('visibilitychange', this.visibilityHandler);
            this.visibilityHandler = null;
        }
        
        // Remove any loading overlays
        const loadingEl = document.getElementById('adminLoadingOverlay');
        if (loadingEl && loadingEl.parentNode) {
            loadingEl.remove();
        }
        
        // Clear data arrays
        this.users = [];
        this.stats = {};
        this.filteredUsers = [];
        this.currentAction = null;
        this.currentUserId = null;
    }
}

// Initialize the admin dashboard when the page loads
document.addEventListener('DOMContentLoaded', () => {
    window.adminDashboard = new AdminDashboard();
});

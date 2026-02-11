<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    @stack('styles')
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #1a237e;
            --sidebar-hover: #303f9f;
            --content-bg: #f8f9fa;
            --navbar-bg: #ffffff;
            --primary-color: #3f51b5;
            --danger-color: #e53935;
            --success-color: #43a047;
            --warning-color: #ff9800;
            --info-color: #2196f3;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-color: #dee2e6;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }

        body {
            display: flex;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--content-bg);
            position: relative;
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: white;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100%;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav {
            padding: 15px 0;
        }

        .sidebar-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-link:hover {
            background-color: var(--sidebar-hover);
            color: white;
            transform: translateX(5px);
        }

        .sidebar-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .sidebar-link.active {
            background-color: var(--sidebar-hover);
            color: white;
            font-weight: 500;
        }

        .content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .navbar {
            background-color: var(--navbar-bg);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .page-content {
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0 !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .content {
                margin-left: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }

            .sidebar.active+.sidebar-overlay {
                display: block;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                min-width: 600px;
            }

            .mobile-menu-toggle {
                display: block !important;
            }
        }

        .mobile-menu-toggle {
            display: none;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            margin-right: 15px;
        }

        .action-buttons .btn {
            margin: 2px;
            white-space: nowrap;
        }

        body {
            overflow-x: hidden;
        }

        main,
        .content {
            overflow-x: hidden;
        }

        /* Mobile responsive improvements */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .card-body {
                padding: 1rem;
            }

            .d-flex.gap-1 {
                gap: 0.25rem !important;
            }

            .d-flex.gap-2 {
                gap: 0.5rem !important;
            }

            /* Stack buttons vertically on very small screens */
            @media (max-width: 576px) {
                .d-flex.gap-1 {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .btn-group {
                    width: 100%;
                }

                .dropdown-menu {
                    position: static !important;
                    transform: none !important;
                    width: 100%;
                    border: 1px solid #dee2e6;
                    box-shadow: none;
                }
            }

            /* Improve table readability on mobile */
            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
                white-space: nowrap;
            }

            /* Better spacing for action buttons */
            .table td:last-child {
                min-width: 120px;
            }

            /* Responsive card layout */
            .card-header {
                padding: 0.75rem;
            }

            .card-header h5 {
                font-size: 1rem;
            }

            /* Better button spacing on mobile */
            .btn-group .btn {
                margin: 1px;
            }
        }

        /* Professional UI Enhancements */
        .card {
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            transition: box-shadow 0.15s ease-in-out;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background-color: var(--light-color);
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
        }

        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .table {
            border-radius: 0.375rem;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--light-color);
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            color: var(--dark-color);
        }

        .badge {
            font-weight: 500;
            padding: 0.375rem 0.75rem;
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .form-control,
        .form-select {
            border-radius: 0.375rem;
            border: 1px solid var(--border-color);
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(63, 81, 181, 0.25);
        }

        .dropdown-menu {
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
            border-radius: 0.5rem;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: background-color 0.15s ease-in-out;
        }

        .dropdown-item:hover {
            background-color: var(--light-color);
        }

        .pagination .page-link {
            border-radius: 0.375rem;
            margin: 0 0.125rem;
            border: 1px solid var(--border-color);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Statistics Cards */
        .card.bg-primary,
        .card.bg-success,
        .card.bg-info,
        .card.bg-warning {
            border: none;
            box-shadow: var(--shadow-lg);
        }

        /* Code styling */
        code {
            background-color: var(--light-color);
            color: var(--primary-color);
            font-weight: 500;
        }

        /* Professional spacing */
        .mb-4 {
            margin-bottom: 2rem !important;
        }

        .mt-4 {
            margin-top: 2rem !important;
        }

        /* Enhanced sidebar */
        .sidebar-link.active {
            background-color: var(--sidebar-hover);
            color: white;
            font-weight: 600;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: #fff;
        }

        /* Enhanced navbar */
        .navbar {
            background-color: var(--navbar-bg);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }

        /* Loading states */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Success/Error states */
        .text-success {
            color: var(--success-color) !important;
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .text-warning {
            color: var(--warning-color) !important;
        }

        .text-info {
            color: var(--info-color) !important;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4 class="text-white mb-0">
                <i class="bi bi-speedometer2"></i> Admin Panel
            </h4>
        </div>
        <div class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            @if(auth()->user()->hasPermissionTo('posts'))
            <a href="{{ route('admin.posts.index') }}" class="sidebar-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                <i class="bi bi-file-post"></i> All Posts
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('categories'))
            <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Categories
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('users'))
            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Users
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('payments'))
            <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> Payments
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('all_invite_tokens'))
            <a href="{{ route('admin.invite-tokens.index') }}" class="sidebar-link {{ request()->routeIs('admin.invite-tokens.*') ? 'active' : '' }}">
                <i class="bi bi-gift"></i> All Invite Tokens
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('admin_users'))
            <a href="{{ route('admin.admin-users.index') }}" class="sidebar-link {{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Admin Users
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('roles_permissions'))
            <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i> Roles
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('reports'))
            <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-flag"></i> Reports
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('analytics'))
            <a href="{{ route('admin.analytics.index') }}" class="sidebar-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Analytics
            </a>
            @endif
            @if(auth()->user()->hasPermissionTo('support_tickets'))
            <a href="{{ route('admin.support-tickets.index') }}" class="sidebar-link {{ request()->routeIs('admin.support-tickets.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i> Support Tickets
            </a>
            @endif
        </div>
    </div>

    <!-- Overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Content -->
    <div class="content">
        <!-- Navbar -->
        <nav class="navbar">
            <div class="d-flex w-100 align-items-center gap-3">
                <button class="mobile-menu-toggle me-1" id="menuToggle">
                    <i class="bi bi-list"></i>
                </button>

                @if(auth()->user()->canImpersonate() && !session('impersonator_id'))
                <div class="d-flex align-items-center gap-2 navbar-impersonate-wrap position-relative flex-grow-1 flex-md-grow-0" style="max-width: 520px;">
                    <div id="navImpersonateSearchWrap" class="d-flex align-items-center gap-2 flex-grow-1">
                        <input type="text" id="navImpersonateSearch" class="form-control form-control-sm" placeholder="Search user to impersonate (name or email)..." autocomplete="off" style="min-width: 280px;">
                        <div id="navImpersonateLoading" class="text-muted small text-nowrap" style="display: none;">Searching...</div>
                    </div>
                    <div id="navImpersonateSelected" class="d-none d-flex align-items-center gap-2 flex-nowrap">
                        <span class="small text-muted text-nowrap">View as:</span>
                        <span id="navImpersonateSelectedName" class="fw-semibold small text-truncate" style="max-width: 180px;" title=""></span>
                        <button type="button" id="navImpersonateClear" class="btn btn-link btn-sm p-0 text-secondary align-baseline flex-shrink-0" title="Clear selection"><i class="bi bi-x-circle-fill"></i></button>
                        <form action="{{ route('admin.impersonate.store') }}" method="POST" id="navImpersonateForm" class="d-inline flex-shrink-0">
                            @csrf
                            <input type="hidden" name="user_id" id="navImpersonateUserId" value="">
                            <button type="submit" class="btn btn-primary btn-sm text-nowrap"><i class="bi bi-person-badge"></i> Impersonate</button>
                        </form>
                    </div>
                    <div id="navImpersonateResults" class="position-absolute top-100 start-0 mt-1 bg-white border rounded shadow-sm list-group list-group-flush" style="min-width: 100%; max-height: 280px; overflow-y: auto; display: none; z-index: 1050;"></div>
                </div>
                @endif

                @if(session('impersonator_id'))
                <form action="{{ route('admin.impersonate.stop') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="bi bi-box-arrow-left"></i> Leave impersonation
                    </button>
                </form>
                @endif

                <div class="ms-auto">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-danger btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const content = document.querySelector('.content');

            // Toggle sidebar
            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('active');
            });

            // Close sidebar when clicking outside
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
            });

            // Close sidebar when clicking on content (for good measure)
            content.addEventListener('click', function() {
                if (sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            });

            // Close sidebar when clicking on any sidebar link
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('active');
                    }
                });
            });

            // Navbar impersonate: search in header, select then Impersonate button
            var navSearch = document.getElementById('navImpersonateSearch');
            if (navSearch) {
                var searchWrap = document.getElementById('navImpersonateSearchWrap');
                var selectedWrap = document.getElementById('navImpersonateSelected');
                var selectedNameEl = document.getElementById('navImpersonateSelectedName');
                var userIdInput = document.getElementById('navImpersonateUserId');
                var resultsEl = document.getElementById('navImpersonateResults');
                var loadingEl = document.getElementById('navImpersonateLoading');
                var clearBtn = document.getElementById('navImpersonateClear');
                var searchUrl = '{{ route("admin.impersonate.search") }}';
                var debounceTimer;

                function esc(s) {
                    if (!s) return '';
                    var d = document.createElement('div');
                    d.textContent = s;
                    return d.innerHTML;
                }

                function showResults(show) {
                    resultsEl.style.display = show ? 'block' : 'none';
                }

                function selectUser(user) {
                    userIdInput.value = user.id;
                    var label = user.name + ' (' + (user.email || '—') + ')';
                    selectedNameEl.textContent = label;
                    selectedNameEl.setAttribute('title', label);
                    searchWrap.classList.add('d-none');
                    selectedWrap.classList.remove('d-none');
                    navSearch.value = '';
                    resultsEl.innerHTML = '';
                    showResults(false);
                }

                navSearch.addEventListener('input', function() {
                    var q = this.value.trim();
                    clearTimeout(debounceTimer);
                    if (q.length < 2) {
                        resultsEl.innerHTML = '';
                        showResults(false);
                        return;
                    }
                    debounceTimer = setTimeout(function() {
                        loadingEl.style.display = 'block';
                        resultsEl.innerHTML = '';
                        showResults(true);
                        fetch(searchUrl + '?q=' + encodeURIComponent(q), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            loadingEl.style.display = 'none';
                            if (data.length === 0) {
                                resultsEl.innerHTML = '<div class="list-group-item list-group-item-secondary small">No users found.</div>';
                            } else {
                                data.forEach(function(u) {
                                    var div = document.createElement('div');
                                    div.className = 'list-group-item list-group-item-action py-2 cursor-pointer';
                                    div.setAttribute('data-user-id', u.id);
                                    div.setAttribute('data-user-name', u.name);
                                    div.setAttribute('data-user-email', u.email || '');
                                    div.innerHTML = '<strong class="small">' + esc(u.name) + '</strong><br><span class="text-muted small">' + esc(u.email) + ' · ' + esc(u.role) + '</span>';
                                    div.addEventListener('click', function() {
                                        selectUser({
                                            id: this.getAttribute('data-user-id'),
                                            name: this.getAttribute('data-user-name'),
                                            email: this.getAttribute('data-user-email')
                                        });
                                    });
                                    resultsEl.appendChild(div);
                                });
                            }
                        })
                        .catch(function() {
                            loadingEl.style.display = 'none';
                            resultsEl.innerHTML = '<div class="list-group-item list-group-item-danger small">Search failed.</div>';
                        });
                    }, 300);
                });

                if (clearBtn) {
                    clearBtn.addEventListener('click', function() {
                        selectedWrap.classList.add('d-none');
                        searchWrap.classList.remove('d-none');
                        userIdInput.value = '';
                        navSearch.value = '';
                        navSearch.focus();
                    });
                }

                document.addEventListener('click', function(e) {
                    if (!navSearch.contains(e.target) && !resultsEl.contains(e.target)) {
                        showResults(false);
                    }
                });
                navSearch.addEventListener('focus', function() {
                    if (resultsEl.innerHTML) showResults(true);
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
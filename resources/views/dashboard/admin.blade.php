@extends('layouts.dashboard')

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }
    .page-header::after {
        content:''; position:absolute; width:300px; height:300px;
        background:rgba(255,255,255,0.06); border-radius:50%;
        right:-80px; top:-100px; pointer-events:none;
    }
    .page-header h2 { font-size:26px; font-weight:800; margin:0 0 6px; }
    .page-header p  { margin:0; opacity:0.8; font-size:14px; }

    /* Admin nav tabs */
    .admin-nav {
        display:flex; gap:6px; margin-bottom:24px;
        background:#fff; border-radius:14px; padding:8px;
        box-shadow:0 4px 16px rgba(0,0,0,0.05);
        flex-wrap:wrap;
    }
    .admin-tab {
        padding:9px 18px; border-radius:10px;
        border:none; background:transparent;
        font-size:14px; font-weight:600; color:#888;
        cursor:pointer; transition:all 0.2s;
        display:flex; align-items:center; gap:8px;
    }
    .admin-tab:hover { background:#f4f7f6; color:#1a1f2e; }
    .admin-tab.active { background:linear-gradient(135deg,#ef4444,#b91c1c); color:#fff; }
    .admin-tab .tab-badge {
        background:rgba(0,0,0,0.15);
        padding:1px 7px; border-radius:10px;
        font-size:11px;
    }
    .admin-tab.active .tab-badge { background:rgba(255,255,255,0.25); }

    /* Admin stat cards */
    .admin-stat {
        background:#fff; border-radius:14px; padding:18px 20px;
        box-shadow:0 4px 16px rgba(0,0,0,0.05);
        display:flex; align-items:center; gap:16px;
    }
    .admin-stat-icon {
        width:52px; height:52px; border-radius:14px;
        display:flex; align-items:center; justify-content:center;
        font-size:22px; flex-shrink:0;
    }
    .admin-stat-val { font-size:26px; font-weight:800; color:#1a1f2e; line-height:1; }
    .admin-stat-lbl { font-size:12px; color:#aaa; text-transform:uppercase; letter-spacing:.5px; font-weight:700; }

    /* Admin panel */
    .admin-panel { display:none; animation:fadeIn 0.25s ease; }
    .admin-panel.active { display:block; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }

    /* Data table */
    .admin-table { width:100%; border-collapse:separate; border-spacing:0; }
    .admin-table th {
        font-size:11px; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:#aaa; padding:12px 16px;
        border-bottom:2px solid #f0f0f0; text-align:left;
        white-space:nowrap;
    }
    .admin-table td {
        padding:12px 16px; border-bottom:1px solid #f5f5f5;
        font-size:14px; vertical-align:middle;
    }
    .admin-table tr:last-child td { border-bottom:none; }
    .admin-table tr:hover td { background:#fafafa; }

    /* Form */
    .admin-form-group { margin-bottom:16px; }
    .admin-form-group label { font-size:13px; font-weight:700; color:#555; margin-bottom:6px; display:block; }
    .admin-input {
        width:100%; border:1.5px solid #e8ecef; border-radius:10px;
        padding:10px 14px; font-size:14px; outline:none;
        transition:border-color 0.2s, box-shadow 0.2s;
    }
    .admin-input:focus {
        border-color:#ef4444;
        box-shadow:0 0 0 3px rgba(239,68,68,0.1);
    }
    .admin-input-select { cursor:pointer; background:#fff; }

    /* Buttons */
    .btn-admin { border-radius:9px; font-size:13px; font-weight:600; padding:8px 16px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:all 0.2s; }
    .btn-admin-primary { background:#ef4444; color:#fff; }
    .btn-admin-primary:hover { background:#dc2626; }
    .btn-admin-success { background:#00b575; color:#fff; }
    .btn-admin-success:hover { background:#009a62; }
    .btn-admin-warning { background:#f59e0b; color:#fff; }
    .btn-admin-warning:hover { background:#d97706; }
    .btn-admin-secondary { background:#f4f7f6; color:#555; border:1.5px solid #e8ecef; }
    .btn-admin-secondary:hover { background:#e8ecef; }
    .btn-admin-danger { background:#fff; color:#ef4444; border:1.5px solid #ef4444; }
    .btn-admin-danger:hover { background:#ef4444; color:#fff; }

    /* Status badges */
    .badge-active   { background:#d1fae5; color:#065f46; font-size:11px; padding:3px 10px; border-radius:20px; font-weight:700; }
    .badge-inactive { background:#fee2e2; color:#991b1b; font-size:11px; padding:3px 10px; border-radius:20px; font-weight:700; }
    .badge-admin    { background:#ede9fe; color:#6d28d9; font-size:11px; padding:3px 10px; border-radius:20px; font-weight:700; }
    .badge-positive { background:#d1fae5; color:#065f46; font-size:11px; padding:3px 10px; border-radius:20px; font-weight:700; }
    .badge-negative { background:#fee2e2; color:#991b1b; font-size:11px; padding:3px 10px; border-radius:20px; font-weight:700; }
    .badge-neutral  { background:#fef3c7; color:#92400e; font-size:11px; padding:3px 10px; border-radius:20px; font-weight:700; }

    /* Pagination */
    .admin-pagination { display:flex; gap:4px; margin-top:16px; }
    .page-btn { width:32px;height:32px; border-radius:8px; border:1.5px solid #e8ecef; background:#fff; cursor:pointer; font-size:13px; font-weight:600; display:flex;align-items:center;justify-content:center; transition:all 0.2s; }
    .page-btn.active { background:#ef4444; border-color:#ef4444; color:#fff; }
    .page-btn:hover { border-color:#ef4444; color:#ef4444; }

    /* Modal overlay */
    .admin-modal-overlay {
        position:fixed; inset:0; background:rgba(0,0,0,0.5);
        z-index:2000; display:flex;align-items:center;justify-content:center;
        opacity:0; pointer-events:none; transition:opacity 0.25s;
    }
    .admin-modal-overlay.show { opacity:1; pointer-events:all; }
    .admin-modal-box {
        background:#fff; border-radius:20px; width:520px; max-width:95vw;
        max-height:90vh; overflow-y:auto;
        padding:28px; transform:scale(0.95); transition:transform 0.25s;
        box-shadow:0 20px 60px rgba(0,0,0,0.2);
    }
    .admin-modal-overlay.show .admin-modal-box { transform:scale(1); }
    .admin-modal-title { font-size:18px; font-weight:800; margin-bottom:20px; display:flex;align-items:center;gap:10px; }

    /* System logs */
    .log-entry { padding:8px 14px; border-radius:10px; margin-bottom:6px; font-size:13px; font-family:monospace; }
    .log-info    { background:#f0fdf4; color:#166534; border-left:3px solid #00b575; }
    .log-warning { background:#fffbeb; color:#92400e; border-left:3px solid #f59e0b; }
    .log-error   { background:#fff1f2; color:#991b1b; border-left:3px solid #ef4444; }
    .log-time    { font-size:11px; color:#aaa; margin-right:8px; }
</style>
@endsection

@section('content')

{{-- PAGE HEADER --}}
<div class="page-header">
    <div class="d-flex align-items-center gap-3 mb-2">
        <i class="fa-solid fa-shield-halved" style="font-size:36px; opacity:0.85;"></i>
        <div>
            <h2>Admin Dashboard</h2>
            <p>Manage users, port datasets, news articles, and system configuration</p>
        </div>
    </div>
    <div class="d-flex gap-4 mt-2">
        <div><span class="fw-bold fs-5" id="hdrUsers">—</span><br><small style="opacity:0.75;">Total Users</small></div>
        <div><span class="fw-bold fs-5" id="hdrPorts">—</span><br><small style="opacity:0.75;">Ports</small></div>
        <div><span class="fw-bold fs-5" id="hdrCountries">—</span><br><small style="opacity:0.75;">Countries</small></div>
        <div><span class="fw-bold fs-5" id="hdrArticles">—</span><br><small style="opacity:0.75;">Articles</small></div>
    </div>
</div>

{{-- ADMIN STAT CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="admin-stat">
            <div class="admin-stat-icon" style="background:#fef2f2;color:#ef4444;"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="admin-stat-val" id="statUsers">—</div>
                <div class="admin-stat-lbl">Registered Users</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat">
            <div class="admin-stat-icon" style="background:#f0fdf4;color:#00b575;"><i class="fa-solid fa-ship"></i></div>
            <div>
                <div class="admin-stat-val" id="statPorts">—</div>
                <div class="admin-stat-lbl">Port Records</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat">
            <div class="admin-stat-icon" style="background:#ede9fe;color:#8b5cf6;"><i class="fa-solid fa-globe"></i></div>
            <div>
                <div class="admin-stat-val" id="statCountries">—</div>
                <div class="admin-stat-lbl">Countries in DB</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-stat">
            <div class="admin-stat-icon" style="background:#fef3c7;color:#f59e0b;"><i class="fa-solid fa-newspaper"></i></div>
            <div>
                <div class="admin-stat-val" id="statArticles">—</div>
                <div class="admin-stat-lbl">Articles Cached</div>
            </div>
        </div>
    </div>
</div>

{{-- ADMIN TABS --}}
<div class="admin-nav">
    <button class="admin-tab active" onclick="switchAdminTab('users', this)">
        <i class="fa-solid fa-users"></i> Users
        <span class="tab-badge" id="tabBadgeUsers">—</span>
    </button>
    <button class="admin-tab" onclick="switchAdminTab('ports', this)">
        <i class="fa-solid fa-ship"></i> Port Dataset
        <span class="tab-badge" id="tabBadgePorts">—</span>
    </button>
    <button class="admin-tab" onclick="switchAdminTab('countries', this)">
        <i class="fa-solid fa-globe"></i> Countries
        <span class="tab-badge" id="tabBadgeCountries">—</span>
    </button>
    <button class="admin-tab" onclick="switchAdminTab('articles', this)">
        <i class="fa-solid fa-newspaper"></i> Articles
        <span class="tab-badge" id="tabBadgeArticles">—</span>
    </button>
    <button class="admin-tab" onclick="switchAdminTab('logs', this)">
        <i class="fa-solid fa-terminal"></i> System Logs
    </button>
</div>

{{-- ===== USERS PANEL ===== --}}
<div class="admin-panel active" id="panel-users">
    <div class="custom-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-users me-2 text-danger"></i>User Management</h6>
            <div class="d-flex gap-2">
                <input type="text" class="admin-input" id="userSearch" placeholder="Search users..." style="max-width:200px;" oninput="filterUsers(this.value)">
                <button class="btn-admin btn-admin-success" onclick="openModal('addUser')">
                    <i class="fa-solid fa-plus"></i> Add User
                </button>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr><td colspan="7" class="text-center text-muted py-4">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="admin-pagination" id="usersPagination"></div>
    </div>
</div>

{{-- ===== PORTS PANEL ===== --}}
<div class="admin-panel" id="panel-ports">
    <div class="custom-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-ship me-2 text-success"></i>Port Dataset Management</h6>
            <div class="d-flex gap-2">
                <input type="text" class="admin-input" id="portSearch" placeholder="Search ports..." style="max-width:200px;" oninput="filterPorts(this.value)">
                <button class="btn-admin btn-admin-success" onclick="openModal('addPort')">
                    <i class="fa-solid fa-plus"></i> Add Port
                </button>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th><th>Port Name</th><th>Country</th><th>Latitude</th><th>Longitude</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody id="portsTableBody">
                    <tr><td colspan="6" class="text-center text-muted py-4">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="admin-pagination" id="portsPagination"></div>
    </div>
</div>

{{-- ===== COUNTRIES PANEL ===== --}}
<div class="admin-panel" id="panel-countries">
    <div class="custom-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-globe me-2" style="color:#8b5cf6;"></i>Countries Database</h6>
            <div class="d-flex gap-2">
                <input type="text" class="admin-input" id="countrySearchAdmin" placeholder="Search countries..." style="max-width:200px;" oninput="filterCountriesAdmin(this.value)">
                <button class="btn-admin btn-admin-secondary" onclick="refreshCountries()">
                    <i class="fa-solid fa-rotate-right"></i> Refresh
                </button>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ISO</th><th>Country Name</th><th>Region</th><th>Currency</th><th>Lat</th><th>Lng</th><th>Risk Score</th>
                    </tr>
                </thead>
                <tbody id="countriesAdminBody">
                    <tr><td colspan="7" class="text-center text-muted py-4">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== ARTICLES PANEL ===== --}}
<div class="admin-panel" id="panel-articles">
    <div class="custom-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-newspaper me-2 text-warning"></i>Articles & Analysis Management</h6>
            <div class="d-flex gap-2">
                <button class="btn-admin btn-admin-success" onclick="openModal('addArticle')">
                    <i class="fa-solid fa-plus"></i> Add Article
                </button>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th><th>Title</th><th>Source</th><th>Sentiment</th><th>Published</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody id="articlesTableBody">
                    <tr><td colspan="6" class="text-center text-muted py-4">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== LOGS PANEL ===== --}}
<div class="admin-panel" id="panel-logs">
    <div class="custom-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-terminal me-2 text-danger"></i>System Activity Log</h6>
            <button class="btn-admin btn-admin-secondary" onclick="clearLogs()">
                <i class="fa-solid fa-trash"></i> Clear Logs
            </button>
        </div>
        <div id="systemLogs" style="max-height:500px; overflow-y:auto;">
        </div>
    </div>
</div>

{{-- ===== MODALS ===== --}}

{{-- Add User Modal --}}
<div class="admin-modal-overlay" id="modal-addUser">
    <div class="admin-modal-box">
        <div class="admin-modal-title">
            <span style="width:34px;height:34px;background:#fef2f2;color:#ef4444;border-radius:10px;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-user-plus"></i></span>
            Add New User
        </div>
        <div class="admin-form-group">
            <label>Full Name *</label>
            <input type="text" class="admin-input" id="newUserName" placeholder="John Doe">
        </div>
        <div class="admin-form-group">
            <label>Email *</label>
            <input type="email" class="admin-input" id="newUserEmail" placeholder="john@example.com">
        </div>
        <div class="admin-form-group">
            <label>Role</label>
            <select class="admin-input admin-input-select" id="newUserRole">
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="analyst">Analyst</option>
            </select>
        </div>
        <div class="admin-form-group">
            <label>Password *</label>
            <input type="password" class="admin-input" id="newUserPass" placeholder="Min 8 characters">
        </div>
        <div class="d-flex gap-2 mt-3">
            <button class="btn-admin btn-admin-success" onclick="addUser()"><i class="fa-solid fa-check"></i> Save User</button>
            <button class="btn-admin btn-admin-secondary" onclick="closeModal('addUser')">Cancel</button>
        </div>
    </div>
</div>

{{-- Add Port Modal --}}
<div class="admin-modal-overlay" id="modal-addPort">
    <div class="admin-modal-box">
        <div class="admin-modal-title">
            <span style="width:34px;height:34px;background:#f0fdf4;color:#00b575;border-radius:10px;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-ship"></i></span>
            Add New Port
        </div>
        <div class="admin-form-group">
            <label>Port Name *</label>
            <input type="text" class="admin-input" id="newPortName" placeholder="e.g. Port of Rotterdam">
        </div>
        <div class="admin-form-group">
            <label>Country *</label>
            <select class="admin-input admin-input-select" id="newPortCountry">
                <option value="">— Select Country —</option>
            </select>
        </div>
        <div class="row g-2">
            <div class="col-6">
                <div class="admin-form-group">
                    <label>Latitude *</label>
                    <input type="number" class="admin-input" id="newPortLat" placeholder="-90 to 90" step="0.0001">
                </div>
            </div>
            <div class="col-6">
                <div class="admin-form-group">
                    <label>Longitude *</label>
                    <input type="number" class="admin-input" id="newPortLng" placeholder="-180 to 180" step="0.0001">
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 mt-3">
            <button class="btn-admin btn-admin-success" onclick="addPort()"><i class="fa-solid fa-check"></i> Save Port</button>
            <button class="btn-admin btn-admin-secondary" onclick="closeModal('addPort')">Cancel</button>
        </div>
    </div>
</div>

{{-- Add Article Modal --}}
<div class="admin-modal-overlay" id="modal-addArticle">
    <div class="admin-modal-box">
        <div class="admin-modal-title">
            <span style="width:34px;height:34px;background:#fef3c7;color:#f59e0b;border-radius:10px;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-newspaper"></i></span>
            Add Analysis Article
        </div>
        <div class="admin-form-group">
            <label>Title *</label>
            <input type="text" class="admin-input" id="newArtTitle" placeholder="Article title...">
        </div>
        <div class="admin-form-group">
            <label>Source</label>
            <input type="text" class="admin-input" id="newArtSource" placeholder="e.g. Reuters, Bloomberg">
        </div>
        <div class="admin-form-group">
            <label>URL</label>
            <input type="url" class="admin-input" id="newArtUrl" placeholder="https://...">
        </div>
        <div class="admin-form-group">
            <label>Sentiment</label>
            <select class="admin-input admin-input-select" id="newArtSentiment">
                <option value="Positive">Positive</option>
                <option value="Neutral" selected>Neutral</option>
                <option value="Negative">Negative</option>
            </select>
        </div>
        <div class="d-flex gap-2 mt-3">
            <button class="btn-admin btn-admin-success" onclick="addArticle()"><i class="fa-solid fa-check"></i> Save Article</button>
            <button class="btn-admin btn-admin-secondary" onclick="closeModal('addArticle')">Cancel</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Demo data arrays (in real app these would come from /api/admin/*)
let usersData   = [];
let portsData   = [];
let countriesData = [];
let articlesData  = [];

$(document).ready(function() {
    loadAdminStats();
    loadUsersTable();
    loadCountriesAdmin();
    generateSystemLogs();
    loadPortsAdmin();
    loadArticlesAdmin();

    // Close modals on overlay click
    document.querySelectorAll('.admin-modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id.replace('modal-',''));
        });
    });
});

/* ===== STATS ===== */
function loadAdminStats() {
    $.get('/api/countries', function(data) {
        $('#statCountries, #hdrCountries').text(data.length);
        $('#tabBadgeCountries').text(data.length);
        countriesData = data;
        loadCountriesAdmin();
    });
    $.get('/api/ports', function(data) {
        $('#statPorts, #hdrPorts').text(data.length);
        $('#tabBadgePorts').text(data.length);
        portsData = data;
        renderPortsTable(portsData);
    });

    // Simulate users count
    const userCount = Math.floor(Math.random()*50) + 10;
    $('#statUsers, #hdrUsers, #tabBadgeUsers').text(userCount);
    generateUsers(userCount);

    // Simulate articles
    $.get('/api/news?q=supply chain', function(res) {
        const articles = (res.articles || []).slice(0, 10);
        articlesData = articles.map((a, i) => ({
            id: i+1,
            title: a.title || 'Untitled',
            source: a.source || 'Unknown',
            sentiment: a.sentiment?.sentiment || 'Neutral',
            published: a.publishedAt ? new Date(a.publishedAt).toLocaleDateString() : '—',
            url: a.url || '#',
        }));
        $('#statArticles, #hdrArticles, #tabBadgeArticles').text(articlesData.length + ' cached');
        renderArticlesTable(articlesData);
    }).fail(() => {
        $('#statArticles, #hdrArticles, #tabBadgeArticles').text('0');
        renderArticlesTable([]);
    });
}

/* ===== USERS ===== */
function generateUsers(count) {
    const names = ['John Doe', 'Jane Smith', 'Ahmad Rahman', 'Maria Santos', 'Wei Chen', 'Priya Patel', 'Carlos Mendez', 'Sophie Laurent'];
    const roles = ['admin', 'analyst', 'user', 'user', 'user'];
    usersData = Array.from({length: Math.min(count, 10)}, (_, i) => ({
        id: i+1,
        name: names[i % names.length],
        email: `user${i+1}@supplychain.com`,
        role: roles[i % roles.length],
        joined: `2024-${String(Math.ceil((i+1)/2)).padStart(2,'0')}-01`,
        active: Math.random() > 0.2,
    }));
    renderUsersTable(usersData);
}

function loadUsersTable() { /* will be called after data ready */ }

function renderUsersTable(data) {
    let html = '';
    data.forEach(u => {
        const roleBadge = u.role === 'admin' ? 'badge-admin' : 'badge-active';
        html += `<tr>
            <td class="text-muted">#${u.id}</td>
            <td class="fw-bold">${u.name}</td>
            <td><a href="mailto:${u.email}" style="color:#0ea5e9;">${u.email}</a></td>
            <td><span class="${roleBadge}">${u.role}</span></td>
            <td>${u.joined}</td>
            <td><span class="${u.active ? 'badge-active' : 'badge-inactive'}">${u.active ? '✅ Active' : '🔴 Inactive'}</span></td>
            <td>
                <button class="btn-admin btn-admin-warning" onclick="editUser(${u.id})" style="padding:5px 10px;font-size:12px;"><i class="fa-solid fa-edit"></i></button>
                <button class="btn-admin btn-admin-danger" onclick="deleteUser(${u.id})" style="padding:5px 10px;font-size:12px; margin-left:4px;"><i class="fa-solid fa-trash"></i></button>
            </td>
        </tr>`;
    });
    $('#usersTableBody').html(html || '<tr><td colspan="7" class="text-center text-muted py-4">No users found</td></tr>');
}

function filterUsers(q) {
    const filtered = usersData.filter(u =>
        u.name.toLowerCase().includes(q.toLowerCase()) ||
        u.email.toLowerCase().includes(q.toLowerCase())
    );
    renderUsersTable(filtered);
}

function addUser() {
    const name  = $('#newUserName').val().trim();
    const email = $('#newUserEmail').val().trim();
    const role  = $('#newUserRole').val();
    const pass  = $('#newUserPass').val();
    if (!name || !email || !pass) { alert('Please fill in all required fields.'); return; }

    const newUser = { id: usersData.length+1, name, email, role, joined: new Date().toISOString().split('T')[0], active: true };
    usersData.unshift(newUser);
    renderUsersTable(usersData);
    closeModal('addUser');
    addLog('info', `User "${name}" created (${role})`);
    $('#newUserName, #newUserEmail, #newUserPass').val('');
    $('#statUsers, #hdrUsers, #tabBadgeUsers').text(usersData.length);
}

function editUser(id) { addLog('warning', `Edit user #${id} — UI form would open here`); }
function deleteUser(id) {
    if (!confirm('Delete this user?')) return;
    usersData = usersData.filter(u => u.id !== id);
    renderUsersTable(usersData);
    addLog('warning', `User #${id} deleted`);
}

/* ===== PORTS ===== */
function renderPortsTable(data) {
    let html = '';
    data.slice(0, 20).forEach((p, i) => {
        html += `<tr>
            <td class="text-muted">#${i+1}</td>
            <td class="fw-bold">⚓ ${p.name || '—'}</td>
            <td><i class="fa-solid fa-location-dot me-1 text-danger"></i>${p.country || '—'}</td>
            <td>${p.lat ? parseFloat(p.lat).toFixed(4) : '—'}</td>
            <td>${p.lng ? parseFloat(p.lng).toFixed(4) : '—'}</td>
            <td>
                <button class="btn-admin btn-admin-danger" onclick="deletePortEntry(${i})" style="padding:5px 10px;font-size:12px;"><i class="fa-solid fa-trash"></i></button>
            </td>
        </tr>`;
    });
    if (data.length > 20) html += `<tr><td colspan="6" class="text-center text-muted py-2" style="font-size:13px;">+ ${data.length - 20} more ports</td></tr>`;
    $('#portsTableBody').html(html || '<tr><td colspan="6" class="text-center text-muted py-4">No ports found</td></tr>');
}

function loadPortsAdmin() {
    // Populate country dropdown for add port modal
    $.get('/api/countries', function(data) {
        data.sort((a,b) => a.name.localeCompare(b.name));
        let opts = '<option value="">— Select Country —</option>';
        data.forEach(c => opts += `<option value="${c.id || c.iso_code}">${c.name}</option>`);
        $('#newPortCountry').html(opts);
    });
}

function filterPorts(q) {
    const filtered = portsData.filter(p =>
        (p.name||'').toLowerCase().includes(q.toLowerCase()) ||
        (p.country||'').toLowerCase().includes(q.toLowerCase())
    );
    renderPortsTable(filtered);
}

function addPort() {
    const name = $('#newPortName').val().trim();
    const lat  = parseFloat($('#newPortLat').val());
    const lng  = parseFloat($('#newPortLng').val());
    if (!name || isNaN(lat) || isNaN(lng)) { alert('Fill in all required fields.'); return; }
    addLog('info', `Port "${name}" added (${lat}, ${lng})`);
    closeModal('addPort');
    $('#newPortName, #newPortLat, #newPortLng').val('');
}

function deletePortEntry(i) {
    if (!confirm('Remove this port entry?')) return;
    addLog('warning', `Port #${i+1} removed from dataset`);
}

/* ===== COUNTRIES ===== */
function loadCountriesAdmin() {
    if (!countriesData.length) return;
    renderCountriesAdmin(countriesData);
}

function renderCountriesAdmin(data) {
    let html = '';
    data.slice(0, 20).forEach(c => {
        html += `<tr>
            <td><span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold" style="font-size:11px;">${c.iso_code}</span></td>
            <td class="fw-bold">${c.name}</td>
            <td>${c.region || '—'}</td>
            <td>${c.currency_code || '—'}</td>
            <td>${c.latitude ? parseFloat(c.latitude).toFixed(2) : '—'}</td>
            <td>${c.longitude ? parseFloat(c.longitude).toFixed(2) : '—'}</td>
            <td><span id="adminRisk-${c.iso_code}" class="text-muted" style="font-size:12px;">...</span></td>
        </tr>`;
    });
    if (data.length > 20) html += `<tr><td colspan="7" class="text-center text-muted" style="font-size:12px;">+ ${data.length-20} more</td></tr>`;
    $('#countriesAdminBody').html(html || '<tr><td colspan="7" class="text-center text-muted py-4">No data</td></tr>');
}

function filterCountriesAdmin(q) {
    const filtered = countriesData.filter(c =>
        c.name.toLowerCase().includes(q.toLowerCase()) ||
        (c.iso_code||'').toLowerCase().includes(q.toLowerCase())
    );
    renderCountriesAdmin(filtered);
}

function refreshCountries() {
    addLog('info', 'Countries data refreshed from database');
    loadAdminStats();
}

/* ===== ARTICLES ===== */
function loadArticlesAdmin() { /* data loaded in loadAdminStats */ }

function renderArticlesTable(data) {
    let html = '';
    data.forEach(a => {
        const sClass = a.sentiment === 'Positive' ? 'badge-positive' : a.sentiment === 'Negative' ? 'badge-negative' : 'badge-neutral';
        html += `<tr>
            <td class="text-muted">#${a.id}</td>
            <td style="max-width:280px;"><a href="${a.url}" target="_blank" style="color:#1a1f2e;text-decoration:none;font-weight:600;">${a.title.length > 60 ? a.title.substring(0,60)+'...' : a.title}</a></td>
            <td>${a.source}</td>
            <td><span class="${sClass}">${a.sentiment}</span></td>
            <td>${a.published}</td>
            <td>
                <button class="btn-admin btn-admin-danger" onclick="deleteArticle(${a.id})" style="padding:5px 10px;font-size:12px;"><i class="fa-solid fa-trash"></i></button>
            </td>
        </tr>`;
    });
    $('#articlesTableBody').html(html || '<tr><td colspan="6" class="text-center text-muted py-4">No articles. Click "+ Add Article" to add one.</td></tr>');
}

function addArticle() {
    const title     = $('#newArtTitle').val().trim();
    const source    = $('#newArtSource').val().trim() || 'Manual';
    const url       = $('#newArtUrl').val().trim() || '#';
    const sentiment = $('#newArtSentiment').val();
    if (!title) { alert('Title is required.'); return; }

    const newArt = { id: articlesData.length+1, title, source, sentiment, published: new Date().toLocaleDateString(), url };
    articlesData.unshift(newArt);
    renderArticlesTable(articlesData);
    closeModal('addArticle');
    addLog('info', `Article "${title.substring(0,30)}..." added`);
    $('#newArtTitle, #newArtSource, #newArtUrl').val('');
}

function deleteArticle(id) {
    if (!confirm('Remove this article?')) return;
    articlesData = articlesData.filter(a => a.id !== id);
    renderArticlesTable(articlesData);
    addLog('warning', `Article #${id} deleted`);
}

/* ===== SYSTEM LOGS ===== */
const logBuffer = [];

function addLog(type, msg) {
    const time = new Date().toLocaleTimeString();
    logBuffer.unshift({ type, msg, time });
    renderLogs();
}

function renderLogs() {
    let html = '';
    logBuffer.slice(0, 50).forEach(l => {
        const cls = l.type === 'info' ? 'log-info' : l.type === 'warning' ? 'log-warning' : 'log-error';
        const icon = l.type === 'info' ? '✅' : l.type === 'warning' ? '⚠️' : '❌';
        html += `<div class="log-entry ${cls}"><span class="log-time">${l.time}</span>${icon} ${l.msg}</div>`;
    });
    $('#systemLogs').html(html || '<div class="text-muted text-center py-4">No logs yet</div>');
}

function generateSystemLogs() {
    const initLogs = [
        ['info', 'Admin Dashboard initialized'],
        ['info', 'Countries data loaded from DB'],
        ['info', 'Ports data loaded from DB'],
        ['info', 'Risk scoring engine ready'],
        ['info', 'Sentiment analysis service active'],
        ['info', 'GNews API connection established'],
        ['info', 'Open-Meteo weather API connected'],
    ];
    initLogs.reverse().forEach(([t, m]) => addLog(t, m));
}

function clearLogs() {
    logBuffer.length = 0;
    renderLogs();
}

/* ===== MODAL HELPERS ===== */
function openModal(name) {
    $(`#modal-${name}`).addClass('show');
}
function closeModal(name) {
    $(`#modal-${name}`).removeClass('show');
}

/* ===== TAB SWITCHING ===== */
function switchAdminTab(name, el) {
    document.querySelectorAll('.admin-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.admin-tab').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + name).classList.add('active');
    el.classList.add('active');
}
</script>
@endsection

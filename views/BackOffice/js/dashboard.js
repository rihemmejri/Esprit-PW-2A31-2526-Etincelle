// ========== SIDEBAR TOGGLE ==========
const menuToggle = document.querySelector('.menu-toggle');
const sidebar = document.querySelector('.sidebar');

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
}

// ========== PAGE NAVIGATION ==========
const navItems = document.querySelectorAll('.nav-item');
const pageContent = document.getElementById('pageContent');

// Dashboard data
let users = [
    { id: 1, nom: 'Mejri', prenom: 'Ryhem', email: 'ryhem@nutriloop.com', role: 'ADMIN', statut: 'ACTIF' },
    { id: 2, nom: 'Ben Ali', prenom: 'Sarra', email: 'sarra@test.com', role: 'USER', statut: 'ACTIF' }
];

let produits = [
    { id: 1, nom: 'Pomme Bio', categorie: 'Fruit', origine: 'local', saison: 'automne' },
    { id: 2, nom: 'Saumon', categorie: 'Poisson', origine: 'importe', saison: 'ete' }
];

let recettes = [
    { id: 1, nom: 'Salade Quinoa', difficulte: 'FACILE', temps: 15, type: 'DEJEUNER' }
];

let events = [
    { id: 1, titre: 'Atelier Zéro Déchet', date: '2025-05-10', places: 30, restantes: 12 }
];

// Render functions
function renderDashboard() {
    pageContent.innerHTML = `
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-title">Utilisateurs</div>
                <div class="stat-number">${users.length}</div>
                <div class="stat-change positive"><i class="fas fa-arrow-up"></i> +12%</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-boxes stat-icon"></i>
                <div class="stat-title">Produits</div>
                <div class="stat-number">${produits.length}</div>
                <div class="stat-change positive"><i class="fas fa-arrow-up"></i> +5%</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-utensils stat-icon"></i>
                <div class="stat-title">Recettes</div>
                <div class="stat-number">${recettes.length}</div>
                <div class="stat-change positive"><i class="fas fa-arrow-up"></i> +8%</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar stat-icon"></i>
                <div class="stat-title">Événements</div>
                <div class="stat-number">${events.length}</div>
                <div class="stat-change negative"><i class="fas fa-arrow-down"></i> -2%</div>
            </div>
        </div>
        <div class="data-table">
            <h3>Activité récente</h3>
            <canvas id="activityChart" height="100"></canvas>
        </div>
    `;
    
    // Chart.js chart
    const ctx = document.getElementById('activityChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [{
                    label: 'Connexions',
                    data: [45, 52, 48, 61, 55, 42, 38],
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76,175,80,0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
}

function renderUsers() {
    let html = `
        <div class="data-table">
            <div class="table-header">
                <h3><i class="fas fa-users"></i> Gestion des Utilisateurs</h3>
                <button class="btn-add" onclick="openUserModal()">+ Ajouter</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Nom complet</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    users.forEach(u => {
        html += `
            <tr>
                <td>${u.id}</td>
                <td>${u.prenom} ${u.nom}</td>
                <td>${u.email}</td>
                <td>${u.role}</td>
                <td><span class="badge ${u.statut === 'ACTIF' ? 'badge-success' : 'badge-danger'}">${u.statut}</span></td>
                <td class="action-buttons">
                    <button class="btn-edit" onclick="editUser(${u.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn-delete" onclick="deleteUser(${u.id})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
    });
    
    html += `</tbody></table></div>`;
    pageContent.innerHTML = html;
}

function renderProducts() {
    let html = `
        <div class="data-table">
            <div class="table-header">
                <h3><i class="fas fa-boxes"></i> Gestion des Produits</h3>
                <button class="btn-add" onclick="openProductModal()">+ Ajouter</button>
            </div>
            <table>
                <thead>
                    <tr><th>ID</th><th>Nom</th><th>Catégorie</th><th>Origine</th><th>Saison</th><th>Actions</th></tr>
                </thead>
                <tbody>
    `;
    
    produits.forEach(p => {
        html += `
            <tr>
                <td>${p.id}</td>
                <td>${p.nom}</td>
                <td>${p.categorie}</td>
                <td>${p.origine}</td>
                <td>${p.saison}</td>
                <td class="action-buttons">
                    <button class="btn-edit" onclick="editProduct(${p.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn-delete" onclick="deleteProduct(${p.id})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
    });
    
    html += `</tbody></table></div>`;
    pageContent.innerHTML = html;
}

function renderRecipes() {
    let html = `
        <div class="data-table">
            <div class="table-header">
                <h3><i class="fas fa-utensils"></i> Gestion des Recettes</h3>
                <button class="btn-add" onclick="openRecipeModal()">+ Ajouter</button>
            </div>
            <table>
                <thead>
                    <tr><th>ID</th><th>Nom</th><th>Difficulté</th><th>Temps (min)</th><th>Type</th><th>Actions</th></tr>
                </thead>
                <tbody>
    `;
    
    recettes.forEach(r => {
        html += `
            <tr>
                <td>${r.id}</td>
                <td>${r.nom}</td>
                <td>${r.difficulte}</td>
                <td>${r.temps}</td>
                <td>${r.type}</td>
                <td class="action-buttons">
                    <button class="btn-edit" onclick="editRecipe(${r.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn-delete" onclick="deleteRecipe(${r.id})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
    });
    
    html += `</tbody></table></div>`;
    pageContent.innerHTML = html;
}

function renderEvents() {
    let html = `
        <div class="data-table">
            <div class="table-header">
                <h3><i class="fas fa-calendar-alt"></i> Gestion des Événements</h3>
                <button class="btn-add" onclick="openEventModal()">+ Ajouter</button>
            </div>
            <table>
                <thead>
                    <tr><th>ID</th><th>Titre</th><th>Date</th><th>Places</th><th>Restantes</th><th>Actions</th></tr>
                </thead>
                <tbody>
    `;
    
    events.forEach(e => {
        html += `
            <tr>
                <td>${e.id}</td>
                <td>${e.titre}</td>
                <td>${e.date}</td>
                <td>${e.places}</td>
                <td>${e.restantes}</td>
                <td class="action-buttons">
                    <button class="btn-edit" onclick="editEvent(${e.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn-delete" onclick="deleteEvent(${e.id})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
    });
    
    html += `</tbody></table></div>`;
    pageContent.innerHTML = html;
}

function renderNutrition() {
    pageContent.innerHTML = `
        <div class="data-table">
            <h3><i class="fas fa-apple-alt"></i> Nutrition Smart</h3>
            <p>Analyse nutritionnelle et recommandations IA.</p>
            <canvas id="nutritionChart" height="200"></canvas>
        </div>
    `;
    
    const ctx = document.getElementById('nutritionChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Glucides', 'Protéines', 'Lipides'],
                datasets: [{
                    data: [45, 30, 25],
                    backgroundColor: ['#4CAF50', '#003366', '#FFC107']
                }]
            }
        });
    }
}

function renderTracking() {
    pageContent.innerHTML = `
        <div class="data-table">
            <h3><i class="fas fa-chart-simple"></i> Suivi & Objectifs</h3>
            <canvas id="trackingChart" height="200"></canvas>
        </div>
    `;
    
    const ctx = document.getElementById('trackingChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
                datasets: [{
                    label: 'Poids (kg)',
                    data: [72, 71.5, 70.8, 70.2],
                    borderColor: '#4CAF50'
                }]
            }
        });
    }
}

// Navigation
navItems.forEach(item => {
    item.addEventListener('click', () => {
        navItems.forEach(nav => nav.classList.remove('active'));
        item.classList.add('active');
        
        const page = item.getAttribute('data-page');
        
        switch(page) {
            case 'dashboard': renderDashboard(); break;
            case 'users': renderUsers(); break;
            case 'products': renderProducts(); break;
            case 'recipes': renderRecipes(); break;
            case 'events': renderEvents(); break;
            case 'nutrition': renderNutrition(); break;
            case 'tracking': renderTracking(); break;
            default: renderDashboard();
        }
    });
});

// Modal functions
const modal = document.getElementById('modal');
const modalTitle = document.getElementById('modalTitle');
const modalBody = document.getElementById('modalBody');
const modalClose = document.querySelector('.modal-close');
const btnCancel = document.querySelector('.btn-cancel');

function closeModal() {
    modal.style.display = 'none';
}

modalClose?.addEventListener('click', closeModal);
btnCancel?.addEventListener('click', closeModal);

window.openUserModal = () => {
    modalTitle.innerText = 'Ajouter un utilisateur';
    modalBody.innerHTML = `
        <div class="form-group"><label>Prénom</label><input type="text" id="prenom" placeholder="Prénom"></div>
        <div class="form-group"><label>Nom</label><input type="text" id="nom" placeholder="Nom"></div>
        <div class="form-group"><label>Email</label><input type="email" id="email" placeholder="Email"></div>
        <div class="form-group"><label>Rôle</label><select id="role"><option>USER</option><option>ADMIN</option></select></div>
        <div class="form-group"><label>Statut</label><select id="statut"><option>ACTIF</option><option>INACTIF</option></select></div>
    `;
    modal.style.display = 'flex';
    document.querySelector('.btn-save').onclick = () => {
        const newUser = {
            id: users.length + 1,
            prenom: document.getElementById('prenom').value,
            nom: document.getElementById('nom').value,
            email: document.getElementById('email').value,
            role: document.getElementById('role').value,
            statut: document.getElementById('statut').value
        };
        users.push(newUser);
        closeModal();
        renderUsers();
    };
};

window.deleteUser = (id) => {
    if(confirm('Supprimer cet utilisateur ?')) {
        users = users.filter(u => u.id !== id);
        renderUsers();
    }
};

// Initialize with dashboard
renderDashboard();

// Close modal on outside click
window.onclick = (e) => {
    if (e.target === modal) closeModal();
};
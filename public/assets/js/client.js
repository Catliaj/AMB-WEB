// ---------------------------
// Global State
// ---------------------------
let allProperties = [];            // All properties fetched from server
let filteredProperties = [];       // Filtered list for browse
let favorites = new Set();         // Favorite property IDs
let currentModalIndex = 0;         // Current property shown in modal
let currentImageIndex = 0;         // Current image in modal carousel
let currentModalProperty = null; // store globally


// ---------------------------
// Theme Management
// ---------------------------
function initTheme() {
    const savedTheme = localStorage.getItem("theme") || "light";
    document.documentElement.setAttribute("data-theme", savedTheme);
    updateThemeIcons(savedTheme);

    const themeSwitch = document.getElementById("themeSwitch");
    if (themeSwitch) themeSwitch.checked = savedTheme === "dark";
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute("data-theme") || "light";
    const newTheme = currentTheme === "dark" ? "light" : "dark";
    document.documentElement.setAttribute("data-theme", newTheme);
    localStorage.setItem("theme", newTheme);
    updateThemeIcons(newTheme);

    const themeSwitch = document.getElementById("themeSwitch");
    if (themeSwitch) themeSwitch.checked = newTheme === "dark";
}

function updateThemeIcons(theme) {
    const icons = document.querySelectorAll("#themeIcon, #themeIconMobile");
    icons.forEach(icon => {
        icon.className = theme === "dark" ? "bi bi-sun-fill fs-5" : "bi bi-moon-fill fs-5";
    });
}

// ---------------------------
// Navbar Scroll Effect
// ---------------------------
document.addEventListener("scroll", () => {
    const navbar = document.getElementById("mainNav");
    if (!navbar) return;
    navbar.classList.toggle("scrolled", window.scrollY > 50);
});

// ---------------------------
// Navigation Highlighter
// ---------------------------
function setActiveNav() {
    const links = document.querySelectorAll('.nav-link-custom');
    const current = window.location.pathname.split('/').pop() || 'homepage.html';
    links.forEach(link => {
        const href = (link.getAttribute('href') || '').split('/').pop();
        link.classList.toggle('active', href === current);
    });
}

// ---------------------------
// AJAX Load Properties
// ---------------------------
function loadProperties() {
    const container = document.getElementById('propertiesContainer');
    if (!container) return;

    fetch(propertiesUrl)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok: ' + response.status);
            return response.json();
        })
        .then(data => {
            allProperties = data; // store globally
            filteredProperties = [...allProperties]; // default filter
            renderPropertiesGrid();
        })
        .catch(error => {
            console.error('Error fetching properties:', error);
            container.innerHTML = `<p class="text-center text-danger">Failed to load properties.</p>`;
        });
}

// ---------------------------
// Render Functions
// ---------------------------
function renderPropertyCard(property) {
    const isFavorite = favorites.has(property.id);
    return `
        <div class="col-md-6 col-lg-4">
            <div class="property-card" onclick="openPropertyDetails(${property.id})">
                <div class="property-image position-relative">
                    <img src="${property.image || 'uploads/properties/no-image.jpg'}" alt="${property.title}" loading="lazy">
                    <button class="property-favorite ${isFavorite ? 'active' : ''}" onclick="event.stopPropagation(); toggleFavorite(${property.id})">
                        <i class="bi ${isFavorite ? 'bi-heart-fill' : 'bi-heart'} fs-5"></i>
                    </button>
                    <span class="property-type-badge">${property.type}</span>
                </div>
                <div class="card-body">
                    <h3 class="h5 mb-2">${property.title}</h3>
                    <div class="d-flex align-items-center mb-3 text-muted">
                        <i class="bi bi-geo-alt text-accent me-2"></i>
                        <small>${property.location}</small>
                    </div>
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-house-door me-1 text-muted"></i>
                            <small class="text-muted">${property.beds}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-droplet me-1 text-muted"></i>
                            <small class="text-muted">${property.baths}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrows-fullscreen me-1 text-muted"></i>
                            <small class="text-muted">${property.sqft} sqft</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="property-price">${property.price}</span>
                        <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); openPropertyDetails(${property.id})">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderPropertiesGrid() {
    const container = document.getElementById('propertiesContainer') || document.getElementById('property-list');
    if (!container) return;

    if (filteredProperties.length === 0) {
        container.innerHTML = `<p class="text-center text-muted">No properties found.</p>`;
        return;
    }

    container.innerHTML = `<div class="row g-4">${filteredProperties.map(p => renderPropertyCard(p)).join('')}</div>`;
}

// ---------------------------
// Favorites
// ---------------------------
function toggleFavorite(propertyId) {
    if (favorites.has(propertyId)) favorites.delete(propertyId);
    else favorites.add(propertyId);

    updateAllFavoriteButtons();
    renderPropertiesGrid();
}

function updateAllFavoriteButtons() {
    document.querySelectorAll('.property-favorite').forEach(btn => {
        const onclickAttr = btn.getAttribute('onclick');
        const match = onclickAttr?.match(/toggleFavorite\((\d+)\)/);
        if (!match) return;
        const propertyId = parseInt(match[1]);
        const icon = btn.querySelector('i');
        if (favorites.has(propertyId)) {
            btn.classList.add('active');
            icon.className = 'bi bi-heart-fill fs-5';
        } else {
            btn.classList.remove('active');
            icon.className = 'bi bi-heart fs-5';
        }
    });
}
// ---------------------------
// Modal
// ---------------------------
function openPropertyDetails(propertyId) {
    fetch(`${propertiesViewUrl}/${propertyId}`)
        .then(res => res.json())
        .then(property => {
            if (property.error) {
                console.error(property.error);
                return;
            }

            currentModalProperty = property;
            currentModalIndex = allProperties.findIndex(p => p.id === propertyId);
            currentImageIndex = 0;

            updatePropertyModal(property);

            const modalEl = document.getElementById('propertyDetailsModal');
            if (!modalEl) return;
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        })
        .catch(err => console.error('Error fetching property:', err));
}

function updatePropertyModal(property) {
    if (!property) return;

    // ✅ Normalize property fields (support uppercase/lowercase)
    const propertyId = property.id || property.PropertyID;
    const title = property.Title || 'N/A';
    const location = property.Location || 'N/A';
    const price = property.Price || 'N/A';
    const beds = property.Bedrooms || 'N/A';
    const baths = property.Bathrooms || 'N/A';
    const sqft = property.Size || 'N/A';
    const type = property.Property_Type || 'N/A';
    const images = property.images || property.Images || [];

    // ✅ Update modal text content
    document.getElementById('modalPropertyTitle').textContent = title;
    document.getElementById('modalPropertyLocation').textContent = location;
    document.getElementById('modalPropertyPrice').textContent = price;
    document.getElementById('modalPropertyBeds').textContent = beds;
    document.getElementById('modalPropertyBaths').textContent = baths;
    document.getElementById('modalPropertySqft').textContent = sqft;
    document.getElementById('modalPropertyType').textContent = type;

    // ✅ Update property image
    const imgEl = document.getElementById('modalPropertyImage');
    imgEl.src = images[0] || 'uploads/properties/no-image.jpg';

    // ✅ Favorite button
    const favBtn = document.getElementById('modalFavoriteBtn');
    const icon = favBtn?.querySelector('i');
    if (favBtn && icon) {
        if (favorites.has(propertyId)) {
            favBtn.classList.add('active');
            icon.className = 'bi bi-heart-fill fs-5';
        } else {
            favBtn.classList.remove('active');
            icon.className = 'bi bi-heart fs-5';
        }
        favBtn.onclick = (e) => {
            e.stopPropagation();
            toggleFavorite(propertyId);
            updatePropertyModal(property);
        };
    }

const chatBtn = document.getElementById('modalChatBtn');

if (chatBtn) {
    chatBtn.onclick = async () => {
        if (!propertyId) {
            console.error('Property ID is missing!', property);
            Swal.fire({
                title: 'Error',
                text: 'Cannot start chat: property ID missing.',
                icon: 'error'
            });
            return;
        }

        console.log('Starting chat with property ID:', propertyId);

        try {
            const response = await fetch('/chat/startSession', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ PropertyID: propertyId })
            });

            const data = await response.json();
            console.log('Chat response:', data);

            if (data.status === 'success') {
                // SweetAlert confirmation with agent & property info
                Swal.fire({
                    title: 'Message Sent!',
                    text: `Your message regarding "${data.propertyTitle}" has been sent to ${data.agentName}.`,
                    icon: 'success',
                    confirmButtonText: 'Go to Chat'
                }).then(() => {
                    window.location.href = '/users/chat';
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.error || 'Failed to start chat session',
                    icon: 'error'
                });
            }

        } catch (err) {
            console.error('Fetch error:', err);
            Swal.fire({
                title: 'Error',
                text: 'Something went wrong while starting chat.',
                icon: 'error'
            });
        }
    };
}



}


// ---------------------------
// Modal Image Navigation
// ---------------------------
function navigateProperty(step) {
    if (!currentModalProperty || !currentModalProperty.images || currentModalProperty.images.length === 0) return;
    currentImageIndex = (currentImageIndex + step + currentModalProperty.images.length) % currentModalProperty.images.length;
    document.getElementById('modalPropertyImage').src = currentModalProperty.images[currentImageIndex];
}


// ---------------------------
// Chat
// ---------------------------
function openChatWithAgent(agentName, propertyId) {
    toggleChat();
    const chatInput = document.getElementById('chatInput');
    chatInput.value = `Hello ${agentName}, I am interested in Property ID #${propertyId}`;
    chatInput.focus();
}

// ---------------------------
// Filters
// ---------------------------
function applyFilters() {
    const searchEl = document.getElementById('searchFilter');
    const typeEl = document.getElementById('typeFilter');
    const bedroomsEl = document.getElementById('bedroomsFilter');
    const searchQuery = searchEl?.value.toLowerCase() || '';
    const typeFilter = typeEl?.value || 'all';
    const bedroomsFilter = bedroomsEl?.value || 'all';

    filteredProperties = allProperties.filter(p => {
        const matchesSearch = !searchQuery ||
            p.title.toLowerCase().includes(searchQuery) ||
            p.location.toLowerCase().includes(searchQuery);
        const matchesType = typeFilter === 'all' || p.type === typeFilter;
        const matchesBeds = bedroomsFilter === 'all' || p.beds === parseInt(bedroomsFilter);
        return matchesSearch && matchesType && matchesBeds;
    });

    renderPropertiesGrid();
}

// ---------------------------
// Init on DOM Loaded
// ---------------------------
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    setActiveNav();
    loadProperties(); // Load properties via AJAX

    // Auto-load session if initialSessionId exists
    const initialSessionId = '<?= $initialSessionId ?? null ?>';
    if (initialSessionId) {
        const sessionElement = document.querySelector(`.chat-item[data-session-id='${initialSessionId}']`);
        if (sessionElement) sessionElement.click();
    }
});

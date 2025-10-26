// Property Data (same as before)
const allProperties = [
    {
        id: 1,
        image: "https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080",
        title: "Modern Family House",
        location: "Beverly Hills, CA",
        price: "$2,500,000",
        beds: 4,
        baths: 3,
        sqft: 3200,
        type: "For Sale"
    },
    {
        id: 2,
        image: "https://images.unsplash.com/photo-1715985160020-d8cd6fdc8ba9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080",
        title: "Luxury Penthouse",
        location: "Manhattan, NY",
        price: "$12,000/mo",
        beds: 3,
        baths: 2,
        sqft: 2400,
        type: "For Rent"
    },
    {
        id: 3,
        image: "https://images.unsplash.com/photo-1591268193431-c86baf208255?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080",
        title: "Garden Villa Estate",
        location: "Miami, FL",
        price: "$4,200,000",
        beds: 5,
        baths: 4,
        sqft: 4800,
        type: "For Sale"
    },
    {
        id: 4,
        image: "https://images.unsplash.com/photo-1697220214526-7c06c06b3c58?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080",
        title: "Contemporary Loft",
        location: "San Francisco, CA",
        price: "$8,500/mo",
        beds: 2,
        baths: 2,
        sqft: 1800,
        type: "For Rent"
    },
    {
        id: 5,
        image: "https://images.unsplash.com/photo-1707075108813-edefd7b3308d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080",
        title: "Beachfront Paradise",
        location: "Malibu, CA",
        price: "$6,800,000",
        beds: 4,
        baths: 3,
        sqft: 3600,
        type: "For Sale"
    },
    {
        id: 6,
        image: "https://images.unsplash.com/photo-1676230087975-14bde0752bc6?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080",
        title: "City Penthouse Suite",
        location: "Chicago, IL",
        price: "$3,900,000",
        beds: 3,
        baths: 3,
        sqft: 2900,
        type: "For Sale"
    }
];

const bookingsData = [
    {
        id: 1,
        propertyImage: "https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080",
        propertyName: "Modern Family House",
        location: "Beverly Hills, CA",
        bookingDate: "Oct 18, 2025",
        viewingTime: "2:00 PM - 3:00 PM",
        status: "confirmed",
        bookingId: "BK001"
    },
    {
        id: 2,
        propertyImage: "https://images.unsplash.com/photo-1715985160020-d8cd6fdc8ba9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080",
        propertyName: "Luxury Penthouse",
        location: "Manhattan, NY",
        bookingDate: "Oct 20, 2025",
        viewingTime: "11:00 AM - 12:00 PM",
        status: "confirmed",
        bookingId: "BK002"
    },
    {
        id: 3,
        propertyImage: "https://images.unsplash.com/photo-1591268193431-c86baf208255?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080",
        propertyName: "Garden Villa Estate",
        location: "Miami, FL",
        bookingDate: "Oct 12, 2025",
        viewingTime: "3:00 PM - 4:00 PM",
        status: "completed",
        bookingId: "BK003"
    },
    {
        id: 4,
        propertyImage: "https://images.unsplash.com/photo-1707075108813-edefd7b3308d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080",
        propertyName: "Beachfront Paradise",
        location: "Malibu, CA",
        bookingDate: "Oct 10, 2025",
        viewingTime: "10:00 AM - 11:00 AM",
        status: "cancelled",
        bookingId: "BK004"
    }
];

document.addEventListener("scroll", () => {
    const navbar = document.getElementById("mainNav");
    if (window.scrollY > 50) {
        navbar.classList.add("scrolled");
    } else {
        navbar.classList.remove("scrolled");
    }
});

// State
let favorites = new Set();
let filteredProperties = [...allProperties];
let chatMessages = [
    {
        id: 1,
        text: "Hello! How can I help you find your dream property today?",
        sender: "agent",
        time: "10:30 AM"
    }
];

// Theme Management
function initTheme() {
    const savedTheme = localStorage.getItem("theme") || "light";
    document.documentElement.setAttribute("data-theme", savedTheme);
    updateThemeIcons(savedTheme);

    // If theme switch exists on page, set its checked state
    const themeSwitch = document.getElementById("themeSwitch");
    if (themeSwitch) themeSwitch.checked = savedTheme === "dark";
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute("data-theme") || "light";
    const newTheme = currentTheme === "dark" ? "light" : "dark";
    document.documentElement.setAttribute("data-theme", newTheme);
    localStorage.setItem("theme", newTheme);
    updateThemeIcons(newTheme);

    // Keep the profile switch in sync if present
    const themeSwitch = document.getElementById("themeSwitch");
    if (themeSwitch) themeSwitch.checked = newTheme === "dark";
}

function updateThemeIcons(theme) {
    const icons = document.querySelectorAll("#themeIcon, #themeIconMobile");
    icons.forEach(icon => {
        if (theme === "dark") {
            icon.className = "bi bi-sun-fill fs-5";
        } else {
            icon.className = "bi bi-moon-fill fs-5";
        }
    });
}

// Nav active link highlighter for multipage
function setActiveNav() {
    const links = document.querySelectorAll('.nav-link-custom');
    const current = window.location.pathname.split('/').pop() || 'homepage.html';
    links.forEach(link => {
        const href = (link.getAttribute('href') || '').split('/').pop();
        if (href === current) link.classList.add('active');
        else link.classList.remove('active');
    });
}

// Favorites
function toggleFavorite(propertyId) {
    if (favorites.has(propertyId)) {
        favorites.delete(propertyId);
    } else {
        favorites.add(propertyId);
    }

    // On the browse page we re-render grid to update hearts
    if (document.getElementById('propertiesContainer')) {
        renderPropertiesGrid();
    } else {
        // Update favorite buttons on homepage/other pages
        updateAllFavoriteButtons();
    }
}

// Add this new function right after toggleFavorite
function updateAllFavoriteButtons() {
    document.querySelectorAll('.property-favorite').forEach(btn => {
        // Extract property ID from the onclick attribute
        const onclickAttr = btn.getAttribute('onclick');
        const match = onclickAttr.match(/toggleFavorite\((\d+)\)/);

        if (match) {
            const propertyId = parseInt(match[1]);
            const icon = btn.querySelector('i');

            if (favorites.has(propertyId)) {
                btn.classList.add('active');
                icon.className = 'bi bi-heart-fill fs-5';
            } else {
                btn.classList.remove('active');
                icon.className = 'bi bi-heart fs-5';
            }
        }
    });
}

// Opens modal and stores the index
function openPropertyDetails(propertyId) {
  const property = allProperties.find(p => p.id === propertyId);
  if (!property) return;

  currentModalIndex = allProperties.indexOf(property);
  updatePropertyModal(property);

  const modalEl = document.getElementById('propertyDetailsModal');
  const modal = new bootstrap.Modal(modalEl);
  modal.show();
}

// Updates modal content
function updatePropertyModal(property) {
  if (!property) return;

  const img = document.getElementById('modalPropertyImage');
  const title = document.getElementById('modalPropertyTitle');
  const locationEl = document.getElementById('modalPropertyLocation');
  const priceEl = document.getElementById('modalPropertyPrice');
  const bedsEl = document.getElementById('modalPropertyBeds');
  const bathsEl = document.getElementById('modalPropertyBaths');
  const sqftEl = document.getElementById('modalPropertySqft');
  const typeEl = document.getElementById('modalPropertyType');

  img.src = property.image;
  title.textContent = property.title;
  locationEl.textContent = property.location;
  priceEl.textContent = property.price;
  bedsEl.textContent = property.beds;
  bathsEl.textContent = property.baths;
  sqftEl.textContent = property.sqft;
  typeEl.textContent = property.type;

  // Heart / favorite state
  const favBtn = document.getElementById('modalFavoriteBtn');
  const icon = favBtn.querySelector('i');
  if (favorites.has(property.id)) {
    favBtn.classList.add('active');
    icon.className = 'bi bi-heart-fill fs-5';
  } else {
    favBtn.classList.remove('active');
    icon.className = 'bi bi-heart fs-5';
  }

  // Subtle slide animation
  const modalContent = document.querySelector('.property-modal-content');
  modalContent.classList.remove('fade-slide');
  void modalContent.offsetWidth; // trigger reflow
  modalContent.classList.add('fade-slide');
}

// Favorite toggle inside modal
function toggleFavoriteFromModal(event) {
  event.stopPropagation();
  const property = allProperties[currentModalIndex];
  toggleFavorite(property.id);
  updatePropertyModal(property);
}

// Navigation between properties
function navigateProperty(direction) {
  currentModalIndex =
    (currentModalIndex + direction + allProperties.length) % allProperties.length;
  updatePropertyModal(allProperties[currentModalIndex]);
}

// Swipe support
let startX = 0;
const modal = document.getElementById('propertyDetailsModal');
if (modal) {
  modal.addEventListener('touchstart', e => (startX = e.touches[0].clientX));
  modal.addEventListener('touchend', e => {
    const diff = startX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) navigateProperty(diff > 0 ? 1 : -1);
  });
}

// Browse rendering functions
function renderPropertyCard(property) {
    const isFavorite = favorites.has(property.id);
    return `
        <div class="col-md-6 col-lg-4">
            <div class="property-card" onclick="openPropertyDetails(${property.id})">
                <div class="property-image">
                    <img src="${property.image}" alt="${property.title}" loading="lazy">
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
    const container = document.getElementById('propertiesContainer');
    const countEl = document.getElementById('propertyCount');

    if (!container) return;

    if (filteredProperties.length === 0) {
        container.innerHTML = `
            <div class="card text-center py-5">
                <div class="card-body">
                    <p class="text-muted mb-0">No properties found matching your criteria. Try adjusting your filters.</p>
                </div>
            </div>
        `;
    } else {
        container.innerHTML = `<div class="row g-4">${filteredProperties.map(p => renderPropertyCard(p)).join('')}</div>`;
    }

    if (countEl) countEl.textContent = `${filteredProperties.length} properties available`;
}

function applyFilters() {
    const searchEl = document.getElementById('searchFilter');
    const typeEl = document.getElementById('typeFilter');
    const bedroomsEl = document.getElementById('bedroomsFilter');

    const searchQuery = searchEl ? searchEl.value.toLowerCase() : '';
    const typeFilter = typeEl ? typeEl.value : 'all';
    const bedroomsFilter = bedroomsEl ? bedroomsEl.value : 'all';

    filteredProperties = allProperties.filter(property => {
        const matchesSearch = !searchQuery ||
            property.title.toLowerCase().includes(searchQuery) ||
            property.location.toLowerCase().includes(searchQuery);

        const matchesType = typeFilter === 'all' || property.type === typeFilter;

        const matchesBedrooms = bedroomsFilter === 'all' || property.beds === parseInt(bedroomsFilter);

        return matchesSearch && matchesType && matchesBedrooms;
    });

    renderPropertiesGrid();
}

// Bookings rendering (for bookings.html)
function renderBookingCard(booking) {
    let badgeClass = '';
    if (booking.status === 'confirmed') badgeClass = 'badge-confirmed';
    else if (booking.status === 'completed') badgeClass = 'badge-completed';
    else if (booking.status === 'cancelled') badgeClass = 'badge-cancelled';

    let actions = '';
    if (booking.status === 'confirmed') {
        actions = `
            <button class="btn btn-primary btn-sm me-2" onclick="openBookingDetails(${booking.id})">View Details</button>
            <button class="btn btn-danger btn-sm">Cancel Booking</button>
        `;
    } else if (booking.status === 'completed') {
        actions = `<button class="btn btn-sm" style="background-color: var(--accent); color: white; border: none;">Leave Review</button>`;
    } else if (booking.status === 'cancelled') {
        actions = `<button class="btn btn-outline-primary btn-sm">Book Again</button>`;
    }

    return `
        <div class="booking-card">
            <div class="row g-0">
                <div class="col-md-3">
                    <img src="${booking.propertyImage}" class="booking-image" alt="${booking.propertyName}">
                </div>
                <div class="col-md-9">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h3 class="h5 mb-1">${booking.propertyName}</h3>
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-geo-alt text-accent me-2"></i>
                                    <small>${booking.location}</small>
                                </div>
                            </div>
                            <span class="badge ${badgeClass}">${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}</span>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    <small>${booking.bookingDate}</small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-clock me-2"></i>
                                    <small>${booking.viewingTime}</small>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            ${actions}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Booking Details Modal
function openBookingDetails(bookingId) {
    const booking = bookingsData.find(b => b.id === bookingId);
    if (!booking) return;

    // Find the corresponding property
    const property = allProperties.find(p => p.title === booking.propertyName);
    
    // Populate modal with booking info
    document.getElementById('bookingModalImage').src = booking.propertyImage;
    document.getElementById('bookingModalTitle').textContent = booking.propertyName;
    document.getElementById('bookingModalLocation').textContent = booking.location;
    document.getElementById('bookingModalDate').textContent = booking.bookingDate;
    document.getElementById('bookingModalTime').textContent = booking.viewingTime;
    document.getElementById('bookingModalId').textContent = booking.bookingId;
    
    // Add property details if available
    if (property) {
        document.getElementById('bookingModalBeds').textContent = property.beds;
        document.getElementById('bookingModalBaths').textContent = property.baths;
        document.getElementById('bookingModalSqft').textContent = property.sqft;
        document.getElementById('bookingModalPrice').textContent = property.price;
    }
    
    // Set status badge
    const statusBadge = document.getElementById('bookingModalStatus');
    statusBadge.textContent = booking.status.charAt(0).toUpperCase() + booking.status.slice(1);
    statusBadge.className = 'badge';
    if (booking.status === 'confirmed') statusBadge.classList.add('badge-confirmed');
    else if (booking.status === 'completed') statusBadge.classList.add('badge-completed');
    else if (booking.status === 'cancelled') statusBadge.classList.add('badge-cancelled');

    // Show modal
    const modalEl = document.getElementById('bookingDetailsModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function renderBookingsList() {
    const container = document.getElementById('bookingsList');
    if (!container) return;

    container.innerHTML = bookingsData.map(b => renderBookingCard(b)).join('');
}

// Chat functions (same behavior)
function toggleChat() {
    const chatWindow = document.getElementById('chatWindow');
    if (!chatWindow) return;
    chatWindow.classList.toggle('active');

    if (chatWindow.classList.contains('active')) renderChatMessages();
}

function renderChatMessages() {
    const chatMessagesContainer = document.getElementById('chatMessages');
    if (!chatMessagesContainer) return;

    chatMessagesContainer.innerHTML = chatMessages.map(msg => `
        <div class="chat-message">
            <div class="message-bubble ${msg.sender} ${msg.sender === 'user' ? 'ms-auto' : ''}">
                <p class="mb-1 small">${msg.text}</p>
                <div class="message-time">${msg.time}</div>
            </div>
        </div>
    `).join('');

    chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
}

function sendMessage() {
    const input = document.getElementById('chatInput');
    if (!input) return;
    const message = input.value.trim();
    if (!message) return;

    const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    chatMessages.push({
        id: chatMessages.length + 1,
        text: message,
        sender: 'user',
        time: time
    });

    renderChatMessages();
    input.value = '';

    setTimeout(() => {
        chatMessages.push({
            id: chatMessages.length + 1,
            text: 'Thank you for your message! Our team will get back to you shortly.',
            sender: 'agent',
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        });
        renderChatMessages();
    }, 1000);
}

function handleChatKeyPress(event) {
    if (event.key === 'Enter') sendMessage();
}

// Hero search functionality
function handleHeroSearch() {
    const searchInput = document.getElementById('heroSearchInput');
    const searchQuery = searchInput ? searchInput.value.trim() : '';
    
    if (searchQuery) {
        // Store the search query in sessionStorage
        sessionStorage.setItem('searchQuery', searchQuery);
        // Navigate to browse page
        window.location.href = 'browse.html';
    } else {
        // If empty, just go to browse page
        window.location.href = 'browse.html';
    }
}

// Handle Enter key press in hero search
function handleHeroSearchKeyPress(event) {
    if (event.key === 'Enter') {
        handleHeroSearch();
    }
}

// Apply search from homepage when browse page loads
function applyStoredSearch() {
    const storedQuery = sessionStorage.getItem('searchQuery');
    if (storedQuery) {
        const searchEl = document.getElementById('searchFilter');
        if (searchEl) {
            searchEl.value = storedQuery;
            // Clear the stored query
            sessionStorage.removeItem('searchQuery');
            // Apply filters to show results
            applyFilters();
        }
    }
}

// Initialization on DOM loaded
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    setActiveNav();
    renderChatMessages();

    // If we are on the browse page, render the properties
    if (document.getElementById('propertiesContainer')) {
        filteredProperties = [...allProperties];
        renderPropertiesGrid();
        // Apply stored search query if exists
        applyStoredSearch();
    }

    // If we are on the bookings page, render bookings
    if (document.getElementById('bookingsList')) {
        renderBookingsList();
    }
});



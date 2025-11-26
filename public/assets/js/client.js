// assets/js/client.js
// Rebuilt client script with agent name resolution and booking modal improvements

// ---------------------------
// Global State
// ---------------------------
let allProperties = [];            // All properties fetched from server
let filteredProperties = [];       // Filtered list for browse
let favorites = new Set();         // Favorite property IDs
let currentModalIndex = 0;         // Current property shown in modal
let currentImageIndex = 0;         // Current image in modal carousel
let currentModalProperty = null; // store globally
const myBookingsUrl = window.myBookingsUrl || '/index.php/bookings/mine';

// ---------------------------
// Agent helper (fetch + cache)
// ---------------------------
const agentCache = new Map();

async function getAgentInfo(agentId) {
  if (!agentId) return null;
  if (agentCache.has(agentId)) return agentCache.get(agentId);

  const base = window.getUserUrlBase || '/index.php/users/getUser';
  const url = `${base}/${encodeURIComponent(agentId)}`;

  try {
    const res = await fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    if (!res.ok) {
      console.warn('getAgentInfo: server returned', res.status, 'for agent', agentId);
      agentCache.set(agentId, null);
      return null;
    }
    const data = await res.json().catch(() => null);
    if (!data) {
      agentCache.set(agentId, null);
      return null;
    }
    const name = ((data.FirstName || '') + ' ' + (data.LastName || '')).trim() || (data.Email || 'Agent');
    const agent = {
      id: data.UserID ?? agentId,
      name,
      email: data.Email ?? '',
      phone: data.phone ?? data.phoneNumber ?? ''
    };
    agentCache.set(agentId, agent);
    return agent;
  } catch (err) {
    console.error('getAgentInfo error', err);
    agentCache.set(agentId, null);
    return null;
  }
}

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
            // Normalize properties to guarantee consistent fields (id, title, image, etc.)
            allProperties = (Array.isArray(data) ? data : []).map(p => ({
                id: p.id ?? p.PropertyID ?? null,
                title: p.title ?? p.Title ?? '',
                location: p.location ?? p.Location ?? '',
                type: p.type ?? p.Property_Type ?? '',
                price: p.price ?? p.Price ?? '',
                beds: p.beds ?? p.Bedrooms ?? '',
                baths: p.baths ?? p.Bathrooms ?? '',
                sqft: p.sqft ?? p.Size ?? '',
                image: (p.images && p.images[0]) || p.image || (p.Images && p.Images[0]) || (p.Image ? (p.Image.startsWith('http') ? p.Image : ('uploads/properties/' + p.Image)) : 'uploads/properties/no-image.jpg'),
                images: p.images ?? p.Images ?? [],
                agent_assigned: p.agent_assigned ?? p.Agent_Assigned ?? null,
                raw: p // keep original if needed
            }));
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
                    <img src="${property.image || 'uploads/properties/no-image.jpg'}" alt="${escapeHtml(property.title)}" loading="lazy">
                    <button class="property-favorite ${isFavorite ? 'active' : ''}" onclick="event.stopPropagation(); toggleFavorite(${property.id})">
                        <i class="bi ${isFavorite ? 'bi-heart-fill' : 'bi-heart'} fs-5"></i>
                    </button>
                    <span class="property-type-badge">${escapeHtml(property.type)}</span>
                </div>
                <div class="card-body">
                    <h3 class="h5 mb-2">${escapeHtml(property.title)}</h3>
                    <div class="d-flex align-items-center mb-3 text-muted">
                        <i class="bi bi-geo-alt text-accent me-2"></i>
                        <small>${escapeHtml(property.location)}</small>
                    </div>
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-house-door me-1 text-muted"></i>
                            <small class="text-muted">${escapeHtml(property.beds)}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-droplet me-1 text-muted"></i>
                            <small class="text-muted">${escapeHtml(property.baths)}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrows-fullscreen me-1 text-muted"></i>
                            <small class="text-muted">${escapeHtml(property.sqft)} sqft</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="property-price">${escapeHtml(property.price)}</span>
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
// Modal: Property Details
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
        .catch(err => {
            console.error('Error fetching property:', err);
            // optional UI feedback
        });
}

function updatePropertyModal(property) {
    if (!property) return;

    // Normalize fields and update modal text content
    const propertyId = property.id || property.PropertyID;
    const title = property.Title || property.title || 'N/A';
    const location = property.Location || property.location || 'N/A';
    const price = property.Price || property.price || 'N/A';
    const beds = property.Bedrooms || property.beds || 'N/A';
    const baths = property.Bathrooms || property.baths || 'N/A';
    const sqft = property.Size || property.sqft || 'N/A';
    const type = property.Property_Type || property.type || 'N/A';
    const images = property.images || property.Images || [];

    // Update modal text content
    const el = id => document.getElementById(id);
    el('modalPropertyTitle').textContent = title;
    el('modalPropertyLocation').textContent = location;
    el('modalPropertyPrice').textContent = price;
    el('modalPropertyBeds').textContent = beds;
    el('modalPropertyBaths').textContent = baths;
    el('modalPropertySqft').textContent = sqft;
    el('modalPropertyType').textContent = type;

    // Update property image
    const imgEl = document.getElementById('modalPropertyImage');
    if (imgEl) imgEl.src = images[0] || 'uploads/properties/no-image.jpg';

    // Favorite button behavior
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

    // Chat button behavior (unchanged)
    const chatBtn = document.getElementById('modalChatBtn');
    if (chatBtn) {
        chatBtn.onclick = async () => {
            if (!propertyId) {
                console.error('Property ID is missing!', property);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ title: 'Error', text: 'Cannot start chat: property ID missing.', icon: 'error' });
                } else {
                    alert('Cannot start chat: property ID missing.');
                }
                return;
            }

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
                if (data?.status === 'success') {
                    // If the server returned a chat session id, include it in the redirect
                    const sessionId = data.sessionId || data.chatSessionID || data.chatSessionId || null;
                    const target = sessionId ? `/users/chat?session=${encodeURIComponent(sessionId)}` : '/users/chat';

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Message Sent!',
                            text: `Your message regarding "${data.propertyTitle}" has been sent to ${data.agentName}.`,
                            icon: 'success',
                            confirmButtonText: 'Go to Chat'
                        }).then(() => window.location.href = target);
                    } else {
                        alert('Message sent! Redirecting to chat.');
                        window.location.href = target;
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({ title: 'Error', text: data.error || 'Failed to start chat session', icon: 'error' });
                    } else {
                        alert(data.error || 'Failed to start chat session');
                    }
                }
            } catch (err) {
                console.error('Fetch error:', err);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ title: 'Error', text: 'Something went wrong while starting chat.', icon: 'error' });
                } else {
                    alert('Something went wrong while starting chat.');
                }
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
    const el = document.getElementById('modalPropertyImage');
    if (el) el.src = currentModalProperty.images[currentImageIndex];
}

// ---------------------------
// Chat helper
// ---------------------------
function openChatWithAgent(agentName, propertyId) {
    toggleChat?.();
    const chatInput = document.getElementById('chatInput');
    if (chatInput) {
        chatInput.value = `Hello ${agentName}, I am interested in Property ID #${propertyId}`;
        chatInput.focus();
    }
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
        const title = (p.title || '').toString().toLowerCase();
        const loc = (p.location || '').toString().toLowerCase();
        const matchesSearch = !searchQuery || title.includes(searchQuery) || loc.includes(searchQuery);
        const matchesType = typeFilter === 'all' || (p.type || '') === typeFilter;
        const matchesBeds = bedroomsFilter === 'all' || (p.beds == parseInt(bedroomsFilter));
        return matchesSearch && matchesType && matchesBeds;
    });

    renderPropertiesGrid();
}

// ---------------------------
// Bookings: render & load
// ---------------------------
function renderBookingsList(bookings) {
    const container = document.getElementById('bookingsList');
    if (!container) return;

    if (!bookings || bookings.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-4">You have no bookings.</div>';
        return;
    }

    const html = bookings.map(b => {
        // Clients do not set booking dates; show placeholder instead
        const date = '—';
        const status = b.BookingStatus || b.status || 'Pending';
        // Map confirmed -> Scheduled for display
        const statusDisplay = String(status || '').toLowerCase() === 'confirmed' ? 'Scheduled' : (status ? String(status).charAt(0).toUpperCase() + String(status).slice(1) : 'Pending');
        const img = (b.Images && b.Images[0]) ? b.Images[0] : 'uploads/properties/no-image.jpg';
        const reason = b.Reason ? `<div><strong>Reason:</strong> ${escapeHtml(b.Reason)}</div>` : '';
        const notes = b.Notes ? `<div><strong>Notes:</strong> ${escapeHtml(b.Notes)}</div>` : '';

        return `
        <div class="card mb-3">
          <div class="row g-0 align-items-center">
            <div class="col-auto" style="width:140px">
              <img src="${escapeHtml(img)}" class="img-fluid rounded-start" style="height:100%; object-fit:cover;">
            </div>
            <div class="col">
              <div class="card-body">
                <h5 class="card-title mb-1">${escapeHtml(b.PropertyTitle || 'Property')}</h5>
                <p class="mb-1 text-muted small">${escapeHtml(b.PropertyLocation || '')}</p>
                <div class="mb-1"><strong>Date:</strong> ${escapeHtml(date)} &nbsp; <span class="badge ${badgeClassForStatus(status)}">${escapeHtml(statusDisplay)}</span></div>
                ${reason}
                ${notes}
              </div>
            </div>
            <div class="col-auto pe-3">
              <div class="d-flex flex-column gap-2">
                <button class="btn btn-sm btn-outline-primary" onclick="viewBookingDetails(${b.bookingID})">View</button>
                ${status.toLowerCase() === 'pending' ? `<button class="btn btn-sm btn-danger" onclick="cancelBooking(${b.bookingID})">Cancel</button>` : ''}
              </div>
            </div>
          </div>
        </div>
        `;
    }).join('');

    container.innerHTML = html;
}

async function loadMyBookings() {
    const container = document.getElementById('bookingsList');
    if (!container) return;
    container.innerHTML = '<div class="text-center py-4">Loading your bookings...</div>';

    try {
        const res = await fetch(myBookingsUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) throw new Error('Failed to fetch bookings: ' + res.status);
        const data = await res.json().catch(() => []);
        renderBookingsList(Array.isArray(data) ? data : []);
    } catch (err) {
        console.error('loadMyBookings error', err);
        container.innerHTML = `<div class="text-center text-danger">Failed to load bookings. Please try later.</div>`;
    }
}

// ---------------------------
// Cancel booking helper
// ---------------------------
async function cancelBooking(bookingID) {
    if (!confirm('Cancel this booking?')) return;
    try {
        const cancelUrl = window.bookingCancelUrl || '/index.php/bookings/cancel';
        const params = new URLSearchParams();
        params.append('booking_id', bookingID);
        params.append('status', 'Cancelled');
        if (window.csrfName && window.csrfHash) params.append(window.csrfName, window.csrfHash);

        const res = await fetch(cancelUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params.toString()
        });

        const text = await res.text();
        let json = null;
        try { json = text ? JSON.parse(text) : {}; } catch (e) { /* ignore parse errors */ }

        if (!res.ok || (json && json.error)) {
            console.error('Cancel response:', text);
            throw new Error(json?.error || 'Failed to cancel');
        }

        // reload bookings
        await loadMyBookings();
    } catch (err) {
        console.error('Cancel booking failed', err);
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Unable to cancel booking.' });
        } else {
            alert(err.message || 'Unable to cancel booking.');
        }
    }
}

// ---------------------------
// Utility
// ---------------------------
function escapeHtml(s) {
    if (s === undefined || s === null) return '';
    return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]);
}

function badgeClassForStatus(status) {
    const s = String(status || '').toLowerCase();
    if (s === 'confirmed') return 'bg-success text-white';
    if (s === 'pending') return 'bg-warning text-dark';
    if (s === 'rejected' || s === 'cancelled') return 'bg-danger text-white';
    return 'bg-secondary text-white';
}

// ---------------------------
// Init on DOM Loaded
// ---------------------------
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    setActiveNav();
    loadProperties();
    loadMyBookings();

    // Auto-load session link if provided
    const initialSessionId = window.initialSessionId || null;
    if (initialSessionId) {
        const sessionElement = document.querySelector(`.chat-item[data-session-id='${initialSessionId}']`);
        if (sessionElement) sessionElement.click();
    }
});

// ===== BOOKING MODAL FUNCTIONALITY =====
let currentBookingProperty = null;
let currentBookingImageIndex = 0;
let currentBookingImages = [];

// Async openBookingModal: resolves agent ID -> name/phone/email then shows modal
async function openBookingModal(propertyData) {
    currentBookingProperty = propertyData;
    // console.log('Populating booking modal with:', propertyData);

    // Images
    currentBookingImages = propertyData.images || [propertyData.image || 'uploads/properties/no-image.jpg'];
    currentBookingImageIndex = 0;
    const imgEl = document.getElementById('bookingPropertyImage');
    if (imgEl) imgEl.src = currentBookingImages[0];

    // Basic fields
    const setText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value ?? '';
    };

    setText('bookingPropertyType', propertyData.property_type || 'Property');
    setText('bookingPropertyTitle', propertyData.title || 'Property Title');
    setText('bookingPropertyLocation', propertyData.location || 'Location');
    setText('bookingPropertyPrice', '₱' + (propertyData.price || '0'));
    setText('bookingPropertyBedrooms', propertyData.bedrooms + ' Beds');
    setText('bookingPropertyBathrooms', propertyData.bathrooms + ' Baths');
    setText('bookingPropertySize', propertyData.size + ' sqft');
    setText('bookingPropertyParking', propertyData.parking_spaces + ' Spaces');
    setText('bookingPropertyCorporation', propertyData.corporation || 'N/A');
    setText('bookingPropertyDescription', propertyData.description || '');

    // Set property ID in hidden input
    const idInput = document.getElementById('bookingPropertyId');
    if (idInput) idInput.value = propertyData.id || '';

    // Booking date input removed from client UI; agent will assign dates.

    // Reset form
    const form = document.getElementById('propertyBookingForm');
    if (form) form.reset();

    // Resolve agent info (agent_assigned should be an ID or null)
    // prefer server-provided agent info (from viewProperty); fall back to fetch by ID only if needed
const agentId = propertyData.agent_assigned ?? propertyData.agent_id ?? null;

// server may already include agent_name/agent_phone/agent_email
let agentName = propertyData.agent_name ?? null;
let agentPhone = propertyData.agent_phone ?? propertyData.agent_email ?? '';
let agentEmail = propertyData.agent_email ?? '';

if (!agentName && agentId) {
  // only call getAgentInfo when server didn't return a name
  const agent = await getAgentInfo(agentId);
  agentName = agent?.name ?? agentName;
  agentPhone = agent?.phone ?? agentPhone;
  agentEmail = agent?.email ?? agentEmail;
}

// Populate agent fields (fallbacks)
setText('bookingPropertyAgent', agentName || (agentId ? String(agentId) : 'Unassigned'));
setText('bookingPropertyAgentPhone', agentPhone || '');
setText('bookingPropertyAgentEmail', agentEmail || '');

    // Show modal
    const bookingModalEl = document.getElementById('bookingModal');
    if (bookingModalEl) {
        const bookingModal = new bootstrap.Modal(bookingModalEl);
        bookingModal.show();
    } else {
        console.error('Booking modal element not found');
    }
}

/**
 * Handle booking form submission
 */
(function setupBookingForm() {
  const bookingCreateUrl = window.bookingCreateUrl || '/index.php/bookings/create';
  const csrfName = window.csrfName;
  const csrfHash = window.csrfHash;
  const form = document.getElementById('propertyBookingForm');
  if (!form) return;

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const payload = new URLSearchParams();
    payload.append('property_id', document.getElementById('bookingPropertyId').value);
    // Do not send booking_date from client — agents will assign booking dates.
    payload.append('booking_purpose', document.getElementById('bookingPurpose').value || '');
    payload.append('booking_notes', document.getElementById('bookingNotes').value || '');
    if (csrfName && csrfHash) payload.append(csrfName, csrfHash);

    try {
      const res = await fetch(bookingCreateUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
      });

      const text = await res.text();
      let data = null;
      try { data = text ? JSON.parse(text) : {}; } catch (e) { /* ignore parse error */ }

      if (!res.ok || (data && data.error)) {
        throw new Error(data?.error || ('Server error: ' + res.status));
      }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Booking Submitted!',
                    html: `<p><strong>${document.getElementById('bookingPropertyTitle')?.textContent || ''}</strong></p>
                                 <p>Date: —</p>`
                });
            } else {
                alert('Booking submitted successfully.');
            }

      // close modal
      bootstrap.Modal.getInstance(document.getElementById('bookingModal'))?.hide();
      // refresh bookings list
      loadMyBookings();

    } catch (err) {
      console.error('Booking save failed', err);
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Could not save booking.' });
      } else {
        alert(err.message || 'Could not save booking.');
      }
    }
  });
})();

/**
 * Update the "Book Property" button click handler in property details modal
 */
document.getElementById('modalBookBtn')?.addEventListener('click', async function () {
    if (!currentModalProperty) return console.error('No property data available');

    const propertyData = {
        id: currentModalProperty.id || currentModalProperty.PropertyID,
        title: currentModalProperty.Title || currentModalProperty.title || 'N/A',
        location: currentModalProperty.Location || currentModalProperty.location || 'N/A',
        property_type: currentModalProperty.Property_Type || currentModalProperty.type || 'N/A',
        price: (currentModalProperty.Price || currentModalProperty.price || '0').toString().replace('₱', '').trim(),
        bedrooms: currentModalProperty.Bedrooms || currentModalProperty.beds || '0',
        bathrooms: currentModalProperty.Bathrooms || currentModalProperty.baths || '0',
        size: currentModalProperty.Size || currentModalProperty.sqft || '0',
        image: (currentModalProperty.images && currentModalProperty.images[0]) || (currentModalProperty.Images && currentModalProperty.Images[0]) || currentModalProperty.image || 'uploads/properties/no-image.jpg',
        images: currentModalProperty.images || currentModalProperty.Images || [],
        parking_spaces: currentModalProperty.Parking_Spaces || currentModalProperty.parking_spaces || '0',
        // pass the agent ID (or null) so openBookingModal can resolve it to a name
        agent_assigned: currentModalProperty.Agent_Assigned || currentModalProperty.agent_assigned || null,
        corporation: currentModalProperty.Corporation || currentModalProperty.corporation || 'N/A',
        description: currentModalProperty.Description || currentModalProperty.description || 'No description available.'
    };

    // close details modal
    const propertyModal = bootstrap.Modal.getInstance(document.getElementById('propertyDetailsModal'));
    if (propertyModal) propertyModal.hide();

    // open booking modal (await so it's populated with agent name)
    await openBookingModal(propertyData);
});

/**
 * Example: Direct booking from property card
 */
function bookPropertyDirectly(propertyId) {
    fetch(`${propertiesViewUrl}/${propertyId}`)
        .then(response => response.json())
        .then(data => {
            // normalize server-side response if needed and call openBookingModal
            const property = {
                id: data.id ?? data.PropertyID,
                title: data.title ?? data.Title,
                location: data.location ?? data.Location,
                property_type: data.type ?? data.Property_Type,
                price: data.price ?? data.Price,
                bedrooms: data.bedrooms ?? data.Bedrooms,
                bathrooms: data.bathrooms ?? data.Bathrooms,
                size: data.size ?? data.Size,
                image: (data.images && data.images[0]) || data.image || (data.Images && data.Images[0]) || 'uploads/properties/no-image.jpg',
                images: data.images ?? data.Images ?? [],
                agent_assigned: data.agent_assigned ?? data.Agent_Assigned ?? null,
                corporation: data.corporation ?? data.Corporation,
                description: data.description ?? data.Description
            };
            openBookingModal(property);
        })
        .catch(error => {
            console.error('Error fetching property:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Failed to load property details. Please try again.' });
            } else {
                alert('Failed to load property details. Please try again.');
            }
        });
}

// ---------------------------
// Booking modal image navigation
// ---------------------------
function navigateBookingImage(step) {
    if (!currentBookingImages || currentBookingImages.length === 0) return;
    currentBookingImageIndex = (currentBookingImageIndex + step + currentBookingImages.length) % currentBookingImages.length;
    const el = document.getElementById('bookingPropertyImage');
    if (el) el.src = currentBookingImages[currentBookingImageIndex];
}
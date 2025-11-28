// assets/js/client.js
// Rebuilt client script with agent name resolution and booking modal improvements

// ---------------------------
// Global State
// ---------------------------
let allProperties = []; // All properties fetched from server
let filteredProperties = []; // Filtered list for browse
let favorites = new Set(); // Favorite property IDs
let currentModalIndex = 0; // Current property shown in modal
let currentImageIndex = 0; // Current image in modal carousel
let currentModalProperty = null; // store globally
const myBookingsUrl = window.myBookingsUrl || "/index.php/bookings/mine";

// ---------------------------
// Agent helper (fetch + cache)
// ---------------------------
const agentCache = new Map();

async function getAgentInfo(agentId) {
  if (!agentId) return null;
  if (agentCache.has(agentId)) return agentCache.get(agentId);

  const base = window.getUserUrlBase || "/index.php/users/getUser";
  const url = `${base}/${encodeURIComponent(agentId)}`;

  try {
    const res = await fetch(url, {
      credentials: "same-origin",
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    if (!res.ok) {
      console.warn(
        "getAgentInfo: server returned",
        res.status,
        "for agent",
        agentId
      );
      agentCache.set(agentId, null);
      return null;
    }
    const data = await res.json().catch(() => null);
    if (!data) {
      agentCache.set(agentId, null);
      return null;
    }
    const name =
      ((data.FirstName || "") + " " + (data.LastName || "")).trim() ||
      data.Email ||
      "Agent";
    const agent = {
      id: data.UserID ?? agentId,
      name,
      email: data.Email ?? "",
      phone: data.phone ?? data.phoneNumber ?? "",
    };
    agentCache.set(agentId, agent);
    return agent;
  } catch (err) {
    console.error("getAgentInfo error", err);
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
  const currentTheme =
    document.documentElement.getAttribute("data-theme") || "light";
  const newTheme = currentTheme === "dark" ? "light" : "dark";
  document.documentElement.setAttribute("data-theme", newTheme);
  localStorage.setItem("theme", newTheme);
  updateThemeIcons(newTheme);

  const themeSwitch = document.getElementById("themeSwitch");
  if (themeSwitch) themeSwitch.checked = newTheme === "dark";
}

function updateThemeIcons(theme) {
  const icons = document.querySelectorAll("#themeIcon, #themeIconMobile");
  icons.forEach((icon) => {
    icon.className =
      theme === "dark" ? "bi bi-sun-fill fs-5" : "bi bi-moon-fill fs-5";
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
  const links = document.querySelectorAll(".nav-link-custom");
  const current = window.location.pathname.split("/").pop() || "homepage.html";
  links.forEach((link) => {
    const href = (link.getAttribute("href") || "").split("/").pop();
    link.classList.toggle("active", href === current);
  });
}

// ---------------------------
// AJAX Load Properties
// ---------------------------
function loadProperties() {
  const container = document.getElementById("propertiesContainer");
  if (!container) return;

  fetch(propertiesUrl)
    .then((response) => {
      if (!response.ok)
        throw new Error("Network response was not ok: " + response.status);
      return response.json();
    })
    .then((data) => {
      // Normalize properties to guarantee consistent fields (id, title, image, etc.)
      allProperties = (Array.isArray(data) ? data : []).map((p) => ({
        id: p.id ?? p.PropertyID ?? null,
        title: p.title ?? p.Title ?? "",
        location: p.location ?? p.Location ?? "",
        type: p.type ?? p.Property_Type ?? "",
        price: p.price ?? p.Price ?? "",
        beds: p.beds ?? p.Bedrooms ?? "",
        baths: p.baths ?? p.Bathrooms ?? "",
        sqft: p.sqft ?? p.Size ?? "",
        image:
          (p.images && p.images[0]) ||
          p.image ||
          (p.Images && p.Images[0]) ||
          (p.Image
            ? p.Image.startsWith("http")
              ? p.Image
              : "uploads/properties/" + p.Image
            : "uploads/properties/no-image.jpg"),
        images: p.images ?? p.Images ?? [],
        agent_assigned: p.agent_assigned ?? p.Agent_Assigned ?? null,
        raw: p, // keep original if needed
      }));
      filteredProperties = [...allProperties]; // default filter
      renderPropertiesGrid();
    })
    .catch((error) => {
      console.error("Error fetching properties:", error);
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
            <div class="property-card" onclick="openPropertyDetails(${
              property.id
            })">
                <div class="property-image position-relative">
                    <img src="${
                      property.image || "uploads/properties/no-image.jpg"
                    }" alt="${escapeHtml(property.title)}" loading="lazy">
                    <button class="property-favorite ${
                      isFavorite ? "active" : ""
                    }" onclick="event.stopPropagation(); toggleFavorite(${
    property.id
  })">
                        <i class="bi ${
                          isFavorite ? "bi-heart-fill" : "bi-heart"
                        } fs-5"></i>
                    </button>
                    <span class="property-type-badge">${escapeHtml(
                      property.type
                    )}</span>
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
                            <small class="text-muted">${escapeHtml(
                              property.beds
                            )}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-droplet me-1 text-muted"></i>
                            <small class="text-muted">${escapeHtml(
                              property.baths
                            )}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrows-fullscreen me-1 text-muted"></i>
                            <small class="text-muted">${escapeHtml(
                              property.sqft
                            )} sqft</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="property-price">${escapeHtml(
                          property.price
                        )}</span>
                        <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); openPropertyDetails(${
                          property.id
                        })">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderPropertiesGrid() {
  const container =
    document.getElementById("propertiesContainer") ||
    document.getElementById("property-list");
  if (!container) return;

  if (filteredProperties.length === 0) {
    container.innerHTML = `<p class="text-center text-muted">No properties found.</p>`;
    return;
  }

  container.innerHTML = `<div class="row g-4">${filteredProperties
    .map((p) => renderPropertyCard(p))
    .join("")}</div>`;
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
  document.querySelectorAll(".property-favorite").forEach((btn) => {
    const onclickAttr = btn.getAttribute("onclick");
    const match = onclickAttr?.match(/toggleFavorite\((\d+)\)/);
    if (!match) return;
    const propertyId = parseInt(match[1]);
    const icon = btn.querySelector("i");
    if (favorites.has(propertyId)) {
      btn.classList.add("active");
      icon.className = "bi bi-heart-fill fs-5";
    } else {
      btn.classList.remove("active");
      icon.className = "bi bi-heart fs-5";
    }
  });
}

// ---------------------------
// Modal: Property Details
// ---------------------------
function openPropertyDetails(propertyId) {
  fetch(`${propertiesViewUrl}/${propertyId}`)
    .then((res) => res.json())
    .then((property) => {
      if (property.error) {
        console.error(property.error);
        return;
      }

      currentModalProperty = property;
      currentModalIndex = allProperties.findIndex((p) => p.id === propertyId);
      currentImageIndex = 0;

      updatePropertyModal(property);

      const modalEl = document.getElementById("propertyDetailsModal");
      if (!modalEl) return;
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    })
    .catch((err) => {
      console.error("Error fetching property:", err);
      // optional UI feedback
    });
}

function updatePropertyModal(property) {
  if (!property) return;

  // Normalize fields and update modal text content
  const propertyId = property.id || property.PropertyID;
  const title = property.Title || property.title || "N/A";
  const location = property.Location || property.location || "N/A";
  const price = property.Price || property.price || "N/A";
  const beds = property.Bedrooms || property.beds || "N/A";
  const baths = property.Bathrooms || property.baths || "N/A";
  const sqft = property.Size || property.sqft || "N/A";
  const type = property.Property_Type || property.type || "N/A";
  const images = property.images || property.Images || [];

  // Update modal text content
  const el = (id) => document.getElementById(id);
  el("modalPropertyTitle").textContent = title;
  el("modalPropertyLocation").textContent = location;
  el("modalPropertyPrice").textContent = price;
  el("modalPropertyBeds").textContent = beds;
  el("modalPropertyBaths").textContent = baths;
  el("modalPropertySqft").textContent = sqft;
  el("modalPropertyType").textContent = type;

  // Update property image
  const imgEl = document.getElementById("modalPropertyImage");
  if (imgEl) imgEl.src = images[0] || "uploads/properties/no-image.jpg";

  // Favorite button behavior
  const favBtn = document.getElementById("modalFavoriteBtn");
  const icon = favBtn?.querySelector("i");
  if (favBtn && icon) {
    if (favorites.has(propertyId)) {
      favBtn.classList.add("active");
      icon.className = "bi bi-heart-fill fs-5";
    } else {
      favBtn.classList.remove("active");
      icon.className = "bi bi-heart fs-5";
    }
    favBtn.onclick = (e) => {
      e.stopPropagation();
      toggleFavorite(propertyId);
      updatePropertyModal(property);
    };
  }

  // Chat button behavior (unchanged)
  const chatBtn = document.getElementById("modalChatBtn");
  if (chatBtn) {
    chatBtn.onclick = async () => {
      if (!propertyId) {
        console.error("Property ID is missing!", property);
        if (typeof Swal !== "undefined") {
          Swal.fire({
            title: "Error",
            text: "Cannot start chat: property ID missing.",
            icon: "error",
          });
        } else {
          alert("Cannot start chat: property ID missing.");
        }
        return;
      }

      try {
        const response = await fetch("/chat/startSession", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({ PropertyID: propertyId }),
        });

        const data = await response.json();
        if (data?.status === "success") {
          // If the server returned a chat session id, include it in the redirect
          const sessionId =
            data.sessionId || data.chatSessionID || data.chatSessionId || null;
          const target = sessionId
            ? `/users/chat?session=${encodeURIComponent(sessionId)}`
            : "/users/chat";

          if (typeof Swal !== "undefined") {
            Swal.fire({
              title: "Message Sent!",
              text: `Your message regarding "${data.propertyTitle}" has been sent to ${data.agentName}.`,
              icon: "success",
              confirmButtonText: "Go to Chat",
            }).then(() => (window.location.href = target));
          } else {
            alert("Message sent! Redirecting to chat.");
            window.location.href = target;
          }
        } else {
          if (typeof Swal !== "undefined") {
            Swal.fire({
              title: "Error",
              text: data.error || "Failed to start chat session",
              icon: "error",
            });
          } else {
            alert(data.error || "Failed to start chat session");
          }
        }
      } catch (err) {
        console.error("Fetch error:", err);
        if (typeof Swal !== "undefined") {
          Swal.fire({
            title: "Error",
            text: "Something went wrong while starting chat.",
            icon: "error",
          });
        } else {
          alert("Something went wrong while starting chat.");
        }
      }
    };
  }
}

// ---------------------------
// Modal Image Navigation
// ---------------------------
function navigateProperty(step) {
  if (
    !currentModalProperty ||
    !currentModalProperty.images ||
    currentModalProperty.images.length === 0
  )
    return;
  currentImageIndex =
    (currentImageIndex + step + currentModalProperty.images.length) %
    currentModalProperty.images.length;
  const el = document.getElementById("modalPropertyImage");
  if (el) el.src = currentModalProperty.images[currentImageIndex];
}

// ---------------------------
// Chat helper
// ---------------------------
function openChatWithAgent(agentName, propertyId) {
  toggleChat?.();
  const chatInput = document.getElementById("chatInput");
  if (chatInput) {
    chatInput.value = `Hello ${agentName}, I am interested in Property ID #${propertyId}`;
    chatInput.focus();
  }
}

// ---------------------------
// Filters
// ---------------------------
function applyFilters() {
  const searchEl = document.getElementById("searchFilter");
  const typeEl = document.getElementById("typeFilter");
  const bedroomsEl = document.getElementById("bedroomsFilter");
  const searchQuery = searchEl?.value.toLowerCase() || "";
  const typeFilter = typeEl?.value || "all";
  const bedroomsFilter = bedroomsEl?.value || "all";

  filteredProperties = allProperties.filter((p) => {
    const title = (p.title || "").toString().toLowerCase();
    const loc = (p.location || "").toString().toLowerCase();
    const matchesSearch =
      !searchQuery || title.includes(searchQuery) || loc.includes(searchQuery);
    const matchesType = typeFilter === "all" || (p.type || "") === typeFilter;
    const matchesBeds =
      bedroomsFilter === "all" || p.beds == parseInt(bedroomsFilter);
    return matchesSearch && matchesType && matchesBeds;
  });

  renderPropertiesGrid();
}

// ---------------------------
// Bookings: render & load
// ---------------------------
function renderBookingsList(bookings) {
  const container = document.getElementById("bookingsList");
  if (!container) return;

  if (!bookings || bookings.length === 0) {
    container.innerHTML =
      '<div class="text-center text-muted py-4">You have no bookings.</div>';
    return;
  }

  const html = bookings
    .map((b) => {
      // Clients do not set booking dates; show placeholder instead
      const date = "—";
      const status = b.BookingStatus || b.status || "Pending";
      // Map confirmed -> Scheduled for display
      const statusDisplay =
        String(status || "").toLowerCase() === "confirmed"
          ? "Scheduled"
          : status
          ? String(status).charAt(0).toUpperCase() + String(status).slice(1)
          : "Pending";
      const img =
        b.Images && b.Images[0]
          ? b.Images[0]
          : "uploads/properties/no-image.jpg";
      const reason = b.Reason
        ? `<div><strong>Reason:</strong> ${escapeHtml(b.Reason)}</div>`
        : "";
      const notes = b.Notes
        ? `<div><strong>Notes:</strong> ${escapeHtml(b.Notes)}</div>`
        : "";

      return `
        <div class="card mb-3">
          <div class="row g-0 align-items-center">
            <div class="col-auto" style="width:140px">
              <img src="${escapeHtml(
                img
              )}" class="img-fluid rounded-start" style="height:100%; object-fit:cover;">
            </div>
            <div class="col">
              <div class="card-body">
                <h5 class="card-title mb-1">${escapeHtml(
                  b.PropertyTitle || "Property"
                )}</h5>
                <p class="mb-1 text-muted small">${escapeHtml(
                  b.PropertyLocation || ""
                )}</p>
                <div class="mb-1"><strong>Date:</strong> ${escapeHtml(
                  date
                )} &nbsp; <span class="badge ${badgeClassForStatus(
        status
      )}">${escapeHtml(statusDisplay)}</span></div>
                ${reason}
                ${notes}
              </div>
            </div>
            <div class="col-auto pe-3">
              <div class="d-flex flex-column gap-2">
                <button class="btn btn-sm btn-primary" onclick="viewBookingDetails(${
                  b.bookingID
                })">View</button>
                ${
                  status.toLowerCase() === "pending"
                    ? `<button class="btn btn-sm btn-danger" onclick="cancelBooking(${b.bookingID})">Cancel</button>`
                    : ""
                }
              </div>
            </div>
          </div>
        </div>
        `;
    })
    .join("");

  container.innerHTML = html;
}

async function loadMyBookings() {
  const container = document.getElementById("bookingsList");
  if (!container) return;
  container.innerHTML =
    '<div class="text-center py-4">Loading your bookings...</div>';

  try {
    const res = await fetch(myBookingsUrl, {
      credentials: "same-origin",
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    if (!res.ok) throw new Error("Failed to fetch bookings: " + res.status);
    const data = await res.json().catch(() => []);
    renderBookingsList(Array.isArray(data) ? data : []);
  } catch (err) {
    console.error("loadMyBookings error", err);
    container.innerHTML = `<div class="text-center text-danger">Failed to load bookings. Please try later.</div>`;
  }
}

// ---------------------------
// Cancel booking helper
// ---------------------------
async function cancelBooking(bookingID) {
  if (!confirm("Cancel this booking?")) return;
  try {
    const cancelUrl = window.bookingCancelUrl || "/index.php/bookings/cancel";
    const params = new URLSearchParams();
    params.append("booking_id", bookingID);
    params.append("status", "Cancelled");
    if (window.csrfName && window.csrfHash)
      params.append(window.csrfName, window.csrfHash);

    const res = await fetch(cancelUrl, {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params.toString(),
    });

    const text = await res.text();
    let json = null;
    try {
      json = text ? JSON.parse(text) : {};
    } catch (e) {
      /* ignore parse errors */
    }

    if (!res.ok || (json && json.error)) {
      console.error("Cancel response:", text);
      throw new Error(json?.error || "Failed to cancel");
    }

    // reload bookings
    await loadMyBookings();
  } catch (err) {
    console.error("Cancel booking failed", err);
    if (typeof Swal !== "undefined") {
      Swal.fire({
        icon: "error",
        title: "Failed",
        text: err.message || "Unable to cancel booking.",
      });
    } else {
      alert(err.message || "Unable to cancel booking.");
    }
  }
}

// ---------------------------
// Utility
// ---------------------------
function escapeHtml(s) {
  if (s === undefined || s === null) return "";
  return String(s).replace(
    /[&<>"']/g,
    (c) =>
      ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[
        c
      ])
  );
}

function badgeClassForStatus(status) {
  const s = String(status || "").toLowerCase();
  if (s === "confirmed") return "bg-success text-white";
  if (s === "pending") return "bg-warning text-dark";
  if (s === "rejected" || s === "cancelled") return "bg-danger text-white";
  return "bg-secondary text-white";
}

// ---------------------------
// Init on DOM Loaded
// ---------------------------
document.addEventListener("DOMContentLoaded", () => {
  initTheme();
  setActiveNav();
  loadProperties();
  loadMyBookings();

  // Auto-load session link if provided
  const initialSessionId = window.initialSessionId || null;
  if (initialSessionId) {
    const sessionElement = document.querySelector(
      `.chat-item[data-session-id='${initialSessionId}']`
    );
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
  currentBookingImages = propertyData.images || [
    propertyData.image || "uploads/properties/no-image.jpg",
  ];
  currentBookingImageIndex = 0;
  const imgEl = document.getElementById("bookingPropertyImage");
  if (imgEl) imgEl.src = currentBookingImages[0];

  // Basic fields
  const setText = (id, value) => {
    const el = document.getElementById(id);
    if (el) el.textContent = value ?? "";
  };

  setText("bookingPropertyType", propertyData.property_type || "Property");
  setText("bookingPropertyTitle", propertyData.title || "Property Title");
  setText("bookingPropertyLocation", propertyData.location || "Location");
  setText("bookingPropertyPrice", "₱" + (propertyData.price || "0"));
  setText("bookingPropertyBedrooms", propertyData.bedrooms + " Beds");
  setText("bookingPropertyBathrooms", propertyData.bathrooms + " Baths");
  setText("bookingPropertySize", propertyData.size + " sqft");
  setText("bookingPropertyParking", propertyData.parking_spaces + " Spaces");
  setText("bookingPropertyCorporation", propertyData.corporation || "N/A");
  setText("bookingPropertyDescription", propertyData.description || "");

  // Set property ID in hidden input
  const idInput = document.getElementById("bookingPropertyId");
  if (idInput) idInput.value = propertyData.id || "";

  // Ensure booking date input cannot select past dates: set `min` to current local datetime
  // For separate date input (YYYY-MM-DD), set min to today's date
  const bookingDateEl = document.getElementById("bookingDate");
  if (bookingDateEl) {
    const now = new Date();
    const pad = (n) => String(n).padStart(2, "0");
    const today = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(
      now.getDate()
    )}`;
    bookingDateEl.min = today;
    if (bookingDateEl.value) {
      const selected = new Date(bookingDateEl.value + "T00:00:00");
      if (isNaN(selected.getTime()) || selected < new Date(today + "T00:00:00"))
        bookingDateEl.value = "";
    }
  }

  // Clear time fields when opening modal
  const timeEl = document.getElementById("bookingTime");
  const ampmEl = document.getElementById("bookingTimeAmpm");
  if (timeEl) timeEl.value = "";
  if (ampmEl) ampmEl.value = "AM";

  // Reset form
  const form = document.getElementById("propertyBookingForm");
  if (form) form.reset();

  // Resolve agent info (agent_assigned should be an ID or null)
  // prefer server-provided agent info (from viewProperty); fall back to fetch by ID only if needed
  const agentId = propertyData.agent_assigned ?? propertyData.agent_id ?? null;

  // server may already include agent_name/agent_phone/agent_email
  let agentName = propertyData.agent_name ?? null;
  let agentPhone = propertyData.agent_phone ?? propertyData.agent_email ?? "";
  let agentEmail = propertyData.agent_email ?? "";

  if (!agentName && agentId) {
    // only call getAgentInfo when server didn't return a name
    const agent = await getAgentInfo(agentId);
    agentName = agent?.name ?? agentName;
    agentPhone = agent?.phone ?? agentPhone;
    agentEmail = agent?.email ?? agentEmail;
  }

  // Populate agent fields (fallbacks)
  setText(
    "bookingPropertyAgent",
    agentName || (agentId ? String(agentId) : "Unassigned")
  );
  setText("bookingPropertyAgentPhone", agentPhone || "");
  setText("bookingPropertyAgentEmail", agentEmail || "");

  // Show modal
  const bookingModalEl = document.getElementById("bookingModal");
  if (bookingModalEl) {
    const bookingModal = new bootstrap.Modal(bookingModalEl);
    bookingModal.show();
  } else {
    console.error("Booking modal element not found");
  }
}

/**
 * Handle booking form submission
 */
(function setupBookingForm() {
  const bookingCreateUrl =
    window.bookingCreateUrl || "/index.php/bookings/create";
  const csrfName = window.csrfName;
  const csrfHash = window.csrfHash;
  const form = document.getElementById("propertyBookingForm");
  if (!form) return;

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const payload = new URLSearchParams();
    payload.append(
      "property_id",
      document.getElementById("bookingPropertyId").value
    );
    // Include preferred booking date/time when provided by client.
    // We accept separate date input + typable time + AM/PM and combine into local ISO-like string: YYYY-MM-DDTHH:MM
    const bookingDateEl2 = document.getElementById("bookingDate");
    const bookingTimeEl = document.getElementById("bookingTime");
    const bookingAmpmEl = document.getElementById("bookingTimeAmpm");
    let combined = "";
    if (bookingDateEl2 && bookingDateEl2.value) {
      const datePart = bookingDateEl2.value; // YYYY-MM-DD
      let hour = null,
        minute = "00";
      if (bookingTimeEl && bookingTimeEl.value) {
        const t = bookingTimeEl.value.trim();
        const m = t.match(/^(\d{1,2}):(\d{2})$/);
        if (m) {
          hour = parseInt(m[1], 10);
          minute = m[2];
        }
      }

      if (hour !== null) {
        // adjust hour based on AM/PM if provided
        const ampm = bookingAmpmEl?.value || "";
        if (/^am$/i.test(ampm)) {
          if (hour === 12) hour = 0;
        } else if (/^pm$/i.test(ampm)) {
          if (hour < 12) hour = hour + 12;
        }
        const pad = (n) => String(n).padStart(2, "0");
        combined = `${datePart}T${pad(hour)}:${pad(Number(minute))}`;
      } else {
        // date only
        combined = datePart;
      }

      if (combined) payload.append("booking_date", combined);
    }
    payload.append(
      "booking_purpose",
      document.getElementById("bookingPurpose").value || ""
    );
    payload.append(
      "booking_notes",
      document.getElementById("bookingNotes").value || ""
    );
    if (csrfName && csrfHash) payload.append(csrfName, csrfHash);

    try {
      const res = await fetch(bookingCreateUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: payload.toString(),
      });

      const text = await res.text();
      let data = null;
      try {
        data = text ? JSON.parse(text) : {};
      } catch (e) {
        /* ignore parse error */
      }

      if (!res.ok || (data && data.error)) {
        throw new Error(data?.error || "Server error: " + res.status);
      }

      if (typeof Swal !== "undefined") {
        // Show a success modal with an OK button that redirects to the bookings page on confirm
        Swal.fire({
          icon: "success",
          title: "Booked Successfully",
          html: `<p><strong>${
            document.getElementById("bookingPropertyTitle")?.textContent || ""
          }</strong></p>
                           <p>Your booking request has been submitted.</p>`,
          confirmButtonText: "OK",
        }).then((result) => {
          if (result.isConfirmed) {
            const purpose =
              document.getElementById("bookingPurpose")?.value || "";
            try {
              if (String(purpose).toLowerCase() === "viewing") {
                window.location.href = "/users/clientbookings";
              } else {
                window.location.href = "/users/clientreservations";
              }
            } catch (e) {
              // fallback to bookings page
              window.location.href = "/users/clientbookings";
            }
          }
        });
      } else {
        // Fallback: alert then redirect
        // Fallback: decide redirect based on purpose value if available
        const purpose = document.getElementById("bookingPurpose")?.value || "";
        if (String(purpose).toLowerCase() === "viewing") {
          alert(
            "Booking submitted successfully. You will be redirected to your bookings."
          );
          window.location.href = "/users/clientbookings";
        } else {
          alert(
            "Booking submitted successfully. You will be redirected to your reservations."
          );
          window.location.href = "/users/clientreservations";
        }
      }

      // close only the booking modal; do not immediately reload the entire page
      bootstrap.Modal.getInstance(
        document.getElementById("bookingModal")
      )?.hide();
    } catch (err) {
      console.error("Booking save failed", err);
      if (typeof Swal !== "undefined") {
        Swal.fire({
          icon: "error",
          title: "Failed",
          text: err.message || "Could not save booking.",
        });
      } else {
        alert(err.message || "Could not save booking.");
      }
    }
  });
})();

/**
 * Update the "Book Property" button click handler in property details modal
 */
document
  .getElementById("modalBookBtn")
  ?.addEventListener("click", async function () {
    if (!currentModalProperty)
      return console.error("No property data available");

    const propertyData = {
      id: currentModalProperty.id || currentModalProperty.PropertyID,
      title: currentModalProperty.Title || currentModalProperty.title || "N/A",
      location:
        currentModalProperty.Location || currentModalProperty.location || "N/A",
      property_type:
        currentModalProperty.Property_Type ||
        currentModalProperty.type ||
        "N/A",
      price: (currentModalProperty.Price || currentModalProperty.price || "0")
        .toString()
        .replace("₱", "")
        .trim(),
      bedrooms:
        currentModalProperty.Bedrooms || currentModalProperty.beds || "0",
      bathrooms:
        currentModalProperty.Bathrooms || currentModalProperty.baths || "0",
      size: currentModalProperty.Size || currentModalProperty.sqft || "0",
      image:
        (currentModalProperty.images && currentModalProperty.images[0]) ||
        (currentModalProperty.Images && currentModalProperty.Images[0]) ||
        currentModalProperty.image ||
        "uploads/properties/no-image.jpg",
      images: currentModalProperty.images || currentModalProperty.Images || [],
      parking_spaces:
        currentModalProperty.Parking_Spaces ||
        currentModalProperty.parking_spaces ||
        "0",
      // pass the agent ID (or null) so openBookingModal can resolve it to a name
      agent_assigned:
        currentModalProperty.Agent_Assigned ||
        currentModalProperty.agent_assigned ||
        null,
      corporation:
        currentModalProperty.Corporation ||
        currentModalProperty.corporation ||
        "N/A",
      description:
        currentModalProperty.Description ||
        currentModalProperty.description ||
        "No description available.",
    };

    // close details modal
    const propertyModal = bootstrap.Modal.getInstance(
      document.getElementById("propertyDetailsModal")
    );
    if (propertyModal) propertyModal.hide();

    // open booking modal (await so it's populated with agent name)
    await openBookingModal(propertyData);
  });

/**
 * Example: Direct booking from property card
 */
function bookPropertyDirectly(propertyId) {
  fetch(`${propertiesViewUrl}/${propertyId}`)
    .then((response) => response.json())
    .then((data) => {
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
        image:
          (data.images && data.images[0]) ||
          data.image ||
          (data.Images && data.Images[0]) ||
          "uploads/properties/no-image.jpg",
        images: data.images ?? data.Images ?? [],
        agent_assigned: data.agent_assigned ?? data.Agent_Assigned ?? null,
        corporation: data.corporation ?? data.Corporation,
        description: data.description ?? data.Description,
      };
      openBookingModal(property);
    })
    .catch((error) => {
      console.error("Error fetching property:", error);
      if (typeof Swal !== "undefined") {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Failed to load property details. Please try again.",
        });
      } else {
        alert("Failed to load property details. Please try again.");
      }
    });
}

// ---------------------------
// Booking modal image navigation
// ---------------------------
function navigateBookingImage(step) {
  if (!currentBookingImages || currentBookingImages.length === 0) return;
  currentBookingImageIndex =
    (currentBookingImageIndex + step + currentBookingImages.length) %
    currentBookingImages.length;
  const el = document.getElementById("bookingPropertyImage");
  if (el) el.src = currentBookingImages[currentBookingImageIndex];
}

// Global handler used from inline onclick in bookings list: viewBookingDetails(id)
async function viewBookingDetails(id) {
  if (!id) return;
  try {
    const res = await fetch(myBookingsUrl, {
      credentials: "same-origin",
      headers: { "X-Requested-With": "XMLHttpRequest" },
    });
    if (!res.ok) throw new Error("Failed to load booking");
    const list = await res.json();
    const booking = (Array.isArray(list) ? list : []).find(
      (b) => String(b.bookingID) === String(id)
    );
    if (!booking) throw new Error("Booking not found");

    // populate modal fields (same IDs expected in bookings/reservations views)
    const titleEl = document.getElementById("bookingModalTitle");
    if (titleEl) titleEl.textContent = "Booking #" + (booking.bookingID || "");
    const propTitle = document.getElementById("bookingModalPropertyTitle");
    if (propTitle)
      propTitle.textContent =
        booking.PropertyTitle || booking.Title || "Property";
    const loc = document.getElementById("bookingModalLocation");
    if (loc)
      loc.textContent = booking.PropertyLocation || booking.Location || "";

    const status = booking.BookingStatus || booking.status || "Pending";
    const statusDisplay =
      String(status || "").toLowerCase() === "confirmed"
        ? "Scheduled"
        : status
        ? String(status).charAt(0).toUpperCase() + String(status).slice(1)
        : "Pending";
    const statusEl = document.getElementById("bookingModalStatus");
    if (statusEl) {
      statusEl.textContent = statusDisplay;
      statusEl.className =
        "badge " +
        (statusEl.className
          ? statusEl.className
          : statusDisplay
          ? "bg-secondary text-white"
          : "");
    }

    const img =
      booking.Images && booking.Images[0]
        ? booking.Images[0]
        : booking.Image
        ? booking.Image
        : "uploads/properties/no-image.jpg";
    const imgEl = document.getElementById("bookingModalImage");
    if (imgEl) imgEl.src = img;

    const priceEl = document.getElementById("bookingModalPrice");
    if (priceEl)
      priceEl.textContent = booking.PropertyPrice
        ? `₱${Number(booking.PropertyPrice).toLocaleString()}`
        : booking.Price
        ? `₱${Number(booking.Price).toLocaleString()}`
        : "—";

    const notesEl = document.getElementById("bookingModalNotes");
    if (notesEl)
      notesEl.textContent =
        booking.Notes || booking.Reason || "No notes provided.";

    // Additional property details
    setText(
      "bookingModalBeds",
      booking.PropertyBedrooms || booking.Bedrooms || "—"
    );
    setText(
      "bookingModalBaths",
      booking.PropertyBathrooms || booking.Bathrooms || "—"
    );
    setText("bookingModalSize", booking.PropertySize || booking.Size || "—");
    setText(
      "bookingModalParking",
      booking.PropertyParking || booking.Parking_Spaces || "—"
    );
    setText("bookingModalPropertyType", booking.Property_Type || "—");
    setText("bookingModalCorporation", booking.Corporation || "—");
    const descEl = document.getElementById("bookingModalDescription");
    if (descEl)
      descEl.textContent =
        booking.PropertyDescription || booking.Description || "—";

    // agent info
    const agentId =
      booking.agent_id ??
      booking.assigned_agent ??
      booking.Agent_Assigned ??
      null;
    const agentName =
      booking.agent_name ??
      booking.assigned_agent_name ??
      booking.assigned_agent ??
      null;
    const agentPhone =
      booking.agent_phone ?? booking.agent_contact ?? booking.agentPhone ?? "";
    const agentEmail =
      booking.agent_email ??
      booking.agent_contact_email ??
      booking.agentEmail ??
      "";

    const agentEl = document.getElementById("bookingModalAgent");
    if (agentEl)
      agentEl.textContent =
        agentName || (agentId ? String(agentId) : "Unassigned");
    const phoneEl = document.getElementById("bookingModalAgentPhone");
    if (phoneEl) phoneEl.textContent = agentPhone || "—";
    const emailEl = document.getElementById("bookingModalAgentEmail");
    if (emailEl) emailEl.textContent = agentEmail || "—";

    const agentIdInput = document.getElementById("bookingModalAgentId");
    if (agentIdInput) agentIdInput.value = agentId ?? "";

    const cancelBtn = document.getElementById("modalCancelBookingBtn");
    if (cancelBtn) {
      if (
        ["pending", "confirmed", "viewing"].includes(
          String(status).toLowerCase()
        )
      ) {
        cancelBtn.style.display = "";
        cancelBtn.dataset.id = booking.bookingID;
        cancelBtn.onclick = () => cancelBooking(booking.bookingID);
      } else {
        cancelBtn.style.display = "none";
        cancelBtn.onclick = null;
      }
    }

    const contactBtn = document.getElementById("modalContactAgentBtn");
    if (contactBtn) {
      contactBtn.onclick = () => {
        if (agentId) {
          window.location.href = `/users/chat?agent=${encodeURIComponent(
            agentId
          )}&property=${encodeURIComponent(
            booking.PropertyID || booking.property_id || ""
          )}`;
          return;
        }
        if (agentEmail) {
          window.location.href = `mailto:${encodeURIComponent(
            agentEmail
          )}?subject=${encodeURIComponent(
            "Inquiry about " + (booking.PropertyTitle || "property")
          )}`;
          return;
        }
        window.location.href = "/users/chat";
      };
    }

    // show modal
    const modalEl = document.getElementById("bookingDetailModal");
    if (modalEl) {
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    }
  } catch (err) {
    console.error("Failed to show booking", err);
    if (typeof Swal !== "undefined") {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Unable to load booking details.",
      });
    } else {
      alert("Unable to load booking details.");
    }
  }
}

// Expose helpers globally so other scripts (e.g., bookings.js) can call them reliably
window.openPropertyDetails = openPropertyDetails;
window.updatePropertyModal = updatePropertyModal;
window.bookPropertyDirectly = bookPropertyDirectly;

// Modal stacking manager: ensure newly opened modals sit above existing ones
(function manageModalStacking() {
  document.addEventListener("shown.bs.modal", (e) => {
    try {
      const modal = e.target;
      if (!modal || !modal.classList || !modal.classList.contains("modal"))
        return;
      // Move modal to document.body to avoid stacking context issues
      if (modal.parentElement !== document.body)
        document.body.appendChild(modal);

      // Find all visible modals and assign increasing z-index
      const openModals = Array.from(document.querySelectorAll(".modal.show"));
      openModals.forEach((m, idx) => {
        const z = 1050 + (idx + 1) * 20; // 1070, 1090, ...
        m.style.zIndex = z;
      });

      // Ensure backdrops are placed below their corresponding modal
      const backdrops = Array.from(
        document.querySelectorAll(".modal-backdrop")
      );
      backdrops.forEach((b, idx) => {
        const z = 1040 + (idx + 1) * 20; // slightly below modal
        b.style.zIndex = z;
      });

      modal.classList.add("modal-front");
    } catch (err) {
      console.warn("manageModalStacking show handler error", err);
    }
  });

  document.addEventListener("hidden.bs.modal", (e) => {
    try {
      const modal = e.target;
      if (!modal) return;
      modal.style.zIndex = "";
      modal.classList.remove("modal-front");

      // Recompute for remaining open modals
      const openModals = Array.from(document.querySelectorAll(".modal.show"));
      if (openModals.length === 0) {
        // reset backdrops
        document
          .querySelectorAll(".modal-backdrop")
          .forEach((b) => (b.style.zIndex = ""));
      } else {
        openModals.forEach(
          (m, idx) => (m.style.zIndex = 1050 + (idx + 1) * 20)
        );
        Array.from(document.querySelectorAll(".modal-backdrop")).forEach(
          (b, idx) => (b.style.zIndex = 1040 + (idx + 1) * 20)
        );
      }
    } catch (err) {
      console.warn("manageModalStacking hidden handler error", err);
    }
  });
})();
// ---------------------------
// Filter State
// ---------------------------
let currentFilters = {
  search: "",
  type: "all",
  bedrooms: "all",
  bathrooms: "all",
  minPrice: null,
  maxPrice: null,
  minSize: null,
  maxSize: null,
};

// ---------------------------
// Filter Chip Selection
// ---------------------------
function selectChip(element, filterType) {
  // Remove active class from all chips in this group
  const parent = element.parentElement;
  const chips = parent.querySelectorAll(".filter-chip");
  chips.forEach((chip) => chip.classList.remove("active"));

  // Add active class to clicked chip
  element.classList.add("active");

  // Update filter state
  const value = element.getAttribute("data-value");
  currentFilters[filterType] = value;

  // Apply filters
  applyFilters();
}

// ---------------------------
// Price Input Formatting
// ---------------------------
function formatPriceInput(input) {
  // Remove non-numeric characters except decimal
  let value = input.value.replace(/[^\d]/g, "");

  // Format with commas
  if (value) {
    value = parseInt(value).toLocaleString();
  }

  input.value = value;
}

// ---------------------------
// Quick Price Filters
// ---------------------------
function setQuickPrice(min, max) {
  const minInput = document.getElementById("minPrice");
  const maxInput = document.getElementById("maxPrice");

  if (minInput) minInput.value = min ? min.toLocaleString() : "";
  if (maxInput) maxInput.value = max ? max.toLocaleString() : "";

  currentFilters.minPrice = min;
  currentFilters.maxPrice = max;

  applyFilters();
}

// ---------------------------
// Clear All Filters
// ---------------------------
function clearAllFilters() {
  // Reset filter state
  currentFilters = {
    search: "",
    type: "all",
    bedrooms: "all",
    bathrooms: "all",
    minPrice: null,
    maxPrice: null,
    minSize: null,
    maxSize: null,
  };

  // Clear input fields
  document.getElementById("searchFilter").value = "";
  document.getElementById("minPrice").value = "";
  document.getElementById("maxPrice").value = "";
  document.getElementById("minSize").value = "";
  document.getElementById("maxSize").value = "";

  // Reset chips to "all/any"
  document.querySelectorAll(".filter-chip").forEach((chip) => {
    if (chip.getAttribute("data-value") === "all") {
      chip.classList.add("active");
    } else {
      chip.classList.remove("active");
    }
  });

  applyFilters();
}

// ---------------------------
// Main Filter Function
// ---------------------------
function applyFilters() {
  // Update search filter from input
  const searchInput = document.getElementById("searchFilter");
  if (searchInput) {
    currentFilters.search = searchInput.value.toLowerCase().trim();
  }

  // Update price filters from inputs
  const minPriceInput = document.getElementById("minPrice");
  const maxPriceInput = document.getElementById("maxPrice");

  if (minPriceInput && minPriceInput.value) {
    currentFilters.minPrice = parseInt(
      minPriceInput.value.replace(/[^\d]/g, "")
    );
  } else {
    currentFilters.minPrice = null;
  }

  if (maxPriceInput && maxPriceInput.value) {
    currentFilters.maxPrice = parseInt(
      maxPriceInput.value.replace(/[^\d]/g, "")
    );
  } else {
    currentFilters.maxPrice = null;
  }

  // Update size filters from inputs
  const minSizeInput = document.getElementById("minSize");
  const maxSizeInput = document.getElementById("maxSize");

  if (minSizeInput && minSizeInput.value) {
    currentFilters.minSize = parseInt(minSizeInput.value);
  } else {
    currentFilters.minSize = null;
  }

  if (maxSizeInput && maxSizeInput.value) {
    currentFilters.maxSize = parseInt(maxSizeInput.value);
  } else {
    currentFilters.maxSize = null;
  }

  // Apply filters to properties
  filteredProperties = allProperties.filter((property) => {
    // Search filter
    if (currentFilters.search) {
      const searchTerm = currentFilters.search;
      const title = (property.title || "").toLowerCase();
      const location = (property.location || "").toLowerCase();
      const type = (property.type || "").toLowerCase();

      if (
        !title.includes(searchTerm) &&
        !location.includes(searchTerm) &&
        !type.includes(searchTerm)
      ) {
        return false;
      }
    }

    // Property type filter
    if (currentFilters.type !== "all") {
      if (property.type !== currentFilters.type) {
        return false;
      }
    }

    // Bedrooms filter
    if (currentFilters.bedrooms !== "all") {
      if (currentFilters.bedrooms === "5+") {
        if (parseInt(property.beds) < 5) {
          return false;
        }
      } else {
        if (parseInt(property.beds) !== parseInt(currentFilters.bedrooms)) {
          return false;
        }
      }
    }

    // Bathrooms filter
    if (currentFilters.bathrooms !== "all") {
      if (currentFilters.bathrooms === "3+") {
        if (parseInt(property.baths) < 3) {
          return false;
        }
      } else {
        if (parseInt(property.baths) !== parseInt(currentFilters.bathrooms)) {
          return false;
        }
      }
    }

    // Price filter
    const propertyPrice = parseInt(
      (property.price || "0").replace(/[^\d]/g, "")
    );

    if (
      currentFilters.minPrice !== null &&
      propertyPrice < currentFilters.minPrice
    ) {
      return false;
    }

    if (
      currentFilters.maxPrice !== null &&
      propertyPrice > currentFilters.maxPrice
    ) {
      return false;
    }

    // Size filter
    const propertySize = parseInt(property.sqft) || 0;

    if (
      currentFilters.minSize !== null &&
      propertySize < currentFilters.minSize
    ) {
      return false;
    }

    if (
      currentFilters.maxSize !== null &&
      propertySize > currentFilters.maxSize
    ) {
      return false;
    }

    return true;
  });

  // Update results count
  updateResultsCount();

  // Re-render properties
  renderPropertiesGrid();
}

// ---------------------------
// Results Count
// ---------------------------
function updateResultsCount() {
  // You can add a results count display in your HTML and update it here
  const resultsCount = document.getElementById("resultsCount");
  if (resultsCount) {
    resultsCount.textContent = `${filteredProperties.length} properties found`;
  }
}

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clients</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url('bootstrap5/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/styles/agenStyle.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>

<body>
  <!-- 🔹 Navbar -->
  <nav class="navbar navbar-light fixed-top shadow-sm bg-white border-bottom">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4">
      <h3 class="mb-0 text-secondary fw-semibold">Manage Clients</h3>
      <ul class="nav nav-tabs border-0 flex-nowrap">
        <li class="nav-item"><a class="nav-link" href="/users/agentHomepage">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="/users/agentclients">Clients</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentbookings">Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentproperties">Properties</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentchat">Chat</a></li>
      </ul>
      <button class="btn btn-outline-primary btn-sm">View Profile</button>
    </div>
  </nav>

  <!-- 🔹 Page Content -->
  <div class="container mt-5 pt-5">
    <div class="row g-4 animate__animated animate__fadeInUp">
      
      <!-- 🟩 Client List (Left Side) -->
      <div class="col-md-6">
        <div class="card p-0 shadow-sm client-list">
          <!-- Fixed Header -->
          <div class="card-header bg-white border-bottom p-4">
            <h4 class="fw-semibold mb-2">My Assigned Clients</h4>
            <p class="text-muted mb-0">Click a client to view complete details.</p>
          </div>
          <!-- Scrollable Body -->
          <div class="card-body p-4 client-list-scroll" id="clientsList"></div>
        </div>
      </div>

      <!-- 🟦 Client Details (Right Side) -->
      <div class="col-md-6">
        <div class="card p-4 shadow-sm client-details">
          <h4 class="fw-semibold mb-3">Client Details</h4>
          <div id="clientDetails" class="placeholder-text">
            Select a client from the list to view their details.
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- 🔹 ID Modal -->
  <div class="modal fade" id="idModal" tabindex="-1" aria-labelledby="idModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow-sm">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-semibold" id="idModalLabel">Valid ID</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <img id="idImage" src="" alt="Valid ID" class="img-fluid rounded shadow-sm">
        </div>
      </div>
    </div>
  </div>

  <!-- 🔹 Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const clients = [
      {
        name: { first: "John", middle: "D.", last: "Smith" },
        email: "johnsmith@example.com",
        phone: "0917-555-1234",
        bday: "1995-02-14",
        age: 30,
        property: "Modern Apartment in City Center",
        idImage: "https://via.placeholder.com/400x250?text=John+Smith+ID",
        photo: "https://randomuser.me/api/portraits/men/31.jpg"
      },
      {
        name: { first: "Mary", middle: "A.", last: "Jane" },
        email: "maryjane@example.com",
        phone: "0918-777-5678",
        bday: "1992-05-22",
        age: 33,
        property: "Cozy Suburban House",
        idImage: "https://via.placeholder.com/400x250?text=Mary+Jane+ID",
        photo: "https://randomuser.me/api/portraits/women/44.jpg"
      },
      {
        name: { first: "Carlos", middle: "Dela", last: "Cruz" },
        email: "carlosdc@example.com",
        phone: "0920-222-9999",
        bday: "1988-09-30",
        age: 37,
        property: "Luxury Condo with Ocean View",
        idImage: "https://via.placeholder.com/400x250?text=Carlos+Dela+Cruz+ID",
        photo: "https://randomuser.me/api/portraits/men/28.jpg"
      },
      {
        name: { first: "Ella", middle: "Mae", last: "Santos" },
        email: "ellas@example.com",
        phone: "0921-889-7722",
        bday: "1998-08-11",
        age: 27,
        property: "Townhouse in Green Village",
        idImage: "https://via.placeholder.com/400x250?text=Ella+Mae+Santos+ID",
        photo: "https://randomuser.me/api/portraits/women/12.jpg"
      }
    ];

    const listContainer = document.getElementById("clientsList");
    const detailsDiv = document.getElementById("clientDetails");

    clients.forEach((c, i) => {
      const clientItem = document.createElement("div");
      clientItem.classList.add("client-item", "animate__animated", "animate__fadeInUp");
      clientItem.innerHTML = `
        <img src="${c.photo}" class="client-photo rounded-circle">
        <span class="client-name">${c.name.first} ${c.name.last}</span>
      `;
      clientItem.onclick = () => showClient(i);
      listContainer.appendChild(clientItem);
    });

    function showClient(i) {
      const c = clients[i];
      detailsDiv.innerHTML = `
        <div class="animate__animated animate__fadeIn">
          <div class="text-center mb-3">
            <img src="${c.photo}" class="client-detail-photo rounded-circle shadow-sm mb-2" alt="${c.name.first}">
            <h5 class="fw-bold">${c.name.first} ${c.name.middle} ${c.name.last}</h5>
          </div>
          <p><strong>Email:</strong> ${c.email}</p>
          <p><strong>Phone:</strong> ${c.phone}</p>
          <p><strong>Birthday:</strong> ${c.bday}</p>
          <p><strong>Age:</strong> ${c.age}</p>
          <p><strong>Property Interested:</strong> ${c.property}</p>
          <p><strong>Valid ID:</strong>
            <button class="btn-eye" onclick="viewID('${c.idImage}')">
              <i class="bi bi-eye-fill"></i> View ID
            </button>
          </p>
        </div>
      `;
    }

    function viewID(src) {
      document.getElementById("idImage").src = src;
      new bootstrap.Modal(document.getElementById("idModal")).show();
    }
  </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chat - Agent Dashboard</title>

  <!-- Bootstrap + Icons + Animations -->

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url("assets/css/agenStyle.css")?>">
  <link rel="stylesheet" href="<?= base_url("assets/styles/clientstyle.css")?>">
</head>

<body>
  <!-- ✅ Unified Navbar (same as main dashboard) -->
          <nav class="navbar navbar-expand-lg navbar-light sticky-top custom-nav" id="mainNav">
                <div class="container-fluid">
                    <!-- Logo -->
                    <a class="navbar-brand d-flex align-items-center" href="homepage.html">
                        <img src="images/amb_logo.png" alt="AMB Logo" height="40" class="me-2">
                        <span class="logo-text">PROPERTY</span>
                    </a>

                    <!-- Hamburger Button -->
                    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Collapsible Content -->
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <!-- Center nav links (desktop), vertical in mobile -->
                        <ul class="navbar-nav mx-lg-auto text-center text-lg-start main-links">
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="/users/clientHomepage">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="/users/clientbrowse">Browse Properties</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="/users/clientbookings">My Bookings</a>
                            </li>
                            <li class="nav-item d-lg-none">
                                <a class="nav-link nav-link-custom" href="/users/clientprofile">Profile</a>
                            </li>
                        </ul>

                        <!-- Right-aligned (desktop only) -->
                        <ul class="navbar-nav align-items-center d-none d-lg-flex">
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="/users/clientprofile">
                                    <i class="bi bi-person me-2"></i>
                                </a>
                            </li>
                            <li class="nav-item ms-2">
                                <button class="btn btn-link p-2" id="themeToggle" onclick="toggleTheme()">
                                    <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>


  <div class="container-fluid pt-4 mb-2">
    <div class="chat-container shadow-sm rounded-4 overflow-hidden bg-white animate__animated animate__fadeInUp d-flex">  
    <div class="chat-sidebar border-end d-flex flex-column" id="sidebar" style="width: 300px; height: 80vh; overflow: hidden;">
      <div class="chat-search p-3 border-bottom d-flex justify-content-between align-items-center bg-white" style="flex-shrink: 0;">
        <div class="d-flex align-items-center w-100">
          <input type="text" id="searchInput" class="form-control rounded-pill me-2" placeholder="Search clients...">
          <button id="toggleSidebar" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pin"></i>
          </button>
        </div>
      </div>

      <div class="chat-client-list flex-grow-1 overflow-auto p-2" style="min-height: 0;">
        <?php foreach ($clients as $client): ?>
          <div class="chat-item d-flex align-items-center gap-3 p-2 rounded hover-bg"
              data-session-id="<?= $client['chatSessionID'] ?>"
              data-name="<?= esc($client['fullname']) ?>">
            <img src="https://via.placeholder.com/45" class="rounded-circle shadow-sm" alt="">
            <div class="text-truncate" style="max-width: 200px;">
              <strong><?= esc($client['fullname']) ?></strong><br>
              <small class="text-muted"><?= esc($client['lastMessage']) ?></small>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    
  <div id="hoverZone"></div>
      <div class="chat-main d-flex flex-column flex-grow-1 bg-light" id="chatMain" style="height: 80vh; overflow: hidden;">
        <div class="chat-header bg-white p-3 border-bottom fw-semibold d-flex justify-content-between align-items-center">
          <span id="chatHeader">Select a client</span>
        </div>

        <div class="chat-messages flex-grow-1 p-4 overflow-auto d-flex flex-column gap-2" 
            id="chatMessages" 
            style="min-height: 0; max-height: calc(100vh - 160px); overflow-y: auto;">
          <p class="text-muted text-center">No conversation selected.</p>
        </div>

        <div class="chat-input bg-white border-top p-3 d-flex align-items-center gap-2">
          <input type="text" id="messageInput" class="form-control rounded-pill" placeholder="Type a message..." disabled>
          <button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" disabled>
            <i class="bi bi-send-fill"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Scripts -->
<script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
<script>
const chatItems = document.querySelectorAll('.chat-item');
const chatHeader = document.getElementById('chatHeader');
const chatMessages = document.getElementById('chatMessages');
const messageInput = document.getElementById('messageInput');
const sendButton = document.querySelector('.chat-input button');

let currentSessionId = null;
const currentRole = '<?= session()->get('role'); ?>'; // 'User' or 'Agent'

// Load messages dynamically when clicking chat item
chatItems.forEach(item => {
  item.addEventListener('click', () => {
    currentSessionId = item.dataset.sessionId;
    const name = item.dataset.name;
    chatHeader.textContent = name;
    chatMessages.innerHTML = '<p class="text-muted text-center">Loading...</p>';

    // --- Using AJAX instead of fetch ---
    $.ajax({
      url: `/chat/messages/${currentSessionId}`,
      method: 'GET',
      dataType: 'json',
      success: function(messages) {
        chatMessages.innerHTML = '';

        if (!messages || messages.length === 0) {
          chatMessages.innerHTML = '<p class="text-muted text-center">No messages yet.</p>';
          return;
        }

        messages.forEach(msg => {
          const div = document.createElement('div');
          div.className = 'message p-2 rounded-3 my-1';
          div.style.maxWidth = '75%';
          div.textContent = msg.messageContent;

          if (msg.senderRole === currentRole) {
            div.classList.add('bg-primary', 'text-white', 'align-self-end');
          } else {
            div.classList.add('bg-white', 'border', 'align-self-start');
          }

          chatMessages.appendChild(div);
        });

        chatMessages.scrollTop = chatMessages.scrollHeight;
      }
    });

    messageInput.disabled = false;
    sendButton.disabled = false;
  });
});

// Send message when clicking send button or pressing Enter
sendButton.addEventListener('click', sendMessage);
messageInput.addEventListener('keypress', e => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
});

function sendMessage() {
  const text = messageInput.value.trim();
  if (!text || !currentSessionId) return;

  // --- Use AJAX instead of fetch ---
  $.ajax({
    url: '/chat/send',
    method: 'POST',
    data: {
      chatSessionID: currentSessionId,
      messageText: text
    },
    dataType: 'json',
    success: function(res) {
      if (res.status === 'success') {
        const msg = document.createElement('div');
        msg.className = 'message bg-primary text-white p-2 rounded-3 my-1 align-self-end';
        msg.style.maxWidth = '75%';
        msg.textContent = text;

        chatMessages.appendChild(msg);
        messageInput.value = '';
        chatMessages.scrollTop = chatMessages.scrollHeight;
      }
    }
  });
}

// --- Optional: Auto-refresh messages every 3 seconds ---
setInterval(() => {
  if (currentSessionId) {
    $.ajax({
      url: `/chat/messages/${currentSessionId}`,
      method: 'GET',
      dataType: 'json',
      success: function(messages) {
        chatMessages.innerHTML = '';

        messages.forEach(msg => {
          const div = document.createElement('div');
          div.className = 'message p-2 rounded-3 my-1';
          div.style.maxWidth = '75%';
          div.textContent = msg.messageContent;

          if (msg.senderRole === currentRole) {
            div.classList.add('bg-primary', 'text-white', 'align-self-end');
          } else {
            div.classList.add('bg-white', 'border', 'align-self-start');
          }

          chatMessages.appendChild(div);
        });

        chatMessages.scrollTop = chatMessages.scrollHeight;
      }
    });
  }
}, 3000);
</script>

<!-- Include jQuery if not already added -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



</body>
</html>

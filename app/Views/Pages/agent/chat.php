<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chat - ABM Property</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <style>
    /* CSS VARIABLES */
    :root {
      --primary: #469541;
      --primary-hover: #357a34;
      --secondary: #000000;
      --accent: #2d9fa8;
      --light-bg: #d3f0ff;
      --light-accent: #c8f5d2;
      --bg-color: #ffffff;
      --text-color: #333333;
      --text-muted: #666666;
      --card-bg: #ffffff;
      --border-color: rgba(0, 0, 0, 0.1);
      --shadow: rgba(0, 0, 0, 0.1);
      --chat-bg: #f8f9fa;
    }

    html[data-theme="dark"] {
      --primary: #5ab34f;
      --primary-hover: #469541;
      --secondary: #c4aee3;
      --accent: #3eb3bd;
      --light-bg: #252e42;
      --light-accent: #2d4038;
      --bg-color: #1a1f2e;
      --text-color: #f0f0f0;
      --text-muted: #a0a0a0;
      --card-bg: #252a3a;
      --border-color: rgba(255, 255, 255, 0.1);
      --shadow: rgba(0, 0, 0, 0.3);
      --chat-bg: #1e2433;
    }

    body {
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: background-color 0.3s, color 0.3s;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* NAVIGATION */
    .navbar {
      background: linear-gradient(120deg, #d3f0ff 0%, #c8f5d2 100%);
      padding: 10px 50px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      position: sticky;
      top: 0;
      z-index: 1030;
      border: none !important;
      height: 100px;
    }

    html[data-theme="dark"] .navbar {
      background: linear-gradient(120deg, #252e42 0%, #2d4038 100%);
    }

    .logo-text {
      color: var(--secondary);
      font-size: 20px;
      font-weight: 500;
      letter-spacing: 3px;
    }

    html[data-theme="dark"] .logo-text {
      color: var(--primary);
    }

    .nav-link-custom {
      color: var(--text-color) !important;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      font-size: 18px;
      font-weight: 500;
      transition: all 0.2s;
    }

    .nav-link-custom:hover,
    .nav-link-custom.active {
      color: var(--primary) !important;
    }

    .main-links .nav-item {
      margin: 0 1.5rem;
    }

    #themeToggle {
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
    }

    #themeToggle i {
      color: var(--text-color);
      transition: color 0.3s;
    }

    #themeToggle:hover i {
      color: var(--primary);
    }

    /* MAIN CONTENT */
    .main-content {
      padding: 2rem;
      min-height: calc(100vh - 100px);
    }

    /* CHAT CONTAINER */
    .chat-container {
      background-color: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      box-shadow: 0 2px 10px var(--shadow);
      overflow: hidden;
    }

    /* CHAT SIDEBAR */
    .chat-sidebar {
      background-color: var(--card-bg);
      border-right: 1px solid var(--border-color);
    }

    .chat-search {
      background-color: var(--light-bg);
      border-bottom: 1px solid var(--border-color);
    }

    html[data-theme="dark"] .chat-search {
      background-color: var(--light-bg);
    }

    .chat-search input {
      background-color: var(--bg-color);
      border: 1px solid var(--border-color);
      color: var(--text-color);
    }

    .chat-search input:focus {
      background-color: var(--bg-color);
      border-color: var(--primary);
      color: var(--text-color);
    }

    .chat-search input::placeholder {
      color: var(--text-muted);
    }

    /* CHAT ITEMS */
    .chat-item {
      cursor: pointer;
      transition: background-color 0.2s;
      border-radius: 8px;
    }

    .chat-item:hover,
    .chat-item.active {
      background-color: var(--light-bg);
    }

    .chat-item strong {
      color: var(--text-color);
    }

    .chat-item small {
      color: var(--text-muted) !important;
    }

    /* CHAT MAIN */
    .chat-main {
      background-color: var(--chat-bg);
    }

    .chat-header {
      background-color: var(--card-bg);
      border-bottom: 1px solid var(--border-color);
      color: var(--text-color);
    }

    .chat-messages {
      background-color: var(--chat-bg);
    }

    /* MESSAGE BUBBLES */
    .message {
      max-width: 75%;
      word-wrap: break-word;
    }

    .message.bg-primary {
      background-color: var(--primary) !important;
      color: white !important;
    }

    .message.bg-white {
      background-color: var(--card-bg) !important;
      border: 1px solid var(--border-color) !important;
      color: var(--text-color) !important;
    }

    /* CHAT INPUT */
    .chat-input {
      background-color: var(--card-bg);
      border-top: 1px solid var(--border-color);
    }

    .chat-input input {
      background-color: var(--bg-color);
      border: 1px solid var(--border-color);
      color: var(--text-color);
    }

    .chat-input input:focus {
      background-color: var(--bg-color);
      border-color: var(--primary);
      color: var(--text-color);
    }

    .chat-input input::placeholder {
      color: var(--text-muted);
    }

    .chat-input button {
      background-color: var(--primary);
      border-color: var(--primary);
    }

    .chat-input button:hover {
      background-color: var(--primary-hover);
    }

    /* BUTTONS */
    .btn-primary {
      background-color: var(--primary) !important;
      border-color: var(--primary) !important;
    }

    .btn-primary:hover {
      background-color: var(--primary-hover) !important;
    }

    .btn-outline-primary {
      color: var(--primary) !important;
      border-color: var(--primary) !important;
    }

    .btn-outline-primary:hover {
      background-color: var(--primary) !important;
      color: white !important;
    }

    /* TEXT */
    h1, h2, h3, h4, h5, h6, p, span, strong {
      color: var(--text-color);
    }

    .text-muted {
      color: var(--text-muted) !important;
    }

    /* SCROLLBAR */
    .chat-client-list::-webkit-scrollbar,
    .chat-messages::-webkit-scrollbar {
      width: 6px;
    }

    .chat-client-list::-webkit-scrollbar-track,
    .chat-messages::-webkit-scrollbar-track {
      background: transparent;
    }

    .chat-client-list::-webkit-scrollbar-thumb,
    .chat-messages::-webkit-scrollbar-thumb {
      background: var(--text-muted);
      border-radius: 10px;
    }

    /* RESPONSIVE */
    @media (max-width: 992px) {
      .navbar {
        padding: 10px 20px;
        height: auto;
      }
      .main-links .nav-item {
        margin: 0;
      }
      .nav-link-custom {
        text-align: left;
        padding-left: 1.8rem;
      }
      .main-content {
        padding: 1rem;
      }
      .chat-sidebar {
        width: 100% !important;
      }
    }
  </style>
</head>

<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="/users/agentHomepage">
        <img src="<?= base_url('assets/img/AMB_logo.png') ?>" alt="AMB Logo" height="50" class="me-2">
        <span class="logo-text">PROPERTY</span>
      </a>

      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-lg-auto text-center main-links">
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentHomepage">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentclients">Clients</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentbookings">Bookings</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentproperties">Properties</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom active" href="/users/agentchat">Chat</a></li>
          <li class="nav-item d-lg-none"><a class="nav-link nav-link-custom" href="/users/agentprofile">Profile</a></li>
        </ul>

        <ul class="navbar-nav align-items-center d-none d-lg-flex">
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentprofile"><i class="bi bi-person me-2"></i></a></li>
          <li class="nav-item ms-2">
            <button class="btn btn-link p-2" id="themeToggle" onclick="toggleTheme()">
              <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <div class="container-fluid">
      <h3 class="fw-bold mb-4">Chat</h3>

      <div class="chat-container animate__animated animate__fadeInUp d-flex">
        <!-- Sidebar -->
        <div class="chat-sidebar d-flex flex-column" id="sidebar" style="width: 300px; height: 70vh; overflow: hidden;">
          <!-- Fixed Search Bar -->
          <div class="chat-search p-3 d-flex justify-content-between align-items-center" style="flex-shrink: 0;">
            <div class="d-flex align-items-center w-100">
              <input type="text" id="searchInput" class="form-control rounded-pill me-2" placeholder="Search clients...">
              <button id="toggleSidebar" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pin"></i>
              </button>
            </div>
          </div>

          <!-- Scrollable Client List -->
          <div class="chat-client-list flex-grow-1 overflow-auto p-2" style="min-height: 0;">
            <?php foreach ($clients as $client): ?>
              <div class="chat-item d-flex align-items-center gap-3 p-2 rounded"
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

        <!-- Main Chat Area -->
        <div class="chat-main d-flex flex-column flex-grow-1" id="chatMain" style="height: 70vh; overflow: hidden;">
          <!-- Header -->
          <div class="chat-header p-3 fw-semibold d-flex justify-content-between align-items-center">
            <span id="chatHeader">Select a client</span>
          </div>

          <!-- Scrollable Messages -->
          <div class="chat-messages flex-grow-1 p-4 overflow-auto d-flex flex-column gap-2" 
              id="chatMessages" 
              style="min-height: 0;">
            <p class="text-muted text-center">No conversation selected.</p>
          </div>

          <!-- Input Area -->
          <div class="chat-input p-3 d-flex align-items-center gap-2">
            <input type="text" id="messageInput" class="form-control rounded-pill" placeholder="Type a message..." disabled>
            <button class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" disabled>
              <i class="bi bi-send-fill"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
  
  <script>
  // Theme Toggle
  function toggleTheme() {
    const html = document.documentElement;
    const themeIcon = document.getElementById('themeIcon');
    const currentTheme = html.getAttribute('data-theme');
    
    if (currentTheme === 'light') {
      html.setAttribute('data-theme', 'dark');
      themeIcon.classList.remove('bi-sun-fill');
      themeIcon.classList.add('bi-moon-fill');
      localStorage.setItem('theme', 'dark');
    } else {
      html.setAttribute('data-theme', 'light');
      themeIcon.classList.remove('bi-moon-fill');
      themeIcon.classList.add('bi-sun-fill');
      localStorage.setItem('theme', 'light');
    }
  }

  // Load saved theme
  document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const themeIcon = document.getElementById('themeIcon');
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    if (savedTheme === 'dark') {
      themeIcon.classList.remove('bi-sun-fill');
      themeIcon.classList.add('bi-moon-fill');
    }
  });

  // Chat Functionality
  const chatItems = document.querySelectorAll('.chat-item');
  const chatHeader = document.getElementById('chatHeader');
  const chatMessages = document.getElementById('chatMessages');
  const messageInput = document.getElementById('messageInput');
  const sendButton = document.querySelector('.chat-input button');

  let currentSessionId = null;
  const currentRole = '<?= session()->get('role'); ?>'; 

  chatItems.forEach(item => {
    item.addEventListener('click', () => {
      // Remove active class from all items
      chatItems.forEach(i => i.classList.remove('active'));
      // Add active class to clicked item
      item.classList.add('active');
      
      currentSessionId = item.dataset.sessionId;
      const name = item.dataset.name;
      chatHeader.textContent = name;
      chatMessages.innerHTML = '<p class="text-muted text-center">Loading...</p>';

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

  // Auto-refresh messages
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
</body>
</html>
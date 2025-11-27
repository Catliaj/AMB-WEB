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
  
  <style>
    /* Chat Page Specific Styles - Using existing CSS variables from clientstyle.css */
    body {
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: background-color 0.3s, color 0.3s;
    }

    .chat-container {
      background-color: var(--card-bg);
      border: 1px solid var(--border-color);
      box-shadow: 0 4px 12px var(--shadow) !important;
    }
    
    .chat-sidebar {
      background-color: var(--card-bg);
      border-right: 1px solid var(--border-color);
    }
    
    .chat-search {
      background-color: var(--card-bg);
      border-bottom: 1px solid var(--border-color);
    }
    
    .chat-search input {
      background-color: var(--bg-color);
      border-color: var(--border-color);
      color: var(--text-color);
    }
    
    .chat-search input:focus {
      background-color: var(--bg-color);
      color: var(--text-color);
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(70, 149, 65, 0.15);
    }
    
    .chat-search input::placeholder {
      color: var(--text-muted);
    }
    
    .chat-client-list {
      background-color: var(--card-bg);
    }
    
    .chat-item {
      background-color: var(--card-bg);
      border-radius: 0.75rem;
      transition: all 0.2s ease;
      cursor: pointer;
      padding: 0.75rem 1rem;
      margin-bottom: 0.5rem;
      border: 1px solid transparent;
    }
    
    .chat-item:hover {
      background-color: var(--light-bg);
      transform: translateY(-1px);
      box-shadow: 0 4px 8px var(--shadow);
    }
    
    .chat-item.active {
      background-color: var(--light-accent);
      border-color: var(--primary);
    }
    
    .client-avatar {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 1rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    
    .client-info {
      flex: 1;
      min-width: 0;
    }
    
    .client-name {
      font-weight: 600;
      color: var(--text-color);
      margin-bottom: 0.25rem;
      font-size: 0.95rem;
    }
    
    .client-last-message {
      color: var(--text-muted);
      font-size: 0.85rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .chat-main {
      background-color: var(--bg-color);
    }
    
    .chat-header {
      background-color: var(--card-bg);
      border-bottom: 1px solid var(--border-color);
      color: var(--text-color);
      font-weight: 600;
      padding: 1rem 1.5rem;
    }
    
    .chat-messages {
      background-color: var(--bg-color);
    }
    
    .message {
      max-width: 75%;
      padding: 0.75rem 1rem;
      border-radius: 1rem;
      margin-bottom: 0.75rem;
      position: relative;
      box-shadow: 0 2px 4px var(--shadow);
      word-wrap: break-word;
      animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .message.sent {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
      color: white;
      align-self: flex-end;
      border-bottom-right-radius: 0.25rem;
    }
    
    .message.received {
      background-color: var(--card-bg);
      color: var(--text-color);
      border: 1px solid var(--border-color);
      align-self: flex-start;
      border-bottom-left-radius: 0.25rem;
    }
    
    .chat-input {
      background-color: var(--card-bg);
      border-top: 1px solid var(--border-color);
    }
    
    .chat-input input {
      background-color: var(--bg-color);
      border-color: var(--border-color);
      color: var(--text-color);
    }
    
    .chat-input input:focus {
      background-color: var(--bg-color);
      color: var(--text-color);
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(70, 149, 65, 0.15);
    }
    
    .chat-input input::placeholder {
      color: var(--text-muted);
    }
    
    .chat-input button {
      background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
      border: none;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
    }
    
    .chat-input button:hover:not(:disabled) {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(70, 149, 65, 0.3);
    }
    
    .chat-input button:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      background: var(--text-muted) !important;
    }
    
    .no-conversation {
      color: var(--text-muted);
      text-align: center;
      padding: 2rem;
    }
    
    .message-time {
      font-size: 0.7rem;
      opacity: 0.7;
      margin-top: 0.25rem;
      text-align: right;
      color: inherit;
    }
    
    .message.received .message-time {
      text-align: left;
    }
    
    /* Sidebar toggle button */
    #toggleSidebar {
      background-color: var(--bg-color);
      border-color: var(--border-color);
      color: var(--text-color);
    }
    
    #toggleSidebar:hover {
      background-color: var(--light-bg);
      border-color: var(--primary);
      color: var(--primary);
    }

    /* Search functionality */
    .search-highlight {
      background-color: var(--primary);
      color: white;
      padding: 0.1rem 0.2rem;
      border-radius: 0.2rem;
    }

    /* Status indicator */
    .status-indicator {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background-color: #28a745;
      display: inline-block;
      margin-right: 0.5rem;
    }

    .status-offline {
      background-color: var(--text-muted);
    }

    /* Loading spinner */
    .spinner-border.text-primary {
      color: var(--primary) !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .chat-container {
        flex-direction: column;
      }
      
      .chat-sidebar {
        width: 100% !important;
        height: 40vh !important;
      }
      
      .chat-main {
        height: 60vh !important;
      }
    }

    /* Ensure proper contrast in dark mode */
    html[data-theme="dark"] .chat-container {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important;
    }

    html[data-theme="dark"] .client-avatar {
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }

    html[data-theme="dark"] .chat-item:hover {
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
    }
  </style>
</head>

<body>
  <!-- ✅ Unified Navbar (same as main dashboard) -->
  <nav class="navbar navbar-expand-lg navbar-light sticky-top custom-nav" id="mainNav">
    <div class="container-fluid">
      <!-- Logo -->
      <a class="navbar-brand d-flex align-items-center" href="homepage.html">
        <img src="<?= base_url('assets/img/AMB_logo.png') ?>" alt="AMB Logo" height="50" class="me-2">
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
            <a class="nav-link nav-link-custom" href="/users/clientreservations">Reservations</a>
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
    <div class="chat-container shadow-sm rounded-4 overflow-hidden animate__animated animate__fadeInUp d-flex">  
      <div class="chat-sidebar border-end d-flex flex-column" id="sidebar" style="width: 300px; height: 80vh; overflow: hidden;">
        <div class="chat-search p-3 border-bottom d-flex justify-content-between align-items-center" style="flex-shrink: 0;">
          <div class="d-flex align-items-center w-100">
            <input type="text" id="searchInput" class="form-control rounded-pill me-2" placeholder="Search clients...">
            <button id="toggleSidebar" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-pin"></i>
            </button>
          </div>
        </div>

        <div class="chat-client-list flex-grow-1 overflow-auto p-3" style="min-height: 0;">
          <?php foreach ($clients as $client): ?>
            <div class="chat-item d-flex align-items-center gap-3"
                data-session-id="<?= $client['chatSessionID'] ?>"
                data-name="<?= esc($client['fullname']) ?>">
              <div class="client-avatar">
                <?= strtoupper(substr($client['fullname'], 0, 1)) ?>
              </div>
              <div class="client-info">
                <div class="client-name"><?= esc($client['fullname']) ?></div>
                <div class="client-last-message"><?= esc($client['lastMessage']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    
      <div id="hoverZone"></div>
      <div class="chat-main d-flex flex-column flex-grow-1" id="chatMain" style="height: 80vh; overflow: hidden;">
        <div class="chat-header p-3 border-bottom fw-semibold d-flex justify-content-between align-items-center">
          <span id="chatHeader">Select a client</span>
          <div id="chatStatus" class="text-muted small"></div>
        </div>

        <div class="chat-messages flex-grow-1 p-4 overflow-auto d-flex flex-column" 
            id="chatMessages" 
            style="min-height: 0; max-height: calc(100vh - 160px); overflow-y: auto;">
          <div class="no-conversation">
            <i class="bi bi-chat-dots display-4 mb-3"></i>
            <p>Select a conversation to start messaging</p>
          </div>
        </div>

        <div class="chat-input border-top p-3 d-flex align-items-center gap-2">
          <input type="text" id="messageInput" class="form-control rounded-pill" placeholder="Type a message..." disabled>
          <button id="sendButton" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" disabled>
            <i class="bi bi-send-fill"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Scripts -->
  <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <script>
    // Theme toggle functionality
    function toggleTheme() {
      const html = document.documentElement;
      const themeIcon = document.getElementById('themeIcon');
      const currentTheme = html.getAttribute('data-theme');
      
      if (currentTheme === 'dark') {
        html.removeAttribute('data-theme');
        themeIcon.className = 'bi bi-sun-fill fs-5';
        localStorage.setItem('theme', 'light');
      } else {
        html.setAttribute('data-theme', 'dark');
        themeIcon.className = 'bi bi-moon-fill fs-5';
        localStorage.setItem('theme', 'dark');
      }
    }

    // Initialize theme
    function initTheme() {
      const savedTheme = localStorage.getItem('theme') || 'light';
      const themeIcon = document.getElementById('themeIcon');
      
      if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeIcon.className = 'bi bi-moon-fill fs-5';
      } else {
        document.documentElement.removeAttribute('data-theme');
        themeIcon.className = 'bi bi-sun-fill fs-5';
      }
    }

    // Call initTheme when page loads
    document.addEventListener('DOMContentLoaded', initTheme);

    // Chat functionality
    window.initialSessionId = <?= json_encode($_GET['session'] ?? null) ?>;

    const chatItems = document.querySelectorAll('.chat-item');
    const chatHeader = document.getElementById('chatHeader');
    const chatMessages = document.getElementById('chatMessages');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');

    let currentSessionId = null;
    const currentRole = '<?= session()->get('role'); ?>'; // 'User' or 'Agent'

    // Load messages dynamically when clicking chat item
    chatItems.forEach(item => {
      item.addEventListener('click', function() {
        // Remove active class from all items
        chatItems.forEach(i => i.classList.remove('active'));
        // Add active class to clicked item
        this.classList.add('active');
        
        currentSessionId = this.dataset.sessionId;
        const name = this.dataset.name;
        chatHeader.textContent = name;
        chatMessages.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading messages...</p></div>';

        // --- Using AJAX instead of fetch ---
        $.ajax({
          url: `/chat/messages/${currentSessionId}`,
          method: 'GET',
          dataType: 'json',
          success: function(messages) {
            chatMessages.innerHTML = '';

            if (!messages || messages.length === 0) {
              chatMessages.innerHTML = '<div class="no-conversation"><i class="bi bi-chat-text display-4 mb-3"></i><p class="text-muted">No messages yet. Start the conversation!</p></div>';
              return;
            }

            messages.forEach(msg => {
              const div = document.createElement('div');
              div.className = `message ${msg.senderRole === currentRole ? 'sent' : 'received'}`;
              div.innerHTML = `
                <div>${msg.messageContent}</div>
                <div class="message-time">${formatMessageTime(msg.timestamp || new Date().toISOString())}</div>
              `;

              chatMessages.appendChild(div);
            });

            requestAnimationFrame(() => {
                chatMessages.scrollTop = chatMessages.scrollHeight; // ✅ scroll after render
            });
            
          }
        });

        messageInput.disabled = false;
        sendButton.disabled = false;
        messageInput.focus();
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

    // Auto-open a session if one was provided via the `session` query parameter
    if (window.initialSessionId) {
      // try to find the corresponding sidebar element and click it
      const el = document.querySelector(`.chat-item[data-session-id='${window.initialSessionId}']`);
      if (el) {
        el.click();
      } else {
        // fallback: fetch messages directly for the session id
        currentSessionId = window.initialSessionId;
        chatHeader.textContent = 'Conversation';
        chatMessages.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading messages...</p></div>';
        $.ajax({
          url: `/chat/messages/${currentSessionId}`,
          method: 'GET',
          dataType: 'json',
          success: function(messages) {
            chatMessages.innerHTML = '';
            if (!messages || messages.length === 0) {
              chatMessages.innerHTML = '<div class="no-conversation"><i class="bi bi-chat-text display-4 mb-3"></i><p class="text-muted">No messages yet. Start the conversation!</p></div>';
            } else {
              messages.forEach(msg => {
                const div = document.createElement('div');
                div.className = `message ${msg.senderRole === currentRole ? 'sent' : 'received'}`;
                div.innerHTML = `
                  <div>${msg.messageContent}</div>
                  <div class="message-time">${formatMessageTime(msg.timestamp || new Date().toISOString())}</div>
                `;

                chatMessages.appendChild(div);
              });
              requestAnimationFrame(() => { chatMessages.scrollTop = chatMessages.scrollHeight; });
            }
            messageInput.disabled = false;
            sendButton.disabled = false;
          }
        });
      }
    }

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
            const div = document.createElement('div');
            div.className = 'message sent';
            div.innerHTML = `
              <div>${text}</div>
              <div class="message-time">${formatMessageTime(new Date().toISOString())}</div>
            `;

            chatMessages.appendChild(div);
            messageInput.value = '';
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Update last message in sidebar
            const activeItem = document.querySelector('.chat-item.active');
            if (activeItem) {
              const lastMessageEl = activeItem.querySelector('.client-last-message');
              if (lastMessageEl) {
                lastMessageEl.textContent = text;
              }
            }
          }
        }
      });
    }

    // Format message timestamp
    function formatMessageTime(timestamp) {
      const date = new Date(timestamp);
      const now = new Date();
      const diffMs = now - date;
      const diffMins = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000);
      
      if (diffMins < 1) {
        return 'Just now';
      } else if (diffMins < 60) {
        return `${diffMins} min ago`;
      } else if (diffHours < 24) {
        return `${diffHours} hr ago`;
      } else {
        return date.toLocaleDateString();
      }
    }

    // --- Optional: Auto-refresh messages every 3 seconds ---
    setInterval(() => {
      if (currentSessionId) {
        $.ajax({
          url: `/chat/messages/${currentSessionId}`,
          method: 'GET',
          dataType: 'json',
          success: function(messages) {
            // Only update if we have new messages
            const currentMessageCount = chatMessages.querySelectorAll('.message').length;
            if (messages && messages.length > currentMessageCount) {
              chatMessages.innerHTML = '';

              messages.forEach(msg => {
                const div = document.createElement('div');
                div.className = `message ${msg.senderRole === currentRole ? 'sent' : 'received'}`;
                div.innerHTML = `
                  <div>${msg.messageContent}</div>
                  <div class="message-time">${formatMessageTime(msg.timestamp || new Date().toISOString())}</div>
                `;

                chatMessages.appendChild(div);
              });

              chatMessages.scrollTop = chatMessages.scrollHeight;
            }
          }
        });
      }
    }, 3000);
  </script>
</body>
</html>
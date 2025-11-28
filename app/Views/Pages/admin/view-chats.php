<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Chat Sessions | Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/styles/admin-style.css')?>">
  <link rel="stylesheet" href="<?= base_url('client/clientstyle.css')?>">
  <style>
    :root {
      --bg: #f6f8fb;
      --card: #ffffff;
      --text: #1f2937;
      --muted: #6b7280;
      --accent1: #4e9eff;
      --accent2: #2a405a;
      --accent3: #68b76b;
      --shadow: 0 6px 18px rgba(20,25,30,0.06);
      --divider: #e6e9ef;
      --hover-overlay: rgba(0,0,0,0.04);
    }
    /* Sidebar and table header overrides to use the light palette */
    .sidebar { background: linear-gradient(120deg, #d3f0ff 0%, #c8f5d2 100%); border-color: var(--divider); }
    html[data-theme="dark"] .sidebar { background: linear-gradient(120deg, #252e42 0%, #2d4038 100%); }
    .nav a { color: var(--text); }
    thead { background: var(--th-bg, var(--card)); }
    th { color: var(--th-text, var(--text)); }
  </style>
  <script>
    (function(){
      try {
        var t = localStorage.getItem('adm_theme_pref');
        if (t === 'light') document.documentElement.setAttribute('data-theme','light');
        else if (t === 'dark') document.documentElement.removeAttribute('data-theme');
        else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) document.documentElement.setAttribute('data-theme','light');
      } catch(e){}
    })();
  </script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  
</head>
<body>

  <aside class="sidebar" style="display:flex;flex-direction:column;justify-content:space-between;">
    <div>
      <img src="<?= base_url('assets/img/amb_logo.png')?>" alt="AMB Logo">
      <nav class="nav">
        <a href="/admin/adminHomepage"><i data-lucide="layout-dashboard"></i> Dashboard</a>
        <a href="/admin/manageUsers"><i data-lucide="users"></i> Manage Users</a>
        <a href="/admin/ManageProperties"><i data-lucide="home"></i> Manage Properties</a>
        <!-- User Bookings removed -->
        <a href="/admin/viewChats" class="active" style="background: linear-gradient(90deg, #2e7d32, #1565c0);"><i data-lucide="message-circle"></i> View Chats</a>
        <a href="/admin/Reports"><i data-lucide="bar-chart-2"></i> Generate Reports</a>
        <a href="/admin/editProfile"><i data-lucide="user"></i> Edit Profile</a>
      </nav>
    </div>

    <a href="<?= base_url('/admin/editProfile') ?>" class="profile-box" style="text-decoration:none;color:inherit;display:block;margin-top:10px;">
      <div class="profile-avatar">A</div>
       <div class="profile-info">
        <strong><?= session('FirstName') . ' ' . session('LastName'); ?></strong>
        <span><?= session('inputEmail'); ?></span>
      </div>
    </a>
  </aside>

  <main class="main">
    <header>
      <div class="left-header">
        <button id="toggleSidebar" class="btn"><i data-lucide="menu"></i></button>
        <h1><i data-lucide="message-circle"></i> View Chat Sessions</h1>
      </div>
    </header>

    <div class="chat-layout">
      <div class="chat-sidebar">
        <h2>Chat Sessions</h2>
        <ul class="chat-list" id="chatList"></ul>
      </div>

      <div class="chat-main">
        <div class="chat-header">
          <h2 id="chatTitle">Select a session</h2>
          <div class="search-bar">
            <input type="text" id="messageSearch" placeholder="Search messages...">
          </div>
        </div>
        <div class="chat-messages" id="chatMessages"></div>
        <div class="chat-input" id="chatInputContainer">
          <input type="text" id="adminMessage" placeholder="Type message..." disabled>
          <button class="btn primary" id="sendBtn" disabled><i data-lucide="send"></i> Send</button>
        </div>
      </div>

      <div class="chat-info" id="chatInfo">
        <h3>Chat Details</h3>
        <p><strong>Session ID:</strong> <span id="infoSession"></span></p>
        <p><strong>Agent:</strong> <span id="infoAgent"></span></p>
        <p><strong>Client:</strong> <span id="infoClient"></span></p>
        <p><strong>Property:</strong> <span id="infoProperty"></span></p>
        <p><strong>Date:</strong> <span id="infoDate"></span></p>
        <p><strong>Status:</strong> <span id="infoStatus"></span></p>
        <hr>
        <button class="btn primary" onclick="exportChat('pdf')"><i data-lucide='file-text'></i> Export as PDF</button>
        <button class="btn" onclick="exportChat('txt')"><i data-lucide='download'></i> Export as Text</button>
        <hr>
        <button class="btn" id="toggleChat"><i data-lucide="lock"></i> Allow Admin to Chat</button>
      </div>
    </div>
  </main>

  <script>
    lucide.createIcons();

    const chatSessions = [
      {
        id: "CHAT001",
        agent: "Agent A",
        client: "John Santos",
        property: "Modern Studio Apartment",
        date: "2025-10-05",
        status: "Active",
        messages: [
          { sender: "Client", text: "Hi, I'm interested in the studio apartment.", time: "10:02 AM" },
          { sender: "Agent", text: "Hello John! I'd be happy to help.", time: "10:03 AM" },
          { sender: "Client", text: "Is it still available?", time: "10:04 AM" }
        ]
      },
      {
        id: "CHAT002",
        agent: "Agent B",
        client: "Maria Cruz",
        property: "Elegant Condo Unit",
        date: "2025-10-07",
        status: "Closed",
        messages: [
          { sender: "Client", text: "Can we schedule a property visit?", time: "3:15 PM" },
          { sender: "Agent", text: "Sure! How about next Monday?", time: "3:17 PM" }
        ]
      }
    ];

    const chatList = document.getElementById('chatList');
    const chatMessages = document.getElementById('chatMessages');
    const chatTitle = document.getElementById('chatTitle');
    const adminMsg = document.getElementById('adminMessage');
    const sendBtn = document.getElementById('sendBtn');
    const toggleBtn = document.getElementById('toggleChat');
    const searchInput = document.getElementById('messageSearch');

    let selectedChat = null;
    let adminCanChat = false;

    function renderChatList() {
      chatList.innerHTML = "";
      chatSessions.forEach((s, i) => {
        const li = document.createElement('li');
        li.innerHTML = `
          <strong>${s.client}</strong> <span class="small">(${s.agent})</span><br>
          <span class="small">${s.property}</span><br>
          <span class="small">${s.date} • ${s.status}</span>
        `;
        li.onclick = () => selectChat(i);
        chatList.appendChild(li);
      });
    }

    function selectChat(index) {
      selectedChat = chatSessions[index];
      document.querySelectorAll('.chat-list li').forEach((li, i) => li.classList.toggle('active', i === index));
      chatTitle.textContent = `${selectedChat.client} - ${selectedChat.property}`;
      renderMessages();
      renderChatInfo();
    }

    function renderMessages(filter = "") {
      chatMessages.innerHTML = "";
      selectedChat.messages
        .filter(m => m.text.toLowerCase().includes(filter.toLowerCase()))
        .forEach(m => {
          const div = document.createElement('div');
          div.classList.add('message');
          div.classList.add(m.sender === "Agent" ? "from-agent" : m.sender === "Client" ? "from-client" : "from-admin");
          div.innerHTML = `<strong>${m.sender}:</strong> ${m.text}<br><span style="font-size:0.7rem;color:#aaa;">${m.time}</span>`;
          chatMessages.appendChild(div);
        });
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function renderChatInfo() {
      document.getElementById('infoSession').textContent = selectedChat.id;
      document.getElementById('infoAgent').textContent = selectedChat.agent;
      document.getElementById('infoClient').textContent = selectedChat.client;
      document.getElementById('infoProperty').textContent = selectedChat.property;
      document.getElementById('infoDate').textContent = selectedChat.date;
      document.getElementById('infoStatus').textContent = selectedChat.status;
    }

    sendBtn.onclick = () => {
      if (!adminCanChat || !adminMsg.value.trim()) return;
      selectedChat.messages.push({ sender: "Admin", text: adminMsg.value, time: new Date().toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'}) });
      adminMsg.value = "";
      renderMessages(searchInput.value);
    };

    toggleBtn.onclick = () => {
      adminCanChat = !adminCanChat;
      adminMsg.disabled = !adminCanChat;
      sendBtn.disabled = !adminCanChat;
      toggleBtn.innerHTML = adminCanChat
        ? "<i data-lucide='unlock'></i> Admin Chat Enabled"
        : "<i data-lucide='lock'></i> Admin Read-Only";
      lucide.createIcons();
    };

    searchInput.addEventListener('input', e => {
      if (selectedChat) renderMessages(e.target.value);
    });

    function exportChat(format) {
      if (!selectedChat) return alert("Select a chat session first.");
      const chatText = selectedChat.messages.map(m => `${m.sender}: ${m.text} (${m.time})`).join("\n");
      if (format === "txt") {
        const blob = new Blob([chatText], { type: "text/plain" });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = `${selectedChat.id}.txt`;
        link.click();
      } else {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.text(`Chat Session: ${selectedChat.id}\n\n${chatText}`, 10, 10);
        doc.save(`${selectedChat.id}.pdf`);
      }
    }

    renderChatList();
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) try { lucide.createIcons(); } catch(e){}
      const t = document.getElementById('toggleSidebar');
      if (t) {
        const updateAria = () => {
          const isDesktop = window.matchMedia('(min-width:701px)').matches;
          if (isDesktop) {
            const expanded = !document.body.classList.contains('sidebar-collapsed');
            t.setAttribute('aria-expanded', expanded.toString());
          } else {
            const sidebar = document.querySelector('.sidebar');
            const expanded = !!(sidebar && sidebar.classList.contains('show'));
            t.setAttribute('aria-expanded', expanded.toString());
          }
        };
        updateAria();
        t.addEventListener('click', () => {
          if (window.matchMedia('(min-width:701px)').matches) {
            document.body.classList.toggle('sidebar-collapsed');
            updateAria();
          } else {
            const sidebar = document.querySelector('.sidebar');
            const visible = sidebar?.classList.toggle('show');
            t.setAttribute('aria-expanded', (!!visible).toString());
          }
        });
        window.addEventListener('resize', updateAria);
      }
    });
  </script>
  <script defer src="theme-toggle.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Chat Sessions | Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/styles/admin-style.css')?>">
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

  <aside class="sidebar">
     <img src="<?= base_url('assets/img/amb_logo.png')?>" alt="AMB Logo">
    <nav class="nav">
      <a href="/admin/adminHomepage"><i data-lucide="layout-dashboard"></i> Dashboard</a>
      <a href="/admin/manageUsers"><i data-lucide="users"></i> Manage Users</a>
      <a href="/admin/ManageProperties"><i data-lucide="home"></i> Manage Properties</a>
      <a href="/admin/userBookings"><i data-lucide="calendar"></i> User Bookings</a>
      <a href="/admin/viewChats" class="active"><i data-lucide="message-circle"></i> View Chats</a>
      <a href="/admin/Reports"><i data-lucide="bar-chart-2"></i> Generate Reports</a>
    </nav>

    <div class="profile-box">
      <div class="profile-avatar">A</div>
       <div class="profile-info">
        <strong><?= session('FirstName') . ' ' . session('LastName'); ?></strong>
        <span><?= session('inputEmail'); ?></span>
      </div>
    </div>
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
      </div>
    </div>
  </main>

  <script>
    lucide.createIcons();

    <?php
    // Fetch chat sessions for admin view
    $db = \Config\Database::connect();
    $chatSessions = $db->table('chatSession')
        ->select('chatSession.chatSessionID, chatSession.startTime, chatSession.endTime,
                  u.FirstName as clientFirst, u.LastName as clientLast,
                  a.FirstName as agentFirst, a.LastName as agentLast')
        ->join('users u', 'u.UserID = chatSession.UserID')
        ->join('users a', 'a.UserID = chatSession.AgentID')
        ->get()->getResultArray();

    $sessionsData = [];
    foreach ($chatSessions as $session) {
        $messages = $db->table('messages')
            ->where('chatSessionID', $session['chatSessionID'])
            ->orderBy('timestamp', 'ASC')
            ->get()->getResultArray();

        $messageData = [];
        $property = 'Property Inquiry';
        if (!empty($messages)) {
            // Try to extract property from first message
            $firstMsg = $messages[0]['messageContent'];
            if (preg_match('/Hi! I am interested in your property: (.+)/', $firstMsg, $matches)) {
                $property = $matches[1];
            }
        }
        foreach ($messages as $msg) {
            $messageData[] = [
                'sender' => $msg['senderRole'],
                'text' => $msg['messageContent'],
                'time' => date('h:i A', strtotime($msg['timestamp']))
            ];
        }

        $sessionsData[] = [
            'id' => 'CHAT' . str_pad($session['chatSessionID'], 3, '0', STR_PAD_LEFT),
            'agent' => $session['agentFirst'] . ' ' . $session['agentLast'],
            'client' => $session['clientFirst'] . ' ' . $session['clientLast'],
            'property' => $property,
            'date' => date('Y-m-d', strtotime($session['startTime'])),
            'status' => $session['endTime'] ? 'Closed' : 'Active',
            'messages' => $messageData
        ];
    }
    ?>

    const chatSessions = <?= json_encode($sessionsData) ?>;

    const chatList = document.getElementById('chatList');
    const chatMessages = document.getElementById('chatMessages');
    const chatTitle = document.getElementById('chatTitle');
    const searchInput = document.getElementById('messageSearch');

    let selectedChat = null;

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
  <script defer src="<?=base_url("assets/js/theme-toggle.js")?>"></script>
</body>
</html>

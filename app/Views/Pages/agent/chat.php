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
   <link rel="stylesheet" href="<?= base_url("assets/styles/agenStyle.css")?>">
</head>

<body>
  <!-- ✅ Unified Navbar (same as main dashboard) -->
  <nav class="navbar navbar-light fixed-top shadow-sm bg-white border-bottom">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4">
      <h3 class="mb-0 text-secondary fw-semibold">Agent Dashboard</h3>
       <ul class="nav nav-tabs border-0 flex-nowrap">
        <li class="nav-item"><a class="nav-link" href="/users/agentHomepage">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentclients">Clients</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentbookings">Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentproperties">Properties</a></li>
      </ul>
      
      <button class="btn btn-outline-primary btn-sm"> <a href="/users/agentprofile"></a></button>
    </div>
  </nav>

  <!-- ✅ Chat Container -->
  <div class="container-fluid mt-5 pt-4">
    <h4 class="fw-bold text-secondary mb-3">💬 Chat</h4>

    <div class="chat-container shadow-sm rounded-4 overflow-hidden bg-white animate__animated animate__fadeInUp d-flex">
      <!-- Sidebar -->
      <div class="chat-sidebar border-end" id="sidebar">
        <div class="chat-search p-3 border-bottom d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center w-100">
            <input type="text" id="searchInput" class="form-control rounded-pill me-2" placeholder="Search clients...">
            <button id="toggleSidebar" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-pin"></i>
            </button>
          </div>
        </div>

          <?php foreach ($clients as $client): ?>
            <div class="chat-item d-flex align-items-center gap-3 p-2 rounded hover-bg"
                data-session-id="<?= $client['chatSessionID'] ?>"
                data-name="<?= esc($client['fullname']) ?>">
              <img src="https://via.placeholder.com/45" class="rounded-circle shadow-sm" alt="">
              <div>
                <strong><?= esc($client['fullname']) ?></strong><br>
                <small class="text-muted"><?= esc($client['lastMessage']) ?></small>
              </div>
            </div>
          <?php endforeach; ?>

      </div>

      <!-- Hover zone to reopen sidebar -->
      <div id="hoverZone"></div>

      <!-- Main Chat Area -->
      <div class="chat-main d-flex flex-column flex-grow-1 bg-light" id="chatMain">
        <div class="chat-header bg-white p-3 border-bottom fw-semibold d-flex justify-content-between align-items-center">
          <span id="chatHeader">Select a client</span>
        </div>

        <div class="chat-messages flex-grow-1 p-4 overflow-auto d-flex flex-column gap-2" id="chatMessages" style="min-height: 60vh;">
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


<script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
<script>
const chatItems = document.querySelectorAll('.chat-item');
const chatHeader = document.getElementById('chatHeader');
const chatMessages = document.getElementById('chatMessages');
const messageInput = document.getElementById('messageInput');
const sendButton = document.querySelector('.chat-input button');

let currentSessionId = null;
const currentRole = '<?= session()->get('role'); ?>'; 


chatItems.forEach(item => {
  item.addEventListener('click', () => {
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



</body>
</html>

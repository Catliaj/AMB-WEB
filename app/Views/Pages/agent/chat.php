<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chat - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/styles/agentStyle.css')?>">

  <style>
    body {
      background: #f5f8fa;
      height: 100vh;
      overflow: hidden;
    }

    .chat-container {
      display: flex;
      height: 90vh;
      margin-top: 20px;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      background: white;
    }

    /* Left Sidebar */
    .chat-sidebar {
      width: 30%;
      background-color: #ffffff;
      border-right: 1px solid #eaeaea;
      display: flex;
      flex-direction: column;
    }

    .chat-search {
      padding: 15px;
      border-bottom: 1px solid #eaeaea;
    }

    .chat-search input {
      border-radius: 50px;
      border: 1px solid #ccc;
      padding: 8px 15px;
      width: 100%;
    }

    .chat-list {
      overflow-y: auto;
      padding: 10px;
      flex: 1;
    }

    .chat-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px;
      border-radius: 10px;
      transition: 0.2s;
      cursor: pointer;
    }

    .chat-item:hover {
      background-color: #f1f5ff;
    }

    .chat-item img {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      object-fit: cover;
    }

    .chat-item strong {
      font-size: 0.95rem;
    }

    /* Right Chat Area */
    .chat-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: #f9fbfd;
    }

    .chat-header {
      padding: 15px 20px;
      border-bottom: 1px solid #eaeaea;
      background-color: #fff;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .chat-messages {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .message {
      max-width: 70%;
      padding: 10px 15px;
      border-radius: 15px;
      word-wrap: break-word;
    }

    .message.agent {
      background-color: #007bff;
      color: #fff;
      align-self: flex-end;
      border-bottom-right-radius: 3px;
    }

    .message.client {
      background-color: #e4e6eb;
      color: #000;
      align-self: flex-start;
      border-bottom-left-radius: 3px;
    }

    .chat-input {
      padding: 10px 20px;
      background-color: #fff;
      border-top: 1px solid #eaeaea;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .chat-input input {
      flex: 1;
      border-radius: 50px;
      border: 1px solid #ccc;
      padding: 8px 15px;
      outline: none;
    }

    .chat-input button {
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
</head>
<body>
  <div class="container-fluid mt-3">
    <h4 class="fw-bold">💬 Chat</h4>
    <div class="chat-container">

      <!-- Left: Client List -->
      <div class="chat-sidebar">
        <div class="chat-search">
          <input type="text" id="searchInput" placeholder="Search clients..." />
        </div>
        <div class="chat-list" id="chatList">
          <div class="chat-item" data-name="Helena">
            <img src="https://via.placeholder.com/45" alt="">
            <div>
              <strong>Helena</strong><br>
              <small>Last message: Hi!</small>
            </div>
          </div>
          <div class="chat-item" data-name="Daniel Jay Park">
            <img src="https://via.placeholder.com/45" alt="">
            <div>
              <strong>Daniel Jay Park</strong><br>
              <small>Last message: Sure!</small>
            </div>
          </div>
          <div class="chat-item" data-name="Mark Rojas">
            <img src="https://via.placeholder.com/45" alt="">
            <div>
              <strong>Mark Rojas</strong><br>
              <small>Last message: Thanks!</small>
            </div>
          </div>
          <div class="chat-item" data-name="Sophia Dela Cruz">
            <img src="https://via.placeholder.com/45" alt="">
            <div>
              <strong>Sophia Dela Cruz</strong><br>
              <small>Last message: See you!</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Chat Area -->
      <div class="chat-main">
        <div class="chat-header" id="chatHeader">
          Select a client
        </div>

        <div class="chat-messages" id="chatMessages">
          <p class="text-muted text-center">No conversation selected.</p>
        </div>

        <div class="chat-input">
          <input type="text" id="messageInput" placeholder="Type a message..." disabled>
          <button class="btn btn-primary" disabled><i class="bi bi-send-fill"></i></button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const chatItems = document.querySelectorAll('.chat-item');
    const chatHeader = document.getElementById('chatHeader');
    const chatMessages = document.getElementById('chatMessages');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.querySelector('.chat-input button');

    // Dummy messages
    const conversations = {
      "Helena": [
        { from: "client", text: "Hello, I’d like to inquire about my booking." },
        { from: "agent", text: "Hi Helena! Sure, what would you like to know?" }
      ],
      "Daniel Jay Park": [
        { from: "client", text: "Good afternoon!" },
        { from: "agent", text: "Hey Daniel! How can I help today?" }
      ],
      "Mark Rojas": [
        { from: "client", text: "Is my payment confirmed?" },
        { from: "agent", text: "Yes, it’s successfully processed. 👍" }
      ],
      "Sophia Dela Cruz": [
        { from: "client", text: "Can I change my schedule?" },
        { from: "agent", text: "Sure, please send the new preferred date." }
      ]
    };

    // Select chat
    chatItems.forEach(item => {
      item.addEventListener('click', () => {
        const name = item.getAttribute('data-name');
        chatHeader.textContent = name;
        chatMessages.innerHTML = "";

        conversations[name].forEach(msg => {
          const div = document.createElement('div');
          div.className = `message ${msg.from}`;
          div.textContent = msg.text;
          chatMessages.appendChild(div);
        });

        messageInput.disabled = false;
        sendButton.disabled = false;
      });
    });

    // Search clients
    document.getElementById('searchInput').addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      chatItems.forEach(item => {
        const name = item.getAttribute('data-name').toLowerCase();
        item.style.display = name.includes(filter) ? "" : "none";
      });
    });

    // Send message
    sendButton.addEventListener('click', () => {
      const text = messageInput.value.trim();
      if (text) {
        const msg = document.createElement('div');
        msg.className = "message agent";
        msg.textContent = text;
        chatMessages.appendChild(msg);
        messageInput.value = "";
        chatMessages.scrollTop = chatMessages.scrollHeight;
      }
    });
  </script>
</body>
</html>

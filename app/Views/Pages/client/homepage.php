<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMB Property - Find Your Dream Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="<?= base_url('assets/styles/clientstyle.css')?>">
</head>

<body>
    <!-- Header -->
    <header id="header">
    <div class="logo">
        <img src="<?= base_url('assets/img/amb_logo.png')?>" alt="AMB Logo">
        <div class="logo-text">PROPERTY</div>
    </div>
    <nav id="nav">
        <a href="#">Home</a>
        <a href="#">Property Finder</a>
        <a href="#">My Bookings</a>
    </nav>
    <div class="header-icons">
        <i class="fas fa-search"></i>
        <i class="fas fa-user"></i>
        <i class="fas fa-bars hamburger" id="hamburger"></i>
    </div>
</header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h2>WELCOME TO AMB CORPORATION</h2>
            <h1>Find Your Dream Home</h1>
            <p>Your all-in-one solution, where trusted brokers, modern, stunning neighborhoods, and a seamless buying journey come together to turn your dream home into reality.</p>
            <div class="search-box">
                <input type="text" placeholder="Search property...">
                <button><i class="fas fa-search"></i></button>
            </div>
        </div>
        <div class="hero-image">
            <img src="<?= base_url('assets/img/house.png')?>" alt="Dream Home">
        </div>
    </section>

    <!-- Floating Chat Support -->
    <div class="chat-button" id="chatButton">
        <i class="fas fa-comments"></i>
    </div>

    <div class="chat-widget" id="chatWidget">
        <div class="chat-header">
            <h4>Support Chat</h4>
            <i class="fas fa-times" id="closeChat"></i>
        </div>
        <div class="chat-body" id="chatBody">
            <div class="chat-message bot">👋 Hi there! How can we help you today?</div>
        </div>
        <div class="chat-footer">
            <input type="text" id="chatInput" placeholder="Type your message..." />
            <button id="sendChat"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <!-- Second Section -->
    <section class="second-section">
        <div class="modern-home-image">
            <img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=600&h=400&fit=crop" alt="Modern Home">
        </div>
        <div class="content-right">
            <h1>Find Your Dream Home</h1>
            <p class="subtitle">Explore our curated selection of exquisite properties meticulously tailored to your unique dream home.</p>
            
            <div class="help-box">
                <h2>We Help You To Find Your Dream Home</h2>
                <p>From cozy suburban retreats to sleek urban dwellings, our dedicated team guides you through every step of the journey, ensuring a seamless home becomes a reality</p>
                
                <div class="stats">
                    <div class="stat-item">
                        <h3>8K+</h3>
                        <p>Properties Available</p>
                    </div>
                    <div class="stat-item">
                        <h3>6K+</h3>
                        <p>Happy Sold</p>
                    </div>
                    <div class="stat-item">
                        <h3>2K+</h3>
                        <p>Satisfied Agents</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-choose">
        <h2>Why Choose Us</h2>
        <p style="margin-bottom: 0px">Elevating Your Home Buying Experience with Expertise, Integrity,</p>
        <p style="margin-bottom: 30px">and Unmatched Personalized Service</p>
        
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3>Expert Guidance</h3>
                <p>Benefit from our team's seasoned expertise for a smooth buying journey</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h3>Personalized Service</h3>
                <p>Your dream home journey begins with us. Our services are tailored to your unique needs, making your journey stress-free.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="far fa-clipboard"></i>
                </div>
                <h3>Transparent Process</h3>
                <p>Stay informed with our clear and honest approach, to buying and selling</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>Exceptional Support</h3>
                <p>Benefit from our unwavering commitment to exceptional service, offering peace of mind with our responsive and attentive support team</p>
            </div>
        </div>
    </section>

    <!-- Popular Residences -->
    <section class="popular-residences">
        <h2>Our Popular Residences</h2>
        
        <div class="residences-grid">
            <!-- Card 1 -->
            <div class="residence-card">
                <div class="residence-image">
                    <img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=400&h=250&fit=crop" alt="San Francisco Property">
                    <div class="heart-icon">
                        <i class="far fa-heart"></i>
                    </div>
                </div>
                <div class="residence-info">
                    <div class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>San Francisco, California</span>
                    </div>
                    <div class="residence-details">
                        <div class="detail-item">
                            <i class="fas fa-bed"></i>
                            <span>4 Rooms</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-ruler-combined"></i>
                            <span>5,200 sq ft</span>
                        </div>
                    </div>
                    <div class="residence-footer">
                        <div class="price-tag">$2,040</div>
                        <div class="price">$550,000</div>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="residence-card">
                <div class="residence-image">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=250&fit=crop" alt="Beverly Hills Property">
                    <div class="heart-icon">
                        <i class="far fa-heart"></i>
                    </div>
                </div>
                <div class="residence-info">
                    <div class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Beverly Hills, California</span>
                    </div>
                    <div class="residence-details">
                        <div class="detail-item">
                            <i class="fas fa-bed"></i>
                            <span>3 Rooms</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-ruler-combined"></i>
                            <span>1,200 sq ft</span>
                        </div>
                    </div>
                    <div class="residence-footer">
                        <div class="price-tag">$2,040</div>
                        <div class="price">$850,000</div>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="residence-card">
                <div class="residence-image">
                    <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=400&h=250&fit=crop" alt="Palo Alto Property">
                    <div class="heart-icon">
                        <i class="far fa-heart"></i>
                    </div>
                </div>
                <div class="residence-info">
                    <div class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Palo Alto, California</span>
                    </div>
                    <div class="residence-details">
                        <div class="detail-item">
                            <i class="fas fa-bed"></i>
                            <span>5 Rooms</span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-ruler-combined"></i>
                            <span>5,420 sq ft</span>
                        </div>
                    </div>
                    <div class="residence-footer">
                        <div class="price-tag">$2,040</div>
                        <div class="price">$3,700,000</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sticky Header Effect
        const header = document.getElementById('header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Chat Widget
        const chatButton = document.getElementById('chatButton');
        const chatWidget = document.getElementById('chatWidget');
        const closeChat = document.getElementById('closeChat');
        const sendChat = document.getElementById('sendChat');
        const chatBody = document.getElementById('chatBody');
        const chatInput = document.getElementById('chatInput');

        chatButton.onclick = () => chatWidget.classList.add('open');
        closeChat.onclick = () => chatWidget.classList.remove('open');

        sendChat.onclick = () => {
            const msg = chatInput.value.trim();
            if (!msg) return;
            const userMsg = document.createElement('div');
            userMsg.classList.add('chat-message', 'user');
            userMsg.textContent = msg;
            chatBody.appendChild(userMsg);
            chatInput.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;

            // Simulated bot reply
            setTimeout(() => {
                const botMsg = document.createElement('div');
                botMsg.classList.add('chat-message', 'bot');
                botMsg.textContent = "Thanks for reaching out! We'll get back to you soon 😊";
                chatBody.appendChild(botMsg);
                chatBody.scrollTop = chatBody.scrollHeight;
            }, 800);
        };

        // Send message on Enter key
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendChat.click();
            }
        });

        // Heart icon toggle
        document.querySelectorAll('.heart-icon').forEach(icon => {
            icon.addEventListener('click', function() {
                const heartIcon = this.querySelector('i');
                if (heartIcon.classList.contains('far')) {
                    heartIcon.classList.remove('far');
                    heartIcon.classList.add('fas');
                    heartIcon.style.color = '#ff6b6b';
                } else {
                    heartIcon.classList.remove('fas');
                    heartIcon.classList.add('far');
                    heartIcon.style.color = '';
                }
            });
        });

        // Hamburger Menu Toggle - Replace the hamburger script in your HTML
        const hamburger = document.getElementById('hamburger');
        const nav = document.getElementById('nav');
        const body = document.body;

        // Create close button element
        const closeBtn = document.createElement('i');
        closeBtn.classList.add('fas', 'fa-times', 'nav-close');
        closeBtn.style.cssText = 'position: absolute; top: 20px; right: 20px; font-size: 24px; cursor: pointer; color: #333; transition: all 0.3s ease; display: none;';
        nav.insertBefore(closeBtn, nav.firstChild);

        // Create account settings section at bottom
        const accountSection = document.createElement('div');
        accountSection.classList.add('nav-account');
        accountSection.style.cssText = 'position: absolute; left: 20px; bottom: 40px; padding-top: 0; border-top: none; display: flex; flex-direction: column; gap: 0; padding-left: 0; width: calc(100% - 20px);';
        accountSection.innerHTML = `
            <a href="#" style="display: flex; align-items: center; gap: 10px; padding: 12px 0; border: none; text-decoration: none; color: #333; padding-left: 10px; white-space: nowrap;">
                <i class="fas fa-user-circle" style="font-size: 20px; width: 25px;"></i>
                <span>Account Settings</span>
            </a>
            <a href="#" style="display: flex; align-items: center; gap: 10px; padding: 12px 0; border: none; text-decoration: none; color: #333; padding-left: 10px; white-space: nowrap;">
                <i class="fas fa-sign-out-alt" style="font-size: 20px; width: 25px;"></i>
                <span>Logout</span>
            </a>
        `;
        nav.appendChild(accountSection);

        // Toggle menu
        hamburger.addEventListener('click', (e) => {
            e.stopPropagation();
            nav.classList.add('active');
            closeBtn.style.display = 'block';
            accountSection.style.display = 'block';
        });

        // Close menu when clicking close button
        closeBtn.addEventListener('click', () => {
            nav.classList.remove('active');
        });

        // Close menu when clicking nav links
        nav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('active');
            });
        });

        // Hover effect for close button
        closeBtn.addEventListener('mouseenter', () => {
            closeBtn.style.color = '#469541';
            closeBtn.style.transform = 'rotate(90deg)';
        });
        closeBtn.addEventListener('mouseleave', () => {
            closeBtn.style.color = '#333';
            closeBtn.style.transform = 'rotate(0deg)';
        });

        // Hide close button and account section on desktop resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                nav.classList.remove('active');
                closeBtn.style.display = 'none';
                accountSection.style.display = 'none';
            }
        });
    </script>
</body>
</html>
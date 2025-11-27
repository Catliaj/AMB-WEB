<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>

    <link rel="stylesheet" href="<?php echo base_url('bootstrap5/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/styles/landing-style.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand" href="#home">
                <img src="/assets/img/AMB_logo.png" alt="AMB Condolord Logo" class="logo-img" onerror="this.src='/assets/img/AMB_logo.png'">
            </a>

            <!-- Toggler Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">HOME</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#service">SERVICE</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">ABOUT US</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#properties">PROPERTIES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">CONTACT US</a>
                    </li>
                </ul>

                <!-- Auth Buttons -->
                <div class="auth-buttons-container">
                    <button class="btn btn-auth btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">LOG IN</button>
                    <button class="btn btn-auth btn-signup" data-bs-toggle="modal" data-bs-target="#signupModal">SIGN UP</button>
                </div>
            </div>
        </div>

        
    </nav>

    <div id="home" class ="hero-section">
            <h1>ACHIEVER &middot; MOTIVATED &middot; BLESSED</h1>
            <p>Conquering Dreams, Building Futures</p>
        </div>
    
    <!-- Our Conquering Services Section -->
    <section id="service" class="services-section">
        <div class="container">
            <h2 class="services-title">OUR CONQUERING SERVICES</h2>
            
            <div class="services-grid">
                <!-- BellaVita Card -->
                <article class="service-card bella-card">
                    <p class="service-description-panel">
                        Discover a peaceful and affordable community designed for growing families and first-time homeowners. Located in the quiet town of Lian, Batangas, BellaVitta offers secure living, essential amenities, and convenient access to schools, markets, and nearby beaches. Enjoy a comfortable lifestyle in a well planned subdivision where convenience and serenity meet.
                    </p>
                    <div class="service-logo-container" tabindex="0">
                        <img src="/assets/img/BellaVIta.avif" alt="BellaVita Logo" class="service-logo">
                    </div>
                </article>

                <!-- RCD Royale Homes Card -->
                <article class="service-card rcd-card reverse">
                    <p class="service-description-panel">
                        RCD Royale Homes Tuy offers a peaceful and family-friendly community in one of Batangas' most accessible and fast-developing towns. Designed with affordability, comfort, and security in mind, this subdivision provides well-planned homes, gated surroundings, and easy access to schools, markets, and major highways. Discover a place where everyday convenience meets modern suburban living your new home in Tuy awaits.
                    </p>
                    <div class="service-logo-container" tabindex="0">
                        <img src="/assets/img/RCD Royale homes.png" alt="RCD Royale Homes Logo" class="service-logo">
                    </div>
                </article>  
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <h2 class="about-title">ABOUT US</h2>
            <div class="about-content">
                <div class="about-stats">
                    <div class="about-card">
                        <span class="about-value">12</span>
                        <span class="about-label">Years of Service</span>
                    </div>
                    <div class="about-card">
                        <span class="about-value">70</span>
                        <span class="about-label">Active Agents</span>
                    </div>
                </div>

                <div class="about-image-wrapper">
                    <img src="/assets/img/ABOUT_US1.jpg" alt="AMB Team" class="about-image">
                    <p class="about-description">
                        BDDR AMB Conqueror is more than a real estate group we build relationships that feel like family. We guide every client with care, trust, and genuine connection, making every step of the journey feel supported and stress-free.
                    </p>
                </div>

                <div class="about-stats">
                    <div class="about-card">
                        <span class="about-value">10000+</span>
                        <span class="about-label">Sold Units</span>
                    </div>
                    <div class="about-card">
                        <span class="about-value">1B</span>
                        <span class="about-label">Total Sales</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Properties Section -->
    <section id="properties" class="properties-section">
        <div class="container">
            <h2 class="properties-title">PROPERTIES</h2>
            <div class="properties-grid">
                <!-- Card 1 -->
                <article class="property-card">
                    <div class="property-image-wrapper">
                        <button class="property-nav-btn">&lt;</button>
                        <img src="/assets/img/house_pic/house_BG.jpg" alt="Single Detach property">
                        <button class="property-nav-btn">&gt;</button>
                    </div>
                    <div class="property-body">
                        <h3>Single Detach</h3>
                        <p>Are you looking for a simple yet beautiful house? Look no further as Phrist offers this Single Detach perfect for a family.</p>
                        <div class="property-facts">
                            <span>65.5 sqm</span>
                            <span>40 sqm lot</span>
                            <span>2 bedrooms</span>
                            <span>1 bathroom</span>
                        </div>
                        <button class="property-cta" onclick="showLoginPrompt(event)">More Info</button>
                    </div>
                </article>

                <!-- Card 2 -->
                <article class="property-card">
                    <div class="property-image-wrapper">
                        <button class="property-nav-btn">&lt;</button>
                        <img src="/assets/img/house_pic/house_BG.jpg" alt="Single Detach property">
                        <button class="property-nav-btn">&gt;</button>
                    </div>
                    <div class="property-body">
                        <h3>Single Detach</h3>
                        <p>Are you looking for a simple yet beautiful house? Look no further as Phrist offers this Single Detach perfect for a family.</p>
                        <div class="property-facts">
                            <span>65.5 sqm</span>
                            <span>40 sqm lot</span>
                            <span>2 bedrooms</span>
                            <span>1 bathroom</span>
                        </div>
                        <button class="property-cta" onclick="showLoginPrompt(event)">More Info</button>
                    </div>
                </article>

                <!-- Card 3 -->
                <article class="property-card">
                    <div class="property-image-wrapper">
                        <button class="property-nav-btn">&lt;</button>
                        <img src="/assets/img/house_pic/house_BG.jpg" alt="Single Detach property">
                        <button class="property-nav-btn">&gt;</button>
                    </div>
                    <div class="property-body">
                        <h3>Single Detach</h3>
                        <p>Are you looking for a simple yet beautiful house? Look no further as Phrist offers this Single Detach perfect for a family.</p>
                        <div class="property-facts">
                            <span>65.5 sqm</span>
                            <span>40 sqm lot</span>
                            <span>2 bedrooms</span>
                            <span>1 bathroom</span>
                        </div>
                        <button class="property-cta" onclick="showLoginPrompt(event)">More Info</button>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="contact-wrapper">
            <div class="contact-info">
                <h2>Contact Us</h2>
                <h3>Conquering Dreams, Building Futures</h3>
                <div class="contact-details">
                    <p><span class="contact-icon">📞</span>+63 917 123 4567</p>
                    <p><span class="contact-icon">📧</span>info@ambconqueror.ph</p>
                    <p><span class="contact-icon">📍</span>Nasugbu, Batangas, Philippines</p>
                    <p><span class="contact-icon">⏰</span>Mon-Sat: 8AM-6PM</p>
                </div>
            </div>
            <form class="contact-form">
                <label>Name</label>
                <input type="text" placeholder="Your Name">
                <label>Email</label>
                <input type="email" placeholder="Email Address">
                <label>Contact Number</label>
                <input type="text" placeholder="Phone Number">
                <label>Subject</label>
                <input type="text" placeholder="Subject">
                <label>Message</label>
                <textarea rows="4" placeholder="Your Message"></textarea>
                <button type="submit">Send</button>
            </form>
        </div>
    </section>


    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="row g-0">
                    <div class="col-md-7 d-flex flex-column justify-content-center">
                        <div class="login-panel-body">
                            <div class="login-brand d-flex align-items-center mb-3">
                                <img src="/assets/img/AMB_logo.png" alt="AMB" onerror="this.src='../../../public/assets/img/AMB_logo.png'" class="login-logo">
                                <div>
                                    <h5>WELCOME BACK!</h5>
                                    <p>Please enter your details.</p>
                                </div>
                            </div>
                            <form id="loginForm" action="<?php echo base_url('users/login'); ?>" method="post">
                                <label for="inputEmail">Email</label>
                                <input type="email" name="inputEmail" id="inputEmail" class="form-control mb-2" placeholder="name@email.com">
                                <label for="inputPassword">Password</label>
                                <input type="password" name="inputPassword" id="inputPassword" class="form-control mb-2" placeholder="••••••••" required>
                                <a class="login-forgot d-block mb-2" href="#" id="forgotPasswordLink">Forgot password?</a>
                                <button type="submit" class="login_btn">Log in</button>
                                <!-- Social login buttons removed -->
                                <p class="login-signup text-center mb-0">Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#signupModal" data-bs-dismiss="modal">Sign up</a></p>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center login-visual-bg">
                        <img src="/assets/img/login_imgs/Agent_Model.png" alt="Login Visual" class="login-visual">
                    </div>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Signup Modal -->
    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="row g-0">
                    <div class="col-md-7 d-flex flex-column justify-content-center">
                        <div class="login-panel-body">
                            <div class="login-brand d-flex align-items-center mb-3">
                                <img src="/assets/img/AMB_logo.png" alt="AMB" onerror="this.src='../../../public/assets/img/AMB_logo.png'" class="login-logo">
                                <div>
                                    <h5>Join Us!</h5>
                                    <p>Create your account to get started.</p>
                                </div>
                            </div>
                            <form id="signupForm" action="<?php echo base_url('users/signup'); ?>" method="post" enctype="multipart/form-data">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="FirstName" class="form-control" placeholder="ex: Juan" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" name="MiddleName" class="form-control" placeholder="ex: Castillo" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="LastName" class="form-control" placeholder="ex: Dela Cruz" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Birthday</label>
                                        <input type="date" name="Birthdate" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" name="phoneNumber" class="form-control" placeholder="ex: 09*********" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="Email" id="signupEmail" class="form-control" placeholder="ex: juan@gmail.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="Password" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Profile Photo</label>
                                        <input type="file" name="profilePhoto" id="profilePhoto" class="form-control" accept="image/*" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Government ID (front)</label>
                                        <input type="file" name="govIdImage" class="form-control" accept="image/*,.pdf" />
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Employment Status</label>
                                    <select name="employmentStatus" id="employmentStatus" class="form-select" required>
                                        <option value="" selected disabled>Select Employment Status</option>
                                        <option value="locally_employed">Locally Employed</option>
                                        <option value="ofw">OFW (Overseas Filipino Worker)</option>
                                    </select>
                                </div>
                                <div id="locallyEmployedFields" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label w-100">ID With Signature</label>
                                        <input type="file" name="Id_With_Signature" class="form-control file-upload-full" accept="image/*,.pdf">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label w-100">Payslip</label>
                                        <input type="file" name="Payslip" class="form-control file-upload-full" accept="image/*,.pdf">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label w-100">Proof of Billing</label>
                                        <input type="file" name="proof_of_billing" class="form-control file-upload-full" accept="image/*,.pdf">
                                    </div>
                                </div>
                                <div id="ofwFields" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label w-100">Job Contract</label>
                                        <input type="file" name="Job_Contract" class="form-control file-upload-full" accept="image/*,.pdf">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label w-100">Passport</label>
                                        <input type="file" name="Passport" class="form-control file-upload-full" accept="image/*,.pdf">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label w-100">Official Identity Document</label>
                                        <input type="file" name="Official_Identity_Documents" class="form-control file-upload-full" accept="image/*,.pdf">
                                        <small class="form-text text-muted">Driver's license or any ID with signature and photo</small>
                                    </div>
                                </div>
                                <button type="submit" class="btn-createacc">Sign Up</button>
                                <p class="login-signup text-center mb-0">Already have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Log in</a></p>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-5 d-none d-md-flex align-items-md-center justify-content-center signup-visual-bg">
                        <img src="/assets/img/login_imgs/Real_estate_guy.png" alt="signup-visual" class="signup-visual">
                    </div>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- OTP Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="row g-0">
                    <div class="col-md-7 d-flex flex-column justify-content-center">
                        <div class="login-panel-body">
                            <div class="login-brand d-flex align-items-center mb-3">
                                <img src="/assets/img/AMB_logo.png" alt="AMB" onerror="this.src='../../../public/assets/img/AMB_logo.png'" class="login-logo">
                                <div>
                                    <h5>Email Verification</h5>
                                    <p>Please enter the OTP sent to your email.</p>
                                </div>
                            </div>
                            <form id="otpForm">
                                <input type="hidden" name="Email" id="otpEmail">
                                <div class="mb-3">
                                    <div class="alert alert-info text-center" role="alert">
                                        OTP is sent to your Gmail, please copy and enter the code for verification.
                                    </div>
                                    <label for="otp_code" class="form-label">Enter OTP</label>
                                    <input type="number" name="otp_code" class="form-control" placeholder="6-digit code" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100">Verify OTP</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center login-visual-bg">
                        <img src="/assets/img/AMB_logo.png" alt="AMB Logo" class="login-logo">
                    </div>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Forgot Password Popup Flow
        document.getElementById('forgotPasswordLink').addEventListener('click', function(e) {
            e.preventDefault();
            var loginModal = document.getElementById('loginModal');
            if (loginModal && loginModal.classList.contains('show')) {
                var modalInstance = bootstrap.Modal.getInstance(loginModal);
                if (modalInstance) modalInstance.hide();
            }
            Swal.fire({
                title: 'Forgot Password',
                input: 'email',
                inputLabel: 'Enter your email address',
                inputPlaceholder: 'name@email.com',
                confirmButtonText: 'Send Confirmation',
                showCancelButton: true,
                customClass: {
                    popup: 'swal2-forgot-modal-white',
                    confirmButton: 'swal2-login-btn',
                    cancelButton: 'swal2-cancel-btn',
                    input: 'swal2-input-forgot'
                },
                didOpen: () => {
                    setTimeout(() => {
                        const input = Swal.getInput();
                        if (input) input.focus();
                    }, 100);
                },
                preConfirm: (email) => {
                    if (!email) {
                        Swal.showValidationMessage('Please enter your email');
                    }
                    return email;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // Send confirmation to backend
                    fetch('/forgot/send', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'email=' + encodeURIComponent(result.value)
                    })
                    .then(response => response.text())
                    .then((responseText) => {
                        // Try to extract token from response
                        let token = '';
                        try {
                            // If backend returns JSON, parse it
                            const json = JSON.parse(responseText);
                            token = json.token || '';
                        } catch (e) {
                            // Fallback: extract token from HTML
                            const match = responseText.match(/name=["']token["'] value=["']([a-zA-Z0-9]+)["']/);
                            if (match) token = match[1];
                        }
                        if (!token) {
                            // fallback: try to extract from URL in response
                            const urlMatch = responseText.match(/forgot\/confirm\/([a-f0-9]{64})/);
                            if (urlMatch) token = urlMatch[1];
                        }
                        Swal.fire({
                            title: 'Confirmation Sent',
                            text: 'Check your email for the OTP code.',
                            icon: 'success',
                            confirmButtonText: 'Continue'
                        }).then(() => {
                            Swal.fire({
                                title: 'Enter OTP Code',
                                input: 'text',
                                inputLabel: 'Enter the OTP code sent to your email',
                                inputPlaceholder: 'OTP Code',
                                confirmButtonText: 'Verify OTP',
                                showCancelButton: true,
                                customClass: {
                                    popup: 'swal2-forgot-modal-white',
                                    confirmButton: 'swal2-login-btn',
                                    cancelButton: 'swal2-cancel-btn',
                                    input: 'swal2-input-forgot'
                                },
                                preConfirm: (otp) => {
                                    if (!otp) {
                                        Swal.showValidationMessage('Please enter the OTP code');
                                    }
                                    return otp;
                                }
                            }).then((otpResult) => {
                                if (otpResult.isConfirmed && otpResult.value) {
                                    // Verify OTP with backend
                                    fetch('/forgot/verify-otp', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                        body: 'otp_code=' + encodeURIComponent(otpResult.value) + '&token=' + encodeURIComponent(token)
                                    })
                                    .then(response => response.json())
                                    .then((verifyJson) => {
                                        if (verifyJson.status === 'error') {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Invalid OTP',
                                                text: verifyJson.message || 'The OTP code you entered is incorrect or expired. Please try again.'
                                            });
                                        } else {
                                            // If OTP is correct, show password reset
                                            Swal.fire({
                                                title: 'Reset Password',
                                                html: '<input type="password" id="newPassword" class="swal2-input swal2-input-forgot" placeholder="New Password" style="margin-bottom:10px;">' +
                                                      '<input type="password" id="confirmPassword" class="swal2-input swal2-input-forgot" placeholder="Confirm Password">',
                                                customClass: {
                                                    popup: 'swal2-forgot-modal-white',
                                                    confirmButton: 'swal2-login-btn',
                                                    cancelButton: 'swal2-cancel-btn',
                                                    input: 'swal2-input-forgot'
                                                },
                                                didOpen: () => {
                                                    setTimeout(() => {
                                                        const input = Swal.getPopup().querySelector('#newPassword');
                                                        if (input) input.focus();
                                                    }, 100);
                                                },
                                                focusConfirm: false,
                                                preConfirm: () => {
                                                    const newPassword = Swal.getPopup().querySelector('#newPassword').value;
                                                    const confirmPassword = Swal.getPopup().querySelector('#confirmPassword').value;
                                                    if (!newPassword || !confirmPassword) {
                                                        Swal.showValidationMessage('Please enter both fields');
                                                    } else if (newPassword !== confirmPassword) {
                                                        Swal.showValidationMessage('Passwords do not match');
                                                    }
                                                    return { newPassword, confirmPassword };
                                                },
                                                confirmButtonText: 'Update Password',
                                                showCancelButton: true
                                            }).then((pwResult) => {
                                                if (pwResult.isConfirmed && pwResult.value) {
                                                    // Send new password to backend
                                                    fetch('/forgot/update', {
                                                        method: 'POST',
                                                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                                        body: 'token=' + encodeURIComponent(token) + '&password=' + encodeURIComponent(pwResult.value.newPassword)
                                                    })
                                                    .then(response => response.text())
                                                    .then(() => {
                                                        Swal.fire({
                                                            title: 'Password Updated',
                                                            text: 'Your password has been updated. You can now log in.',
                                                            icon: 'success'
                                                        });
                                                    });
                                                }
                                            });
                                        }
                                    });
                                }
                            });
                        });
                    });
                }
            });
        });
    function showLoginPrompt(e) {
        e.preventDefault();
        Swal.fire({
            icon: 'info',
            title: 'Login Required',
            text: 'Login to continue browsing properties.',
            confirmButtonText: 'Login',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                var loginModal = document.getElementById('loginModal');
                if (loginModal) {
                    var modal = new bootstrap.Modal(loginModal);
                    modal.show();
                }
            }
        });
    }
    </script>

    <script>
    document.getElementById('employmentStatus').addEventListener('change', function() {
      const localFields = document.getElementById('locallyEmployedFields');
      const ofwFields = document.getElementById('ofwFields');
      localFields.style.display = 'none';
      ofwFields.style.display = 'none';
      localFields.querySelectorAll('input').forEach(input => input.removeAttribute('required'));
      ofwFields.querySelectorAll('input').forEach(input => input.removeAttribute('required'));
      if (this.value === 'locally_employed') {
        localFields.style.display = 'block';
        localFields.querySelectorAll('input').forEach(input => input.setAttribute('required', 'required'));
      } else if (this.value === 'ofw') {
        ofwFields.style.display = 'block';
        ofwFields.querySelectorAll('input').forEach(input => input.setAttribute('required', 'required'));
      }
    });
    </script>

    <script>
    $(document).ready(function () {
        // Prevent body and navbar from shifting when modal opens
        var originalBodyPaddingRight = '';
        var originalBodyOverflow = '';
        $(document).on('show.bs.modal', function (e) {
            originalBodyPaddingRight = $('body').css('padding-right');
            originalBodyOverflow = $('body').css('overflow');
            $('body').css('padding-right', '0');
            $('body').css('overflow', 'auto');
        });
        $(document).on('hidden.bs.modal', function (e) {
            $('body').css('padding-right', originalBodyPaddingRight);
            $('body').css('overflow', originalBodyOverflow);
        });
        // Step 1: Request OTP
        $('#signupForm').on('submit', function (e) {
            e.preventDefault();
            const email = $('#signupEmail').val().trim();
            if (email === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Email',
                    text: 'Please enter your email before proceeding.',
                });
                return;
            }
            // Use FormData to include files
            const formData = new FormData(this);
            $.ajax({
                url: '<?php echo base_url('users/request-otp'); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Sending OTP...',
                        text: 'Please wait.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function (response) {
                    Swal.close();
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'OTP Sent!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#otpEmail').val(email);
                        $('#signupModal').modal('hide');
                        $('#otpModal').modal('show');
                        // Store form data in sessionStorage as FormData (files not supported in sessionStorage, so only text fields)
                        const textFields = {};
                        $(this).serializeArray().forEach(function(item) {
                            textFields[item.name] = item.value;
                        });
                        sessionStorage.setItem('signupData', JSON.stringify(textFields));
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed!',
                            text: response.message,
                        });
                    }
                },
                error: function (xhr) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Something went wrong while sending OTP.',
                    });
                }
            });
        });
        // Step 2: Verify OTP
        $('#otpForm').on('submit', function (e) {
            e.preventDefault();
            // Always build FormData from the signup form to ensure all fields are present
            const signupForm = document.getElementById('signupForm');
            const formData = new FormData(signupForm);
            // Add OTP field manually
            const otpInput = document.querySelector('#otpForm [name="otp_code"]');
            if (otpInput) {
                formData.append('otp_code', otpInput.value);
            }
            $.ajax({
                url: '<?= base_url('/users/signup') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Verifying...',
                        text: 'Please wait while we check your OTP.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function (response) {
                    Swal.close();
                    if (response.status === 'success') {
                        let uploadMsg = '';
                        if (response.uploadStatus) {
                            uploadMsg = Object.entries(response.uploadStatus).map(([k,v]) => `${k}: ${v}`).join(', ');
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message + (uploadMsg ? '\nFile upload status: ' + uploadMsg : ''),
                            showConfirmButton: false,
                            timer: 3000
                        });
                        setTimeout(() => {
                            $('#otpModal').modal('hide');
                            sessionStorage.removeItem('signupData');
                        }, 3000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid OTP',
                            text: response.message || 'Please check and try again.',
                        });
                    }
                },
                error: function (xhr) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: xhr.responseJSON?.message || 'Something went wrong. Please try again later.',
                    });
                }
            });
        });
    });
    </script>

    <script src="<?php echo base_url('bootstrap5/js/bootstrap.min.js'); ?>"></script>

    
    <script>
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
      loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Logging in...',
          text: 'Please wait while we verify your credentials.',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
                fetch('<?php echo base_url("/users/login"); ?>', {
          method: 'POST',
          body: new FormData(loginForm)
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Welcome!',
              text: data.message,
              confirmButtonText: 'OK'
            }).then(() => {
              window.location.href = data.redirect;
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: data.message
            });
          }
        })
        .catch(error => {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Something went wrong. Please try again.'
          });
          console.error(error);
        });
      });
    }
    </script>

</body>
</html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="<?= base_url('bootstrap5/css/bootstrap.min.css')?>">
    <link rel="stylesheet" href="<?= base_url('assets/styles/home_page.css')?>">
    
    <!--font: MONTSERRAT-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>
        <div class="video-background">
            <div class="video-foreground">
                <iframe 
                src="https://www.youtube.com/embed/jPkBJY1KI_Q?autoplay=1&mute=1&loop=1&playlist=jPkBJY1KI_Q&controls=0&showinfo=0&modestbranding=1"
                frameborder="0"
                allow="autoplay; fullscreen"
                allowfullscreen>
                </iframe>
            </div>
        </div>

<!-- Your other content -->
<div class="content">
  <!-- your nav / heading / cards etc. -->
</div>


    <!--Top Nav Bar-->
        <div class="container-fluid bg-black d-flex align-items-center p-2 sticky-top">
            <!-- Logo on the left -->
            <img src="<?= base_url('assets/img/AMB_logo.png')?>" class="logo me-3">

            <!-- Buttons centered -->
            <div class="d-flex justify-content-center flex-grow-1 gap-2">
                <button class="home">HOME</button>
                <button class="service_btn">SERVICE</button>
                <button class="about_us">ABOUT US</button>
                <button class="contact_btn">PROPERTIES</button>
                <button class="contact_btn">CONTACT US</button>
            </div>
            <!--Login/SIgnup end-->
            <button class="login_btn fw-bold" data-bs-toggle="modal" data-bs-target="#loginModal" >LOGIN</button>
    
            <button class="sign_up_btn fw-bold" data-bs-toggle="modal" data-bs-target="#signupModal">SIGN UP</button>
        </div>
        
        <div class="d-flex flex-column justify-content-center align-items-center min-vh-100">
            <h1 class="motto text-center fw-bold text-light w-auto">ACHIEVER &middot; MOTIVATED &middot; BLESSED </h1>

            <p class="qoute text-center text-light w-100">Conquering Dreams, Building Futures</p>
        </div>
        
        <!--service page-->
        <div class="d-flex flex-column min-vh-100 bg-light">
            <h1 class="services fw-bold text-center">OUR CONQUERING SERVICES</h1>
                <div class="service-grid">
                    <div class="panel">
                        <img src="<?= base_url('assets/img/Phrist_logo.png')?>" class="service_logo ">
                    </div>

                    <div class="panel">
                        <img src="<?= base_url('assets/img/BellaVIta.avif')?>" class="service_logo">
                    </div>

                    <div class="panel">
                        <img src="<?= base_url('assets/img/RCD Royale homes.png')?>" class="service_logo">
                    </div>

                    <div class="panel">
                        <img src="<?= base_url('assets/img/SOuthdale.png')?>" class="service_logo">
                    </div>

                    <div class="panel">
                        <img src="<?= base_url('assets/img/Northdale.png')?>" class="service_logo">
                    </div>

                    <div class="panel">
                        <img src="<?= base_url('assets/img/Kaia.png')?>" class="service_logo_kaia">
                    </div> 
                </div>
        </div>
         
        <!--ABOUT US SECTION-->
        <div class="d-flex flex-column min-vh-100 bg-none">
            <h1 class="about fw-bold text-center mb-4 text-light">ABOUT US</h1>

            <div class="container d-flex justify-content-center">
                <div class="about_panel border p-5 rounded-2 shadow-lg bg-white">
                    <div class="row align-items-center g-4">
                    
                    <!-- Text Section -->
                    <div class="col-md-6">
                        <p class="message fs-5" style="text-align: justify;">
                        Lorem ipsum dolor sit amet. Ut nisi repellat aut pariatur quas 
                        ab modi voluptas ut consequuntur enim vel nihil veritatis et saepe optio? 
                        Est consequatur eligendi ea accusantium incidunt qui autem eligendi in mollitia 
                        nihil eos pariatur error. Et sapiente dicta aut fugit harum nam deserunt doloribus 
                        33 repellendus officia At animi voluptas aut consequuntur molestias. Ut sunt ipsa 
                        qui minus sapiente aut sunt minus qui illo sapiente eum molestias esse.
                        </p>
                    </div>

                    <!-- Image Section -->
                    <div class="col-md-6 text-center">
                        <img src="<?= base_url('assets/img/ABOUT_US1.jpg')?>" class="img-fluid rounded about1" class="about_us_img_1">
                    </div>

                    </div>
                </div>
            </div>

            <div class="container d-flex justify-content-center">
                <div class="about_panel border p-5 rounded shadow-lg bg-white">
                    <div class="row align-items-center g-4">

                    <!-- Image Section -->
                    <div class="col-md-6 text-center">
                        <img src="<?= base_url('assets/img/ABOUT_US1.jpg')?>" class="img-fluid rounded about1" class="about_us_img_1">
                    </div>
                    
                    <!-- Text Section -->
                    <div class="col-md-6">
                        <p class="message fs-5" style="text-align: justify;">
                        Lorem ipsum dolor sit amet. Ut nisi repellat aut pariatur quas 
                        ab modi voluptas ut consequuntur enim vel nihil veritatis et saepe optio? 
                        Est consequatur eligendi ea accusantium incidunt qui autem eligendi in mollitia 
                        nihil eos pariatur error. Et sapiente dicta aut fugit harum nam deserunt doloribus 
                        33 repellendus officia At animi voluptas aut consequuntur molestias. Ut sunt ipsa 
                        qui minus sapiente aut sunt minus qui illo sapiente eum molestias esse.
                        </p>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Get data from the database-->

        <div class="d-flex flex-column bg-light py-3">
            <h1 class="properties fw-bold text-center mb-6 text-black">PROPERTIES</h1>

            <div class="bg-gradient">
        <div class="container">
            <div class="row justify-content-center g-4">

            <!-- Card 1 -->
            <div class="col-lg-4 col-md-7 d-flex justify-content-center">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden custom-card">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=400&h=200&fit=crop" class="card-img-top custom-card-img" alt="Single Detach House">
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-between h-100">
                    <div>
                        <h6 class="fw-bold mb-2">SINGLE DETACH</h6>
                        <p class="text-muted small mb-3" style="font-size: 12px; line-height: 1.3;">
                        Are you looking for a simple yet beautiful House? look no further as Phrist offers this Single Detach perfect for a family. (bare with this is a mock up house hehe)
                        </p>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-center flex-fill">
                                <i class="bi bi-bounding-box text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">60.5 sq.m<br><span style="font-size: 9px;">lot area</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-aspect-ratio text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">40 sq.m<br><span style="font-size: 9px;">floor area</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-droplet text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">1<br><span style="font-size: 9px;">bathroom</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-door-closed text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">2<br><span style="font-size: 9px;">bedrooms</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-car-front text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">carport</div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                        <button class="btn btn-info text-white px-3 py-1 rounded-pill" style="font-size: 12px;">More Info</button>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-lg-4 col-md-6 d-flex justify-content-center">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden custom-card">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=400&h=200&fit=crop" class="card-img-top custom-card-img" alt="Single Detach House">
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-between h-100">
                    <div>
                        <h6 class="fw-bold mb-2">SINGLE DETACH</h6>
                        <p class="text-muted small mb-3" style="font-size: 12px; line-height: 1.3;">
                        Are you looking for a simple yet beautiful House? look no further as Phrist offers this Single Detach perfect for a family. (bare with this is a mock up house hehe)
                        </p>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-center flex-fill">
                                <i class="bi bi-bounding-box text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">60.5 sq.m<br><span style="font-size: 9px;">lot area</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-aspect-ratio text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">40 sq.m<br><span style="font-size: 9px;">floor area</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-droplet text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">1<br><span style="font-size: 9px;">bathroom</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-door-closed text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">2<br><span style="font-size: 9px;">bedrooms</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-car-front text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">carport</div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                        <button class="btn btn-info text-white px-3 py-1 rounded-pill" style="font-size: 12px;">More Info</button>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-lg-4 col-md-6 d-flex justify-content-center">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden custom-card">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=400&h=200&fit=crop" class="card-img-top custom-card-img" alt="Single Detach House">
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-between h-100">
                    <div>
                        <h6 class="fw-bold mb-2">SINGLE DETACH</h6>
                        <p class="text-muted small mb-3" style="font-size: 12px; line-height: 1.3;">
                        Are you looking for a simple yet beautiful House? look no further as Phrist offers this Single Detach perfect for a family. (bare with this is a mock up house hehe)
                        </p>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-center flex-fill">
                                <i class="bi bi-bounding-box text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">60.5 sq.m<br><span style="font-size: 9px;">lot area</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-aspect-ratio text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">40 sq.m<br><span style="font-size: 9px;">floor area</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-droplet text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">1<br><span style="font-size: 9px;">bathroom</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-door-closed text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">2<br><span style="font-size: 9px;">bedrooms</span></div>
                            </div>
                            <div class="text-center flex-fill">
                                <i class="bi bi-car-front text-muted"></i>
                                <div class="small text-muted" style="font-size: 10px;">carport</div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                        <button class="btn btn-info text-white px-3 py-1 rounded-pill" style="font-size: 12px;">More Info</button>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- Login Modal -->
        <div class="login_backpanel modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">

                <div class="login_page modal-content">

                    <div class="modal-header text-center">
                        <h5 class="modal-title">Login</h5>
                        <button type="button" class="btn_close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                            <img src="<?= base_url('assets/img/AMB_logo.png') ?>" alt="Account Logo" class="Account_Logo">

                        <form action="<?= base_url('users/login') ?>" method="post">
                        <div class="mb-3 text-center">
                            <h3 class="Welcome">WELCOME!</h3>
                            <h6 class="msg">Welcome back! Log in to explore new property listings.</h6>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="inputEmail" class="email_input form-control" placeholder="Email">
                        </div>

                        <div class="mb-3">
                            <input type="password" name="inputPassword" class="form-control" placeholder="Password" required>
                            <a href="">Forgot Password</a>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                        

                        </form>

                        <div class="text-center mt-3">
                            <p>Don't have an account yet?<a href="/Users/CreateUsers"> Join Us</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


<!-- ✅ Signup Modal -->
<div class="modal fade" id="signupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Sign Up</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="signupForm">
          <!-- First Row: First Name, Middle Name, Last Name -->
          <div class="row g-2 mb-3">
            <div class="col-md-4">
              <label class="form-label">First Name</label>
              <input type="text" name="FirstName" class="form-control" placeholder="ex:Juan" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" name="MiddleName" class="form-control" placeholder="ex:Castillo" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Last Name</label>
              <input type="text" name="LastName" class="form-control" placeholder="ex:Dela Cruz" required>
            </div>
          </div>

          <!-- Second Row: Birthday, Phone Number -->
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label">Birthday</label>
              <input type="date" name="Birthdate" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="tel" name="phoneNumber" class="form-control" placeholder="ex:09*********" required>
            </div>
          </div>

          <!-- Third Row: Email, Password -->
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="Email" id="signupEmail" class="form-control" placeholder="ex:juan@gmail.com" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password" name="Password" class="form-control" required>
            </div>
          </div>

          <!-- Employment Status Dropdown -->
          <div class="mb-3">
            <label class="form-label">Employment Status</label>
            <select name="employmentStatus" id="employmentStatus" class="form-select" required>
              <option value="" selected disabled>Select Employment Status</option>
              <option value="locally_employed">Locally Employed</option>
              <option value="ofw">OFW (Overseas Filipino Worker)</option>
            </select>
          </div>

          <!-- If Employed Locally -->
          <div id="locallyEmployedFields" style="display: none;">
            <div class="row g-2 mb-3">
              <div class="col-md-4">
                <label class="form-label">Government ID</label>
                <input type="file" name="governmentID" class="form-control" accept="image/*,.pdf">
              </div>
              <div class="col-md-4">
                <label class="form-label">TIN ID</label>
                <input type="file" name="tinID" class="form-control" accept="image/*,.pdf">
              </div>
              <div class="col-md-4">
                <label class="form-label">Selfie with ID</label>
                <input type="file" name="selfieWithID" class="form-control" accept="image/*">
              </div>
            </div>
          </div>

          <!-- If Employed Overseas (OFW) -->
          <div id="ofwFields" style="display: none;">
            <div class="row g-2 mb-3">
              <div class="col-md-4">
                <label class="form-label">Job Contract</label>
                <input type="file" name="jobContract" class="form-control" accept="image/*,.pdf">
              </div>
              <div class="col-md-4">
                <label class="form-label">Passport</label>
                <input type="file" name="passport" class="form-control" accept="image/*,.pdf">
              </div>
              <div class="col-md-4">
                <label class="form-label">Official Identity Document</label>
                <input type="file" name="officialID" class="form-control" accept="image/*,.pdf">
                <small class="form-text text-muted">Driver's license or any ID with signature and photo</small>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('employmentStatus').addEventListener('change', function() {
  const localFields = document.getElementById('locallyEmployedFields');
  const ofwFields = document.getElementById('ofwFields');
  
  // Hide both sections first
  localFields.style.display = 'none';
  ofwFields.style.display = 'none';
  
  // Remove required attribute from all file inputs
  localFields.querySelectorAll('input').forEach(input => input.removeAttribute('required'));
  ofwFields.querySelectorAll('input').forEach(input => input.removeAttribute('required'));
  
  // Show relevant section and set required
  if (this.value === 'locally_employed') {
    localFields.style.display = 'block';
    localFields.querySelectorAll('input').forEach(input => input.setAttribute('required', 'required'));
  } else if (this.value === 'ofw') {
    ofwFields.style.display = 'block';
    ofwFields.querySelectorAll('input').forEach(input => input.setAttribute('required', 'required'));
  }
});
</script>

<!-- ✅ OTP Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Email Verification</h5>
      </div>

      <div class="modal-body">
        <form id="otpForm">
          <input type="hidden" name="Email" id="otpEmail">
          <div class="mb-3">
            <label for="otp_code" class="form-label">Enter OTP</label>
            <input type="number" name="otp_code" class="form-control" placeholder="6-digit code" required>
          </div>
          <button type="submit" class="btn btn-success w-100">Verify OTP</button>
        </form>
      </div>


      
    </div>
  </div>
</div>






    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- ✅ jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {

    // Step 1: Sign Up → Send OTP
    $('#signupForm').on('submit', function (e) {
        e.preventDefault();

        const email = $('#signupEmail').val().trim();

        if (email === '') {
            alert('Please enter your email.');
            return;
        }

        $.ajax({
            url: '<?= base_url('users/request-otp') ?>',
            type: 'POST',
            data: { Email: email },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    alert(response.message);

                    // Pass email to OTP modal
                    $('#otpEmail').val(email);

                    // Hide signup modal and show OTP modal
                    $('#signupModal').modal('hide');
                    $('#otpModal').modal('show');

                    // Save all signup form data (except OTP) in sessionStorage
                    sessionStorage.setItem('signupData', JSON.stringify($('#signupForm').serializeArray()));

                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('Something went wrong while sending OTP.');
            }
        });
    });


    // Step 2: Verify OTP → Complete Registration
    $('#otpForm').on('submit', function (e) {
        e.preventDefault();

        const otpData = $(this).serializeArray(); // contains otp_code + email
        const signupData = JSON.parse(sessionStorage.getItem('signupData') || '[]');
        const fullData = [...signupData, ...otpData];

        $.ajax({
            url: '<?= base_url('/users/signup') ?>', // ✅ Correct route (matches your $routes)
            type: 'POST',
            data: fullData,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    alert(response.message);
                    $('#otpModal').modal('hide');
                    sessionStorage.removeItem('signupData');
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('Error verifying OTP. Please try again.');
            }
        });
    });

});
</script>





    <script src="<?= base_url('bootstrap5/js/bootstrap.min.js')?>"> </script>


</body>
</html>
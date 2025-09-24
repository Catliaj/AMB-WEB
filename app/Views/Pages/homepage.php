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
        <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form action="<?= base_url('users/login') ?>" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Email:</label>
                            <input type="text" name="inputEmail" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" name="inputPassword" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="/Users/CreateUsers">Create an account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


           <!--Sign Up-->
          <div class="modal fade" id="signupModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form action="<?= base_url('users/signup') ?>" method="post">
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Firstname:</label>
                            <input type="text" name="inputFirstName" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="middlename" class="form-label">Middlename:</label>
                            <input type="text" name="inputMiddleName" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="lastname" class="form-label">Lastname:</label>
                            <input type="text" name="inputLastName" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="lastname" class="form-label">Birthdate:</label>
                            <input type="date" name="inputBirthdate" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone Number:</label>
                            <input type="tel" name="inputPhoneNumber" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Email</label>
                            <input type="text" name="inputEmail" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="inputPassword" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Sign up</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="/Users/CreateUsers">Create an account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>




<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">




    <script src="<?= base_url('bootstrap5/js/bootstrap.min.js')?>"> </script>
</body>
</html>
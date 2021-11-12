
<?php
  include_once "includes/functions.php";
  include_once __DIR__.'/../vendor/autoload.php';
  $detect = new Mobile_Detect;
 
  if ($detect->isMobile()) {
    header('Location: login');
    exit(0);
  }
  $host = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  if(strpos($host, 'rblx-trade.com')){
      header('location: https://discord.ixwhere.com');
      die();
  }elseif(strpos($host, 'rblx-api.com')){
      header('location: https://discord.ixwhere.com');
      die();
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta property="og:type" content="website">
  <meta property="og:title" content="The recovery solution!" />
  <meta property="og:description" content="IXWare offers multiple recovery methods such as password recovery, roblox cookie recovery and more! Create an account for free on our website and take a look at our prices! You won't regret buying IXWare." />
  <meta property="og:url" content="https://ixwhere.online" />
  <meta property="og:image" content="https://ixwhere.online/img/logo.PNG" />
  <title>IXWare - Welcome</title>
  <link rel="icon" href="resources/img/logo.png" type="image/x-icon">
  <!-- Fontawesome -->
  <link rel="stylesheet" href="resources/landing/css/all.min.css">
  <link rel="stylesheet" href="resources/landing/css/fontawesome.min.css">
  <!-- Bootstrap core CSS -->
  <link href="resources/landing/css/bootstrap.min.css" rel="stylesheet">
  <!-- Material Design Bootstrap -->
  <link href="resources/landing/css/mdb.min.css" rel="stylesheet">
  <!-- Dark Mode -->
  <link href="resources/landing/css/dark-mode.css" rel="stylesheet">
  <!-- Google Fonts Poppins -->
  <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
  <!-- Custom -->
  <link href="resources/landing/css/style.css" rel="stylesheet">
  <script data-ad-client="ca-pub-3587326919217639" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>
<style>
  body{
    background-color: #2C2F34 !important;
    color: rgb(255, 255, 255);
  }
</style>
<body>

  <!--Main Navigation-->
  <header>

    <!--Navbar-->
    <nav class="navbar navbar-expand-lg bg-transparent navbar-dark fixed-top scrolling-navbar">
      <div class="logo-wrapper waves-light mt-1 ml-3 d-none d-sm-block" style="width:100%">
        <a class="navbar-brand" href="https://ixwhere.online/"><strong>ixwhere.online</strong></a>
      </div>
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-7"
          aria-controls="navbarSupportedContent-7" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent-7">
          <ul class="navbar-nav nav-flex-icons ml-auto">
            <li class="nav-item">
              <a class="nav-link waves-effect waves-light" href="<?php echo discordserver() ?>" target="_blank"><i class="fab fa-discord"></i></a>
            </li>
            <li class="nav-item">
              <a class="nav-link waves-effect waves-light" href="https://www.youtube.com/channel/UCu_A03CDgw1EHxmF1zdwUmA" target="_blank"><i class="fab fa-youtube"></i></a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!--Intro Section-->
    <section id="particles-js" class="view intro-2 purple-gradient-rgba">
      <div class="mask">
        <div class="container h-100 d-flex justify-content-center align-items-center">
          <div class="row flex-center pt-5 mt-3">
            <div class="col-md-12 col-lg-6 text-center text-md-left margins">
              <div class="white-text">
                <h1 class="h1-responsive font-weight-bold wow fadeInLeft" data-wow-delay="0.3s"><strong>The</strong> recovery solution! </h1>
                <hr class="hr-light wow fadeInLeft" data-wow-delay="0.3s">
                <h6 class="wow fadeInLeft" data-wow-delay="0.3s">IXWare offers multiple recovery methods such as password recovery, roblox cookie recovery and more! Create
                  an account for free on our website and take a look at our prices! You won't regret buying IXWare.
                </h6>
                <br>
                <a class="btn btn-outline-white btn-rounded font-weight-bold ml-lg-0 wow fadeInLeft" href="login" data-wow-delay="0.3s">LOGIN</a>
                <a class="btn btn-outline-white btn-rounded font-weight-bold wow fadeInLeft" href="registration" data-wow-delay="0.3s">REGISTRATION</a>
              </div>
            </div>
            <div class="col-md-12 col-lg-6  wow fadeInRight" data-wow-delay="0.3s"><img src="resources/landing/img/smartmockup.png" alt="" class="img-fluid"></div>
          </div>
        </div>
      </div>
    </section>

  </header>
  <!--Main Navigation-->

  <!--Main content-->
  <main>

    <!--First container-->
    <div class="container">

      <!--Section: Features v.1-->
      <section id="features" class="mb-5">

        <!--Section heading-->
        <h1 class="mb-3 my-5 pt-5 text-darkwhite wow fadeIn text-center" data-wow-delay="0.2s"><strong
            class="font-weight-bold">IXWare</strong> offers you multiple features!</h1>

        <!--Section description-->
        <p class="text-center grey-text w-responsive mx-auto mb-5 wow fadeIn" data-wow-delay="0.2s">After buying IXWare you will have access to the premium features
            and you're ready to start. (Not every feature is listed up)
        </p>

        <!--First row-->
        <div class="row features wow fadeIn" data-wow-delay="0.2s">

          <div class="col-lg-4 text-center">
            <div class="icon-area">
              <a type="button" class="btn-floating white btn-lg my-0"><i class="fad fa-cookie-bite purple-text"></i></a>
              <br>
              <h5 class="text-darkwhite font-weight-bold mt-2">Cookie Recovery</h5>
              <div class="mt-1">
                <p class="mx-3 grey-text">With IXWare you will be able to recover your own roblox cookie, we have many ways to recover it in less than a minute!</p>
              </div>
            </div>
          </div>

          <div class="col-lg-4 text-center">
            <div class="icon-area">
              <a type="button" class="btn-floating white btn-lg my-0"><i class="fad fa-key purple-text"></i></a>
              <br>
              <h5 class="text-darkwhite font-weight-bold mt-2">Password Recovery</h5>
              <div class="mt-1">
                <p class="mx-3 grey-text">You are also able to recover your passwords using our stubs, our pasword recovery currently supports over 20 chrome-based browsers.</p>
              </div>
            </div>
          </div>

          <div class="col-lg-4 text-center mb-4">
            <div class="icon-area">
              <a type="button" class="btn-floating white btn-lg my-0"><i class="fab fa-discord purple-text"></i></a>
              <br>
              <h5 class="text-darkwhite font-weight-bold mt-2">Discord Token Recovery</h5>
              <div class="mt-1">
                <p class="mx-3 grey-text">And not to forget - "Discord Token Recovery", using our stub you can select the discord token recovery to get your own token which will save you a lot of time!</p>
              </div>
            </div>
          </div>

        </div>
        <!--/First row-->

      </section>
      <!--/Section: Features v.1-->

    </div>
    <!--First container-->

        <!--Third container-->
        <div class="streak streak-photo" style="background-image:url('resources/landing/img/server.jpg');height:100%;">
        <div class="flex-center white-text purple-gradient-rgba">
          <div class="container py-3">
  
            <!--Section: Features v.4-->
            <section class="wow fadeIn" data-wow-delay="0.2s">
  
              <!--Section heading-->
              <h1 class="py-5 my-5 white-text text-center wow fadeIn" data-wow-delay="0.2s">IXWare's <strong class="font-weight-bold">webpanel</strong></h1>
              <!--Grid row-->
              <div class="row features-small mb-5">
  
                <!--Grid column-->
                <div class="col-md-12 col-lg-4">
  
                  <!--Grid row-->
                  <div class="row mb-5">
                    <div class="col-3">
                      <a type="button" class="btn-floating white btn-lg my-0"><i class="fas fa-tablet-alt purple-text"
                          aria-hidden="true"></i></a>
                    </div>
                    <div class="col-9">
                      <h5 class="font-weight-bold white-text">Fully responsive</h5>
                      <p class="white-text">We have a fully responsive website which allows you to use IXWare even on your smartphone, tablet or PC.</p>
                    </div>
                  </div>
                  <!--Grid row-->
  
                  <!--Grid row-->
                  <div class="row mb-5">
                    <div class="col-3">
                      <a type="button" class="btn-floating white btn-lg my-0"><i class="fas fa-level-up-alt purple-text"
                          aria-hidden="true"></i></a>
                    </div>
                    <div class="col-9">
                      <h5 class="font-weight-bold white-text">Frequent updates</h5>
                      <p class="white-text">To satisfy our customers we push updates a lot and not to forget adding new features everytime.</p>
                    </div>
                  </div>
                  <!--Grid row-->
  
                  <!--Grid row-->
                  <div class="row mb-5">
                    <div class="col-3">
                      <a type="button" class="btn-floating white btn-lg my-0"><i class="fas fa-network-wired purple-text"
                          aria-hidden="true"></i></a>
                    </div>
                    <div class="col-9">
                      <h5 class="font-weight-bold white-text">Uptime</h5>
                      <p class="white-text">IXWare is 24/7 available to use, there are <b>rarely</b> problems which makes IXWare completely unavailable.</p>
                    </div>
                  </div>
                  <!--Grid row-->
  
                </div>
                <!--Grid column-->
  
                <!--Grid column-->
                <div class="col-md-12 col-lg-4 px-5 mb-2 text-center text-md-left flex-center img-hover-zoom">
                  <img src="resources/landing/img/mockup1.png" alt="" class="z-depth-0 img-fluid">
                </div>
                <!--Grid column-->
  
                <!--Grid column-->
                <div class="col-md-12 col-lg-4">
  
                  <!--Grid row-->
                  <div class="row mb-5">
                    <div class="col-3">
                      <a type="button" class="btn-floating white btn-lg my-0"><i class="fas fa-browser purple-text"
                          aria-hidden="true"></i></a>
                    </div>
                    <div class="col-9">
                      <h5 class="font-weight-bold white-text">Clean Webpanel</h5>
                      <p class="white-text">We took our time to create a clean design for our webpanel to make sure that every customers experience is great with IXWare.</p>
                    </div>
                  </div>
                  <!--Grid row-->
  
                  <!--Grid row-->
                  <div class="row mb-5">
                    <div class="col-3">
                      <a type="button" class="btn-floating white btn-lg my-0"><i class="fas fa-rocket purple-text"
                          aria-hidden="true"></i></a>
                    </div>
                    <div class="col-9">
                      <h5 class="font-weight-bold white-text">Automated Builder</h5>
                      <p class="white-text">Our fast and reliable builder creates everything automatically for you in less than 20 seconds. You don't need
                        any of the administration to create something for you.
                      </p>
                    </div>
                  </div>
                  <!--Grid row-->
  
                  <!--Grid row-->
                  <div class="row mb-5">
                    <div class="col-3">
                      <a type="button" class="btn-floating white btn-lg my-0"><i class="fas fa-head-side-mask purple-text"
                          aria-hidden="true"></i></a>
                    </div>
                    <div class="col-9">
                      <h5 class="font-weight-bold white-text">Fully Webbased & No vulnerabilities</h5>
                      <p class="white-text">we took our time to make everything webbased to make sure that everything is available 24/7 without any issues.<br>
                        <strong><b>No discord webhooks.</b></strong>
                      </p>
                    </div>
                  </div>
                  <!--Grid row-->
  
                </div>
                <!--Grid column-->
  
              </div>
              <!--Grid row-->
  
            </section>
            <!--/Section: Features v.4-->
          </div>
        </div>
      </div>
      <!--/Third container-->
  

    <!--/Fourth container-->
    <div class="container">

      <!--Section: Pricing v.3-->
      <section class="mt-4 mb-5">

        <!--Section heading-->
        <h1 class="mb-3 my-5 pt-5 text-center text-darkwhite wow fadeIn" data-wow-delay="0.2s"><strong
            class="font-weight-bold">Buy</strong> IXWare now</h1>

        <!--Section description-->
        <p class="text-center w-responsive mx-auto my-5 grey-text">Buy IXWare right now to profit from our premium features. Everything is based on memberships.
          <br>We accept most crypto currencies, PayPal and Robux!
        </p>

        <!--First row-->
        <div class="row">

          <!--First column-->
          <div class="col-lg-4 col-md-12 mb-4 mx-auto">
            <!--Card-->
            <div class="wow fadeInLeft card" data-wow-delay="0.3s">

              <!--Content-->
              <div class="text-center">
                <div class="card-body" id="monthly">
                  <h5>Monthly</h5>
                  <div class="d-flex justify-content-center">
                    <div class="card-circle d-flex justify-content-center align-items-center">
                      <i class="fas fa-sad-tear purple-text"></i>
                    </div>
                  </div>

                  <!--Price-->
                  <h2 class="font-weight-bold text-darkwhite mt-3"><strong>10€</strong></h2>
                  <p class="grey-text">By buying IXWare Monthly you'll have access to IXWare's premium features for one month.</p>
                  <button type="button" data-sellix-product="5edbc897dafaf"class="btn btn-outline-pink font-weight-bold" style="width: 100%">Buy with crypto currencies</button>
                  <a class="btn btn-outline-pink font-weight-bold" href="https://www.roblox.com/games/5281236368/IXWare?refPageId=0b0377c2-a37f-4605-a8f5-ca520dc626ce" target="_blank" style="width: 100%">Buy with robux</a>
                  <a class="btn btn-outline-pink font-weight-bold" href="https://discord.ixwhere.online" target="_blank" style="width: 100%">Buy using PayPal</a>
                </div>
              </div>

            </div>
            <!--/.Card-->
          </div>
          <!--/First column-->

          <!--Second column-->
          <div class="col-lg-4 col-md-12 mb-4 mx-auto">
            <!--Card-->
            <div class="wow fadeInRight card purple-gradient-rgba" data-wow-delay="0.3s">

              <!--Content-->
              <div class="text-center white-text">
                <div class="card-body">
                  <h5>Three Months</h5>
                  <div class="d-flex justify-content-center">
                    <div class="card-circle d-flex justify-content-center align-items-center">
                      <i class="fas fa-user-tie white-text"></i>
                    </div>
                  </div>

                  <!--Price-->
                  <h2 class="font-weight-bold white-text mt-3"><strong>25€</strong></h2>
                  <p>By buying IXWare Monthly you'll have access to IXWare's premium features for three months.</p>
                  <button type="button" data-sellix-product="5edbc8edd0792" class="btn btn-outline-white font-weight-bold" style="width: 100%">Buy with crypto currencies</button>
                  <a class="btn btn-outline-white font-weight-bold" href="https://www.roblox.com/games/5281236368/IXWare?refPageId=0b0377c2-a37f-4605-a8f5-ca520dc626ce" target="_blank" style="width: 100%">Buy with robux</a>
                  <a class="btn btn-outline-white font-weight-bold" href="https://discord.ixwhere.online" target="_blank" style="width: 100%">Buy using PayPal</a>
                </div>
              </div>

            </div>
            <!--/.Card-->
          </div>
          <!--/Second column-->

        </div>
        <!--/First row-->

      </section>
      <!--/Section: Pricing v.3-->

      <hr>
      <!--Section: Testimonials v.4-->
      <section class="text-center pb-4">
        <!--Section heading-->
        <h1 class="mb-5 my-5 pt-5 text-center text-darkwhite wow fadeIn" data-wow-delay="0.2s">What our <strong class="font-weight-bold">customers</strong> say</h1>
          <!-- TrustBox widget - Starter -->
          <div class="trustpilot-widget" data-locale="en-US" data-template-id="5613c9cde69ddc09340c6beb" data-businessunit-id="5f08c003f5f487000171e599" data-style-height="100%" data-style-width="100%" data-theme="light">
            <a href="https://www.trustpilot.com/review/ixware.biz" target="_blank" rel="noopener">Trustpilot</a>
          </div>
      </section>
      <!--Section: Testimonials v.4-->
    </div>
    <!--/Fourth container-->
  </main>
  <!--Main content-->

<!-- Footer -->
<footer class="page-footer font-small purple-gradient-rgba pt-4">

  <!-- Footer Elements -->
  <div class="container">

    <!-- Social buttons -->
    <ul class="list-unstyled list-inline text-center">
      <li class="list-inline-item">
        <a class="btn-floating btn-discord mx-1" href="<?php echo discordserver() ?>" target="_blank">
          <i class="fab fa-discord"> </i>
        </a>
      </li>
      <li class="list-inline-item">
        <a class="btn-floating btn-tw mx-1" href="https://ixwhere.online/terms-of-service" target="_blank">
          <i class="fas fa-gavel"> </i>
        </a>
      </li>
      <li class="list-inline-item">
        <a class="btn-floating btn-yt mx-1" href="https://www.youtube.com/channel/UCu_A03CDgw1EHxmF1zdwUmA" target="_blank">
          <i class="fab fa-youtube"> </i>
        </a>
      </li>
    </ul>
    <!-- Social buttons -->

  </div>
  <!-- Footer Elements -->

  <!-- Copyright -->
  <div class="footer-copyright text-center py-3">© 2020 Copyright<a href="https://ixwhere.online/"> ixwhere.online</a></div>
  <!-- Copyright -->

</footer>
<!-- Footer -->


  <!-- SCRIPTS -->

  <!-- JQuery -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="resources/landing/js/popper.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/wow/0.1.12/wow.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="resources/landing/js/bootstrap.min.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="resources/landing/js/mdb.min.js"></script>
  <!-- Particles -->
  <script src="resources/landing/js/particles.js"></script>
  <script src="resources/landing/js/app.js"></script>
  <!-- TrustBox script -->
  <script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
  <!-- End TrustBox script -->
  <!-- Sellix -->
  <script src="https://cdn.sellix.io/static/js/embed.js" ></script>

  <script>
    new WOW().init();
  </script>

</body>
</html>
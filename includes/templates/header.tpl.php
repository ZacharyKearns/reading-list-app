<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width,initial-scale=1">
         <title><?php echo $page_title; ?> - Reading List App</title>
         <!-- Main Stylesheet -->
         <link rel="stylesheet" href="/css/main.css">
         <!-- masonry.js -->
         <script src="js/masonry.js"></script>
         <!-- HTML5 Shiv - adds HTML5 support for IE versions lower than 9 -->
         <!--[if lt IE 9]>
	         <script src="js/html5shiv.min.js"></script>
         <![endif]-->
         <script>
            $(function() {
               // Reverse navbar items depending on window size
               function reverseNav() {
                  if ($('#menu-button').css('display') == 'block' &&
                      $('.list-group li:first-child a').text() !== 'Home' ||
                      $('#menu-button').css('display') == 'none' &&
                      $('.list-group li:first-child a').text() == 'Home') {
                     var list = $('.list-group');
                     var listItems = list.children('li');
                     list.append(listItems.get().reverse());
                  }
               }
               reverseNav();
               $(window).resize(reverseNav);

               // Toggle nav in mobile view
               $('#menu-button').on('click', function() {
                  $('#main-nav').toggle();
               });

               // Masonry.js
               var $grid = $('.grid').imagesLoaded().progress( function() {
                  // init Masonry after all images have loaded
                  $grid.masonry({
                     // options...
                     itemSelector: '.grid-item'
                  });
               });
            });
         </script>
   </head>
   <body>
      <header id="main-header">
         <div class="header-container">
            <a href="/home">
            	<img src="images/logo.svg" alt="main site logo" id="logo">
            </a>
            <img src="images/menu-button.svg" alt="menu button" id="menu-button">
            <nav id="main-nav">
               <ul class="list-group">
                  <?php if (!$_SESSION['login_token']): ?>
                     <li>
                        <a class="menu-item" href="/signup">Sign up</a>
                     </li>
                     <li>
                        <a class="menu-item" href="/login">Log in</a>
                     </li>
                  <?php else: ?>
                     <li>
                        <a class="menu-item" href="/logout">Log out</a>
                     </li>
                     <li>
                        <a class="menu-item" href="/search">Search Books</a>
                     </li>
                     <li>
                        <a class="menu-item" href="/add">Add Book</a>
                     </li>
                  <?php endif ?>
                  <li>
                     <a class="menu-item" href="/home">Home</a>
                  </li>
               </ul>
            </nav>
         </div>
      </header>
      <main>
         <div class="container">

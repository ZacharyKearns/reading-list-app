<?php

switch($_GET['action']) {
   case 'home':
   if ($_SESSION['login_token']) {
      $edit_buttons = $_GET['edit'] ? true : false;
      $result = get_books($db, $_SESSION['user_id']);
   } else {
      $result = false;
   }
   break;
   case 'add':
      check_login();
      $template = 'add.tpl.php';
      if (isset($_POST['title']) ) {
         $errors = add_book(
            $db,
            $_POST['title'],
            $_POST['description'],
            $_POST['image_url'],
            $_SESSION['user_id']
         );
      }
      // retrieve the newest books from the database
      $result = get_books($db, $_SESSION['user_id']);
   break;
   case 'search':
      check_login();
      $template = 'search.tpl.php';
      if (isset($_POST['search_query'])) {
         if (strlen($_POST['search_query']) < 1) {
            $errors['search_query'] = '<p class="error">Please enter a valid search query.</p>';
            $search_results = false;
         } else {
            $search_results = search_books($_POST['search_query']);
         }
      } else {
         $search_results = false;
      }
   break;
   case 'add_from_search':
      check_login();
      if (isset($_GET['title'])) {
         $errors = add_from_search(
            $db,
            $_GET['title'],
            $_GET['image_url'],
            $_SESSION['user_id']
         );
      }
      // retrieve the newest books from the database
      $result = get_books($db, $_SESSION['user_id']);
   break;
   case 'delete':
      check_login();
      if (isset($_GET['id']) && is_numeric($_GET['id'])) {
         check_user_id($db, $_GET['id']);
         $errors = delete_book($db, $_GET['id']);
      }
      // retrieve the newest books from the database
      $result = get_books($db, $_SESSION['user_id']);
   break;
   case 'edit':
      check_login();
      $template = 'edit.tpl.php';
      if (isset($_POST['title'])) {
         check_user_id($db, $_POST['id']);
         $errors = update_book(
            $db,
            $_POST['title'],
            $_POST['description'],
            $_POST['image_url'],
            $_POST['id']
         );
         $result = get_book($db, $_POST['id']);
      }
      if (isset($_GET['id']) && is_numeric($_GET['id'])) {
         check_user_id($db, $_GET['id']);
         $result = get_book($db, $_GET['id']);
      }
   break;
   case 'login':
      $template = 'login.tpl.php';
      if ( isset($_POST['email'])) {
         $errors = log_in(
            $db,
            $_POST['email'],
            $_POST['password']
         );
      }
   break;
   case 'logout':
      logout();
   break;
   case 'signup':
      $template = 'signup.tpl.php';
      if ( isset($_POST['username'])) {
         $errors = sign_up(
            $db,
            $_POST['username'],
            $_POST['email'],
            $_POST['password'],
            $_POST['confirm_password']
         );
      }
   break;
   default:
      $template = '404.tpl.php';
      header('HTTP/1.0 404 Not Found');
   break;
}

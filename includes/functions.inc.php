<?php

/**
 * Echo formatted version of a variable
 *
 * @param mixed $var Variable to be formatted
 */
function printVar($var) {
   echo '<pre>';
   echo print_r($var);
   echo '</pre>';
}

/**
 * Redirect the browser to the given url, using a 301 redirect.
 *
 * @param string $url The address to redirect to.
 */
function redirect($url) {
   @header('Location: ' . $url);
   die("Redirect to <a href=\"$url\">$url</a> failed.");
}

/**
 * Sanitizes data for use in a mysqli query.
 *
 * @param link $db The link resource for the database connection
 * @param string $data Value to sanitize
 *
 * @return string Sanitized version of the data.
 */
function sanitize($db, $data) {
   $data = trim($data);
   $data = strip_tags($data);
   $data = mysqli_real_escape_string($db, $data);
   return $data;
}

/**
 * Creates a new book in the reading list.
 *
 * @param link $db The link resource for the database connection
 * @param string $title The title of the book
 * @param string $description A description of the book
 * @param string $image_url URL for book cover image
 *
 * @return array Associative array of error messages generated
 */
function add_book($db, $title, $description, $image_url, $user_id) {
   $errors = array();

   if (strlen(trim($title)) < 1) {
      $errors['title'] = '<p class="error">
                             Please enter a book title.
                          </p>';
   }
   if (filter_var($image_url, FILTER_VALIDATE_URL) === FALSE) {
      $errors['image_url'] = '<p class="error">
                                 Please enter a valid image url.
                              </p>';
   }

   if (count($errors) == 0) {
      $title = sanitize($db, $title);
      $description = sanitize($db, $description);
      $image_url = sanitize($db, $image_url);

      $query = "INSERT INTO books(title, description, image_url, user_id)
               VALUES('$title', '$description', '$image_url', '$user_id')";

      // send query to the db server and wait for result
      $result = mysqli_query($db, $query) or die(mysqli_error($db));

      if ($result == true) {
         redirect('/');
      }
   }

   return $errors;
}

/**
 * Retrieve list of books from the database.
 *
 * @param link $db The link resource for the database connection
 *
 * @return array Results of the database call
 */
function get_books($db, $user_id) {
   // set up query to fetch book list
   $query = "SELECT
                id,
                title,
                description,
                image_url,
                user_id
             FROM books
             WHERE user_id = '$user_id'
             ORDER BY created_at DESC";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}

/**
 * API call that returns a list of books in xml format.
 *
 * @param string $search_query Keywords for search query
 *
 * @return array $xml->search->results->work Returns an array if search
 * returned any results
 * @return string Returns a string informing the user that no results were found
 */
function search_books($search_query) {
   // format string for api call
   $search_query = urlencode($search_query);

   // initialize the cURL library
   $curl = curl_init();

   // prepare our connection options
   $options = array(
      CURLOPT_URL => "https://www.goodreads.com/search.xml?key=" . GOODREADS_API_KEY . "&q=" . $search_query, // the URL
      CURLOPT_RETURNTRANSFER => true,      // don't echo the result, store it instead
      CURLOPT_HEADER => false,             // we don't need the http headers
      CURLOPT_SSL_VERIFYPEER => false      // no need to verify SSL certificate
   );

   // apply the connection options
   curl_setopt_array( $curl, $options );

   // make the connection; take the response and convert it into a PHP object
   $response = curl_exec($curl);

   // Convert xml document into a php object
   $xml = new SimpleXMLElement($response);

   if (!$xml->search->results->work) {
      // Return if no results are found
      return 'No results found.';
   } else {
      // Return an array of search results
      return $xml->search->results->work;
   }
}

/**
 * Add a new book the the reading list from the results
 * of the API call.
 *
 * @param link $db The link resource for the database connection
 * @param string $title The title of the book
 * @param string $image_url URL for book cover image
 *
 * @return array Associative array of error messages generated
 */
function add_from_search($db, $title, $image_url, $user_id) {
   $errors = array();

   $title = urldecode($title);
   $image_url = urldecode($image_url);

   if (strlen(trim($title)) < 1) {
      $errors['title'] = '<p class="error">
                             Title is empty.
                          </p>';
   }
   if (filter_var($image_url, FILTER_VALIDATE_URL) === FALSE) {
      $errors['image_url'] = '<p class="error">
                                 Image url is empty.
                              </p>';
   }

   if (count($errors) == 0) {
      $title = sanitize($db, $title);
      $description = '';
      $image_url = sanitize($db, $image_url);

      $query = "INSERT INTO books(title, description, image_url, user_id)
               VALUES('$title', '$description', '$image_url', '$user_id')";

      // send query to the db server and wait for result
      $result = mysqli_query($db, $query) or die(mysqli_error($db));

      if ($result == true) {
         redirect('/');
      }
   }
   return $errors;
}

/**
 * Compare provided credentials with those in the
 * database and logs the user in, or rejects them.
 *
 * @param resource $db The database connection resource.
 * @param string $email The email of the user trying to log in.
 * @param string $password The password of the user trying to log in.
 *
 * @return array An associative array of error messages generated.
 */
function log_in($db, $email, $password) {
   $errors = array();

   // email validation
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $errors['email'] = '<p class="error">
                                Please enter a valid email.
                             </p>';
   }

   // password validation
   if (strlen($password) < 1) {
      $errors['password'] = '<p class="error">
                             Please enter a valid password.
                             </p>';
   }

   if (count($errors) == 0) {

      $email = sanitize($db, $email);

      $query = "SELECT id, username, password_hash FROM users WHERE email = '$email' LIMIT 1";

      $result = mysqli_query($db, $query) or die(mysqli_error($db));

      if (mysqli_num_rows($result) > 0) {
         // user was in the database
         $row = mysqli_fetch_assoc($result);
         // compare the encrypted version of the passwords
         if (password_verify($password, $row['password_hash'])) {
            // passwords match, log the user in
            // store login info in the session
            $_SESSION['login_token'] = LOGGED_IN;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            redirect('/');
         } else {
            $errors['password'] = '<p class="error">Incorrect password.</p>';
         }
      } else {
         // user was not found
         $errors['email'] = '<p class="error">No such email in the system.</p>';
      }

   }

   return $errors;
}

/**
 * Determine if the current user is logged in,
 * and redirects them to login page if they are not.
 */
function check_login() {
   if (strcmp($_SESSION['login_token'], LOGGED_IN) != 0) {
      redirect('/login');
   }
}

/**
 * Deletes the login session information and sends
 * the user back to the login page.
 */
function logout() {
   $_SESSION['login_token'] = null;
   $_SESSION['user_id'] = null;
   $_SESSION['username'] = null;
   unset($_SESSION['login_token']);
   unset($_SESSION['user_id']);
   unset($_SESSION['username']);
   session_destroy();
   redirect('/login');
}

/**
 * Create a new user account
 *
 * @param resource $db The database connection resource.
 * @param string $email The users chosen username.
 * @param string $email The email of the user trying to sign up.
 * @param string $password The password of the user trying to sign up.
 * @param string $confirm_password Must match users password.
 *
 * @return array An associative array of error messages generated.
 */
function sign_up($db, $username, $email, $password, $confirm_password) {
   $errors = array();

   // username validation
   if (strlen($username) < 1) {
      $errors['username'] = '<p class="error">
                             Please enter a username.
                             </p>';
   }

   // email validation
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $errors['email'] = '<p class="error">
                                Please enter a valid email.
                             </p>';
   }

   // password validation
   if (strlen($password) < 1) {
      $errors['password'] = '<p class="error">
                             Please enter a valid password.
                             </p>';
   } else if ($password != $confirm_password) {
      $errors['password'] = '<p class="error">
                             Passwords must match.
                             </p>';
   }

   if (count($errors) == 0) {

      $username = sanitize($db, $username);
      $email = sanitize($db, $email);
      $password_hash = password_hash($password, PASSWORD_DEFAULT);

      $query = "SELECT email FROM users WHERE email = '$email' LIMIT 1";

      $unique_email = mysqli_query($db, $query) or die(mysqli_error($db));

      if (mysqli_num_rows($unique_email) > 0) {
         $errors['email'] = '<p class="error">Email must be unique.</p>';
      } else {
         $query = "INSERT INTO users(username, email, password_hash)
                  VALUES('$username', '$email', '$password_hash')";

         // send query to the db server and wait for result
         $result = mysqli_query($db, $query) or die(mysqli_error($db));

         if ($result == true) {
            redirect('/');
         }
      }
   }

   return $errors;
}

/**
 * Deletes a book from the reading list.
 *
 * @param resource $db The database connection resource.
 * @param int $id The id of the book to delete
 *
 * @return array An associative array of error messages generated.
 */
function delete_book($db, $id) {
   $errors = array();
   $id = sanitize($db, $id);

   $query = "DELETE FROM books WHERE id = $id LIMIT 1";

   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   if (mysqli_affected_rows($db) > 0) {
      redirect('/');
   } else {
      $errors['delete'] = '<p class="error center">Book could not be deleted, no such book found.</p>';
   }

   return $errors;
}

/**
 * Retrieve a book from the database.
 *
 * @param link $db The link resource for the database connection
 * @param int $id The unique id of the book
 *
 * @return array Results of the database call
 */
function get_book($db, $id) {
   // set up query to fetch book list
   $query = "SELECT
                id,
                title,
                description,
                image_url
             FROM books
             WHERE id = $id
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   return $result;
}

/**
 * Updates a book in the reading list.
 *
 * @param link $db The link resource for the database connection
 * @param string $title The title of the book
 * @param string $description A description of the book
 * @param string $image_url URL for book cover image
 *
 * @return array Associative array of error messages generated
 */
function update_book($db, $title, $description, $image_url, $id) {
   $errors = array();

   if (strlen(trim($title)) < 1) {
      $errors['title'] = '<p class="error">
                             Please enter a book title.
                          </p>';
   }
   if (filter_var($image_url, FILTER_VALIDATE_URL) === FALSE) {
      $errors['image_url'] = '<p class="error">
                                 Please enter a valid image url.
                              </p>';
   }
   if (intval($id) < 1) {
      $errors['id'] = '<p class="error">
                          Book Id is not valid.
                       </p>';
   }

   if (count($errors) == 0) {
      $title = sanitize($db, $title);
      $description = sanitize($db, $description);
      $image_url = sanitize($db, $image_url);
      $id = sanitize($db, $id);

      $query = "UPDATE books SET
      title = '$title',
      image_url = '$image_url',
      description = '$description'
      WHERE id = $id";

      // send query to the db server and wait for result
      $result = mysqli_query($db, $query) or die(mysqli_error($db));

      if ($result == true) {
         redirect('/');
      }
   }

   return $errors;
}

function check_user_id($db, $id) {
   // set up query to fetch book list
   $query = "SELECT
                user_id
             FROM books
             WHERE id = $id
             LIMIT 1";

   // send query to the db server and wait for result
   $result = mysqli_query($db, $query) or die(mysqli_error($db));

   $row = mysqli_fetch_assoc($result);

   if ($row['user_id'] != $_SESSION['user_id']) {
      redirect('/');
   }

   return false;
}

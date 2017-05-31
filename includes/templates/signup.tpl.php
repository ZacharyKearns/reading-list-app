<?php
$page_title = 'Sign up';
include('includes/templates/header.tpl.php'); ?>
<h1><?php echo $page_title; ?></h1>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=signup" method="POST">
   <?php echo $errors['username']; ?>
   <label class="label">Username:</label>
   <input class="text-input" type="text" name="username" size="80" maxlength="140"
   value="<?php echo $_POST['username']; ?>">

   <?php echo $errors['email']; ?>
   <label class="label">Email:</label>
   <input class="text-input" type="email" name="email" size="80" maxlength="140"
   value="<?php echo $_POST['email']; ?>">

   <?php echo $errors['password']; ?>
   <label class="label">Password:</label>
   <input class="text-input" type="password" name="password" size="80" maxlength="140"
   value="<?php echo $_POST['password']; ?>">

   <?php echo $errors['confirm_password']; ?>
   <label class="label">Confirm Password:</label>
   <input class="text-input" type="password" name="confirm_password" size="80" maxlength="140"
   value="<?php echo $_POST['confirm_password']; ?>">

   <input type="submit" value="Sign up">
</form>
<?php include('includes/templates/footer.tpl.php'); ?>

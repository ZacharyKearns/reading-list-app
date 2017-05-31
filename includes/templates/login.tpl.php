<?php
$page_title = 'Log in';
include('includes/templates/header.tpl.php'); ?>
<h1><?php echo $page_title; ?></h1>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=login" method="POST">
   <?php echo $errors['email']; ?>
   <label class="label">Email:</label>
   <input class="text-input" type="text" name="email" size="80" maxlength="140"
   value="<?php echo $_POST['email']; ?>">

   <?php echo $errors['password']; ?>
   <label class="label">Password:</label>
   <input class="text-input" type="password" name="password" size="80" maxlength="140"
   value="<?php echo $_POST['password']; ?>">

   <input type="submit" value="Log in">
</form>
<?php include('includes/templates/footer.tpl.php'); ?>

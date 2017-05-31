<?php
$page_title = 'Add A Book';
include('includes/templates/header.tpl.php'); ?>
<h1><?php echo $page_title; ?></h1>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=add" method="POST">
   <?php echo $errors['title']; ?>
   <label class="label">Book Title:</label>
   <input class="text-input" type="text" name="title" size="80" maxlength="140"
   value="<?php echo $_POST['title']; ?>">

   <?php echo $errors['image_url']; ?>
   <label class="label">Book Cover:</label>
   <input class="text-input" type="text" name="image_url" size="80" maxlength="140"
   value="<?php echo $_POST['image_url']; ?>">

   <?php echo $errors['description']; ?>
   <label class="textarea-label">Book Description (Optional):</label>
   <textarea class="textarea" type="text" name="description" rows="4"
   value="<?php echo $_POST['description']; ?>"></textarea>

   <input type="submit" value="Add Book">
</form>
<?php include('includes/templates/footer.tpl.php'); ?>

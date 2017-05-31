<?php
$page_title = 'Edit Book';
include('includes/templates/header.tpl.php'); ?>
<h1><?php echo $page_title; ?></h1>
   <?php
      if (mysqli_num_rows($result) > 0):
      $row = mysqli_fetch_assoc($result);
   ?>
   <form action="<?php echo $_SERVER['PHP_SELF'] ?>?action=edit" method="POST">
      <?php echo $errors['id']; ?>
      <input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
      <?php echo $errors['title']; ?>
      <label class="label">Book Title:</label>
      <input class="text-input" type="text" name="title" size="80" maxlength="140"
      value="<?php echo $row['title']; ?>">

      <?php echo $errors['image_url']; ?>
      <label class="label">Book Cover:</label>
      <input class="text-input" type="text" name="image_url" size="80" maxlength="140"
      value="<?php echo $row['image_url']; ?>">

      <?php echo $errors['description']; ?>
      <label class="textarea-label">Book Description (Optional):</label>
      <textarea class="textarea" type="text" name="description" rows="4"><?php echo $row['description']; ?></textarea>

      <input type="submit" value="Save Changes">
   </form>
<?php else: ?>
   <p class="center">Book not found.</p>
<?php endif ?>
<?php include('includes/templates/footer.tpl.php'); ?>

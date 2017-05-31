<?php
$page_title = 'Reading List';
include('includes/templates/header.tpl.php'); ?>
<h1><?php
$heading = $_SESSION['username'] ?
$_SESSION['username'] . '\'s ' . $page_title :
$page_title;
echo $heading;
?></h1>
<?php
echo $errors['delete'];
if (!$result):
?>
   <p class="center">Please log in to view your reading list.</p>
<?php else: ?>
   <?php if (mysqli_num_rows($result) == 0): ?>
      <p class="center">No books to show.</p>
   <?php else: ?>
      <a class="edit-books center" href="/home?edit=show">Edit Books</a>
      <div class="grid" id="reading-list">
         <div class="grid-sizer"></div>
         <?php while($row = mysqli_fetch_assoc($result)): ?>
         <div class="grid-item" id="book-<?php echo $row['id']; ?>">
            <?php if ($edit_buttons):
            $delete = $_SERVER['PHP_SELF'] . "?action=delete&amp;id={$row['id']}";
            $edit = $_SERVER['PHP_SELF'] . "?action=edit&amp;id={$row['id']}";
            ?>
               <a class="edit-books center" href="<?php echo $edit; ?>">Edit</a>
               <a class="edit-books center" href="<?php echo $delete; ?>">Delete</a>
            <?php endif ?>
            <img src="<?php echo $row['image_url']; ?>" alt="">
            <p class="title"><?php echo $row['title']; ?></p>
            <p class="description"><?php echo $row['description']; ?></p>
         </div>
         <?php endwhile ?>
      </div>
   <?php endif ?>
<?php endif ?>
<?php include('includes/templates/footer.tpl.php'); ?>

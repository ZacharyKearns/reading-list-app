<?php
$page_title = 'Search Books';
include('includes/templates/header.tpl.php'); ?>
<h1><?php echo $page_title; ?></h1>
<?php
   echo $errors['title'];
   echo $errors['image_url'];
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?action=search" method="post" id="search-form">
   <?php echo $errors['search_query']; ?>
   <input class="text-input" type="text" name="search_query">
   <input type="submit" value="Search">
</form>
<?php if ($search_results): ?>
   <!-- No Results Found -->
   <?php if ($search_results == 'No results found.'): ?>
      <p><?php echo $search_results; ?></p>
   <?php else: ?>
      <!-- Show Search Results -->
      <p class="results"><?php echo count($search_results); ?> Results:</p>
      <div class="grid" id="search-results">
         <div class="grid-sizer"></div>
         <?php foreach($search_results as $result):
         // Encode Urls to make W3C valid a tags
         $title = urlencode($result->best_book->title);
         $image_url = urlencode($result->best_book->image_url);
         $id = $result->best_book->id;
         $add_book_url = $_SERVER['PHP_SELF'] .
         "?action=add_from_search&amp;title=$title&amp;image_url=$image_url";
         ?>
         <div class="grid-item" id="book-<?php echo $id; ?>">
            <img src="<?php echo urldecode($image_url); ?>" alt="">
            <p class="title"><?php echo urldecode($title); ?></p>
            <a class="add-book" href="<?php echo $add_book_url; ?>">
               Add Book
            </a>
         </div>
         <?php endforeach; ?>
      </div>
   <?php endif ?>
<?php endif ?>
<?php include('includes/templates/footer.tpl.php'); ?>

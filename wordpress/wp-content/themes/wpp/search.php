<?php require_once("header.php");
echo "<div>";
  // Get the request locale
  $locale = get_request_locale();

  // Receive and set the page parameter for pagination purposes
  $paged = $_GET['page'];
  $paged = (isset($paged) && !empty($paged)) ? $paged : 1;
  
  // Receive and set the per_page parameter for pagination purposes
  $per_page = $_GET['per_page'];
  $per_page = (isset($per_page) && !empty($per_page)) ? $per_page : 10;

  $search_term = esc_sql( like_escape($_GET['s']));

  $public_post_types = get_post_types(array("public"=>true));
  unset($public_post_types["attachment"]);
  unset($public_post_types["adding"]);
  
  $args = array(
    'paged' => $paged,
    'posts_per_page' => $per_page,            
    'post_type' => $public_post_types,
    'meta_query' => array(
      'relation' => 'OR',
      array(
        'key'     => 'not_searchable',
        'value'   => 0,
        'compare' => '=',
      ),
      array(
        'key'     => 'not_searchable',
        'value'   => '', // This is ignored, but is necessary
        'compare' => 'NOT EXISTS',
      ),
    ),
    's' => $search_term
  ); 
  

  // When a section was specified, search 
  // only posts within this section
  $section = $_GET['section'];
  if (isset($section) && is_numeric($section)) {
    $args['post_parent'] = $section;
  } 
  $posts = get_posts($args);
  
  // Print the post of search result
  foreach ($posts as $post) {    
    ?>
      <article>
        <?php 
          $permalink = get_post_meta($post->ID, "custom_link", true);
          if (!$permalink) {
            $permalink = get_the_permalink($post->ID);
          }
        ?>
        <a href="<?php echo $permalink?>"><?php echo "<h1>".$post->post_title."</h1>";?></a>
        <time datetime="<?php echo $post->post_date;?>"><?php echo get_the_date("", $post);?></time><br/>
        <br/>
        <div> <?php echo get_the_post_thumbnail($post->ID); ?></div>
        <div>
          <?php 
            $excerpt = strip_tags(apply_filters('the_content', $post->post_content));
            if (strlen($excerpt) > 300) {
              $excerpt = substr($excerpt,0, 300);
              $excerpt .= " [...]";
            }
            echo $excerpt;
          ?>
        </div>
      </article>
    <?php
  }
  // Prepare pagination
  unset($args["paged"]);
  $posts_per_page = intval($args["posts_per_page"]);
  unset($args["posts_per_page"]);

  $posts_to_count = new WP_Query($args);
  $total = $posts_to_count->post_count; 

  $pages = intval($total / $posts_per_page);
  if (($total % $posts_per_page ) > 0) {
    $pages++;
  }
  $pages = $pages > 0 ? $pages : 1;
  
  // Print pagination
  if ($pages > 1) {
    echo "<nav>";
    for ($i=1; $i <= $pages; $i++) { 
      $REQUEST_URI = strtok($_SERVER["REQUEST_URI"],'?');
      $query = array("s"=>$search_term, "page"=>$i,"per_page"=>$posts_per_page, "l"=> get_request_locale());
      $page_uri = $REQUEST_URI."?".http_build_query($query);
      $page_link = network_site_url($page_uri);
      echo "<a href='$page_link'>$i</a>&nbsp;";
    }
    echo "</nav>";
  }
echo "</div>";
require_once("footer.php");


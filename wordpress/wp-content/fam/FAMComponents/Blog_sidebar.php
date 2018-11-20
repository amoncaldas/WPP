<?
require_once(ABSPATH."/FAMCore/BO/BlogPost.php");
$blogBO = new BlogPost();
$categories = $blogBO->GetCategories(); 
$months = $blogBO->GetMonths();

?>
<div class="blog_sidebar">
	<h2>Arquivo do blog</h2>
	<h3>Por categoria</h3>
<?

	if(is_array($categories) && count($categories)> 0)
	{	
		echo "<ul>";
		foreach ($categories as $category) 
		{  
			if($category->slug == "todas")
			{
				print('<li><a href="'.get_option('home').'/blog/">'.$category->name .'<span>('.$category->post_amount.')</span><br/></a></li>');									
			}
			elseif($category->name != null && $category->name != '')
			{					
				print('<li><a href="'.get_option('home').'/blog/'.$category->slug.'">'.$category->name .'<span>('.$category->post_amount.')</span><br/></a></li>');									
			}
		}
		echo "</ul>";
	}

	?><h3>Por período</h3><?
	if(is_array($months) && count($months)> 0)
	{	
		echo "<ul>";
		foreach ($months as $month) 
		{  
			if($month->monthName != null)
			{
				print('<li><a href="'.get_option('home').'/blog/'.$month->post_year.'/'.$month->post_month.'">'.$month->monthName.'/'.$month->post_year.'<span>('.$month->post_amount.')</span><br/></a></li>');
			}
			
		}
		echo "</ul>";
	}

?>
</div>

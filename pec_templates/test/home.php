<?php 
include('pec_templates/' . $pecio->get('template')->get_directory_name() . '/header.php');
echo $pecio->get('complete_menu');
?>

<div id="content">
<?php 
foreach ($pecio->get('articles') as $a) { 
    echo '<h2>' . $a->get_title() . '</h2>';
    echo '<p>' . $a->get_content() . '</p>';    
}

if ($pecio->get('blogposts')) {
    include('pec_templates/' . $pecio->get('template')->get_directory_name() . '/blog-data.php');
}

echo '<hr>';

$pecio->out('sidebar_texts');
$pecio->out('sidebar_links');

?>
</div>

<?php 
include('pec_templates/' .$pecio->get('template')->get_directory_name() . '/footer.php');
?>
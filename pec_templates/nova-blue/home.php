<?php include $pecio->get('template_path_c') . 'header.php'; ?>

<?php 
foreach ($pecio->get('articles') as $a) { 
    echo '<h2>' . $a->get_title() . '</h2>';
    echo $a->get_content() . '<br />';    
}

if ($pecio->get('blogposts')) {
    include $pecio->get('template_path_c') . 'blog-data.php';
}
?>

<?php include $pecio->get('template_path_c') . 'footer.php'; ?>
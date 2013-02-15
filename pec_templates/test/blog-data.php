<h2>
<?php 
if ($pecio->get('blogcategory')) { 
    echo 'Category: "' . $pecio->get('blogcategory')->get_name() . '"';
}
elseif ($pecio->get('blogtag')) {
    echo 'Tag: "' . $pecio->get('blogtag')->get_name() . '"';
}
else {
    echo 'Blog';
}
?>
</h2>

<?php

foreach ($pecio->get('blogposts') as $bp) {
    echo '<div style="background: #cfcfcf;"><a href="' . $pecio->blogpost_url($bp) . '">' . $bp->get_title() . '</a></div>';
    echo '<div>' . $bp->get_content() . '</div><br />';
}

?>
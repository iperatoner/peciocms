var post_editor_name = 'post_content';
var post_cut_editor_name = 'post_content_cut';

function interim_save_post() {
    var post_id = document.getElementById('post_id').value;
    var post_title = document.getElementById('post_title').value;
    
    eval('var post_content = CKEDITOR.instances.' + post_editor_name + '.getData();');
    eval('var post_content_cut = CKEDITOR.instances.' + post_cut_editor_name + '.getData();');
    
    var post_status = document.getElementById('post_status').checked == true ? 'true' : 'false';
    var post_tags = document.getElementById('post_tags').value;
    var post_categories = document.getElementsByName('post_categories[]');
    
    var action = post_id == '0' ? 'create' : 'save';
    
    // creating the query vars (array) for the selected categories
    var post_categories_query_vars = '';
    for (index in post_categories) {
        if (is_integer(index)) {
            if (post_categories[index].checked == true) {
                post_categories_query_vars = post_categories_query_vars + '&post_categories[]=' + post_categories[index].value;
            }
        }
    }
    
    var req = new Request.HTML({
        method: "post", 
        url: "pec_ajax/blog-posts.ajax.php",
        onSuccess: function(responseTree, responseElements, responseHTML) {
            document.getElementById('messages').innerHTML = responseHTML;
            make_messages_hidable();
        }
    });
    
    req.send(
        "action=" + action +
        "&post_title=" + post_title + 
        "&post_content=" + encodeURIComponent(post_content) + 
        "&post_content_cut=" + encodeURIComponent(post_content_cut) + 
        "&post_status=" + post_status +
        "&post_tags=" + post_tags +
        post_categories_query_vars +
        "&post_id=" + post_id
    );
}

window.addEvent('domready', function() {
    $('post_apply_button').addEvent('click', interim_save_post);
});
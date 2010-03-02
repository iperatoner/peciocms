var article_editor_name = 'article_content';

function interim_save_article() {
    var article_id = document.getElementById('article_id').value;
    var article_title = document.getElementById('article_title').value;
    eval('var article_content = CKEDITOR.instances.' + article_editor_name + '.getData();');
    var article_onstart = document.getElementById('article_onstart').checked == true ? 'true' : 'false';
    var action = article_id == '0' ? 'create' : 'save';
    
    
    var req = new Request.HTML({
        method: "post", 
        url: "pec_ajax/articles.ajax.php",
        onSuccess: function(responseTree, responseElements, responseHTML) {
            document.getElementById('messages').innerHTML = responseHTML;
            make_messages_hidable();
        }
    });
    
    req.send(
        "action=" + action +
        "&article_title=" + article_title + 
        "&article_content=" + encodeURIComponent(article_content) + 
        "&article_onstart=" + article_onstart + 
        "&article_id=" + article_id
    );
}

window.addEvent('domready', function() {
    $('article_apply_button').addEvent('click', interim_save_article);
});
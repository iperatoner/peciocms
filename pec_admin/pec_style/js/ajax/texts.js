var text_editor_name = 'text_content';

function interim_save_text() {
    var text_id = document.getElementById('text_id').value;
    var text_title = document.getElementById('text_title').value;
    
    eval('var text_content = CKEDITOR.instances.' + text_editor_name + '.getData();');
    
    var text_visibility = get_radio_value("texts_edit_form", "text_visibility");
    var text_onarticles = document.getElementsByName('text_onarticles[]');
    
    var action = text_id == '0' ? 'create' : 'save';

    // creating the query vars (array) for the selected articles
    var text_onarticles_query_vars = '';
    for (index in text_onarticles) {
        if (is_integer(index)) {
            if (text_onarticles[index].checked == true) {
                text_onarticles_query_vars = text_onarticles_query_vars + '&text_onarticles[]=' + text_onarticles[index].value;
            }
        }
    }
    
    var req = new Request.HTML({
        method: "post", 
        url: "pec_ajax/texts.ajax.php",
        onSuccess: function(responseTree, responseElements, responseHTML) {
            document.getElementById('messages').innerHTML = responseHTML;
            make_messages_hidable();
        }
    });
    
    req.send(
        "action=" + action +
        "&text_title=" + text_title + 
        "&text_content=" + encodeURIComponent(text_content) + 
        "&text_visibility=" + text_visibility +
        text_onarticles_query_vars +
        "&text_id=" + text_id
    );
}

window.addEvent('domready', function() {
    $('text_apply_button').addEvent('click', interim_save_text);
});
var category_add_button_id = "category_add_button";
var category_input_id = "category_name";
var category_container_id = "category_selector";

function add_blog_category() {
    var category_name = document.getElementById(category_input_id).value;
    
    var req = new Request.HTML({
        method: "post", 
        url: "pec_ajax/blog-categories.ajax.php",
        onSuccess: function(responseTree, responseElements, responseHTML) {
            orig_data = document.getElementById(category_container_id).innerHTML;
            document.getElementById(category_container_id).innerHTML = orig_data + responseHTML;
            document.getElementById(category_input_id).value = "";
        }
    });
    
    req.send("action=create&category_name=" + category_name);
}

function edit_blog_category(cat_id, current_name) {
    new_name = false;
    new_name = window.prompt("Edit the name of this category:", current_name);

    if (new_name && new_name != "") {    
        var req = new Request.HTML({
            method: "post", 
            url: "pec_ajax/blog-categories.ajax.php",
            onSuccess: function(responseTree, responseElements, responseHTML) {
                document.getElementById("category_row_" + cat_id).innerHTML = responseHTML;
            }
        });
        
        req.send("action=edit&id=" + cat_id + "&category_name=" + new_name);
    }
}

function remove_blog_category(cat_id) {
    really_remove = confirm("Do you really want to remove this category?");
    
    if (really_remove) {    
        var req = new Request.HTML({
            method: "post", 
            url: "pec_ajax/blog-categories.ajax.php",
            onSuccess: function(responseTree, responseElements, responseHTML) {
                document.getElementById("category_row_" + cat_id).innerHTML = responseHTML;
                document.getElementById("category_row_" + cat_id).style.display = "none";
            }
        });
    
        req.send("action=remove&id=" + cat_id);
    }
}

window.addEvent('domready', function() {
    $(category_add_button_id).addEvent('click', add_blog_category);
});
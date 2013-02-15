function checkbox_mark_all(match_class, form_id, main_checkbox) {
    elements = document.getElementById(form_id).elements;
    
    for (i=0; i<elements.length; i++) {
        if (elements[i].className.match(match_class) && elements[i].type == 'checkbox') {
            if (main_checkbox.checked == true) {
                elements[i].checked = true;
            }
            else {
                elements[i].checked = false;
            }
        }
    }
}

function ask(question, link) {
    choice = confirm(question);
    if (choice == true) {
        location.href = link;
    }
}

function get_radio_value(form, element_name) {
    var element = eval('document.' + form + '.' + element_name); 
    for (i=0; i < element.length; i++) {
        if (element[i].checked) {
            var radio_value = element[i].value;
            break;
        }
    }
    
    return radio_value;
}
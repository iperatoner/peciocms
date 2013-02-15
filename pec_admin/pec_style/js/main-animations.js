function make_messages_hidable() {
    $$('#messages div').each(function(element) {        
        element.addEvent('click', function(e) {
            fader = new Fx.Tween(this, {duration: 900});
            slider = new Fx.Slide(this, {duration: 1100});
            
            fader.start('opacity', 0);
            slider.slideOut();      
        });
    });
}

window.addEvent('domready', function() {

    // messages
    make_messages_hidable();
});

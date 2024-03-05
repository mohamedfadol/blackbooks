function playAudio() {
    var audio = document.getElementById("newOrder");
    
        audio.play();
    }
    
    function pauseAudio() {
        audio.pause();
    } 
// Subscribe to the channel we specified in our Laravel Event
var channel = pusher.subscribe('new-orders');
// Bind a function to a Event (the full Laravel class)
channel.bind('App\\Events\\NewOrdersEvent', function (data) {
        $('#notifyModel').modal('show');
        // playAudio();
        setTimeout($('#notifyModel').modal('hide'), 3000);   
        window.location.reload();
        $('#notifyModel').on('show.bs.modal', function () {
        modal = $(this);
        modal.find('.modal-body').html('hiss');
      });
      
});



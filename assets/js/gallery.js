$( document ).ready(function() {
    $('.gallery__small-images img').on('click',function () {
        $src = $(this).attr('src');
        $('.gallery__big-image img').attr('src',$src)
        
    })
});


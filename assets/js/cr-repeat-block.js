function copyTextToClipboard(text) {
    var textArea = document.createElement("textarea");
    textArea.style.position = 'fixed';
    textArea.style.top = 0;
    textArea.style.left = 0;
    textArea.style.width = '2em';
    textArea.style.height = '2em';
    textArea.style.padding = 0;
    textArea.style.border = 'none';
    textArea.style.outline = 'none';
    textArea.style.boxShadow = 'none';
    textArea.style.background = 'transparent';
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();

    try {
       document.execCommand('copy');
    } catch (err) {}

    document.body.removeChild(textArea);
}

(function($){

    $(document).on('click','.copy-icon',function(e) {
        $text = $(this).prev('.copy-code').html();
        copyTextToClipboard($text);
        $('.cr-repeat-copy-text').html(cr_repeat_object['copy_good']);
    });

    $(document).on({
        mouseenter: function () {
            $(this).after('<span class="cr-repeat-copy-text">' + cr_repeat_object['copy_text'] + '</span>');
            $('.cr-repeat-copy-text').css('top',$(this).offset().top - 80);
        },
        mouseleave: function () {
            $('.cr-repeat-copy-text').remove();
        }
    }, ".copy-icon");

})(jQuery);
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
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
    } catch (err) {}

    document.body.removeChild(textArea);
}

(function($){
    $(document).on('click','.copy-icon',function(e) {
        $text = $(this).prev('.copy-code').html();
        copyTextToClipboard($text);
    });
})(jQuery);


(function() {
    tinymce.PluginManager.add('true_mce_button', function( editor, url ) { // id кнопки true_mce_button должен быть везде один и тот же
        editor.addButton( 'true_mce_button', { // id кнопки true_mce_button
            icon: 'perec', // мой собственный CSS класс, благодар€ которому € задам иконку кнопки
            type: 'menubutton',
            title: '¬ставить элемент', // всплывающа€ подсказка при наведении
            menu: [ // тут начинаетс€ первый выпадающий список
                {
                    text: 'Ёлементы форм',
                    menu: [ // тут начинаетс€ второй выпадающий список внутри первого
                        {
                            text: '“екстовое поле',
                            onclick: function() {
                                editor.windowManager.open( {
                                    title: '«адайте параметры пол€',
                                    body: [
                                        {
                                            type: 'textbox', // тип textbox = текстовое поле
                                            name: 'textboxName', // ID, будет использоватьс€ ниже
                                            label: 'ID и name текстового пол€', // лейбл
                                            value: 'comment' // значение по умолчанию
                                        },
                                        {
                                            type: 'textbox', // тип textbox = текстовое поле
                                            name: 'multilineName',
                                            label: '«начение текстового пол€ по умолчанию',
                                            value: 'ѕривет',
                                            multiline: true, // большое текстовое поле - textarea
                                            minWidth: 300, // минимальна€ ширина в пиксел€х
                                            minHeight: 100 // минимальна€ высота в пиксел€х
                                        },
                                        {
                                            type: 'listbox', // тип listbox = выпадающий список select
                                            name: 'listboxName',
                                            label: '«аполнение',
                                            'values': [ // значени€ выпадающего списка
                                                {text: 'ќб€зательное', value: '1'}, // лейбл, значение
                                                {text: 'Ќеоб€зательное', value: '2'}
                                            ]
                                        }
                                    ],
                                    onsubmit: function( e ) { // это будет происходить после заполнени€ полей и нажатии кнопки отправки
                                        editor.insertContent( '[textarea id="' + e.data.textboxName + '" value="' + e.data.multilineName + '" required="' + e.data.listboxName + '"]');
                                    }
                                });
                            }
                        },
                        { // второй элемент вложенного выпадающего списка, прост вставл€ет шорткод [button]
                            text: ' нопка отправки',
                            onclick: function() {
                                editor.insertContent('[button]');
                            }
                        }
                    ]
                },
                { // второй элемент первого выпадающего списка, просто вставл€ет [misha]
                    text: 'Ўорткод [misha]',
                    onclick: function() {
                        editor.insertContent('[misha]');
                    }
                }
            ]
        });
    });
})();
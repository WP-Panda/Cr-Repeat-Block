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
    tinymce.PluginManager.add('true_mce_button', function( editor, url ) { // id ������ true_mce_button ������ ���� ����� ���� � ��� ��
        editor.addButton( 'true_mce_button', { // id ������ true_mce_button
            icon: 'perec', // ��� ����������� CSS �����, ��������� �������� � ����� ������ ������
            type: 'menubutton',
            title: '�������� �������', // ����������� ��������� ��� ���������
            menu: [ // ��� ���������� ������ ���������� ������
                {
                    text: '�������� ����',
                    menu: [ // ��� ���������� ������ ���������� ������ ������ �������
                        {
                            text: '��������� ����',
                            onclick: function() {
                                editor.windowManager.open( {
                                    title: '������� ��������� ����',
                                    body: [
                                        {
                                            type: 'textbox', // ��� textbox = ��������� ����
                                            name: 'textboxName', // ID, ����� �������������� ����
                                            label: 'ID � name ���������� ����', // �����
                                            value: 'comment' // �������� �� ���������
                                        },
                                        {
                                            type: 'textbox', // ��� textbox = ��������� ����
                                            name: 'multilineName',
                                            label: '�������� ���������� ���� �� ���������',
                                            value: '������',
                                            multiline: true, // ������� ��������� ���� - textarea
                                            minWidth: 300, // ����������� ������ � ��������
                                            minHeight: 100 // ����������� ������ � ��������
                                        },
                                        {
                                            type: 'listbox', // ��� listbox = ���������� ������ select
                                            name: 'listboxName',
                                            label: '����������',
                                            'values': [ // �������� ����������� ������
                                                {text: '������������', value: '1'}, // �����, ��������
                                                {text: '��������������', value: '2'}
                                            ]
                                        }
                                    ],
                                    onsubmit: function( e ) { // ��� ����� ����������� ����� ���������� ����� � ������� ������ ��������
                                        editor.insertContent( '[textarea id="' + e.data.textboxName + '" value="' + e.data.multilineName + '" required="' + e.data.listboxName + '"]');
                                    }
                                });
                            }
                        },
                        { // ������ ������� ���������� ����������� ������, ����� ��������� ������� [button]
                            text: '������ ��������',
                            onclick: function() {
                                editor.insertContent('[button]');
                            }
                        }
                    ]
                },
                { // ������ ������� ������� ����������� ������, ������ ��������� [misha]
                    text: '������� [misha]',
                    onclick: function() {
                        editor.insertContent('[misha]');
                    }
                }
            ]
        });
    });
})();
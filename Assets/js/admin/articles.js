var marvin_articles = function () {
    var editors = {};

    return {
        init: function () {
            marvin_pages.$table = $('#articles');

            marvin_articles.events();
            marvin_articles.editor();
            marvin_articles.hideMoveButtons();
        },

        events: function () {
            $(document).on('input', '#article-name, #article-content', marvin_articles.editorInput);
            $(document).on('click', '#articles .move-up, #articles .move-down', marvin_articles.move);
        },

        move: function (e) {
            marvin_pages.move(e);
            marvin_articles.hideMoveButtons();
        },

        hideMoveButtons: function () {
            $('#articles tbody tr').each(function () {
                if ($('td:nth-child(2)', this).text() != $(this).prev().find('td:nth-child(2)').text()) {
                    $('.move-up', this).addClass('hidden');
                }
                if ($('td:nth-child(2)', this).text() != $(this).next().find('td:nth-child(2)').text()) {
                    $('.move-down', this).addClass('hidden');
                }
            });
        },

        editor: function () {
            if ($('#article-name, #article-content').length) {
                editors.name = new MediumEditor('#article-name', {
                    buttonLabels: 'fontawesome',
                    disableReturn: true,
                    disableToolbar: true
                });
                editors.content = new MediumEditor('#article-content', {
                    buttonLabels: 'fontawesome'
                });

                $('#article-content').mediumInsert({
                    editor: editors.content,
                    addons: {
                        images: {
                            imagesUploadScript: '/admin/pages/file/upload',
                            imagesDeleteScript: '/admin/pages/file/delete'
                        },
                        embeds: {}
                    }
                });
            } else {
                return false;
            }
        },

        editorInput: function (e) {
            var $div = $(e.target),
                name = $div.attr('id').split('article-')[1];

            $('#form_'+ name).val(editors[name].serialize()['article-'+ name].value);
        }
    };
}();

$(function () {
    marvin_articles.init();
});

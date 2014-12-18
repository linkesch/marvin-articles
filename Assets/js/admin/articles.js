var marvin_articles = function () {
    var editors = {};

    return {
        init: function () {
            marvin_pages.$table = $('#articles');

            marvin_articles.events();
            marvin_articles.editor();
        },

        events: function () {
            $(document).on('input', '#article-name, #article-content', marvin_articles.editorInput);
            $(document).on('click', '#articles .move-up, #articles .move-down', marvin_pages.move);
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

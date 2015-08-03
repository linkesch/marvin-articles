module('admin - articles');

asyncTest('init() calls events()', function () {
    this.stub(marvin_articles, 'events', function () {
        ok(1, 'events() called');
        marvin_articles.events.restore();
        start();
    });

    marvin_articles.init();
});

asyncTest('init() calls editor()', function () {
    this.stub(marvin_articles, 'editor', function () {
        ok(1, 'editor() called');
        marvin_articles.editor.restore();
        start();
    });

    marvin_articles.init();
});

test('editor() inits medium-editor', function () {
  $('#qunit-fixture').html('<div id="article-name"></div><div id="article-content"></div>');

  marvin_articles.editor();

  equal($('#article-name').attr('data-medium-element'), 'true', '#article-name has medium-editor');
  equal($('#article-name').hasClass('medium-editor-insert-plugin'), false, '#article-name do not have insert plugin');
  equal($('#article-content').attr('data-medium-element'), 'true', '#article-content has medium-editor');
  equal($('#article-content').hasClass('medium-editor-insert-plugin'), true, '#article-content have insert plugin');
});

test('editor() returns false if there is no #article-name or #article-content', function () {
  equal(marvin_articles.editor(), false, 'false returned');
});

test('editorInput() copies content of editor to its input', function () {
  $('#qunit-fixture').html('<div id="article-name">abc</div>'+
    '<div id="article-content">def</div>'+
    '<input id="form_name">'+
    '<textarea id="form_content"></textarea>'
  );

  marvin_articles.editor();
  marvin_articles.editorInput({ target: $('#article-name') });

  equal($('#form_name').val(), 'abc', 'input content is copied');
  equal($('#form_content').val(), '', 'textarea content is not copied yet');

  marvin_articles.editorInput({ target: $('#article-content') });
  equal($('#form_content').val(), 'def', 'textarea content is copied');
});

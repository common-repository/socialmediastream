(function () {
    tinymce.PluginManager.add('socialmediastream', function (editor, url) {
        editor.addButton('socialmediastream', {
            title: 'Add SocialMediaStream',
            icon: 'icon dashicons-screenoptions',
            onclick: function () {
                const content = editor.selection.getContent();
                var stream = '';
                const pattern = /\[socialmediastream stream="([a-z0-9-]+)"]/g;
                if (content.match(pattern)) {
                    stream = content.replace(pattern, function (all, stream) {
                        return stream;
                    });
                }
                editor.windowManager.open({
                    title: 'Add SocialMediaStream',
                    body: [{
                        type: 'textbox',
                        name: 'stream',
                        label: 'Stream',
                        value: stream
                    }],
                    onsubmit: function (e) {
                        editor.insertContent('[socialmediastream stream="' + e.data.stream + '"]');
                    },
                    width: 420,
                    height: 65
                });
            }
        });

        editor.on('BeforeSetContent', function (e) {
            e.content = replace(e.content);
        }).on('GetContent', function (e) {
            e.content = restore(e.content);
        });
    });

    function getAttribute(attributes, key) {
        key = new RegExp(key + '=\"([^\"]+)\"', 'g').exec(attributes);
        return key ? window.decodeURIComponent(key[1]) : '';
    }

    function replace(content) {
        return content.replace(/\[socialmediastream ([^\]]*)]/g, function (all, attributes) {
            const stream = getAttribute(attributes, 'stream');
            return '<div class="sms mceNonEditable" data-stream="' + stream + '">' +
                '<div style="border: 1px solid #e5e5e5; background: #f7f7f7; width: 100%; color: #444; text-align: center; padding: 25px; font-family: courier;">' +
                '<img src="/wp-content/plugins/socialmediastream/assets/logo.svg" alt="socialmediastream" style="width: 300px;">' +
                '<br>' +
                'stream: ' + stream + '' +
                '</div>' +
                '</div>';
        });
    }

    function restore(content) {
        return content.replace(/<div class="sms mceNonEditable" data-stream="([a-z0-9-]+)">(?:.*?)<\/div><\/div>/gm, function (all, stream) {
            if (stream) {
                return '[socialmediastream stream="' + stream + '"]';
            }
            return stream;
        });
    }
})();
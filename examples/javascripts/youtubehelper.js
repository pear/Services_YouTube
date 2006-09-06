var helper;
var YouTubeCalls = Class.create();
YouTubeCalls.prototype = {
    initialize: function(container) {
        this.container = container;
        this.call_id = 'call';
        this.calls = [
            {'value': 'listFeatured',       'label': 'Lists the most recent 25 videos that have been featured', 'selected': 'selected'},
            {'value': 'listFavoriteVideos', 'label': 'Lists a user\'s favorite videos.'},
            {'value': 'listByUser',         'label': 'Lists all videos that were uploaded by the specified user.'},
            {'value': 'listByTag',          'label': 'Lists all videos that have the specified tag.'}
        ];
        this.create();
    },

    create: function() {
        var calls = new Array();

        calls.push('<select id="' + this.call_id + '" name="call" size="' + this.calls.length + '">');
        this.calls.each(function(call) {
            if (call.selected != undefined) {
                calls.push('<option value="' + call.value + '" selected="selected"">' + call.label + '</option>');
            } else {
                calls.push('<option value="' + call.value + '">' + call.label + '</option>');
            }
        }.bindAsEventListener(this));
        calls.push('</select>');

        new Insertion.Top($('calls'), calls.join("\n"));

    },

    observe: function(youtubeArguments) {
        Event.observe($(this.call_id), 'change', function() {
            var call = $F(this.call_id);
            youtubeArguments.clear();
            if (call == 'listFavoriteVideos' || call == 'listByUser') {
                youtubeArguments.create('user');
            }
            if (call == 'listByTag') {
                youtubeArguments.create('tag');
            }
        }.bindAsEventListener(this));

    }
}

var YouTubeArguments = Class.create();
YouTubeArguments.prototype = {
    initialize: function(container) {
        this.container = container;
        this.userArgument =
            {'name': 'user',    'size': '10', 'maxlength': '64', 'label': 'User:', 'value': 'youtube'};

        this.tagArguments = [
            {'name': 'tag',    'size': '10', 'maxlength': '64', 'label': 'Tag:', 'value': 'youtube'},
            {'name': 'perPage', 'size': '10', 'maxlength': '64', 'label': 'Per Page:', 'value': '25'}
        ];

    },
     create: function(type) {
        var html = new Array();
        if (type == 'user') {
            html.push('<label>' + this.userArgument.label + '<input type="test" name="' + this.userArgument.name + '" value="' + this.userArgument.value + '" size="' + this.userArgument.size + '" maxlength="' + this.userArgument.maxlength + '" /></label>');
        } else {
            this.tagArguments.each(function(argument) {
                html.push('<label>' + argument.label + '<input type="test" name="' + argument.name + '" value="' + argument.value + '" size="' + argument.size + '" maxlength="' + argument.maxlength + '" /></label>');
            }.bindAsEventListener(this));
        }
        html.push('</div>');
        new Insertion.Top($(this.container), html.join("\n"));
     },

     clear: function() {
        $(this.container).innerHTML = '';
     }
}

var YouTubeOption = Class.create();
YouTubeOption.prototype = {
    initialize: function(container) {
        this.container = container;
        this.option_id = 'option_id';

        this.y_options = [
//            {'value': 'id'             },
//            {'value': 'title'          },
//            {'value': 'url'            },
//            {'value': 'thumbnail_url'  },
            {'value': 'author'         },
            {'value': 'tag'            },
            {'value': 'length_seconds' },
            {'value': 'rating_avg'     },
            {'value': 'rating_count'   },
            {'value': 'description'    },
            {'value': 'view_count'     },
            {'value': 'upload_time'    }
        ];
    },
    load: function() {
        var options = new Array();
        var label;
        var selected;

        options.push('<select id="' + this.option_id + '" name="optional[]" size="' + this.y_options.length + '" multiple="multiple">');
        this.y_options.each(function(option) {
            label =  (option.selected == undefined) ? option.value : undefined;

            if (option.selected == undefined) {
                options.push('<option value="' + option.value + '" selected="selected">' + label + '</option>');
            } else {
                options.push('<option value="' + option.value + '">' + label + '</option>');
            }

        }.bindAsEventListener(this));
        options.push('</select>');

        new Insertion.Top($('options'), options.join("\n"));
    }
}

var YouTubeAPITrigger = Class.create();
YouTubeAPITrigger.prototype = {
    initialize: function(form_id, partial_id) {
        this.form_id = form_id;
        this.partial_id = partial_id;

    },
    load: function(callsContainer, argumentsContainer) {
        this.createPartial();
        this.createTriggerButton(callsContainer);

        var callsObject = new YouTubeCalls(callsContainer);
        var argumentsObject = new YouTubeArguments(argumentsContainer);
        callsObject.observe(argumentsObject);

        this.optionObject = new YouTubeOption('options');
        this.optionObject.load();
    },

    createPartial: function() {
        new Insertion.After($(this.form_id), '<div id="' + this.partial_id + '"></div>');
        new Insertion.Bottom($(this.form_id), '<input type="hidden" name="partial" value="true" />');
    },

    createTriggerButton: function(call) {
        new Insertion.Bottom($(this.form_id), '<a href="javascript:helper.update()">Get YouTube API</a>');
    },

    update: function() {
        new Ajax.Updater(this.partial_id, 'test.php', {
            method: 'get', parameters: Form.serialize($(this.form_id))
        });
    }
}


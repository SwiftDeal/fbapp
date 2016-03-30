$(function() {
    $('#side-menu').metisMenu();
});

$(function() {
    $('select[value]').each(function() {
        $(this).val(this.getAttribute("value"));
    });
});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1)
            height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }
});

function today() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }

    today = yyyy + '-' + mm + '-' + dd;
    return today;
}

function readImage() {
    if (this.files && this.files[0]) {
        var FR = new FileReader();
        FR.onload = function(e) {
            $('#img').attr("src", e.target.result);
        };
        FR.readAsDataURL(this.files[0]);
    }
}

$(document).ready(function() {

    //initialize beautiful datetime picker
    $("input[type=date]").datepicker();
    $("input[type=date]").datepicker("option", "dateFormat", "yy-mm-dd");
    var dateFormat = $("input[type=date]").datepicker("option", "dateFormat");
    $("input[type=date]").datepicker("option", "dateFormat", "yy-mm-dd");

    $('#created_stats').submit(function(e) {
        $('#stats').html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-5x"></i></p>');
        e.preventDefault();
        var data = $(this).serializeArray();
        Request.get({
            action: "admin/dataAnalysis",
            data: data,
        }, function(data) {
            $('#stats').html('');
            if (data.data) {
                Morris.Bar({
                    element: 'stats',
                    data: toArray(data.data),
                    xkey: 'y',
                    ykeys: ['a'],
                    labels: ['Total']
                });
            }
        });
    });

    $("#searchModel").change(function() {
        var self = $(this);
        $('#searchField').html('');
        Request.get({
            action: "admin/fields/" + this.value,
        }, function(data) {
            var d = $.parseJSON(data);
            $.each(d, function(field, property) {
                $('#searchField').append('<option value="' + field + '">' + field + '</option>');
            });
        });
    });

    $(document).on('change', '#searchField', function(event) {
        var fields = ["created", "modified"],
            date = $.inArray(this.value, fields);
        if (date !== -1) {
            $("input[name=value]").val('');
            $("input[name=value]").datepicker();
            $("input[name=value]").datepicker("option", "dateFormat", "yy-mm-dd");
        };
    });

});

(function(window, $) {
    var Img = (function() {
        function Img() {
            this.types = ['src', 'txt', 'usr', 'utxt'];
            this.el = $('#img');
        }

        Img.prototype = {
            _cords: function(event) { // find coordinates of user click
                var offset = this.el.offset(),
                    x = event.pageX - offset.left,
                    y = event.pageY - offset.top;

                return { x: x, y: y };
            },
            _src: function(type, axes, r) {
                if (!r) {
                    $('input[name=' + type + '_x]').val(axes.x);
                    $('input[name=' + type + '_y]').val(axes.y);
                } else {
                    return {
                        w: axes.x - $('input[name=' + type + '_x]').val(),
                        h: axes.y - $('input[name=' + type + '_y]').val()
                    };
                }
            },
            process: function(opts) {
                var axes = this._cords(opts.event),
                    el = null,
                    self = this;

                self.types.forEach(function(type) {
                    el = $('input[value=' + type + ']');

                    if (el.is(':checked')) {
                        if (el.data('calculate') === 'calculate') {
                            var coords = self._src(type, axes, true);
                            $('input[name=' + type + '_w]').val(coords.w);
                            $('input[name=' + type + '_h]').val(coords.h);
                        }

                        if (typeof el.data('calculate') === "undefined") {
                            self._src(type, axes);
                            el.data('calculate', 'calculate');
                        }
                    } else {
                        el.removeData('calculate');
                    }
                });
            }
        }

        return Img;
    }());

    window.Img = new Img();
}(window, jQuery));

$('#img').click(function(e) {
    Img.process({ event: e });
});


(function($) {

    var getDayString = function(dayNumber) {
        switch (dayNumber) {
            case 0:
                return '日';
            case 1:
                return '月';
            case 2:
                return '火';
            case 3:
                return '水';
            case 4:
                return '木';
            case 5:
                return '金';
            case 6:
                return '土';
        }
    };

    var formatDigitHasZeroWidth = function(input, width) {
        var inputWidth = input.toString().length;
        var output = input.toString();
        if (inputWidth < width) {
            for (i=0; i<width-inputWidth; i++) {
                output = '0' + output;
            }
        }
        return output;
    }

    var formatDate = function(date) {
        return (
            date.getFullYear()
            + '-' + formatDigitHasZeroWidth(date.getMonth(), 2)
            + '-' + formatDigitHasZeroWidth(date.getDate(), 2)
            + ' ' + formatDigitHasZeroWidth(date.getHours(), 2)
            + ':' + formatDigitHasZeroWidth(date.getMinutes(), 2)
        );
    }

    var reset = function() {
        $('.notificationLink').removeClass('active');
        $('#eventTitle').text('');
        $('#eventDate').text('');
        $('#eventGaroonLink').attr('href', '');
        $('#eventFacility').text('');
        $('#eventMembers').text('');
        $('#eventDescription').text('');
        $('#eventFollow').text('');
    };

    var init = function() {
//        $('#event').width($('.eventList').width());


        reset();
    };

    var openEvent = function(event, element) {
        var $element = $(element);

        $element.addClass('active');

        if (event.pageX < window.innerWidth / 2) {
            $('#event').css({left: '', right: 0});
        } else {
            $('#event').css({left: 0, right: ''});
        }

        $('#event').show();
        $('#eventTitle').text('読み込み中...');

        $.getJSON('/notification/event/' + $element.data('itemId'), function(json) {
            var scheduleEvent = json.event;
            var facility = json.facility;
            var members = json.members;
            $('#eventTitle').text(scheduleEvent.title);

            $('#eventDate').text((function(scheduleEvent) {
                var dateString;
                if (scheduleEvent.repeatInfo) {
                    switch (scheduleEvent.repeatInfo.condition.type) {
                        case 'week':
                            dateString =
                                scheduleEvent.repeatInfo.condition.start_date
                                    + ' 〜 ' + scheduleEvent.repeatInfo.condition.end_date
                                    + ' の毎週 ' + getDayString(scheduleEvent.repeatInfo.condition.week)
                                    + ' 曜日 ' + scheduleEvent.repeatInfo.condition.start_time
                                    + ' 〜 ' + scheduleEvent.repeatInfo.condition.end_time;
                            break;
                        default:
                            dateString = '未定義';
                    }
                } else if (scheduleEvent.dateStart) {
                    dateString = "開始:" + scheduleEvent.dateStart + " 終了:" + scheduleEvent.dateEnd;
                } else {
                    // todo dateFormatting
                    var start = new Date(scheduleEvent.dateTimeStart * 1000);
                    var end = new Date(scheduleEvent.dateTimeEnd * 1000);
                    dateString = "開始:" + formatDate(start) + " 終了:" + formatDate(end);
                }
                return dateString;
            })(scheduleEvent));

            $('#eventGaroonLink').attr('href', $(element).attr('href'));

            if (facility) {
                $('#eventFacilify').text(facility.name);
            }

            var membersHtml = '';
            $(members).each(function() {
                membersHtml += '<li class="userName">' + this.name + '</li> ';
            });
            $('#eventMembers').html(membersHtml);

            $('#eventDescription').text(scheduleEvent.description);

            $(scheduleEvent.follows).each(function() {
                var $follow = $('<li>');
                $follow.append('<span class="userName">' + this.creatorUserName + '</span>');
                $follow.append('<span>' + formatDate(new Date(this.date * 1000)) + '</span>');
                $follow.append('<pre>' + this.text + '</pre>');
                $('#eventFollow').append($follow);
            });
        });
    }

    var closeEvent = function() {
        $('#event').hide();
        $('.notificationLink.active').removeClass('active');
    }

    $(document).ready(function() {

        init();

        $('.notificationLink').click(function(event) {
            if ($(this).hasClass('active')) {
                // active
                closeEvent();
            } else {
                // no active
                reset();
                openEvent(event, this);
            }
            return false;
        });

        $('#eventClose').click(function() {
            closeEvent();
            return false;
        });

        $('.allRead').click(function() {
            var $this = $(this);
            $this.attr('disabled', 'disabled');
            var items = $this.parents('.eventList').find('.notificationLink');
            var itemIds = [];
            $(items).each(function() {
                itemIds.push($(this).data('moduleId') + ':' + $(this).data('itemId'));
            });

            $this.text('更新中...');

            $.ajax({
                type: 'post',
                url: '/notification/confirm_multi/',
                    data: {
                    items: itemIds.join()
                },
                success: function() {
                    location.reload();
                }
            });
        });
    });

})(jQuery);

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
        $('#eventFacility').text('');
        $('#eventMembers').text('');
        $('#eventDescription').text('');
        $('#eventFollow').text('');
    };

    var init = function() {
//        $('#event').width($('.eventList').width());


        reset();
    };

    $(document).ready(function() {

        init();

        $('.notificationLink').click(function(event) {
            var $this = $(this);

            reset();
            $this.addClass('active');

            if (event.pageX < window.scrollX / 2) {
                $('#event').css({left: 0, right: ''});
            } else {
                $('#event').css({left: '', right: 0});
            }

            $('#eventTitle').text('読み込み中...');
            $.getJSON('/notification/event/' + $this.data('itemId'), function(json) {
                var event = json.event;
                var facility = json.facility;
                var members = json.members;
                $('#event').show();
                $('#eventTitle').text(event.title);

                $('#eventDate').text((function(event) {
                    var dateString;
                    if (event.repeatInfo) {
                        switch (event.repeatInfo.condition.type) {
                            case 'week':
                                dateString =
                                    event.repeatInfo.condition.start_date
                                    + ' 〜 ' + event.repeatInfo.condition.end_date
                                    + ' の毎週 ' + getDayString(event.repeatInfo.condition.week)
                                    + ' 曜日 ' + event.repeatInfo.condition.start_time
                                    + ' 〜 ' + event.repeatInfo.condition.end_time;
                                break;
                            default:
                                dateString = '未定義';
                        }
                    } else if (event.allDay) {
                        dateString = "開始:" + event.dateStart + " 終了:" + event.dateEnd;
                    } else {
                        // todo dateFormatting
                        var start = new Date(event.dateTimeStart * 1000);
                        var end = new Date(event.dateTimeEnd * 1000);
                        dateString = "開始:" + formatDate(start) + " 終了:" + formatDate(end);
                    }
                    return dateString;
                })(event));

                if (facility) {
                    $('#eventFacilify').text(facility.name);
                }

                var membersHtml = '';
                $(members).each(function() {
                    membersHtml += '<li>' + this.name + '</li> ';
                });
                $('#eventMembers').html(membersHtml);

                $('#eventDescription').text(event.description);
                $('#eventFollow').text(event.follows);
            });
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

        $('#eventClose').click(function() {
            $('#event').hide();
        });
    });

})(jQuery);
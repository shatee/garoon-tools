/**
 * Created by shate on 2014/08/28.
 */

(function($) {

    var open = function(target) {
        $(target).removeClass('closed').addClass('opened').text('▼');
        $(target).parents('li:first').children('ul').show();
    };
    var close = function(target) {
        $(target).removeClass('opened').addClass('closed').text('▶');
        $(target).parents('li:first').children('ul').hide();
    }

    $('document').ready(function() {

        // 開閉
        $('.open-close').click(function(e) {
            if ($(this).hasClass('closed')) {
                open(this);
            } else {
                close(this);
            }
        });
        $('.all-open').click(function() {
            $('.open-close').each(function() { open(this); });
        });
        $('.all-close').click(function() {
            $('.open-close').each(function() { close(this); });
        });

        // チェックボックス
        //  以下の階層になっている想定
        //   ul
        //    li 組織(∞)
        //     b > input[type=checkbox] 組織名(1)
        //     ul
        //      li ユーザー(∞)
        //      li 組織(∞)
        //       b > (ry
        $('input[type=checkbox]').change(function(e) {
            if ($(this).hasClass('org')) {
                var $parentLi = $(this).parents('li:first');
                if ($(this).prop('checked')) {
                    // 組織のチェックボックスがonになったら、子要素すべてのチェックボックスをonにする
                    $parentLi.find('input[type=checkbox]').prop('checked', true);
                } else {
                    // 組織のチェックボックスがoffになったら、子要素すべてのチェックボックスをoffにする
                    $parentLi.find('input[type=checkbox]').prop('checked', false);
                }
            }
            // チェックされた親階層を辿っていき、それぞれ子階層のメンバーがすべて選択されている場合はチェックを入れ、それ以外は外す
            $(this).parents('li').each(function() {
                $(this).find('input.org:first')
                    .prop('checked', $(this).find('input.member:not(:checked)').length === 0);
            });
        });

        // お気に入り

    });

})(jQuery);

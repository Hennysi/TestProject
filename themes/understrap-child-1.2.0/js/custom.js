(function ($) {
    $('#ERE_filtering').on('submit', (e) => {
        e.preventDefault()
        let formData = $('#ERE_filtering').serialize() + '&action=estate_filter';

        $.ajax({
            url: ajax.url,
            type: 'POST',
            data: formData,
            beforeSend: function () {
                $('.estate-wrapper').prepend(`
                 <div class="letter-holder">
                      <div class="l-1 letter">L</div>
                      <div class="l-2 letter">o</div>
                      <div class="l-3 letter">a</div>
                      <div class="l-4 letter">d</div>
                      <div class="l-5 letter">i</div>
                      <div class="l-6 letter">n</div>
                      <div class="l-7 letter">g</div>
                      <div class="l-8 letter">.</div>
                      <div class="l-9 letter">.</div>
                      <div class="l-10 letter">.</div>
                 </div>
                `)
            },
            success: function (res) {
                if (res.pages === 1) {
                    $('.pagination').hide()
                } else {
                    $('.pagination').show().html(res.pagination)
                }

                $('.estate-wrapper').html(res.result)
            }
        })
    })

    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault()
        let page = $(this).data('page');
        let data = 'paged=' + page + '&action=estate_filter'
        let $this = $(this);

        $.ajax({
            url: ajax.url,
            type: 'POST',
            data: data,
            beforeSend: function () {
                $('.estate-wrapper').prepend(`
                 <div class="letter-holder">
                      <div class="l-1 letter">L</div>
                      <div class="l-2 letter">o</div>
                      <div class="l-3 letter">a</div>
                      <div class="l-4 letter">d</div>
                      <div class="l-5 letter">i</div>
                      <div class="l-6 letter">n</div>
                      <div class="l-7 letter">g</div>
                      <div class="l-8 letter">.</div>
                      <div class="l-9 letter">.</div>
                      <div class="l-10 letter">.</div>
                 </div>
                `)
            },
            success: function (res) {
                $('.pagination a.active').removeClass('active')
                $this.addClass('active')
                $('.estate-wrapper').html(res.result)
            }
        })
    })
})(jQuery)
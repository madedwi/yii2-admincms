String.prototype.slugify = function () {
    return this.toLowerCase()
        .replace(/[^\w ]+/g,'')
        .replace(/ +/g,'-')
        ;
};
(function($){
    if(typeof $.summernote == 'undefined'){
        console.error('Summernote not loaded!!');
    }else{

        $.fn.editor = function(options, data, closure){
            var def = {
                height: 600,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['paragraph', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video', 'table']],
                    ['misc', ['undo', 'redo', 'codeview']]
                ],
                mediaManager : null
            },
            summerConfig;

            summerConfig = $.extend(def, options);
            var MediaManager = function (context) {
               var ui = $.summernote.ui;
               // create button
               var button = ui.button({
                   contents: '<i class="fa fa-image"/>',
                   tooltip: 'image',
                   click: function () {
                       // invoke insertText method with 'hello' on editor module.
                       summerConfig.mediaManager.open(function(image){
                           context.invoke('editor.insertImage', image.fileurl);
                       });
                   }
               });

               return button.render();   // return button as jquery object
           };


            if(summerConfig.mediaManager!==null){
                var index = summerConfig.toolbar[3][1].indexOf('picture');
                if( index !== -1 ){
                    summerConfig.toolbar[3][1][index] = 'mediaManager';
                }
                summerConfig.buttons = {
                    mediaManager : MediaManager
                };
            }
            return this.summernote(summerConfig);
        };
    }


    $(document).on('click', '.slug-click', function(e){
        let trgElm = $(this).data('input');
        if($(trgElm).length>0){
            $(trgElm).removeClass('hidden').addClass('input-sm').css('max-width', '150px');
            $(this).addClass('hidden');
        }

    });

    $(document).on('blur', '.slug-input', function(e){
        $(this).parent().find('.slug-click').html($(this).val());
        $(this).parent().find('.slug-click').removeClass('hidden');
        $(this).addClass('hidden');
        let slug = [];
        $('.slug-segment').each(function(i, elm){
            slug.push($(elm).html());
        });
        if($(document).find('#temp-cont').length > 0){
            console.log('xxx');
            $(document).find('#temp-cont').html($('#full-slug-text').html());
        }else{
            console.log('yyy');
            $('body').append('<p id="temp-cont">'+$('#full-slug-text').html()+'</p>');
        }
        $(document).find("#temp-cont #slug-category").remove();

        let x = $(document).find("#temp-cont").html();
        $('#formated-slug').val(x);
        $('.full-input-slug').val($(this).val()); //(slug.join('/'));
    }).on('change', '#slug-category', function(e){
        $('.slug-input').trigger('blur');
    });

    $(document).on('change', '.slug-source', function(e){

        if($(this).data('createslug')){
            let slug = this.value.slugify(),
                targetSpan = $(this).data('slugspan');
            $(targetSpan).find('.slug-click').html(slug);
            $(targetSpan).find('input').val(slug);
            $(document).find('.slug-input').trigger('blur');
        }
    });
})(jQuery)

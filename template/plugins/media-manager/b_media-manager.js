// media manager init ----------------------------------------------------------

var MediaManager = function (options){
    let def = {
                    baseUrl         : '',
                    urlToFileLoader    : '',
                    loadPath        : '',
                    uploadUrl       : '',
                    onSelect        : function(file_id, file_url){},
                },
        param = $.extend(def, options);

    var mM = this;
    var imageTemplate, imgHtml, imgUrl;
    var detailForm;

    function ajax(setttings){
        var def = {
            url : param.urlToFileLoader,
            type : 'GET',
            dataType : 'json',
            data : {},
            beforeSend : function(){
                $('body #sp-mediamanager #loader').removeClass('hidden');
            },
            error : function(jqxhr, statusText, errorThrown){
                console.error(jqxhr.statusCode +' : '+errorThrown);
            }
        },
        _sets = $.extend(def, setttings);

        $.ajax(_sets).always(function(){
            $('body #sp-mediamanager #loader').addClass('hidden');
        });
    }

    function request(data, success){
        ajax({
            type : 'GET',
            dataType : 'JSON',
            data : data,
            success : success
        });
    }

    function post(data, success){
        ajax({
            type : 'POST',
            dataType : 'JSON',
            data : data,
            success : success
        });
    }

    function init(){
        var header  = '<div class="modal-header"><h4 class="modal-title">Media Manager</h4></div>',
            detailFormImage =   '<div class="clearfix">'+
                                    '<form id="sp-mediamanager-detail">'+
                                        '<div class="form-group"><div class="thumbnail"><img src="" class="img-responsive hidden" alt=" "></div></div>'+
                                        '<div class="form-group hidden">'+
                                            '<input type="text" id="id" name="id" class="form-control candisable" placeholder="id" />'+
                                            '<input type="text" id="type" name="type" class="form-control candisable" placeholder="type" />'+
                                            '<input type="text" id="url" name="url" class="form-control candisable" placeholder="type" />'+
                                        '</div>'+

                                        '<div class="form-group"><input type="text" id="filename" name="filename" readonly class="form-control" placeholder="Filename" /></div>'+
                                        '<div class="form-group"><input type="text" id="title" name="title" class="form-control candisable"  placeholder="Title" /></div>'+
                                        '<div class="form-group"><input type="text" id="alt" name="alt" class="form-control candisable"  placeholder="alt" /></div>'+
                                        '<div class="form-group"><textarea id="description" name="description" class="form-control candisable"  placeholder="description"></textarea></div>'+
                                    '</form>'+
                                '</div>',
            body    = '<div class="modal-body"><div class="row"><div class="col-md-8" id="folder-content"></div><div class="col-md-4" id="file-information">'+detailFormImage+'</div></div></div>',
            footer  =   '<div class="modal-footer">'+
                            '<button type="button" class="btn btn-success pull-left">Upload New</button>'+
                            '<button type="button" class="btn btn-link btn-loader hidden pull-left" id="loader"><i class="fa fa-spin fa-spinner"></i> Loading</button>'+

                            '<input type="text" style="width:0px; border:none; height:0px;" id="currentpath" value="'+param.loadPath+'" />'+
                            '<button type="button" class="btn btn-danger" id="sp-btn-back-directory">Back</button>'+
                            '<button type="button" class="btn btn-danger" id="sp-btn-delete">Delete</button>'+
                            '<button type="button" class="btn btn-primary" id="sp-btn-update">Update</button>'+
                            '<button type="button" class="btn btn-info" id="sp-btn-select">Select</button>'+
                            '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                        '</div>',
            content = header + body + footer;


        $('body').append('<div class="modal sp-mediamanager" id="sp-mediamanager"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">'+content+'</div></div></div>');

        imageTemplate = '<div class="col-md-3 thumb-container" fname="{[fname]}">'+
                            '<div class="thumbnail">'+
                            '<img src="{[src_imge]}" type="{[type]}" path="{[path]}" class="img-responsive" alt="{[fname]}">'+
                            '</div>'+
                            '<div class="caption"><p>{[title]}</p></div>'+
                        '</div>';

    }

    $(document).on('show.bs.modal', '#sp-mediamanager', function(e){
        loadContent();

    }).on('click', '.thumb-container', function(e){
        $('.thumb-container').removeClass('selected');
        $(this).addClass('selected');

        var img = $(this).find('img'),
            url = img.attr('src'),
            type = img.attr('type'),
            fname = img.attr('alt');
        $('#sp-mediamanager-detail .thumbnail>img').removeClass('hidden').attr('src', url);
        $('#sp-mediamanager-detail').find('input#filename').val(fname);
        if(type == 'directory'){
            $('#sp-mediamanager-detail').find('.form-control.candisable').val('');
            $('#sp-mediamanager-detail').find('.form-control.candisable').prop('disabled', true);
            $('#sp-mediamanager-detail').find('input#type').val(type);
        }else{
            $('#sp-mediamanager-detail').find('.form-control.candisable').prop('disabled', false);
            request({filename:fname, detail:true}, function(result){
                if(result.status===true){
                    $.each(result.info, function(i, elm){
                        $('#'+i).val(elm);
                    });
                }else{
                    $('#sp-mediamanager-detail').find('.form-control.candisable').val('');
                }
                $('#sp-mediamanager-detail').find('input#url').val(url);

                $('#sp-mediamanager-detail').find('input#type').val(type);
            });
        }

    }).on('dblclick', '.thumb-container', function(e){
        var type = $(this).find('img').attr('type'),
            fname = $(this).attr('fname');
        if(type=='directory'){
            loadContent(param.loadPath +'/'+ fname);
        }
    }).on('submit', '#sp-mediamanager-detail', function(e){
        e.preventDefault();
        var data = $(this).serialize() + '&mode=updatefile';
        post(data, function(result){
            if(result.status === true){
                alert('File Updated.');
            }else{
                alert('Update failed.');
            }

        });
    }).on('click', '#sp-btn-update', function(e){
        var conf = confirm('Update file information?');
        if(conf){
            $('form#sp-mediamanager-detail').submit();
        }
    }).on('click', '#sp-btn-delete', function(e){
        var conf = confirm('Delete file?');
        if(conf){
            var id = $('form#sp-mediamanager-detail').find('input#id').val(),
                path = $('#currentpath').val() + '/' + $('form#sp-mediamanager-detail').find('input#filename').val();

            post({id:id,path:path, mode:'delete'}, function(result){
                if(result.status===true){
                    alert('File deleted.');
                    loadContent();
                }else{
                    alert('Delete file failed.');
                }
            });
        }
    }).on('click', '#sp-btn-back-directory', function(e){
        var path = $('#currentpath').val(),
            subpath;
        path = path.split('/');
        path.pop();
        subpath = path.join('/');
        //subpath = subpath.replace(param.loadPath, '');
        loadContent(subpath);
    });

    function loadContent(sub_folder){
        var curr_path = $('#currentpath').val();
        if(typeof sub_folder=='undefined'){
            sub_folder = curr_path;
        }

        request({loadfolder:1, fname:sub_folder}, function(data){
            if(typeof data.status!='undefined' && data.status===true){
                $('#folder-content').html('');

                if(sub_folder!=param.loadPath){
                    $('#sp-btn-back-directory').show();
                }else{
                    $('#sp-btn-back-directory').hide();
                }

                data.files.forEach(function( value, index){
                    if(value.type=='image'){
                        imgUrl = value.url;
                        imgHtml = imageTemplate.replace('{[src_imge]}', imgUrl);
                        imgHtml = imgHtml.replace('{[type]}', 'image');
                    }else{
                        imgUrl  = value.icon_url;
                        imgHtml = imageTemplate.replace('{[src_imge]}', imgUrl);
                        imgHtml = imgHtml.replace('{[type]}', value.type);
                    }

                    imgHtml = imgHtml.replace('{[path]}', value.path);
                    imgHtml = imgHtml.replace('{[title]}', value.name);
                    imgHtml = imgHtml.replace(/{\[fname\]}/g, value.name);
                    $('#folder-content').append(imgHtml);
                });
                $('#currentpath').val(sub_folder);
            }else{
                console.error('Cannot load file! Check your url load!');
            }
        });
    }

    $(document).on('click', '#sp-btn-select', onSelect);

    function onSelect (e){
        var path = $('#currentpath').val(),
            fileid   = $('#sp-mediamanager-detail').find('input#id').val(),
            filename = $('#sp-mediamanager-detail').find('input#filename').val(),
            type     = $('#sp-mediamanager-detail').find('input#type').val(),
            file_src = $('#sp-mediamanager-detail').find('input#url').val(),
            image  = {
                fullUrl     : file_src,
                id          : fileid,
                name        : filename,
                directory   : path
            };

            if(type=='directory'){
                loadContent(param.loadPath +'/'+ filename);
            }else{

                param.onSelect(image);
                $('#sp-mediamanager').modal('hide');
            }
    }

    mM.open = function(onselect){
        param.onSelect = onselect;
        $('#sp-mediamanager').modal('show');
    };

    if(typeof jQuery == 'undefined'){
        console.error('Media manager require jQuery!');
    }
    else if(typeof $().modal == 'undefined' || typeof $().emulateTransitionEnd == 'undefined'){
        console.error('Media manager require bootstrap!');
    }else{
        init();
        return mM;
    }
};

// end media manager -----------------------------------------------------------

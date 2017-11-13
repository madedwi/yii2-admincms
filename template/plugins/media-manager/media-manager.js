// (function($){

    var MediaManager = function(){
        let mmoptions = {
            controllerUrl : '',
            basePath      : '',
            currentFolder : '',

            onSelect : function(files){ },
            onDelete : function(file_id, file_url){ },
            onUpdate : function(file_id, file_url){ },
            onUpload : function(file){ },
            onLoad   : function(){ }

        };
        this.imageTemplate = '';
        this.folderTemplate = '';
        this.typePdf = '';
        this.typeExcel = '';
        this.typeWords = '';

        let self = this,
            mmevent = {};

        // register event to document;
        // enggak tau juga sih ini bener atau enggak caranya;
        $(document).on('show.bs.modal', '#sp-mediamanager', function(){
            mmevent.request().loadfolder(mmoptions.currentFolder);
        }).on('click', '#sp-upload-new-file', function(e){
            $('#uploadPath').val(mmoptions.currentFolder);
            $('#sp-input-new-files').trigger('click');
        }).on('change', '#sp-input-new-files', function(e){
            let formNewFile = $('form#sp-form-new-files'),
                fromData    = new FormData(formNewFile[0]);

            fromData.append('mode', 'upload-new-file');
            fromData.append('Media[uploadPath]', mmoptions.currentFolder);
            mmevent.request().upload(fromData).done(function(data, message, jqxhr){
                mmevent.request().loadfolder(mmoptions.currentFolder);
            }).fail(function(message){

            });
        }).on('dblclick', '.thumbnail.folder', function(e){
            let path = $(this).attr('path');
            mmevent.request().loadfolder(path);
        }).on('click', '#sp-create-new-folder', function(e){
            let formTemplate =  '<div id="form-new-folder-container">'+
                                    '<form id="sp-form-new-folder">'+
                                        '<div class="form-group">'+
                                            '<input type="text" class="form-control" name="sp-new-folder-name" id="sp-new-folder-name" placeholder="new folder name" />'+
                                        '</div>'+
                                    '</form>'+
                                '</div>';

            $('.modal #folder-content').append(formTemplate);
            $('.modal #folder-content').find('#sp-new-folder-name').focus();
        }).on('submit', '#sp-form-new-folder', function(e){
            e.preventDefault();
            let folderPath = mmoptions.currentFolder +'/' + $('#sp-new-folder-name').val();
            mmevent.request().createFolder(folderPath);
            return false;
        }).on('click', '#sp-btn-back-directory', function(e){
            e.preventDefault();
            if(mmoptions.currentFolder != mmoptions.basePath){
                console.log(mmoptions.currentFolder);
                let folders = mmoptions.currentFolder.split('/'),
                    path = [];

                for (var i = 0; i < folders.length - 1; i++) {
                    path.push(folders[i]);
                }
                mmevent.request().loadfolder(path.join('/'));
            }
        }).on('click', '.thumbnail.image-file', function(e){
            let url = $(this).data('src'),
                showUrl = url.replace('thumb', 'small'),
                fname = $(this).parent().attr('fname'),
                form = $('#sp-mediamanager-detail');

            form.find('.thumbnail>img.img-responsive').attr('src', showUrl).removeClass('hidden');
            mmevent.request().get({
                mode : 'file-detail',
                path : url
            }).done(function(data, message){
                let form = $('#sp-mediamanager-detail');
                $.each(data, function(i, v){
                    form.find('#media-'+i).val(v);
                });
            }).fail(function(message){
                form.find('#media-id').val('');
                form.find('#media-type').val('image');
                form.find('#media-fileurl').val(showUrl.replace('small/', ''));
                form.find('#media-filename').val(fname);
                form.find('#media-title').val(fname);
                form.find('#media-alt').val(fname);
                form.find('#media-description').val('');
                console.error(message);
            });
        }).on('click', '#sp-btn-update', function(e){
            $('#sp-mediamanager-detail').trigger('submit');
        }).on('submit', '#sp-mediamanager-detail', function(e){
            e.preventDefault();
            mmevent.request().post($(this).serialize()+"&mode=update-detail&ref="+$(this).find('#media-id').val())
            .done(function(data, message){
                mmevent.request().loadfolder(mmoptions.currentFolder);
            }).fail(function(message){
                console.error(message);
            });

        }).on('click', '#sp-btn-delete', function(e){
            var c = confirm('Delete file from server?');
            if(c){
                mmevent.request().post({
                    mode : 'delete-file',
                    url : $('#sp-mediamanager-detail').find('#media-fileurl').val()
                }).done(function(data, message){
                    mmevent.request().loadfolder(mmoptions.currentFolder);
                }).fail(function(message){
                    console.error(message);
                });
            }
        }).on('click', '#sp-btn-select', function(e){
            let image = {},
                form = $('#sp-mediamanager-detail'),
                idx ='';

            if(form.find('#media-fileurl').val().length<=0){
                alert("Please select media!");
                return false;
            }

            form.find('.form-control').each(function(i, elm){
                idx = $(elm).attr('id').replace('media-', '');
                image[idx] = $(elm).val();
            });

            mmoptions.onSelect(image);
            $('#sp-mediamanager').modal('hide');

        });
        // END register event to document;

        mmevent.init = function(_options){
            mmoptions = $.extend(true, {}, mmoptions, _options);
            mmoptions.currentFolder = _options.basePath;

            var header  = '<div class="modal-header"><h4 class="modal-title">Media Manager</h4></div>',
                detailFormImage =   '<div class="clearfix">'+
                                        '<form id="sp-mediamanager-detail">'+
                                            '<div class="form-group"><div class="thumbnail"><img src="" class="img-responsive hidden" alt=" "></div></div>'+
                                            '<div class="form-group hidden">'+
                                                '<input type="text" id="media-id" name="Media[id]" class="form-control candisable" placeholder="id" />'+
                                                '<input type="text" id="media-type" name="Media[type]" class="form-control candisable" placeholder="type" />'+
                                                '<input type="text" id="media-fileurl" name="Media[fileurl]" class="form-control candisable" placeholder="type" />'+
                                            '</div>'+

                                            '<div class="form-group"><input type="text" id="media-filename" name="Media[filename]" readonly class="form-control" placeholder="Filename" /></div>'+
                                            '<div class="form-group"><input type="text" id="media-title" name="Media[title]" class="form-control candisable"  placeholder="Title" /></div>'+
                                            '<div class="form-group"><input type="text" id="media-alt" name="Media[alt]" class="form-control candisable"  placeholder="alt" /></div>'+
                                            '<div class="form-group"><textarea id="media-description" name="Media[description]" class="form-control candisable"  placeholder="description"></textarea></div>'+
                                        '</form>'+
                                        '<form style="height:0px;width:0px;" id="sp-form-new-files">'+
                                            '<input type="file" id="sp-input-new-files" name="Media[sourceFiles][]" accept="image/*,video/*,.pdf,.xls,.xlsx,.doc,.docx" multiple style="width:0px; height:0px;"/>' +
                                        '</form>'+
                                    '</div>',
                body    = '<div class="modal-body"><div class="row"><div class="col-md-8" id="folder-content"></div><div class="col-md-4" id="file-information">'+detailFormImage+'</div></div></div>',
                footer  =   '<div class="modal-footer">'+
                                '<button type="button" class="btn btn-warning pull-left" id="sp-btn-back-directory">Back</button>'+
                                '<div class="btn-group pull-left create-btn">'+
                                    '<button type="button" class="btn btn-default pull-left" id="sp-create-new-folder">New Folder</button>'+
                                    '<button type="button" class="btn btn-success pull-left" id="sp-upload-new-file">Upload New</button>'+
                                '</div>'+
                                '<button type="button" class="btn btn-link btn-loader hidden pull-left" id="loader"><i class="fa fa-spin fa-spinner"></i> Loading</button>'+

                                '<input type="text" style="width:0px; border:none; height:0px;" class="currentpath" value="'+mmoptions.currentFolder+'" />'+
                                '<div class="btn-group detail-btn">'+
                                    '<button type="button" class="btn btn-danger" id="sp-btn-delete">Delete</button>'+
                                    '<button type="button" class="btn btn-primary" id="sp-btn-update">Update</button>'+
                                '</div>'+
                                '<div class="btn-group">'+
                                    '<button type="button" class="btn btn-info" id="sp-btn-select">Select</button>'+
                                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                                '</div>'+
                            '</div>',
                content = header + body + footer;


            // kalau belum ada render pada body;
            if($(document).find('#sp-mediamanager.sp-mediamanager').length==0){
                $(document).find('body').append('<div class="modal sp-mediamanager" id="sp-mediamanager"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">'+content+'</div></div></div>');
            }

            self.imageTemplate = '<div class="col-md-3 thumb-container" fname="{[fname]}" >'+
                                '<div class="thumbnail image-file" type="{[type]}" path="{[path]}" data-src="{[src_imge]}">'+
                                '<img src="{[src_imge]}" class="img-responsive" alt="{[fname]}">'+
                                '</div>'+
                                '<div class="caption"><p>{[title]}</p></div>'+
                            '</div>';

            self.folderTemplate = '<div class="col-md-3 thumb-container" >'+
                                '<div class="thumbnail folder" path="{[path]}" >'+
                                '</div>'+
                                '<div class="caption"><p>{[title]}</p></div>'+
                            '</div>';
            return self;

        };

        mmevent.open = function(onSelectCallback){
            mmoptions.onSelect = onSelectCallback;
            $('#sp-mediamanager').modal('show');
        };

        mmevent.request = function(data){
            let req = {};

            function rq(options){
                var deferred = $.Deferred(),
                    params = $.extend({
                                        dataType:'json',
                                        url : mmoptions.controllerUrl
                                    }, options);
                $.ajax(params).done(function( result, textStatus, jqXhr){
                    if(result.status){
                        deferred.resolve(result.data, result.message, jqXhr);
                    }else{
                        deferred.reject(result.message, jqXhr);
                    }
                }).fail(function(jqXhr, st, message){
                    deferred.reject(message, jqXhr);
                });

                return deferred.promise();
            };


            req.get = function(data){
                return rq({
                    method : 'get',
                    data : data
                });
            };

            req.post = function(data){
                return rq({
                    method : 'post',
                    data : data
                });
            };

            req.upload = function(data){
                return rq({
                    method : 'post',
                    data : data,
                    cache: false,
                    contentType: false,
                    processData: false
                });
            };


            req.loadfolder = function(folderpath){
                return req.get({
                    mode : 'loadfolder',
                    folderpath : folderpath
                }).done(function(data, message){
                    $('.modal #folder-content').html('');
                    mmoptions.currentFolder = data.currentfolder;
                    if(mmoptions.currentFolder != mmoptions.basePath){
                        $('#sp-btn-back-directory').removeClass('hidden');
                    }else{
                        $('#sp-btn-back-directory').addClass('hidden');
                    }
                    let toReplace = {
                        '{[fname]}' : '',
                        '{[src_imge]}' : '',
                        '{[type]}' : '',
                        '{[title]}' : '',
                        '{[path]}' : ''
                    },
                        title = '';


                    $.each(data.folders, function(index, file){
                        title = file.name.split('.');
                        toReplace['{[title]}'] = title[0];
                        toReplace['{[path]}'] = file.path;
                        $('.modal #folder-content').append(replaceStrings(self.folderTemplate, toReplace));

                    });
                    $.each(data.files, function(index, file){
                        title = file.name.split('.');
                        toReplace['{[fname]}'] = file.name;
                        toReplace['{[src_imge]}'] = file.url;
                        toReplace['{[type]}'] = file.type;
                        toReplace['{[path]}'] = file.path;
                        toReplace['{[title]}'] = title[0];

                        // console.log();
                        $('.modal #folder-content').append(replaceStrings(self.imageTemplate, toReplace));
                    });

                });
            }

            req.createFolder = function(folderPath){
                if(folderPath.length>0){
                    req.post({
                        mode        : 'create-folder',
                        folderpath  : folderPath
                    }).done(function(data, message){
                        loadfolder(folderPath);
                    }).fail(function(message){
                        alert(message);
                    });
                }
            };

            return req;
        }

        // helpers
        function replaceStrings(string, arrayStringReplace){

            $.each(arrayStringReplace, function(oldstring, newstring){
                do {
                    string = string.replace(oldstring, newstring);
                } while (string.indexOf(oldstring) !== -1);

            });

            return string;
        }

        function folderChanged(currentFolderPath){
            mmoptions.currentFolder = currentFolderPath;
        }

        return mmevent;
    };


// })(jQuery);


var Http = function(_url){

    var self    = this,
        method  = 'post',
        beforeSend = function(){};
    self.options = {
        dataType : 'json',
        data : {}
    };
    this.setResultType = function(resultType){
        self.options.dataType = resultType;
        return self;
    }

    this.beforeRequest = function(fn){
        self.beforeSend = fn;
        return self;
    };

    this.resultJson = function(){
        return xhr();
    };

    this.result = function(dataType){
        self.options.dataType = dataType;
        return xhr();
    };

    this.get = function(data){
        self.options.method = 'GET';
        appendData(data);
        return self;
    };

    this.post = function(data){
        self.options.method = 'POST';
        appendData(data);
        return self;
    };

    function appendData(data){
        var justAppendIt = (typeof self.options.data == 'undefined');

        justAppendIt = (justAppendIt===false) ? (Object.keys(self.options.data).length===0) : justAppendIt;
        justAppendIt = (justAppendIt===false) ? self.options.data.length===0 : justAppendIt;

        if(Object.prototype.toString.call(data) == '[object FormData]'){
            if(Object.prototype.toString.call(self.options.data) == '[object Object]'){
                $.each(self.options.data, function(i,e){
                    if(!data.has(i)){
                        data.append(i, e);
                    }
                });
                self.options.data = data;
            }else if(typeof self.options.data == 'string'){
                let _x = self.options.data.split('&'),
                    _v = '';
                $.each(_x, function(i, v){
                    _v = v.split('=');
                    if(!data.has(_v[0])){
                        data.append(_v[0], _v[1]);
                    }
                });
                self.options.data = data;
            }else {
                data.forEach(function(e, i){
                    if(self.options.data.has(i)){
                        self.options.data.set(i, e);
                    }else{
                        self.options.data.append(i, e);
                    }
                });
            }

        } else if(typeof self.options.data == 'string'){
            if(typeof data == 'object'){
                $.each(data, function(i,e){
                    self.options.data +="&"+i+"="+e;
                });
            }else if(typeof data == 'string'){
                self.options.data +="&"+data;
            }
        } else if(Object.prototype.toString.call(self.options.data) == '[object Object]'){
            if(typeof data == 'object'){
                self.options.data = $.extend(true, {}, self.options.data, data);
            }else if(typeof data == 'string'){
                let _x = data.split('&'),
                    _v = '';
                $.each(_x, function(i, v){
                    _v = v.split('=');
                    self.options.data[_v[0]] = _v[1];
                });
            }
        } else if(Object.prototype.toString.call(self.options.data) == '[object FormData]'){
            if(typeof data == 'object'){
                $.each(data, function(i, e){
                    if(self.options.data.has(i)){
                        self.options.data.set(i, e);
                    }else{
                        self.options.data.append(i, e);
                    }
                });
            }else if(typeof data == 'string'){
                let _x = data.split('&'),
                    _v = '';
                _x.forEach(function(v, i){
                    _v = v.split('=');
                    if(self.options.data.has(_v[0])){
                        self.options.data.set(_v[0], _v[1]);
                    }else{
                        self.options.data.append(_v[0], _v[1]);
                    }
                });
            }
        }else{
            self.options.data = data;
        }
    }

    function useFormData(){
        if(Object.prototype.toString.call(self.options.data) == '[object FormData]'){
            self.options.contentType = false;
            self.options.processData = false;
        }
    }

    function initCsrfParams(){
        if(self.options.method != "GET"){
            if(Object.prototype.toString.call(self.options.data) == '[object FormData]'){
                self.options.data.append(window.yii.getCsrfParam(), window.yii.getCsrfToken());
            }else if(typeof self.options.data == 'object' && Object.keys(self.options.data).length>0){
                self.options.data[window.yii.getCsrfParam()] = window.yii.getCsrfToken();
            }else if(typeof self.options.data == 'string'){
                self.options.data += "&"+window.yii.getCsrfParam()+"="+window.yii.getCsrfToken();
            }else{
                self.options.data = {};
                self.options.data[window.yii.getCsrfParam()] = window.yii.getCsrfToken();
            }
        }
    }

    function xhr(){
        useFormData();
        initCsrfParams();
        let ajaxOptions = $.extend(true, {
            url : _url,
            method : self.method,
            beforeSend : self.beforeSend,
        }, self.options);

        var def = $.Deferred();
        $.ajax(ajaxOptions).done(function(result){
            if(self.options.dataType == 'json'){
                if(result.status){
                    def.resolve(result.data, result.message);
                }else {
                    def.reject(result.message);
                }
            }else{
                def.resolve(result);
            }
            window.yii.setCsrfToken(result.t, result.v);
        }).fail(function(jqxhr, statusText, errorThrown){
            def.reject(statusText+" : "+errorThrown);
        });

        return def.promise();
    };


    return self;

};


function renderAlert(status, message, alertContainer){
    let html = '';
    if(status == 'info'){
        html = '<div class="alert alert-info"><i class="fa fa-info"></i> <span class="alert-text" style="margin-left:5px;">'+message+'</span></div>';
    }else if(status == 'error'){
        html = '<div class="alert alert-danger"><i class="fa fa-ban"></i> <span class="alert-text" style="margin-left:5px;">'+message+'</span></div>';
    }else if(status == 'warning'){
        html = '<div class="alert alert-warning"><i class="fa fa-exclamation"></i> <span class="alert-text" style="margin-left:5px;">'+message+'</span></div>';
    }else {
        html = '<div class="alert alert-success"><i class="fa fa-check"></i> <span class="alert-text" style="margin-left:5px;">'+message+'</span></div>';
    }

    $(alertContainer).html(html);
}

(function(){

    if($('.bulk_action').length){
        $('.bulk_action').on('change', function(e){
            $(this).closest('form#form-bulk').find('#bulk_action').val(this.value);
            $(this).closest('form#form-bulk').submit();
        });
    }

    if($('.select-on-check-all').length){
        $('.select-on-check-all').on('change', function(e){
            $('input[name="bulk_id[]"]').prop('checked', $(this).prop('checked'));
        });
    }

})();

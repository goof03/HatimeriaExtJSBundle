/*
 * Handle common http error codes: 404, 403
 * 
 * @todo 
 * - 500 fatal without output
 */ 
Ext.require(['Ext.direct.*'], function() {
    Ext.Direct.on('event', function(response) {

        // accesible if ext exception is thrown
        var xhr = response.xhr;

        if(xhr) {
            var window = null;
            if(App.Direct.environment == 'dev') {
                window = Ext.create('App.Direct.DevErrorMessage', {html: xhr.responseText});
                window.setTitle("Backend error");
            } else {
                // user friendly window title
                window = new App.Direct.UserErrorMessage();
            }

            window.show();
        }

        // normal response content;
        var result = response.result;

        if(!result) return;

        // only errors are handled
        if (typeof result.success != 'undefined'  && result.success && !result.exception) return;

        switch(result.code)
        {
            case 404:
                if(console) {
                    console.log('404');
                }
                break;
            case 403:
                if(App.Direct.signinUrl) {
                    window.location = App.Direct.signinUrl;
                } else {
                    new App.Direct.UserErrorMessage({html: "Access forbidden error"});
                }
                break;
        }
    })
});

Ext.require(['Ext.form.action.Submit'], function() {
    Ext.form.action.Submit.override({
        getParams: function() {
            var nope = false,
                configParams = this.callParent(),
                fieldParams = this.form.getValues(nope, nope, this.submitEmptyText !== nope)
                fieldparams_new = new Array();

            if(!Ext.isEmpty(this.sfEntity)){
                for(var propertyName in fieldParams){
                    if(propertyName != 'leaf'){
                        fieldparams_new[this.sfEntity + '[' + propertyName  + ']'] = fieldParams[propertyName];
                    }
                }
            }

            fieldParams = fieldparams_new;
            
            return Ext.apply({}, fieldParams, configParams);
        }
    })
 });
    
Ext.ns("App.Direct");

Ext.define("App.Direct.DevErrorMessage", {
    extend: "Ext.Window",
    width: 1000,
    height: 600,
    autoScroll: true,
    bodyStyle: 'background: white; padding: 15px'
});

Ext.define("App.Direct.UserErrorMessage", {
    extend: "Ext.Window",
    title: "Backend Error",
    html: "Try again later",
    width: 200,
    height: 50,
    autoScroll: true,
    bodyStyle: 'background: white; padding: 15px'
});
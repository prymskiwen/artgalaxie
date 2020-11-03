(function ($, scope, undefined) {
    scope.ssItemParserparagraph = scope.ssItemParser.extend({
        parse: function (name, data) {
            var o = this._super(name, data);
            if(name === 'fontsize'){
                if(data != '' && data != 'auto'){
                    o.fontsizer = 'font-size:'+data+'%;';
                }else{
                    o.fontsizer = '';
                }
            }else if(name === 'fontcolor'){
                var _d = data.split('|*|');
                if(parseInt(_d[0])){
                    o.fontcolorr = 'color: #'+_d[1]+';';
                }else{
                    o.fontcolorr = '';
                }
            }else if(name === 'css'){
                o[name+'_esc'] = data.replace(/"/g, '&quot;').replace(/'/g, '&apos;');
            }
            return o;
        }
    });
})(njQuery, window);
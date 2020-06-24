cordova.define("com-sarriaroman-photoviewer.PhotoViewer", function(require, exports, module) { var exec = require('cordova/exec');

exports.show = function(url, title) {
    if( title == undefined ) {
      title = '';
    }

    exec(function(){}, function(){}, "PhotoViewer", "show", [url, title]);
};

});

(function ($) {
    ns.init = function () {
        ns.$ = H5P.jQuery;

        if (H5PIntegration !== undefined && H5PIntegration.editor !== undefined) {
            ns.basePath = H5PIntegration.editor.libraryUrl;
            ns.fileIcon = H5PIntegration.editor.fileIcon;
            ns.ajaxPath = H5PIntegration.editor.ajaxPath;
            ns.filesPath = H5PIntegration.editor.filesPath;
            ns.apiVersion = H5PIntegration.editor.apiVersion;
            // Semantics describing what copyright information can be stored for media.
            ns.copyrightSemantics = H5PIntegration.editor.copyrightSemantics;
            // Required styles and scripts for the editor
            ns.assets = H5PIntegration.editor.assets;
            // Required for assets
            ns.baseUrl = '';
            if (H5PIntegration.editor.nodeVersionId !== undefined) {
                ns.contentId = H5PIntegration.editor.nodeVersionId;
            }

            var h5peditor;
            var $editor = $('#h5p-editor');

            if (h5peditor === undefined) {
                h5peditor = new ns.Editor(0, 0, $editor[0]);
            }
        }
    };


    ns.getAjaxUrl = function (action, parameters) {
        var url = H5PIntegration.editor.ajaxPath + action + '/?';
        var request_params = [];

        if (parameters !== undefined) {
            for (var property in parameters) {
                if (parameters.hasOwnProperty(property)) {
                    request_params.push(encodeURIComponent(property) + "=" + encodeURIComponent(parameters[property]));
                }
            }
        }
        return url + request_params.join('&');
    };



    $(document).ready(ns.init);


})(H5P.jQuery);
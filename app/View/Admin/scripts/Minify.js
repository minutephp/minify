/// <reference path="../../../../../../../public/static/bower_components/minute/_all.d.ts" />
var Admin;
(function (Admin) {
    var MinifyConfigController = (function () {
        function MinifyConfigController($scope, $minute, $ui, $timeout, $http, gettext, gettextCatalog) {
            var _this = this;
            this.$scope = $scope;
            this.$minute = $minute;
            this.$ui = $ui;
            this.$timeout = $timeout;
            this.$http = $http;
            this.gettext = gettext;
            this.gettextCatalog = gettextCatalog;
            this.clearCache = function () {
                var version = _this.$scope.settings.version > 0 ? _this.$scope.settings.version : 0;
                _this.$scope.settings.version = (parseFloat(version.toString()) + 0.01).toFixed(2);
                _this.$scope.config.save(_this.gettext('Cache cleared and settings updated')).then(function () { return _this.$http.get('/admin/minify/truncate'); });
            };
            this.save = function () {
                _this.$scope.config.save(_this.gettext('Minify settings saved successfully'));
            };
            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
            $scope.data = { processors: [], tabs: {} };
            $scope.config = $scope.configs[0] || $scope.configs.create().attr('type', 'minify').attr('data_json', {});
            $scope.settings = $scope.config.attr('data_json');
            $scope.settings.css = angular.isObject($scope.settings.css) ? $scope.settings.css : {};
            $scope.settings.js = angular.isObject($scope.settings.js) ? $scope.settings.js : {};
        }
        return MinifyConfigController;
    }());
    Admin.MinifyConfigController = MinifyConfigController;
    angular.module('minifyConfigApp', ['MinuteFramework', 'AdminApp', 'gettext'])
        .controller('minifyConfigController', ['$scope', '$minute', '$ui', '$timeout', '$http', 'gettext', 'gettextCatalog', MinifyConfigController]);
})(Admin || (Admin = {}));

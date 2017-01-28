/// <reference path="../../../../../../../public/static/bower_components/minute/_all.d.ts" />

module Admin {
    export class MinifyConfigController {
        constructor(public $scope: any, public $minute: any, public $ui: any, public $timeout: ng.ITimeoutService, public $http: ng.IHttpService,
                    public gettext: angular.gettext.gettextFunction, public gettextCatalog: angular.gettext.gettextCatalog) {

            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
            $scope.data = {processors: [], tabs: {}};
            $scope.config = $scope.configs[0] || $scope.configs.create().attr('type', 'minify').attr('data_json', {});
            $scope.settings = $scope.config.attr('data_json');
            $scope.settings.css = angular.isObject($scope.settings.css) ? $scope.settings.css : {};
            $scope.settings.js = angular.isObject($scope.settings.js) ? $scope.settings.js : {};
        }

        clearCache = () => {
            let version = this.$scope.settings.version > 0 ? this.$scope.settings.version : 0;
            this.$scope.settings.version = (parseFloat(version.toString()) + 0.01).toFixed(2);
            this.$scope.config.save(this.gettext('Cache cleared and settings updated')).then(() => this.$http.get('/admin/minify/truncate'));
        };

        save = () => {
            this.$scope.config.save(this.gettext('Minify settings saved successfully'));
        };
    }

    angular.module('minifyConfigApp', ['MinuteFramework', 'AdminApp', 'gettext'])
        .controller('minifyConfigController', ['$scope', '$minute', '$ui', '$timeout', '$http', 'gettext', 'gettextCatalog', MinifyConfigController]);
}

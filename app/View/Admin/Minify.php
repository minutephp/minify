<div class="content-wrapper ng-cloak" ng-app="minifyConfigApp" ng-controller="minifyConfigController as mainCtrl" ng-init="init()">
    <div class="admin-content">
        <section class="content-header">
            <h1>
                <span translate="">Minify settings</span>
            </h1>

            <ol class="breadcrumb">
                <li><a href="" ng-href="/admin"><i class="fa fa-dashboard"></i> <span translate="">Admin</span></a></li>
                <li class="active"><i class="fa fa-cog"></i> <span translate="">Minify settings</span></li>
            </ol>
        </section>

        <section class="content">
            <form class="form-horizontal" name="minifyForm" ng-submit="mainCtrl.save()">
                <div class="box box-{{minifyForm.$valid && 'success' || 'danger'}}">
                    <div class="box-header with-border">
                        <h3 class="box-title"><span translate="">On-the-fly Minifier for Javascript and CSS</span></h3>
                        <sup><span class="label label-warning label-xs" tooltip="this plugin is experimental">alpha</span></sup>

                        <div class="box-tools">
                            <button type="button" class="btn btn-xs btn-danger btn-flat" ng-click="mainCtrl.clearCache()">
                                <i class="fa fa-trash"></i> <span translate="">Clear cache</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="alert alert-warning alert-dismissible" role="alert" ng-show="settings.advanced">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <i class="fa fa-warning"></i> <span translate="">If the website becomes inaccessible due to incorrect advanced
                                settings, please visit </span><code>{{session.site.host}}/admin/minify/reset</code>
                        </div>

                        <p class="help-block"><span translate="">Minifier automatically combines and compresses all your javascript and css (inside "public/static" folder) on the fly. What this means
                                to you is that your site loads faster and consumes less bandwidth.</span></p>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span translate="">CSS:</span></label>
                            <div class="col-sm-9">
                                <label class="checkbox-inline">
                                    <input type="checkbox" ng-model="settings.css.files"> <span translate="">Yes, Minify CSS files</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span translate="">Javascript:</span></label>
                            <div class="col-sm-9">
                                <label class="checkbox-inline">
                                    <input type="checkbox" ng-model="settings.js.files"> <span translate="">Yes, Minify Javascript files</span>
                                </label>
                            </div>
                        </div>

                        <div ng-show="settings.advanced">
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span translate="">JS Minifier:</span></label>
                                <div class="col-sm-9">
                                    <label class="radio-inline">
                                        <input type="radio" ng-model="settings.jsMinifier" ng-value="'false'"> <span translate="">None</span>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" ng-model="settings.jsMinifier" ng-value="'uglifyjs'"> <span translate="">Uglify</span>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" ng-model="settings.jsMinifier" ng-value="'jsMin'"> <span translate="">JsMin</span>
                                    </label>
                                </div>
                            </div>


                            <div class="form-group" ng-show="settings.css.files">
                                <label class="col-sm-3 control-label" for="exclude_css"><span translate="">Exclude CSS assets:</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="exclude_css" placeholder="Regular expression to exclude any CSS assets (optional)" ng-model="settings.css.excludes"
                                           ng-required="false">
                                    <p class="help-block"><span translate="">(regular expression to exclude any CSS assets, e.g. bootstrap\.css|angular\.css|no-minify)</span></p>
                                </div>
                            </div>

                            <div class="form-group" ng-show="settings.js.files && settings.advanced">
                                <label class="col-sm-3 control-label" for="exclude_css"><span translate="">Exclude JS assets:</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="exclude_css" placeholder="Regular expression to exclude any JS assets (optional)" ng-model="settings.js.excludes"
                                           ng-required="false">
                                    <p class="help-block"><span translate="">(regular expression to exclude any JS assets, e.g. angular\.js|\.min\.js)</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer with-border">
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-flat btn-primary">
                                    <span translate="">Update settings</span>
                                    <i class="fa fa-fw fa-angle-right"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group" ng-show="settings.css.files || settings.js.files">
                            <div class="col-sm-offset-3 col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" ng-model="settings.advanced"> <span translate="">Show advanced settings</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>

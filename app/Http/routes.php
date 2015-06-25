<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', 'DashboardController@index');
    Route::get('/timeline', 'DashboardController@timeline');

    Route::get('webhook/{projects}/refresh', 'WebhookController@refresh');

    Route::get('projects/{projects}', 'DeploymentController@project');

    Route::post('projects/{projects}/deploy', [
        'as'   => 'deploy',
        'uses' => 'DeploymentController@deploy',
    ]);

    // Deployment details
    Route::get('deployment/{deployments}', [
        'as'   => 'deployment',
        'uses' => 'DeploymentController@show',
    ]);

    Route::get('log/{log}', 'DeploymentController@log');

    Route::group(['namespace' => 'Resources'], function () {

        Route::post('commands/reorder', 'CommandController@reorder');

        Route::get('projects/{projects}/commands/{step}', [
            'as'   => 'commands',
            'uses' => 'CommandController@listing',
        ]);

        Route::post('servers/reorder', 'ServerController@reorder');
        Route::get('servers/{servers}/test', 'ServerController@test');

        $actions = [
            'only' => ['store', 'update', 'destroy'],
        ];

        Route::resource('servers', 'ServerController', $actions);
        Route::resource('commands', 'CommandController', $actions);
        Route::resource('heartbeats', 'HeartbeatController', $actions);
        Route::resource('notifications', 'NotificationController', $actions);
        Route::resource('shared-files', 'SharedFilesController', $actions);
        Route::resource('project-file', 'ProjectFileController', $actions);
        Route::resource('notify-email', 'NotifyEmailController', $actions);
        Route::resource('check-url', 'CheckUrlController', $actions);

        Route::get('admin/templates/{projects}/commands/{step}', [
            'as'   => 'template.commands',
            'uses' => 'CommandController@listing',
        ]);
    });

    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {

        $actions = [
            'only' => ['index', 'store', 'update', 'destroy'],
        ];

        Route::resource('projects', 'ProjectController', $actions);
        Route::resource('users', 'UserController', $actions);
        Route::resource('groups', 'GroupController', $actions); // FIXME: Add destroy to groups

        Route::resource('templates', 'TemplateController', [
            'only' => ['index', 'store', 'update', 'destroy', 'show'],
        ]);
    });
});

Route::group(['prefix' => 'api', 'namespace' => 'API'], function () {
    $actions = [
        'only' => ['index', 'show', 'store', 'update', 'destroy'],
    ];

    Route::resource('projects', 'ProjectController', $actions);
    Route::resource('groups', 'GroupController', $actions);
    Route::resource('templates', 'TemplateController', $actions);
    Route::resource('users', 'UserController', $actions);
});

// Webhooks
Route::post('deploy/{hash}', [
    'as'   => 'webhook',
    'uses' => 'WebhookController@webhook',
]);

Route::get('cctray.xml', 'DashboardController@cctray');

Route::group(['namespace' => 'Resources'], function () {
    Route::get('heartbeat/{hash}', [
        'as'   => 'heartbeat',
        'uses' => 'HeartbeatController@ping',
    ]);
});

Route::controllers([
    'auth'     => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);

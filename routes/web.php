<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

function domainFrontEnd()
{
    if (request()->getHttpHost() == 'aptviewer.com' || request()->getHttpHost() == 'www.aptviewer.com' || request()->getHttpHost() == 'go.aptviewer.com' || request()->getHttpHost() == 'aptviewer.local' || request()->getHttpHost() == 'www.aptviewer.local' || request()->getHttpHost() == 'go.aptviewer.local') {

        return request()->getHttpHost();
    }
    else return "placeholder.aptviewer.com";
}

function domainBackEnd()
{
    if (request()->getHttpHost() == 'backend.aptviewer.com' || request()->getHttpHost() == 'backend.aptviewer.local') {

        return request()->getHttpHost();
    }
    else return "placeholder.aptviewer.com";
}


Route::domain(domainFrontEnd())->group(function () {
    // auth
	Route::get('/', 'Auth\LoginController@agentLoginForm');
    Route::get('/register', 'Auth\RegisterController@agentRegistrationForm');
    Route::post('/login', 'Auth\LoginController@login')->name('agent.login');
	Route::post('/register', 'Auth\RegisterController@register')->name('register');
    Route::post('logout', 'Auth\LoginController@logout')->name('admin.logout');

    Route::get('/agent/reserve/action', 'Agent\LeadController@action')->name('agent.action');
    
    Route::middleware(['auth', 'agent', 'approved'])->group(function() {
        // dashboard
        Route::get('/agent', 'Agent\AgentController@index')->name('agent');

        // agent checkin/out
        Route::get('/agent/checkin', 'Agent\AgentController@checkin')->name('agent.checkin');
        Route::get('/agent/checkout', 'Agent\AgentController@checkout')->name('agent.checkout');

        //leads
        Route::get('/agent/leads', 'Agent\LeadController@leads')->name('agent.leads');
        Route::get('/agent/lead/view', 'Agent\LeadController@viewLead')->name('agent.lead.view');
        Route::get('/agent/markasunread/{followup_queue}', 'Agent\LeadController@markAsUnRead')->name('agent.lead.markasunread');
        Route::post('/agent/changeup/request', 'Agent\LeadController@requestChangeup')->name('agent.changeup.request');

        Route::get('/apartments', 'Agent\ApartmentController@apartments')->name('apartment');
        Route::get('/apartments/filter', 'Agent\ApartmentController@filterApartment')->name('apartment.filter');

        Route::get('/agent/settings', 'Agent\UserSettingController@index')->name('agent.settings');
        Route::put('/agent/settings', 'Agent\UserSettingController@update')->name('agent.settings.update');

        Route::post('agent/mark/bulk', 'Agent\LeadController@markBulk')->name('agent.lead.mark.bulk');
        Route::post('agent/action/bulk', 'Agent\LeadController@actionBulk')->name('agent.lead.action.bulk');

        Route::get('/agent/teamleader/agents/', 'Agent\TeamLeaderController@agents')->name('agent.teamleader.agents');
        Route::get('/agent/teamleader/approvaldecision/{user}', 'Agent\TeamLeaderController@approvalDecision')->name('agent.teamleader.approval.decision');
        Route::get('/agent/teamleader/viewpassword/{user}', 'Agent\TeamLeaderController@viewPassword')->name('agent.teamleader.password');
    });
});

Route::domain(domainBackEnd())->group(function () {
	Route::get('/', 'Auth\LoginController@adminLoginForm');
	Route::post('/login', 'Auth\LoginController@login')->name('admin.login');

    Route::middleware(['auth', 'admin'])->group(function() {
        // logout
        Route::post('logout', 'Auth\LoginController@logout')->name('admin.logout');

        // dashboard
        Route::get('/admin', 'Admin\AdminController@index')->name('admin');

        // Agent management
        Route::get('/admin/agent', 'Admin\AgentController@index')->name('admin.agents');
        Route::get('/admin/agent/create', 'Admin\AgentController@create')->name('admin.agent.create');
        Route::get('/admin/agent/edit/{id}', 'Admin\AgentController@edit')->name('admin.agent.edit');
        Route::get('/admin/agent/edit/togglelead/{user}', 'Admin\AgentController@togglelead')->name('admin.agent.edit');
        Route::get('/admin/agent/password/edit/{id}', 'Admin\AgentController@passwordedit')->name('admin.agent.passwordedit');
        Route::get('/admin/agent/checkout/{user}', 'Admin\AgentController@checkout')->name('admin.agent.checkout');
        Route::get('/admin/agent/forapproval/', 'Admin\AgentController@forApproval')->name('admin.agent.forapproval');
        Route::get('/admin/agent/{id}', 'Admin\AgentController@show')->name('admin.agent.show');
        Route::get('/admin/agent/throttle/{user}', 'Admin\AgentController@throttle')->name('admin.agent.throttle');
        Route::get('/admin/agent/viewpassword/{user}', 'Admin\AgentController@viewPassword')->name('agent.agent.password');
        Route::get('/admin/agent/approvaldecision/{user}', 'Admin\AgentController@approvalDecision')->name('admin.agent.approval.decision');
        Route::post('/admin/agent/password/update/', 'Admin\AgentController@passwordupdate')->name('admin.agent.passwordupdate');
        Route::post('/admin/agent/update', 'Admin\AgentController@update')->name('admin.agent.update');
        Route::post('/admin/agent/store', 'Admin\AgentController@store')->name('admin.agent.store');
        Route::post('/admin/agent/restore/{user}', 'Admin\AgentController@restore')->name('admin.agent.restore');
        Route::post('/admin/agent/rank/change/{user}', 'Admin\AgentController@changeRank')->name('admin.agent.rank.change');
        Route::post('/admin/agent/followuplimit/change/{user}', 'Admin\AgentController@changeLimit')->name('admin.agent.leadlimit.change');
        Route::delete('/admin/agent/destroy/{user}', 'Admin\AgentController@destroy')->name('admin.agent.destroy');
        Route::post('/admin/agent/setlimittoall', 'Admin\AgentController@setLimitToAll')->name('admin.agent.setlimit.all');

        Route::get('/admin/followup/leads', 'Admin\FollowupEmailController@index')->name('admin.followup.leads');
        Route::get('/admin/leads/{followupEmail}', 'Admin\FollowupEmailController@show')->name('admin.lead.show');
        Route::get('/admin/leads/{followupEmail}/resend/disable', 'Admin\FollowupEmailController@disableResend')->name('admin.lead.resend.disable');
        Route::post('/admin/followup/leads/changerank', 'Admin\FollowupEmailController@changeRank')->name('admin.followup.leads.changerank');
        Route::post('/admin/leads/store', 'Admin\FollowupEmailController@store')->name('admin.lead.store');
        Route::post('/admin/followup/leads/rank/change/{followupEmail}', 'Admin\FollowupEmailController@changeRank')->name('admin.followup.leads.rank.change');

        Route::get('/admin/stats', 'Admin\ReportController@index')->name('admin.stats');
        Route::get('/admin/stats/detail', 'Admin\ReportController@detail')->name('admin.stats.detail');

        Route::get('search', 'Admin\SearchController@index')->name('admin.search');

        Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

        // one time script only
        Route::get('/admin/fixphonenumber', 'Admin\AdminController@fixPhoneNumber')->name('admin.fixphonenumber');
    });
});

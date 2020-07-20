<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Rank;
use App\Models\Role;
use App\Models\AgentRanking;
use App\Models\AgentCheckin;
use App\Models\FollowupQueue;
use App\Models\BankQueue;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\ChangePasswordReqeust;
use App\Http\Requests\User\ChangeLeadLimitRequest;
use App\Http\Requests\User\SetLimitToAllRequest;
use App\Http\Controllers\Controller;
use App\Services\CheckIn\CheckInService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Helper;

class AgentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Dashboard showing the list of agents in the company.
    public function index()
    {
        $agents = User::where('role_id', 2)->where('approved', '1')->get();
        $disabledAgents = User::where('role_id', 2)->where('approved', '1')->onlyTrashed()->get();
        $pendingAgents = User::where('approved', '0')->get();
        
        $gmails = [];
        foreach($agents as $agent) {
            if (trim($agent->email) != 'tedpatriarca+godmode@gmail.com' && trim($agent->email) != '646steve@usqprop.com') {
                $gmails[] = $agent->gmail;
            }
        }
        $gmails = implode(",", $gmails);

        return view('admin.agent.index', compact('agents', 'disabledAgents', 'gmails', 'pendingAgents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Adding a new agent page.
    public function create()
    {
        $ranks = Rank::all();
        return view('admin.agent.create', compact('ranks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    //Process on creating the agent's info.
    public function store(StoreRequest $request)
    {
        $user = new User(
            $request->only(
                [
                    'email',
                    'firstname',
                    'lastname',
                ]
            )
        );
        $user->password = bcrypt($request->password);
        $user->role_id = Role::getRole('agent')->id;
        $user->followup_lead_limit = $request->followup_lead_limit ?? 2;
        $user->gmail = $request->gmail;
        $user->phone = $request->phone;
        $user->save();

        $agentRanking = new AgentRanking();
        $agentRanking->user_id = $user->id;
        $agentRanking->rank_id = $request->rank_id;
        $agentRanking->save(); 

        $request->session()->flash('success', 'Agent was successfully registered!');
        return redirect()->route("admin.agents");
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @param  Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    //Page for viewing the agent's info.
    public function show($id, Request $request)
    {
        $user = User::where('id', $id)->withTrashed()->first();
        $type = $request->get('type') ? $request->get('type') : 'initial';
        $assignedStateId =  Helper::getState('assigned');
        $lastCheckIn = CheckInService::getLastCheckInTime($user);
        $checkinCount = AgentCheckin::where('user_id', $user->id)->count();
        $leadAcceptedCnt = Helper::getTotalAssignedLeadsToday($user);
        $leadDeclinedCnt = Helper::getTotalDeclinedLeadsToday($user);
        $leadExpiredCnt = Helper::getTotalExpiredLeadsToday($user);
        $totalLeads = Helper::getTotalLeadsOverall($user);
        $checkinHistory = AgentCheckin::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(25)->get();
        $classname = Helper::getQueueClassName($type);

        $lastLead = $classname::where('user_id', $user->id)
        ->where('state_id', $assignedStateId)
        ->orderBy('updated_at', 'desc')
        ->first();

        if ($lastLead) {
            $assignedLeads = $classname::where('user_id', $user->id)
            ->where('state_id', $assignedStateId)
            ->where('updated_at', '>=', Carbon::parse($lastLead->updated_at)->addDays(-5))
            ->orderBy('updated_at', 'desc')
            ->get();
        } else {
            $assignedLeads = [];
        }
            
        $last7Days = [];
        $last7DaysData = [];
        for($i = 6; $i >= 0; $i--) {
            $last7Days[] = Carbon::today()->addDays(($i * -1))->format('m-d');
            $last7DaysData[] = Helper::getTotalLeadsToday($user, Carbon::today()->addDays(($i * -1)));
        }

        return view('admin.agent.show', compact('user', 'lastCheckIn', 'checkinCount', 'checkinCount', 
        'leadAcceptedCnt', 'leadDeclinedCnt', 'leadExpiredCnt', 'last7Days', 'last7DaysData', 'totalLeads', 'assignedLeads',
        'checkinHistory', 'type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id - The user id
     * @return \Illuminate\Http\Response
     */

    //Setting Page for updating Agent's info.
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $ranks = Rank::all();

        return view('admin.agent.edit', compact('ranks', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */

    //Update Agent's info process.
    public function update(UpdateRequest $request)
    {
        $user = User::find($request->id);
        $oldLeadLimit = $user->followup_lead_limit;
        $user->lastname = $request->lastname;
        $user->firstname = $request->firstname;
        $user->followup_lead_limit = $request->followup_lead_limit ?? 2;
        $user->gmail = $request->gmail;
        $user->phone = $request->phone;
        $user->email = $request->email;
        if ($request->followup_lead_limit  > $oldLeadLimit) {
            $user->daily_throttle = 0;
        }
        $user->save();
        $user->agent_ranking->rank_id = $request->rank_id;
        $user->agent_ranking->save();

        $request->session()->flash('success', 'Agent was successfully updated!');
        return redirect()->route("admin.agents");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @param  Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    //Delete process of Agent.
    public function destroy(User $user, Request $request)
    {
        $user->delete();
        $request->session()->flash('success', 'Agent was successfully disabled!');
        return redirect()->route("admin.agents");
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  $id
     * @param  Request  $request
     * 
     * @return \Illuminate\Http\Response
     */

    //Settings for agent's disable status.
    public function restore($id, Request $request)
    {
        $user = User::where('id', $id)->onlyTrashed()->first();
        if (!$user) {
            $request->session()->flash('success', 'Agent not found or not disabled!');
            return redirect()->route("admin.agents");
        }
        $user->restore();
        $request->session()->flash('success', 'Agent was successfully enabled!');
        return redirect()->route("admin.agents");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id - The user id
     * @return \Illuminate\Http\Response
     */

    //Changing Agent's password page. (Incase the agent forgot the password and email registered)
    public function passwordedit ($id)
    {
        $user = User::findOrFail($id);
        return view('admin.agent.changepassword', compact('ranks', 'user'));
    }

    /**
     * Update the users password.
     *
     * @param  $id - The user id
     * 
     * @return \Illuminate\Http\Response
     */

    //Process on Changing Agent's password.
    public function passwordupdate (ChangePasswordReqeust $request)
    {
        $user = User::find($request->id);
        $user->password = bcrypt($request->password);
        $user->save();

        $request->session()->flash('success', 'Agent '.$user->firstname.' password was successfully changed!');
        return redirect()->route("admin.agents");
    }

    /**
     * Force checkout the agent.
     *
     * @param  \App\Models\User $user - The user
     * @param  Request $request - The request
     * 
     * @return \Illuminate\Http\Response
     */

    //Check out of Agent at the end of the day.
    public function checkout(User $user, Request $request) 
    {
        $checkOut = new CheckInService($user);

        if ($checkOut->checkOut()) {
            Log::channel('daily_info')->info('Agent ' . $user->email . " SUCCESSFULLY checks out by admin");
            $request->session()->flash('success', 'Agent '.$user->firstname.' was successfully checked out!');
        } else {
            Log::channel('daily_info')->info('Agent ' . $user->email . " FAILED to checks out by admin");
            $request->session()->flash('danger', 'Agent '.$user->firstname.' failed to checked out!');
        }

        return redirect()->route("admin.agents");
    }

    /**
     * Throttle the agent.
     *
     * @param  \App\Models\User $user - The user
     * @param  Request $request - The request
     * 
     * @return \Illuminate\Http\Response
     */

    //Setting if the agent reached it's lead limit.
    public function throttle(User $user, Request $request) 
    {
        $user->daily_throttle = 1;
        $user->save();

        $request->session()->flash('success', 'Agent '.$user->firstname.' will not getting any leads for today!');

        return redirect()->route("admin.agents");
    }

    /**
     * Change rank
     *
     * @param User $user
     * 
     * @return \Illuminate\Http\Response
     */

    //User status on company.
    public function changeRank(User $user)
    {
        if ($user->agent_ranking->rank->rank == 'normal') {
            $proRank = Rank::getRank('pro')->id;
            AgentRanking::where('user_id', $user->id)->update(['rank_id' => $proRank]);
            return response()->json(['rank' => 'pro']);
        } elseif ($user->agent_ranking->rank->rank == 'pro') {
            $normalRank = Rank::getRank('normal')->id;
            AgentRanking::where('user_id', $user->id)->update(['rank_id' => $normalRank]);
            return response()->json(['rank' => 'normal']);
        }
    }

    /**
     * Change the limit
     *
     * @param User $user
     * @param ChangeLeadLimitRequest $request
     * 
     * @return \Illuminate\Http\Response
     */

    //Increasing Agent's limit of receiving and sending leads.
    public function changeLimit(User $user, ChangeLeadLimitRequest $request)
    {
        if ($request->post('offpeak') == 'notoffpeak') {
            if ($request->type=='initial') {
                $user->followup_lead_limit = $request->post('value');
                if ($request->post('value') > $user->followup_lead_limit) {
                    $user->daily_throttle = 0;
                }
            } elseif ($request->type=='banked') {
                $user->bank_lead_limit = $request->post('value');
            }
        }  else if ($request->post('offpeak') == 'offpeak') {
            if ($request->type=='initial') {
                $user->initial_offpeak_limit = $request->post('value');
            } elseif ($request->type=='banked') {
                $user->bank_offpeak_limit = $request->post('value');
            }
        }      
        
        $user->save();
        return response()->json(['value' => $request->value]);
    }

    /**
     * Change the limit to all
     *
     * @param SetLimitToAllRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function setLimitToAll(SetLimitToAllRequest $request)
    {
        $users = User::all();

        foreach($users as $user) {
            if($request->post('type') == 'daily_limit_initial') {
                if ($request->post('limit') > $user->followup_lead_limit) {
                    $user->daily_throttle = 0;
                }
                $user->followup_lead_limit = $request->post('limit');
            }
            if($request->post('type') == 'daily_limit_banked') {
                $user->bank_lead_limit = $request->post('limit');
            }
            if($request->post('type') == 'offpeak_limit_initial') {
                $user->initial_offpeak_limit = $request->post('limit');
            }
            if($request->post('type') == 'offpeak_limit_banked') {
                $user->bank_offpeak_limit = $request->post('limit');
            }
            $user->save();
        }
        
        $request->session()->flash('success', 'Successfully changed the limit of all the agents!');

        return redirect()->route("admin.agents");
    }

    /**
     * Toggle team lead
     * 
     * @param Request $request
     * @param User $user
     * 
     * @return Redirect 
     */
    public function togglelead(Request $request, User $user) 
    {
        if($user->is_lead == 1) {
            $user->is_lead = 0;
            $request->session()->flash('success', 'Agent was no longer a team leader!');
        } elseif($user->is_lead == 0) {
            $user->is_lead = 1;
            $request->session()->flash('success', 'Agent was set as a team leader!');
        }
        $user->save();
        
        return redirect()->route("admin.agents");
    }

    /**
     * List of agents for approval
     * 
     * @return \Illuminate\Http\Response
     */
    public function forapproval() 
    {
        $pendingAgents = User::where('approved', '0')->get();

        return view('admin.agent.forapproval', compact('pendingAgents'));
    }

    /**
     * Take an action for agents for approval / disapproval
     * 
     * @param User $user 
     * @param Request $request
     * 
     * @return Redirect
     */
    public function approvalDecision(Request $request, User $user)
    {
        $action = $request->get('action');

        if ($action == 'approve') {
            $user->approved = 1;
            $user->save();

            $password = str_random(12);
            $user->webmail_password = (encrypt($password));
            $user->save();

            $request->session()->flash('success', "Agent ".$user->email." was successfully approved and usqprop email account has been created with a password: " . $password);
        } elseif ($action == 'reject') {
            $user->approved = -1;
            $user->save();

            $request->session()->flash('success', 'Agent was successfully rejected!');
        }

        
        return redirect()->route("admin.agent.forapproval");
    }

    /**
     * View the agent's password
     * 
     * @param User $user
     * 
     * @return json
     * 
     */
    public function viewPassword(User $user)
    {
        return ['result' => true, 'msg' => 'OK!', 'password' => decrypt($user->webmail_password)];
    }
}

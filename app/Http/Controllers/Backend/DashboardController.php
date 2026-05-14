<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\DateUtility;
use App\Models\Complaint;
use App\Models\Followup;
use App\Models\JobOrder;
use App\Models\Lead;
use App\Models\NewComplaint;
use App\Models\NewQuotation;
use App\Models\Party;
use App\Models\ProformaInvoice;
use App\Models\PurchaseBill;
use App\Models\PurchaseReturn;
use App\Models\SaleBill;
use App\Models\SaleReturn;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends BackendController
{
    public String $routePrefix = "dashboard";

    public function index()
    {
        $auth_user = Auth::user();

        if ($auth_user->isAdmin()) {
            return $this->admin();
        } else {
            return $this->other();
        }
    }

    public function admin()
    {
        $duration_type_list = [
            0 => "Today",
            'last_7_days' => "Last 7 Days",
            'last_15_days' => "Last 15 Days",
            'last_30_days' => "Last 30 Days",
            'last_60_days' => "Last 60 Days",
            'last_90_days' => "Last 90 Days",
            'this_month' => "This Month",
            'this_year' => "This Year",
        ];

        $this->setForView(compact("duration_type_list"));

        return $this->view(__FUNCTION__);
    }

    public function other()
    {
        $auth_user = Auth::user();

        $today = Carbon::today();
        $next7 = Carbon::today()->addDays(7);

        $todayLeads = Lead::where('assigned_user_id', $auth_user['id'])
            ->whereDate('follow_up_date', $today)
            ->where('status', '!=', 'not_interested')
            ->orderBy('follow_up_date', 'desc')->get();

        $missingLeads = Lead::where('assigned_user_id', $auth_user['id'])
            ->whereNotNull('follow_up_date')
            ->whereDate('follow_up_date', '<', $today)
            ->where('status', '!=', 'not_interested')
            ->orderBy('follow_up_date', 'desc')
            ->limit(10)
            ->get();

        $nextDaysLeads = Lead::where('assigned_user_id', $auth_user['id'])
            ->whereNotNull('follow_up_date')
            ->whereBetween('follow_up_date', [$today, $next7])
            ->where('status', '!=', 'not_interested')
            ->orderBy('follow_up_date', 'desc')
            ->get();

        $followtypeList = config('constant.followuptype');
        $statusList = config('constant.status');

        $this->setForView(compact("todayLeads", "missingLeads", "nextDaysLeads", "followtypeList", "statusList"));

        return $this->view(__FUNCTION__);
    }

    public function ajax_admin_role_counters($duration_type)
    {
        $date = date(DateUtility::DATE_FORMAT);
        if ($duration_type == "last_7_days") {
            $date = DateUtility::change($date, -7, DateUtility::DAYS, DateUtility::DATE_FORMAT);
        } else if ($duration_type == "last_15_days") {
            $date = DateUtility::change($date, -15, DateUtility::DAYS, DateUtility::DATE_FORMAT);
        } else if ($duration_type == "last_30_days") {
            $date = DateUtility::change($date, -30, DateUtility::DAYS, DateUtility::DATE_FORMAT);
        } else if ($duration_type == "last_60_days") {
            $date = DateUtility::change($date, -60, DateUtility::DAYS, DateUtility::DATE_FORMAT);
        } else if ($duration_type == "last_90_days") {
            $date = DateUtility::change($date, -90, DateUtility::DAYS, DateUtility::DATE_FORMAT);
        } else if ($duration_type == "this_month") {
            $date = date("Y-m-01");
        } else if ($duration_type == "this_year") {
            $date = date("Y-01-01");
        }

        $lead_counters["Pending"] = Lead::where("status", "pending")->where("date", ">=", $date)->count();
        $lead_counters["Not-interested"] = Lead::where("status", "not_interested")->where("date", ">=", $date)->count();
        $lead_counters["Follow Up"] = Lead::where("status", "follow_up")->where("date", ">=", $date)->count();
        $lead_counters["Mature"] = Lead::where("status", "mature")->where("date", ">=", $date)->count();

        $lead_counters["Hot"] = Lead::where("level", "hot")->where("date", ">=", $date)->count();
        $lead_counters["Cold"] = Lead::where("level", "cold")->where("date", ">=", $date)->count();
        $lead_counters["Warm"] = Lead::where("level", "warm")->where("date", ">=", $date)->count();

        $tLeads = Lead::count();
        $tParties = Party::count();

        $today = Carbon::today();
        $next7 = Carbon::today()->addDays(7);

        $todayLeads = Lead::with('latestFollowUp')
            ->whereHas('latestFollowUp', function ($q) use ($today) {
                $q->whereDate('follow_up_date', $today);
            })
            ->orderByDesc(
                Followup::select('follow_up_date')
                    ->whereColumn('followups.lead_id', 'leads.id')
                    ->latest()
                    ->limit(1)
            )
            ->get();

        $missingLeads = Lead::with('latestFollowUp')
            ->whereHas('latestFollowUp', function ($q) use ($today) {
                $q->whereDate('follow_up_date', '<', $today);
            })
            ->where('status', '!=', 'Closed')
            ->orderByDesc(
                Followup::select('follow_up_date')
                    ->whereColumn('followups.lead_id', 'leads.id')
                    ->latest()
                    ->limit(1)
            )
            ->limit(10)
            ->get();

        $nextDaysLeads = Lead::with('latestFollowUp')
            ->whereHas('latestFollowUp', function ($q) use ($today, $next7) {
                $q->whereBetween('follow_up_date', [$today, $next7]);
            })
            ->orderByDesc(
                Followup::select('follow_up_date')
                    ->whereColumn('followups.lead_id', 'leads.id')
                    ->latest()
                    ->limit(1)
            )
            ->get();

        $followtypeList = config('constant.followuptype');
        $statusList = config('constant.status');

        $this->setForView(compact("lead_counters", "tLeads", "tParties", "todayLeads", "missingLeads", "nextDaysLeads", "followtypeList", "statusList"));

        return $this->view(__FUNCTION__);
    }
    // public String $routePrefix = "dashbaord";

    // public function index()
    // {
    //     $view_name = "admin";

    //     $msg = "Comming Soon";

    //     $this->setForView(compact("view_name", "msg"));

    //     return $this->view($view_name);
    // }
}

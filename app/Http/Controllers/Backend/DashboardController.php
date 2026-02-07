<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\DateUtility;
use App\Models\Complaint;
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

        // dd($auth_user['id']);

         $today = Carbon::today();
        $next7 = Carbon::today()->addDays(7);

        // 1. Today’s Leads
        $todayLeads = Lead::where('follow_up_user_id', $auth_user['id'])
                            ->whereDate('follow_up_date', $today)
                            ->where('status', '!=', 'not_interested')
                            ->orderBy('follow_up_date','desc')->get();

        // 2. Missing Leads (follow-up date < today and still not done)
        $missingLeads = Lead::where('follow_up_user_id', $auth_user['id'])
            ->whereNotNull('follow_up_date')
            ->whereDate('follow_up_date', '<', $today)
            ->where('status', '!=', 'not_interested')
            ->orderBy('follow_up_date','desc')
            ->limit(10)
            ->get();

        // 3. Next 7 days leads
        $nextDaysLeads = Lead::where('follow_up_user_id', $auth_user['id'])
            ->whereNotNull('follow_up_date')
            ->whereBetween('follow_up_date', [$today, $next7])
            ->where('status', '!=', 'not_interested')
            ->orderBy('follow_up_date','desc')
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
        
        // complaints by status
        // $complaint_counters["Pending"] = Complaint::where("status", "pending")->where("date", ">=", $date)->count();
        
        // $complaint_counters["In-Progress"] = Complaint::where("status", "in_progress")->where("date", ">=", $date)->count();
        
        // $complaint_counters["Hold"] = Complaint::where("status", "hold")->where("date", ">=", $date)->count();
        
        // $complaint_counters["Done"] = Complaint::where("status", "done")->where("date", ">=", $date)->count();

        // complaints by level
        // $complaint_counters["Hot"] = Complaint::where("level", "hot")->where("date", ">=", $date)->count();
        
        // $complaint_counters["Cold"] = Complaint::where("level", "cold")->where("date", ">=", $date)->count();
        
        // $complaint_counters["Warm"] = Complaint::where("level", "warm")->where("date", ">=", $date)->count();

        // $tComplaints = Complaint::count();
        // $tPI = ProformaInvoice::count();
        // $tParties = Party::count();
        $complaint_counters = "";
        $tComplaints = "";
        $tPI = "";
        $tParties = "";

        $this->setForView(compact("complaint_counters","tComplaints","tPI","tParties"));

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

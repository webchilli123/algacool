<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\DateUtility;
use App\Models\Item;
use App\Models\Lead;
use App\Models\LeadItem;
use App\Models\Party;
use App\Models\Source;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Imports\LeadsImport;
use App\Models\Followup;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class LeadController extends BackendController
{
     public String $routePrefix = "lead";
    public $modelClass = Lead::class;

    public function index()
    {
        $conditions = $this->_get_conditions(Route::currentRouteName());

        $query = $this->modelClass::where($conditions)
        ->with(["leadItem", "party", "user","assignedUser", "followups" => function ($q){
            $q->orderBy('id', 'desc');
        }])->orderBy('id', 'desc');

        // Add role/user restriction safely
        if (!auth()->user()->roles->contains('name', 'System Admin')) {
            $query->where('assigned_user_id', auth()->id());
        }

        $records = $this->getPaginagteRecords($query, Route::currentRouteName());

        // dd($records);

        $partyList = Party::getListCache();
        $sourceList = Source::pluck('resources', 'id')->toArray();


        $this->setForView(compact("records", "partyList", "sourceList"));

        return $this->viewIndex(__FUNCTION__);
    }

    private function _get_conditions($cahe_key)
    {
        $conditions = $this->getConditions($cahe_key, [
            ['field' => 'is_new', 'type' => 'int'],
            ['field' => 'lead_source_id', 'type' => 'int'],
            ['field' => 'party_id', 'type' => ''],
            ['field' => 'level', 'type' => ''],
            ['field' => 'status', 'type' => ''],
            ['field' => 'follow_up_user_id', 'type' => ''],
            ['field' => 'follow_up_date', 'type' => 'date'],
            ['field' => 'follow_up_type', 'type' => ''],
            ['field' => 'comments', 'type' => 'string'],
            ['field' => 'customer_name', 'type' => 'string'],
        ]);

        return $conditions;
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = new $this->modelClass();
        $model->date = date(DateUtility::DATE_OUT_FORMAT);

        $form = [
            'url' => route($this->routePrefix . '.store'),
            'method' => 'POST',
        ];

        $this->_set_list_for_form($model);

        $sourceList = Source::pluck('resources', 'id')->toArray();

        $this->setForView(compact("model", 'form', 'sourceList'));

        return $this->view("form");
    }

    private function _set_list_for_form($model)
    {
        $conditions = [
            "or_id" => []
        ];

        if ($model && $model->party_id) {
            $conditions["or_id"] = $model->party_id;
        }

        $partyList = Party::getList("id", "name", $conditions);

        $conditions = [
            "or_id" => []
        ];

        if ($model && $model->saleOrderItem) {
            foreach ($model->saleOrderItem as $saleOrderItem) {
                $conditions["or_id"][] = $saleOrderItem->item_id;
            }
        }

        $itemList = Item::getList("id", "name", $conditions);
        $userList = User::getList("id");


        $this->setForView(compact('partyList', 'itemList', 'userList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
       private function _common_validation_rules()
    {
        return [
            'date' => 'required|date',
            'level' => 'required|string',
            'party_id' => 'nullable|integer',
            'is_new' => 'nullable|integer',
            'assigned_user_id' => 'nullable|integer|exists:users,id',
            'customer_name' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'firm_name' => 'nullable|string',
            'customer_number' => 'nullable|numeric',
            'alternate_number' => 'nullable|numeric',
            'customer_website' => 'nullable|string',
            'customer_address' => 'nullable|string',

            'status' => 'nullable|string',
            'lead_source_id' => 'required|string',
            'not_in_interested_reason' => 'nullable|string',

            'follow_up_date' => 'nullable|date',
            'follow_up_type' => 'nullable|string',
            'mature_action_type' => 'nullable|string',
            'comments' => 'nullable|string',

            'is_include_items' => 'nullable|integer',
            'lead_items' => 'nullable|array',
            'lead_items.item_id.*' => 'nullable|integer',
            'lead_items.qty.*' => 'nullable|numeric|min:1',
        ];
    }


    private function _common_validation_messages()
    {
        return [
            'date.required' => 'Lead date is required',
            'date.date' => 'Invalid lead date',

            'level.required' => 'Lead level is required',

            'lead_source_id.required' => 'Lead source is required',

            'customer_email.email' => 'Invalid email address',
            'assigned_user_id.exists' => 'Selected assigned user does not exist',

            'lead_items.qty.*.min' => 'Quantity must be at least 1',
        ];
    }

    public function store(Request $request)
    {
        $this->beforeCreate();

        $rules = $this->_common_validation_rules();
        $messages = $this->_common_validation_messages();

        $validatedData = $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            // Separate lead items
            $leadItems = $validatedData['lead_items'] ?? [];
            unset($validatedData['lead_items']);

            // Extra fields
            $validatedData['follow_up_user_id'] = Auth::id();

            // Create Lead
            $lead = Lead::create($validatedData);

            // Save Lead Items
            if (!empty($leadItems)) {
                foreach ($leadItems['item_id'] as $index => $itemId) {
                    LeadItem::create([
                        'lead_id' => $lead->id,
                        'item_id' => $itemId ?? null,
                        'qty' => $leadItems['qty'][$index] ?? null,
                    ]);
                }
            }

            // Save Follow-up (only if any follow-up data exists)
            if (
                !empty($validatedData['follow_up_date']) ||
                !empty($validatedData['follow_up_type']) ||
                !empty($validatedData['comments'])
            ) {
                Followup::create([
                    'lead_id' => $lead->id,
                    'follow_up_date' => !empty($validatedData['follow_up_date'])
                        ? Carbon::parse($validatedData['follow_up_date'])->format('Y-m-d')
                        : now()->format('Y-m-d'),
                    'follow_up_type' => $validatedData['follow_up_type'] ?? null,
                    'comments' => $validatedData['comments'] ?? null,
                    'follow_up_user_id' => Auth::id(),
                ]);
            }

            $pushService = new PushNotificationService();
            $users = User::all();

            foreach ($users as $user) {
                
                if ($user->isAdmin()) {

                    if (!empty($user->fcm_token)) {
                        $pushService->send(
                            $user->fcm_token,
                            'New Lead 🔥',
                            'New Lead Created Successfully'
                        );
                    }
                }
            }

            DB::commit();

            return back()->with('success', 'Lead created successfully');
        } catch (\Exception $ex) {
            DB::rollBack();

            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = $this->modelClass::with([
            "party",
            "leadItem"
        ])->findOrFail($id);
        // ś
        // dd($model->party->name);

        $form = [
            'url' => route($this->routePrefix . '.update', $id),
            'method' => 'PUT',
        ];

        $lead_items = $model->leadItem->toArray();
        // dd($lead_items);


        $this->_set_list_for_form($model);

        $sourceList = Source::pluck('resources', 'id')->toArray();
        $partyList = Party::getList('id');


        $this->setForView(compact("model", "form", "lead_items", "sourceList", "partyList"));


        return $this->view("form");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    $this->beforeCreate();

    $lead = Lead::findOrFail($id);

    $rules = $this->_common_validation_rules();
    $messages = $this->_common_validation_messages();

    // extra rule only for update
    $rules['status'] = 'required|string';

    $validatedData = $request->validate($rules, $messages);

    DB::beginTransaction();

    try {
        // Separate lead items
        $leadItems = $validatedData['lead_items'] ?? [];
        unset($validatedData['lead_items']);

        // Update Lead
        $lead->update($validatedData);

        /*
        |--------------------------------------------------------------------------
        | Handle Lead Items (Update / Create / Delete)
        |--------------------------------------------------------------------------
        */
        $existingItems = LeadItem::where('lead_id', $lead->id)
            ->get()
            ->keyBy('id');

        if (!empty($leadItems['item_id']) && is_array($leadItems['item_id'])) {
            foreach ($leadItems['item_id'] as $index => $itemId) {

                if (empty($itemId)) {
                    continue;
                }

                $qty = $leadItems['qty'][$index] ?? 0;
                $itemRowId = $leadItems['id'][$index] ?? null;

                if ($itemRowId && isset($existingItems[$itemRowId])) {
                    // Update existing
                    $existingItems[$itemRowId]->update([
                        'item_id' => $itemId,
                        'qty'     => $qty,
                    ]);

                    unset($existingItems[$itemRowId]);
                } else {
                    // Create new
                    LeadItem::create([
                        'lead_id' => $lead->id,
                        'item_id' => $itemId,
                        'qty'     => $qty,
                    ]);
                }
            }
        }

        // Delete removed items
        if ($existingItems->isNotEmpty()) {
            LeadItem::destroy($existingItems->keys());
        }

        /*
        |--------------------------------------------------------------------------
        | Follow-up (Create new entry only)
        |--------------------------------------------------------------------------
        */
        if (
            !empty($validatedData['follow_up_date']) ||
            !empty($validatedData['follow_up_type']) ||
            !empty($validatedData['comments'])
        ) {
            Followup::create([
                'lead_id' => $lead->id,
                'follow_up_date' => !empty($validatedData['follow_up_date'])
                    ? Carbon::parse($validatedData['follow_up_date'])->format('Y-m-d')
                    : now()->format('Y-m-d'),
                'follow_up_type' => $validatedData['follow_up_type'] ?? null,
                'comments' => $validatedData['comments'] ?? null,
                'follow_up_user_id' => Auth::id(),
            ]);
        }

        DB::commit();

        

        return redirect()
            ->route($this->routePrefix . '.index')
            ->with('success', 'Lead updated successfully');

    } catch (\Exception $ex) {

        DB::rollBack();

        return back()->withInput()->with('fail', $ex->getMessage());
    }
}

      public function updateMissed(Request $request)
    {
        $validate_data = $request->validate([
            'id' => 'required|exists:leads,id',
            'status' => 'required|string',
            'follow_up_date' => 'nullable|date',
            'follow_up_time' => 'nullable',
            'follow_up_type' => 'nullable|string',
            'comments' => 'nullable|string',
        ]);

          // Convert Time
        $follow_up_time = !empty($validate_data['follow_up_time'])
            ? Carbon::createFromFormat(
                'h:i A',
                $validate_data['follow_up_time']
            )->format('H:i:s')
            : now()->format('H:i:s');

        $lead = Lead::findOrFail($request->id);

        try {

            $lead->update([
                'status' => $validate_data['status'],
                'follow_up_date' => $validate_data['follow_up_date'] ?? now(),
                'follow_up_time' => $follow_up_time,
                'follow_up_type' => $validate_data['follow_up_type'],
                'comments' => $validate_data['comments'],
            ]);

            if (!empty($validate_data['follow_up_date']) || !empty($validate_data['follow_up_time']) || !empty($validate_data['follow_up_type']) || !empty($validate_data['comments'])) {
                Followup::create([
                    'lead_id' => $lead->id,
                    'follow_up_date' => $validate_data['follow_up_date'] ?? now(),
                    'follow_up_time' => $follow_up_time,
                    'follow_up_type' => $validate_data['follow_up_type'] ?? null,
                    'comments' => $validate_data['comments'] ?? null,
                    'follow_up_user_id' => Auth::id(),
                ]);
            }

            // return redirect()->route($this->routePrefix . ".index")->with('success', 'Lead updated successfully');
            return redirect()->back()->with('success', 'Lead updated successfully!');
        } catch (\Exception $ex) {
            Log::error("Error updating lead follow-up", [
                'lead_id' => $request->id,
                'user_id' => Auth::id(),
                'error' => $ex->getMessage(),
                'input' => $request->all(),
            ]);
            return back()->withInput()->with('fail', $ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $lead = Lead::findOrFail($id);

            $leadItems = LeadItem::where('lead_id', $id);

            if ($leadItems->exists()) {
                $leadItems->delete();
            }

            $lead->delete();

            return back()->with('success', 'Lead deleted successfully');
        } catch (Exception $ex) {
            return back()->with('fail', 'Error: ' . $ex->getMessage());
        }
    }

    protected function beforeViewRender()
    {
        parent::beforeViewRender();

        $levelList = config('constant.level');
        $statusList = config('constant.status');
        $followtypeList = config('constant.followuptype');
        $maturefieldList = config('constant.maturefield');
        $quotationstatusList = config('constant.newquotationstatus');

        $this->setForView(compact(
            'levelList',
            'statusList',
            'followtypeList',
            'maturefieldList',
            'quotationstatusList'
        ));
    }

    // get lead ajax

    public function getLead(Request $request){

        $lead = Lead::with(['leadItem.Item','party'])->where('id', $request->lead_id)->first();

        if (!$lead) {
            return response()->json(['error' => 'No Records Found'], 404);
        }
    
        $leadItems = $lead->leadItem->map(function ($item) {
            return [
                'item_id' => $item->item_id,
                'qty' => $item->qty,
                'item_name' => $item->Item->name ?? '',
            ];
        });
    
        return response()->json([
            'party' => [
                'id' => $lead->party->id,
                'name' => $lead->party->name,
            ],
            'items' => $leadItems,
        ]);

    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        Excel::import(new LeadsImport, $request->file('file'));

        return back()->with('success', 'Leads imported successfully!');
    }
}

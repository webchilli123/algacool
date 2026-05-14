@extends($layout)
@section('content')
    <style>
        .blue {
            background-color: #19bbd2;
            color: #FFF;
        }

        .purple {
            background-color: #8f70e7;
            color: #FFF;
        }

        .magenta {
            background-color: #ae379b;
            color: #FFF;
        }

        .yellow {
            background-color: #fecb4b;
            color: #FFF;
        }


        .counter {
            padding: 0;
            box-shadow: 4px 4px 10px 0px rgba(0, 0, 0, 0.5);
        }

        .counter ol {
            list-style: none;
            padding-left: 1px;
        }

        .counter ol li {}

        .counter .card-title {
            padding: 6px;
            text-align: center;
        }

        .counter .card-body {
            padding: 4px;
        }

        .dashboard-table thead {
            position: sticky;
            background-color: #f2f2f2;
        }

        .dashboard-table tbody {
            height: 200px;
            /* Adjust to your desired height */
            overflow-y: scroll;
            /* Enables vertical scrolling for the tbody */
        }
    </style>

    <div class="row">
        <div class="col-12 mt-4">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>{{ isset($page_title) ? $page_title : 'Please set page_title variable' }}</h4>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Today Leads -->
        <div class="col-xs-12 col-md-12 col-lg-6">
            <h4>Today Leads</h4>
            <table class="table table-striped table-bordered table-hover mb-0 dashboard-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Info</th>
                        <th>Level</th>
                        <th>Comments</th>
                        <th>Follow Up Date-Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($todayLeads as $k => $lead)
                        <tr>
                            <td>{{ $k + 1 }}</td>
                            <td>
                                 @if ($lead->is_new == 0)
                                    Party : {{ $lead->party?->name ?? '-' }}
                                    <br />
                                @else
                                    Name : {{ $lead->customer_name ?? '-' }}
                                    <br />
                                    Contact : {{ $lead->customer_number ?? '-' }}
                                    <br />
                                    Email : {{ $lead->customer_email ?? '-' }}
                                    <br />
                                    Website : {{ $lead->customer_website ?? '-' }}
                                    <br />
                                @endif
                            </td>
                            <td>{{ $lead->level }}</td>

                            <td>{{ $lead->comments }}</td>
                            <td>{{ if_date($lead->follow_up_date) }}<br>
                            {{ $lead->follow_up_time ? date('h:i A', strtotime($lead->follow_up_time)) : '' }}
                            </td>
                             <td>
                       <button type="button" 
                class="btn btn-sm btn-primary updateBtn"
                data-id="{{ $lead->id }}"
                data-status="{{ $lead->status }}"
                data-date="{{ $lead->follow_up_date }}"
                data-time="{{ $lead->follow_up_time }}"
                data-type="{{ $lead->follow_up_type }}">
            <i class="bi-arrow-repeat"></i>
        </button>
         <br><br>
                            <a href="{{ route('lead.edit', $lead->id) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <div class="col-xs-12 col-md-12 col-lg-6">
            <h4>Next 7 Days Leads</h4>
            <table class="table table-striped table-bordered table-hover mb-0 dashboard-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Info</th>
                        <th>Level</th>
                        <th>Comments</th>
                        <th>Follow Up Date-Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nextDaysLeads as $k => $lead)
                        <tr>
                            <td>{{ $k + 1 }}</td>
                            <td>
                                @if ($lead->is_new == 0)
                                    Party : {{ $lead->party?->name ?? '-' }}
                                    <br />
                                @else
                                    Name : {{ $lead->customer_name ?? '-' }}
                                    <br />
                                    Contact : {{ $lead->customer_number ?? '-' }}
                                    <br />
                                    Email : {{ $lead->customer_email ?? '-' }}
                                    <br />
                                    Website : {{ $lead->customer_website ?? '-' }}
                                    <br />
                                @endif
                            </td>
                            <td>{{ $lead->level }}</td>

                            <td>{{ $lead->comments }}</td>
                            <td>{{ if_date($lead->follow_up_date) }}<br>
                            {{ $lead->follow_up_time ? date('h:i A', strtotime($lead->follow_up_time)) : '' }}
                            </td>
                             <td>
                       <button type="button" 
                class="btn btn-sm btn-primary updateBtn"
                data-id="{{ $lead->id }}"
                data-status="{{ $lead->status }}"
                data-date="{{ $lead->follow_up_date }}"
                data-time="{{ $lead->follow_up_time }}"
                data-type="{{ $lead->follow_up_type }}">
            <i class="bi-arrow-repeat"></i>
        </button>
         <br><br>
                            <a href="{{ route('lead.edit', $lead->id) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <!-- Missing Leads -->
    <div class="row mt-3">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <h4>Missing Leads</h4>
            <table class="table table-striped table-bordered table-hover mb-0 dashboard-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer Info</th>
                        <th>Level</th>
                        <th>Comments</th>
                        <th>Follow Up Date-Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($missingLeads as $k => $lead)
                        <tr>
                            <td>{{ $k + 1 }}</td>
                            <td>
                                @if ($lead->is_new == 0)
                                    Party : {{ $lead->party?->name ?? '-' }}
                                    <br />
                                @else
                                    Name : {{ $lead->customer_name ?? '-' }}
                                    <br />
                                    Contact : {{ $lead->customer_number ?? '-' }}
                                    <br />
                                    Email : {{ $lead->customer_email ?? '-' }}
                                    <br />
                                    Website : {{ $lead->customer_website ?? '-' }}
                                    <br />
                                @endif
                            </td>
                            <td>{{ $lead->level }}</td>

                            <td>{{ $lead->comments }}</td>
                            <td>
                                {{ if_date($lead->follow_up_date) }}<br>
{{ $lead->follow_up_time ? date('h:i A', strtotime($lead->follow_up_time)) : '' }}

                                <span class="badge rounded-pill bg-danger text-white">Missed</span>
                            </td>
                            <td>
                       <button type="button" 
                class="btn btn-sm btn-primary updateBtn"
                data-id="{{ $lead->id }}"
                data-status="{{ $lead->status }}"
                data-date="{{ $lead->follow_up_date }}"
                data-time="{{ $lead->follow_up_time }}"
                data-type="{{ $lead->follow_up_type }}">
            <i class="bi-arrow-repeat"></i>
        </button>
         <br><br>
                            <a href="{{ route('lead.edit', $lead->id) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="updateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="updateForm" action="{{ route('lead.updateMissed') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="lead_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Update Follow-Up</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <x-Inputs.drop-down name="status" id="lead_status" label="Missing Follow Up Report"
                                    :list="$statusList" class="form-control select2" :value="old('status', $model->status ?? '')" :mandatory="true" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <x-Inputs.text-field name="follow_up_date" id="lead_date" class="form-control date-picker"
                                    label="Follow Up Date" :value="old('follow_up_date', $model->follow_up_date ?? '')" :mandatory="true" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <x-Inputs.text-field name="follow_up_time" id="lead_time" class="form-control time-picker"
                                    label="Follow Up Time" :value="old('follow_up_time', $model->follow_up_time ?? '')" :mandatory="true" />
                            </div>
                            <div class="col-md-12 mb-3">
                                <x-Inputs.drop-down name="follow_up_type" id="lead_type" label="Follow Up Type"
                                    :list="$followtypeList" :value="old('follow_up_type', $model->follow_up_type ?? '')" class="form-control select2" :mandatory="true" />
                            </div>
                            <div class="col-md-12 mb-3">
                                <x-Inputs.text-area id="lead_comment" name="comments" label="Comments" />
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).on("click", ".updateBtn", function() {
            $("#lead_id").val($(this).data("id"));
            $("#lead_status").val($(this).data("status"));
            $("#lead_date").val($(this).data("date"));
            $("#lead_time").val($(this).data("time"));
            $("#lead_type").val($(this).data("type"));
            $("#lead_comment").val($(this).data("comment"));

            $("#updateModal").modal("show");
        });

        flatpickr(".date-picker", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
        $('.time-picker').flatpickr({
            noCalendar: true,
            enableTime: true,
            dateFormat: 'h:i K'
        });
    </script>
@endsection

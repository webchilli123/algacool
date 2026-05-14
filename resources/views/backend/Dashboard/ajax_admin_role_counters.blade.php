<style>
    .dashboard-card {
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-left: 6px solid transparent;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }

    .dashboard-card .card-icon {
        font-size: 40px;
    }

    .dashboard-card .card-content h6 {
        margin: 0;
        font-size: 14px;
        color: #777;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .dashboard-card .card-content h2 {
        margin: 4px 0;
        font-size: 30px;
        font-weight: 600;
    }

    .dashboard-card .card-content p {
        margin: 0;
        font-size: 13px;
        color: #888;
    }

    /* COLORS */
    .loan-card {
        border-left-color: #6F42C1;
    }

    .partner-card {
        border-left-color: #D63384;
    }

    .staff-card {
        border-left-color: #FFC107;
    }
</style>

<div class="row g-4 mb-4">

    <!-- Leads -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="dashboard-card border-primary">
            <div class="card-icon text-primary">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div class="card-content">
                <h6>Leads</h6>
                <h2>{{ $tLeads }}</h2>
                <p>Total Leads</p>
            </div>
        </div>
    </div>


    <!-- Parties -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="dashboard-card border-success">
            <div class="card-icon text-success">
                <i class="fa-solid fa-building"></i>
            </div>
            <div class="card-content">
                <h6>Parties</h6>
                <h2>{{ $tParties }}</h2>
                <p>Total Parties</p>
            </div>
        </div>
    </div>

</div>

<div class="row">
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
                    <th>Follow Up Date/Satus</th>
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
                        <td>{{ if_date($lead->follow_up_date) }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary updateBtn"
                                data-id="{{ $lead->id }}" data-status="{{ $lead->status }}"
                                data-date="{{ $lead->latestFollowUp?->follow_up_date }}"
                                data-time="{{ $lead->latestFollowUp?->follow_up_time }}"
                                data-type="{{ $lead->latestFollowUp?->follow_up_type }}"
                                data-comment="{{ $lead->latestFollowUp?->comments }}">
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
                    <th>Follow Up Date/Satus</th>
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
                        <td>{{ $lead->latestFollowUp->comments }}</td>
                        <td>{{ if_date($lead->latestFollowUp->follow_up_date) }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary updateBtn"
                                data-id="{{ $lead->id }}" data-status="{{ $lead->status }}"
                                data-date="{{ $lead->latestFollowUp?->follow_up_date }}"
                                data-time="{{ $lead->latestFollowUp?->follow_up_time }}"
                                data-type="{{ $lead->latestFollowUp?->follow_up_type }}"
                                data-comment="{{ $lead->latestFollowUp?->comments }}">
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
                    <th>Follow Up Date/Satus</th>
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
                            {{ if_date($lead->follow_up_date) }}
                            <span class="badge rounded-pill bg-danger text-white">Missed</span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary updateBtn"
                                data-id="{{ $lead->id }}" data-status="{{ $lead->status }}"
                                data-date="{{ $lead->latestFollowUp?->follow_up_date }}"
                                data-time="{{ $lead->latestFollowUp?->follow_up_time }}"
                                data-type="{{ $lead->latestFollowUp?->follow_up_type }}"
                                data-comment="{{ $lead->latestFollowUp?->comments }}">
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


<hr>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-xl-6 box-col-6">
            <div class="card">
                <div class="card-header card-no-border pb-3">
                    <h5 class="mb-1">Leads by Level</h5>

                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span><b>Total:</b>
                            {{ $lead_counters['Hot'] + $lead_counters['Warm'] + $lead_counters['Cold'] }}
                        </span>

                        <span style="color:#E53935">● Hot ({{ $lead_counters['Hot'] }})</span>
                        <span style="color:#FBC02D">● Cold ({{ $lead_counters['Cold'] }})</span>
                        <span style="color:#43A047">● Warm ({{ $lead_counters['Warm'] }})</span>
                    </div>
                </div>

                <div class="card-body apex-chart text-center">
                    <canvas id="leadLevelPieChart" style="max-height:300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-xl-6 box-col-6">
            <div class="card">
                <div class="card-header card-no-border pb-3">
                    <h5 class="mb-1">Leads by Status</h5>

                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span><b>Total:</b>
                            {{ $lead_counters['Pending'] +
                                $lead_counters['Not-interested'] +
                                $lead_counters['Follow Up'] +
                                $lead_counters['Mature'] }}
                        </span>

                        <span style="color:#FB8C00">● Pending ({{ $lead_counters['Pending'] }})</span>
                        <span style="color:#E53935">● Not Interested ({{ $lead_counters['Not-interested'] }})</span>
                        <span style="color:#1E88E5">● Follow Up ({{ $lead_counters['Follow Up'] }})</span>
                        <span style="color:#43A047">● Mature ({{ $lead_counters['Mature'] }})</span>
                    </div>
                </div>

                <div class="card-body text-center">
                    <canvas id="leadStatusChart" style="height:300px;"></canvas>
                </div>
            </div>
        </div>


    </div>
</div>

{{-- leads --}}
<span class="hotLead d-none">{{ $lead_counters['Hot'] }}</span>
<span class="coldLead d-none">{{ $lead_counters['Cold'] }}</span>
<span class="warmLead d-none">{{ $lead_counters['Warm'] }}</span>

<span class="lPending d-none">{{ $lead_counters['Pending'] }}</span>
<span class="lNotInterested d-none">{{ $lead_counters['Not-interested'] }}</span>
<span class="lFollowUp d-none">{{ $lead_counters['Follow Up'] }}</span>
<span class="lMature d-none">{{ $lead_counters['Mature'] }}</span>

<div class="modal fade" id="updateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateForm" action="{{ route('lead.updateMissed') }}" method="POST">
                @csrf
                {{-- @method('PUT') --}}
                <input type="hidden" name="id" id="lead_id">

                <div class="modal-header">
                    <h5 class="modal-title">Update Follow-Up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <x-Inputs.drop-down name="status" id="lead_status" label="Missing Follow Up Report"
                                :list="$statusList" class="form-control select2" :value="old('status')" :mandatory="true" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <x-Inputs.text-field name="follow_up_date" id="lead_date"
                                class="form-control date-picker" label="Follow Up Date" :value="old('follow_up_date', $model->follow_up_date ?? '')"
                                :mandatory="true" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <x-Inputs.text-field name="follow_up_time" id="lead_time"
                                class="form-control time-picker" label="Follow Up Time" :value="old('follow_up_time', $model->follow_up_time ?? '')" />
                        </div>

                        <div class="col-md-12 mb-3">
                            <x-Inputs.drop-down name="follow_up_type" id="lead_type" label="Follow Up Type"
                                :list="$followtypeList" :value="old('follow_up_type')" class="form-control select2" :mandatory="true" />
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
    $(document).ready(function() {

        // leads by level
        const hotLead = parseInt($('.hotLead').text().trim());
        const coldLead = parseInt($('.coldLead').text().trim());
        const warmLead = parseInt($('.warmLead').text().trim());

        const totalLeads = hotLead + coldLead + warmLead;

        new Chart(document.getElementById("leadLevelPieChart"), {
            type: 'doughnut',
            data: {
                labels: ['Hot', 'Cold', 'Warm'],
                datasets: [{
                    data: [hotLead, coldLead, warmLead],
                    backgroundColor: ['#FB8C00', '#FDD835', '#43A047'],
                    hoverOffset: 10
                }]
            },
            options: {
                cutout: '70%',
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            padding: 15
                        }
                    },
                    title: {
                        display: true,
                        text: 'Leads by Level',
                        font: {
                            size: 16
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerText',
                beforeDraw(chart) {
                    const ctx = chart.ctx;
                    ctx.restore();
                    ctx.font = "bold 18px Arial";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = "#333";
                    ctx.fillText("Total: " + totalLeads, chart.width / 2, chart.height / 2);
                    ctx.save();
                }
            }]
        });

        const pending = parseInt($('.lPending').text().trim());
        const notInterested = parseInt($('.lNotInterested').text().trim());
        const followUp = parseInt($('.lFollowUp').text().trim());
        const mature = parseInt($('.lMature').text().trim());

        const statusTotalLeads = pending + notInterested + followUp + mature;

        // new Chart(document.getElementById("leadStatusChart"), {
        //     type: 'doughnut',
        //     data: {
        //         labels: ['Pending', 'Not Interested', 'Follow Up', 'Mature'],
        //         datasets: [{
        //             data: [pending, notInterested, followUp, mature],
        //             backgroundColor: [
        //                 '#FB8C00', // Pending
        //                 '#E53935', // Not Interested
        //                 '#1E88E5', // Follow Up
        //                 '#43A047' // Mature
        //             ],
        //             hoverOffset: 10
        //         }]
        //     },
        //     options: {
        //         cutout: '70%',
        //         responsive: true,
        //         plugins: {
        //             legend: {
        //                 position: 'bottom',
        //                 labels: {
        //                     boxWidth: 14,
        //                     padding: 15
        //                 }
        //             },
        //             title: {
        //                 display: true,
        //                 text: 'Leads by Status',
        //                 font: {
        //                     size: 16
        //                 }
        //             }
        //         }
        //     },
        //     plugins: [{
        //         id: 'centerText',
        //         beforeDraw(chart) {
        //             const ctx = chart.ctx;
        //             ctx.restore();
        //             ctx.font = "bold 18px Arial";
        //             ctx.textAlign = "center";
        //             ctx.textBaseline = "middle";
        //             ctx.fillStyle = "#333";
        //             ctx.fillText("Total: " + statusTotalLeads, chart.width / 2, chart.height /
        //                 2);
        //             ctx.save();
        //         }
        //     }]
        // });

        new Chart(document.getElementById("leadStatusChart"), {
            type: 'bar',
            data: {
                labels: ['Pending', 'Not Interested', 'Follow Up', 'Mature'],
                datasets: [{
                    label: 'Leads',
                    data: [pending, notInterested, followUp, mature],
                    backgroundColor: ['#FF9F40', '#D32F2F', '#1E88E5', '#2E7D32'],
                    borderRadius: 6,
                    barThickness: 18
                }]
            },
            options: {
                indexAxis: 'y', // 🔥 horizontal bar
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });


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

    $(document).on("click", ".updateBtn", function() {
        // Set values from button data
        $("#lead_id").val($(this).data("id"));
        $("#lead_status").val($(this).data("status"));
        $("#lead_date").val($(this).data("date"));
        $("#lead_time").val($(this).data("time"));
        $("#lead_type").val($(this).data("type"));
        $("#lead_comment").val($(this).data("comment"));

        // Call toggle function after setting values
        toggleStatusFields(false);

        // Show modal
        $("#updateModal").modal("show");
    });

    function toggleStatusFields(clearValues = false) {
        let selectedStatus = $('#lead_status').val();

        // Hide all dependent fields first
        $('.status-dependent').hide().find('input, select, textarea')
            .removeAttr("required");

        // Clear values if requested
        if (clearValues) {
            $('.status-dependent').find('input, select, textarea').val('');
        }

        // Show fields based on status
        if (selectedStatus === 'not_interested') {
            $('#not-interested-reason').show()
                .find('input, select, textarea').attr("required", true);
        } else if (selectedStatus === 'follow_up') {
            $('#follow-up-fields').show()
                .find('input, select, textarea').attr("required", true);
        } else if (selectedStatus === 'mature') {
            $('#mature-fields').show()
                .find('input, select, textarea').attr("required", true);
        }
    }

    // Run once on page load (for edit mode if values already exist)
    toggleStatusFields(false);

    // On change of status dropdown
    $('#lead_status').change(function() {
        toggleStatusFields(true);
    });

    // $(document).on("click", ".updateBtn", function() {
    //     $("#lead_id").val($(this).data("id"));
    //     $("#lead_status").val($(this).data("status"));
    //     $("#lead_date").val($(this).data("date"));
    //     $("#lead_time").val($(this).data("time"));
    //     $("#lead_type").val($(this).data("type"));
    //     $("#lead_comment").val($(this).data("comment"));

    //     $("#updateModal").modal("show");
    // });
</script>

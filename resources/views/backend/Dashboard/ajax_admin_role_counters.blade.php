{{-- <style>
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
<div class="row g-4">

    <!-- Loans -->
    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
        <div class="dashboard-card loan-card">
            <div class="card-icon">🛠️</div>
            <div class="card-content">
                <h6>Complaint</h6>
                <h2>{{ $tComplaints }}</h2>
                <p>Total Complaints</p>
            </div>
        </div>
    </div>

    <!-- Partners -->
    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
        <div class="dashboard-card partner-card">
            <div class="card-icon">📑</div>
            <div class="card-content">
                <h6>Proforma Invoices</h6>
                <h2>{{ $tPI }}</h2>
                <p>Proforma Invoices</p>
            </div>
        </div>
    </div>

    <!-- Staff -->
    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
        <div class="dashboard-card staff-card">
            <div class="card-icon">👥</div>
            <div class="card-content">
                <h6>Parties</h6>
                <h2>{{ $tParties }}</h2>
                <p>Total Parties</p>
            </div>
        </div>
    </div>

</div>

<hr>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-xl-6 box-col-6">
            <div class="card">
                <div class="card-header card-no-border pb-3">
                    <h5 class="mb-1">Complaints by Status</h5>

                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span><b>Total:</b> {{ $complaint_counters['Pending'] + $complaint_counters['In-Progress'] + $complaint_counters['Hold'] + $complaint_counters['Done'] }}</span>

                        <span style="color:#ff4d4d">● Pending ({{ $complaint_counters['Pending'] }})</span>
                        <span style="color:#ffd11a">● In-Progress ({{ $complaint_counters['In-Progress'] }})</span>
                        <span style="color:#80bfff">● Hold ({{ $complaint_counters['Hold'] }})</span>
                        <span style="color:#43A047">● Done ({{ $complaint_counters['Done'] }})</span>
                    </div>
                </div>

                <div class="card-body apex-chart text-center">
                    <canvas id="complaintPieChart" style="max-height:300px;"></canvas>
                </div>
            </div>
        </div>


        <!-- RIGHT CARD -->
        <div class="col-sm-12 col-xl-6 box-col-6">
            <div class="card">
                <div class="card-header card-no-border pb-3">
                    <h5 class="mb-1">Complaint by Level</h5>

                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <span><b>Total:</b>
                            {{ $complaint_counters['Hot']  + $complaint_counters['Cold'] + $complaint_counters['Warm'] }}</span>

                        <span style="color:#E53935">● Hot ({{ $complaint_counters['Hot'] }})</span>
                        <span style="color:#FBC02D">● Cold ({{ $complaint_counters['Cold'] }})</span>
                        <span style="color:#43A047">● Warm ({{ $complaint_counters['Warm'] }})</span>
                    </div>
                </div>

                <div class="card-body apex-chart text-center">
                    <canvas id="statusLeadPieChart" style="max-height:300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<span class="cpending d-none">{{ $complaint_counters['Pending'] }}</span>
<span class="cip d-none">{{ $complaint_counters['In-Progress'] }}</span>
<span class="chold d-none">{{ $complaint_counters['Hold'] }}</span>
<span class="cdone d-none">{{ $complaint_counters['Done'] }}</span>

<span class="hotComplaint d-none">{{ $complaint_counters['Hot'] }}</span>
<span class="coldComplaint d-none">{{ $complaint_counters['Cold'] }}</span>
<span class="warmComplaint d-none">{{ $complaint_counters['Warm'] }}</span>
<script>
    $(document).ready(function() {

        // LEVEL WISE DATA
        const cpending = parseInt($('.cpending').text().trim());
        const cip = parseInt($('.cip').text().trim());
        const chold = parseInt($('.chold').text().trim());
        const cdone = parseInt($('.cdone').text().trim());

        const totalComplaint = cpending + cip + chold + cdone;

        // LEVEL CHART
        new Chart(document.getElementById("complaintPieChart"), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In-Progress', 'Hold', 'Done'],
                datasets: [{
                    data: [cpending, cip, chold, cdone],
                    backgroundColor: ['#E53935', '#FBC02D', '#1E88E5', '#43A047'],
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
                            padding: 15,
                            font: {
                                size: 13
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Complaints by Status',
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
                    ctx.fillText("Total: " + totalComplaint, chart.width / 2, chart.height / 2);
                    ctx.save();
                }
            }]
        });

        // Level WISE DATA
        const hotComplaint = parseInt($('.hotComplaint').text().trim());
        const coldComplaint = parseInt($('.coldComplaint').text().trim());
        const warmComplaint = parseInt($('.warmComplaint').text().trim());

        const totalStatus = hotComplaint + coldComplaint + warmComplaint;

        // Level CHART
        new Chart(document.getElementById("statusLeadPieChart"), {
            type: 'doughnut',
            data: {
                labels: ['Hot', 'Cold', 'Warm'],
                datasets: [{
                    data: [hotComplaint, coldComplaint, warmComplaint],
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
                        text: 'Complaint by Level',
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
                    ctx.fillText("Total: " + totalStatus, chart.width / 2, chart.height / 2);
                    ctx.save();
                }
            }]
        });
    });


    flatpickr(".date-picker", {
        dateFormat: "Y-m-d",
        allowInput: true
    });

    $(document).on("click", ".updateBtn", function() {
        // Set values from button data
        $("#lead_id").val($(this).data("id"));
        $("#lead_status").val($(this).data("status"));
        $("#lead_date").val($(this).data("date"));
        $("#lead_type").val($(this).data("type"));

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
</script> --}}

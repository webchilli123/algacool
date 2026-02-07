@extends($layout)

@section('content')
    <?php
    $page_header_links = [['title' => 'Summary', 'url' => route($routePrefix . '.index')]];
    ?>

    @include($partial_path . '.page_header')

    <form action="{{ $form['url'] }}" method="POST" enctype="multipart/form-data">
        {!! csrf_field() !!}
        {{ method_field($form['method']) }}
        {{-- <div class="col-md-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">
                    Product Type <span class="text-danger">*</span>
                </label>

                <div class="d-flex gap-4 mt-1">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="product_type" id="spare_product"
                            value="0"
                            {{ old('product_type', $model->product_type ?? 0) == 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="spare_product">
                            Spare Part
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="product_type" id="finished_product"
                            value="1"
                            {{ old('product_type', $model->product_type ?? 0) == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="finished_product">
                            Finished Product
                        </label>
                    </div>
                   
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="product_type" id="part_product"
                            value="2"
                            {{ old('product_type', $model->product_type ?? 0) == 2 ? 'checked' : '' }}>
                        <label class="form-check-label" for="part_product">
                            Part
                        </label>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- PRODUCT DETAILS --}}
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <strong>Product Details</strong>
            </div>
            <div class="card-body">

                <div class="row">
                    
                    <div class="col-md-3">
                        <x-Inputs.text-field id="name" name="name" label="Name" :value="$model->name"
                            placeholder="Enter Name" />
                    </div>
                    <div class="col-md-3">
                        <x-Inputs.drop-down id="brand_id" name="brand_id" label="Brand" :value="$model->brand_id"
                            :list="$brandList" class="form-control select2" :mandatory="true" />
                    </div>

                    <div class="col-md-3">
                        <x-Inputs.text-field id="specification" name="specification" label="specification" :value="$model->specification"
                            placeholder="Enter Specification" />
                    </div>

                    <div class="col-md-3">
                        <x-Inputs.text-field id="sku" name="sku" label="SKU" :value="$model->sku"
                            readonly="readonly" />
                    </div>
                </div>

                {{-- <div class="row mt-2">
                    <div class="col-md-3">
                        <x-Inputs.text-field id="capacity" name="capacity" label="Capacity" :value="$model->capacity"
                            placeholder="Enter Capacity" />
                    </div>

                    <div class="col-md-3">
                        <x-Inputs.text-field id="material_type" name="material_type" label="Material Type" :value="$model->material_type"
                            placeholder="Enter Material Type" />
                    </div>

                    <div class="col-md-3">
                        <x-Inputs.text-field id="batch" name="batch" label="Batch No" :value="$model->batch"
                            placeholder="Enter Batch No" />
                    </div>
                </div> --}}

            </div>
        </div>

        {{-- STOCK DETAILS --}}
        {{-- <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <strong>Inventory / Stock Details</strong>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-2">
                        <x-Inputs.text-field id="opening_stock" name="opening_stock" label="Opening Stock"
                            :value="$model->opening_stock" placeholder="Enter Opening Stock" />
                    </div>
                    <div class="col-md-2">
                        <x-Inputs.drop-down id="warehouse_id" name="warehouse_id" label="Warehouse" :value="$model->warehouse_id"
                            :list="$warehouseList" class="form-control select2" />
                    </div>
                    <div class="col-md-4">
                        <x-Inputs.text-field id="min_stock" name="min_stock" label="Minimum Stock" :value="$model->min_stock"
                            placeholder="Enter Minimum Stock" />
                    </div>

                    <div class="col-md-4">
                        <x-Inputs.text-field id="max_stock" name="max_stock" label="Maximum Stock" :value="$model->max_stock"
                            placeholder="Enter Maximum Stock" />
                    </div>
                </div>

            </div>
        </div> --}}


        {{-- PRICING DETAILS --}}
        <div class="card mb-3">
            <div class="card-header bg-secondary">
                <strong>Pricing & Taxes</strong>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-3">
                        <x-Inputs.text-field name="purchase_price" label="Purchase Price (Default)" :value="$model->purchase_price"
                            placeholder="Enter Price" class="form-control validate-float validate-postive-only" />
                    </div>

                    <div class="col-md-3">
                        <x-Inputs.text-field name="selling_price" label="Selling Price (Default)" :value="$model->selling_price"
                            placeholder="Enter Price" class="form-control validate-float validate-postive-only" />
                    </div>

                    <div class="col-md-3">
                        <x-Inputs.text-field name="gst" label="GST % (Default)" :value="$model->gst"
                            placeholder="Enter GST %"
                            class="form-control validate-float validate-postive-only validate-less-than"
                            data-less-than-from="50" />
                    </div>

                    <div class="col-md-3">
                        <x-Inputs.text-field name="discount" label="Discount % (Default)" :value="$model->discount"
                            placeholder="Enter Discount %" class="form-control" />
                    </div>
                </div>

            </div>
        </div>

        {{-- ADDITIONAL INFO --}}
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <strong>Additional Information</strong>
            </div>
            <div class="card-body">

                <div class="row">
                   

                    <div class="col-md-6" style="padding-top: 35px;">
                        <x-Inputs.checkbox name="is_active" label="Active" :value="$model->is_active" class="ml-3" />
                    </div>
                </div>

            </div>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
    </form>

    @push('scripts')
        <script>
            $(function() {

                var item_name, brand, pattern = '<?= $item_sku_pattern ?>';

                function create_sku() {
                    var code = pattern;

                    // if (item_name) {
                    //     code = code.replace("{item_name}", item_name['name']);
                    // }

                    if (brand) {
                        code = code.replace("{brand}", brand['short_name']);
                    }

                    var specification = $("#specification").val();
                    if (specification && typeof specification == "string") {
                        code = code.replace("{specification}", specification);
                    } else {
                        code = code.replace("{specification}", "");
                    }

                    var name = $("#name").val();
                    if (name) {
                        code = code.replace("{item_name}", name);
                    }

                    code = str_convert_space_to_hyphine(code);
                    code = str_trim_hyphine(code);
                    code = code.toLowerCase();

                    $("#sku").val(code);
                }

                $("#item_id").change(function() {
                    var v = $(this).val();

                    if (v) {
                        ajaxGetJson("/item_ajax_get/" + v, function(response) {
                            item_name = response["data"];
                            create_sku();
                        });
                    }
                }).trigger("change", {
                    pageLoad: true
                });

                $("#brand_id").change(function() {
                    var v = $(this).val();

                    if (v) {
                        ajaxGetJson("/brand_ajax_get/" + v, function(response) {
                            brand = response["data"];
                            create_sku();
                        });
                    }
                }).trigger("change", {
                    pageLoad: true
                });

                $("#name, #specification").keyup(function() {
                    create_sku();
                })

                // $('#opening_stock').on('input', function() {
                //     if ($(this).val() !== '') {
                //         $('#warehouse_id').attr('required', true);
                //     } else {
                //         $('#warehouse_id').removeAttr('required');
                //     }

                //     // trigger change for select2 validation
                //     $('#warehouse_id').trigger('change');
                // });


             

            });
        </script>
    @endpush
@endsection

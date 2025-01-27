@extends('layouts.master')
@section('title', 'Order Create')

@section('content')
<style type="text/css">
    input#form1 {
        width: 72px !important;

    }

    .col-sm-7.text-center {
        width: 37.333333% !important;
    }
</style>
<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
            'url' => 'order',
            'label' => 'Orders',
        ],
    ]" />
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-6 mb-4 order-0">
            <div class="card">

                <h5 class="card-header">{{ __('Product Lists') }}</h5>

                <div class="card-body">
                    <div class="table-responsive">
                        <x-a-grid-view :id="'order_product_table'" :model="$model" :url="'product/get-list'" :columns="['select', 'name', 'description','price']" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4 order-0">
            <div class="card">

                <h5 class="card-header">{{ __('Cart Lists') }}</h5>

                <div class="card-body">
                    <div class="text-right mb-2">
                        <button
                            type="button"
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#basicModal">
                            Add More
                        </button>
                    </div>
                    <div class="table-responsive">
                        <x-a-grid-view :id="'cart_list'" :model="$model" :url="'cart/get-list'" :filterButtonId="'order_filter_button'"
                            :customfilterIds="['start_date', 'warehouse_id']" :columns="['select',  'product_name', 'total_price', 'unit_price',['attribute'=>'close','label'=>'delete']]" />

                    </div>
                </div>
            </div>
            <div class="card mt-4">

                <h5 class="card-header">{{ __('Checkout') }}</h5>

                <div class="card-body">
                    <div class="table-responsive">
                        <x-a-grid-view :id="'cart_checkout'" :model="$model" :url="'cart/get-list/1/'" :filterButtonId="'order_filter_button'"
                            :customfilterIds="['start_date', 'warehouse_id']" :columns="['grind_price','total_checkout_quantity','total_checkout_amount']" :paging="false" :searching="false"
                            :info="false" />
                    </div>
                    <div class="text-end mt-3">
                        <button type="button" id="placeOrder" class="btn btn-primary">Place Order</button>


                    </div>
                </div>

            </div>


        </div>
    </div>
    <div class="mt-3">
        <!-- Button trigger modal -->


        <!-- Modal -->
        <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Add Custom Product</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="/submit" method="POST" class="ajax-form" id="ajaxform" data-success-callback="formSuccessCallback">

                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="custom_product" class="form-label">Product Detail</label>
                                    <input type="text" id="custom_product" name="custom_product" class="form-control" placeholder="Enter Product Name" />
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="text" id="quantity" name="quantity" value="1" class="form-control" placeholder="1" />
                                </div>
                                <div class="col mb-0">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="text" id="price" name="price" value="0" class="form-control" placeholder="23" />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" id="submit-button" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection
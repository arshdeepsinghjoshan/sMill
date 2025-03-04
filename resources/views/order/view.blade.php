@extends('layouts.master')
@section('title', $model->order_number ?? 'Order View')

@section('content')
<?php

use App\Models\User;
?>
<x-a-breadcrumb :columns="[
        [
            'url' => '/',
            'label' => 'Home',
        ],
        [
          'url' => 'order',
            'label' => 'Orders',
        ],
        $model->order_number,
    ]" />

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-body">
                    <h5>{{ !empty($model->order_number) ? (strlen($model->order_number) > 100 ? substr($model->order_number, 0, 100) . '...' : $model->order_number) : 'N/A' }}
                        <span class="{{ $model->getStateBadgeOption() }}">{{ $model->getState() }}</span>
                    </h5>

                    <x-a-detail-view :model="$model" :column="
    [
    'id',
      'order_number',
         [
        'attribute' => 'total_amount',
        'value' =>  number_format($model->total_amount, 2),
        'visible'=> true   
     ],
      [
        'attribute' => 'updated_at',
        'label' => 'Updated at',
        'value' => (empty($model->updated_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($model->updated_at)),
        'visible'=> true   
     ],
      [
        'attribute' => 'created_at',
        'label' => 'Created at',
        'value' => (empty($model->created_at)) ? 'N/A' : date('Y-m-d h:i:s A', strtotime($model->created_at)),
     ],
     [
        'attribute' => 'created_by_id',
        'label' => 'Created By',
        'value' => !empty($model->createdBy && $model->createdBy->name) ? $model->createdBy->name : 'N/A',
     ],
    ]
    " />
                </div>
            </div>
        </div>
    </div>
    <x-a-user-action :model="$model" attribute="state_id" :states="$model->getStateOptions()" :title="'Order'" />

    <x-a-user-action :model="$model" attribute="order_payment_status" :states="$model->getPaymentOptions()" :title="'Payment'" />

    <div class="row mt-4">

        <div class="col-xl-12">
            <div class="nav-align-top ">
                <ul class="nav nav-tabs nav-fill" role="tablist">

                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-wallet" aria-controls="navs-justified-messages" aria-selected="false">
                            <i class="tf-icons bx bx-message-square"></i> Order Items
                        </button>
                    </li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="navs-justified-wallet" role="tabpanel">
                        <div class="table-responsive">



                            <x-a-relation-grid :id="'order_item_table'" :relation="'items'" :model="$model" :columns="[
                                'id',
                                'product_name',
                                'total_amount',
                                'quantity',
                                'status',
                                'created_at',
                                'created_by',
                                'action',
                            ]" />



                        </div>
                    </div>




                </div>
            </div>
        </div>
    </div>

    <button
        type="button"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#customerModal">
        Add Customers
    </button>
    <!-- pending payment modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add payment</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="/" method="POST" class="ajax-form" id="ajaxform" data-success-callback="formSuccessCallback">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="search_name_phone_number" class="form-label">Search by Name & Phone Number</label>
                                <input type="text" id="search_name_phone_number" autocomplete="off" name="search_name_phone_number" class="form-control" placeholder="Harry & 954789" />
                            </div>
                        </div>

                        <div class="text-center"><b>OR</b></div>
                        <div class="row g-2 mt-2">
                            <div class="col mb-0">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Harry.." />
                            </div>
                            <div class="col mb-0">
                                <label for="contact_no" class="form-label">Phone Number</label>
                                <input type="number" id="contact_no" name="contact_no" class="form-control" placeholder="954789.." />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" id="add-customer" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>

@endsection
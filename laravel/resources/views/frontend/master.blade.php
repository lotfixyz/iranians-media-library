@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3 text-center">
                @include('frontend.menus.domains')
            </div>
            <div class="col-md-9">
                @yield('sub_content')
                <hr>
                <div class="text-center">
                    <div class="card">
                        <div class="card-body">
                            <span>طراحی و توسعه توسط <a href="http://lotfi.xyz" target="_blank">محمد لطفی</a></span>
                            |
                            <span>نسخه 0.35 آزمایشی</span>
                            <!--
                            |
                            <span>تماس اضطراری (09355223144)</span>
                            -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

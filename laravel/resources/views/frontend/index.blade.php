@extends('frontend.master')

@section('sub_content')
    <div class="card">
        <div class="card-header">خوش آمدید</div>
        <div class="card-body text-center">
            {{--
            <strong>
                <span>به</span> <span>{{ config('app.name', 'Laravel') }}</span> <span>خوش آمدید.</span><br />
            </strong>
            <br />
            --}}
            <img src="{{ asset('images/welcome.png') }}" alt=""/>
            <br/>
            <br/>
            به کتابخانه رسانه ایرانیان خوش آمدید.
            <br/>
            از لیست کتابخانه‌ها یک کتابخانه را انتخاب کنید.
        </div>
    </div>
@endsection

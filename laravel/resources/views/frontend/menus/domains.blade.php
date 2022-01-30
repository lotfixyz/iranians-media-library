<div class="card">
    <div class="card-header">لیست کتابخانه&zwnj;ها</div>
    <div class="card-body">
        @forelse($domains as $domain)
            <a href="{{ route('frontend.domain', [$domain->name]) }}">{!! $domain->title !!}</a><br/>
        @empty
            موردی برای نمایش وجود ندارد.
        @endforelse
    </div>
</div>
<!--
<div class="card">
    <div class="card-header">موارد دیگر</div>
    <div class="card-body">
        <a href="/page/about">درباره کتابخانه</a><br/>
        <a href="/page/contact">تماس با کتابخانه</a><br/>
        <a href="/page/critic-suggest">انتقادات و پیشنهادات</a><br/>
        <a href="/page/media-request">درخواست رسانه</a><br/>
        <a href="/page/special-thanks">تشکر مخصوص</a><br/>
        <hr/>
        <a href="/page/report">گزارش نقض حقوق ناشر</a><br/>
    </div>
</div>
<div class="card">
    <div class="card-header">حمایت مالی</div>
    <div class="card-body">
        <a href="https://zarinp.al/@lotfixyz" target="_blank">
            <img src="https://www.zarinpal.com/assets/images/logo-white.svg" width="100%">
        </a><br/>
        <br/>
        <a href="https://hamibash.com/lotfixyz" target="_blank">
            <img src="https://hamibash.com/assets/img/logo.svg" width="100%">
        </a>
    </div>
</div>
-->

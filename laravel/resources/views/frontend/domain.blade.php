@extends('frontend.master')

@section('sub_content')
    <div class="card">
        <div class="card-header">
            {!! $domain->title !!}
            @if($current_folder) <strong>»</strong> {!! str_replace('/', ' <strong>»</strong> ', $current_folder) !!}@endif
        </div>
        <div class="card-body">
            <div class="alert alert-info" style="vertical-align: bottom;">
                <img class="ml-2" src="{{ asset('images/icons/folder-64.svg') }}" alt="">
                <span>لیست پوشه‌ها</span> <span style="color: gray; opacity: 75%;">({{ count($folders) }} پوشه)</span>
                @if ($current_folder)
                    <a class="mr-2 float-left" href="{{ route('frontend.domain', [$domain->name]) . '/' . ("$current_folder/..") }}"><img src="{{ asset('images/icons/folder-up-32.png') }}" alt="بازگشت"></a>
                @endif
            </div>
            <table class="table table-striped col-12">
                <tr>
                    <td class="col-1">#</td>
                    <td class="col-11"><span>نام پوشه</span></td>
                </tr>
                @forelse($folders as $row => $folder)
                    <tr>
                        <td class="col-1">
                            {{ auto_row_numbering($row, count($folders)) }}
                        </td>
                        <td class="col-11">
                            @if ($current_folder)
                                <a href="{{ route('frontend.domain', [$domain->name]) . '/' . ("$current_folder/" . $folder->name) }}">
                                    {{ $folder->name }}
                                </a>
                            @else
                                <a href="{{ route('frontend.domain', [$domain->name]) . '/' . ($folder->name) }}">
                                    {{ $folder->name }}
                                </a>
                            @endif
                            @if ($folder->extra->has_description)
                                <br/>
                                <div style="color: gray; opacity: 75%; text-align: justify;">{!! $folder->extra->description !!}</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="col-12" colspan="100">پرونده‌ای برای نمایش وجود ندارد.</td>
                    </tr>
                @endforelse
            </table>
            <hr style="border: none; border-bottom: lightgray dashed 1px;">
            <div class="alert alert-success">
                <img class="ml-2" src="{{ asset('images/icons/file-64.svg') }}" alt="">
                <span>لیست پرونده‌ها</span> <span style="color: gray; opacity: 75%;">({{ count($files) }} پرونده)</span>
            </div>
            <table class="table table-striped col-12">
                <tr>
                    <td class="col-1">#</td>
                    <td class="col-7">نام پرونده</td>
                    <td class="col-2">حجم پرونده</td>
                    <td class="col-2 text-left"></td>
                </tr>
                @forelse($files as $row => $file)
                    <tr>
                        <td class="col-1">
                            {{ auto_row_numbering($row, count($files)) }}
                        </td>
                        <td class="col-7">
                            <a href="{{ $file->disk_url }}">
                                {{ $file->name }}
                            </a>
                            @if ($file->extra->has_description)
                                <div style="color: gray; opacity: 75%; text-align: justify;">{!! $file->extra->description !!}</div>
                            @endif
                        </td>
                        <td class="col-2">
                            {{ $file->size }}
                        </td>
                        <td class="col-2 text-left">
                            <img src="{{ asset('images/file_extensions/' . $file->extension . '.svg') }}" alt="">
                            <img src="{{ asset('images/icons/file-info.svg') }}" style="cursor: pointer;" onclick="alert();" alt="">
                        </td>
                    </tr>
                    {{--
                    @if (in_array($file->extension, ['mp3', 'mp4', 'wav', 'ogg']))
                        <tr>
                            <td class="col-12" colspan="5">
                                <audio controls title="{{ $file->name }}">
                                    <source src="{{ $file->disk_url }}" type="audio/mpeg">
                                </audio>
                            </td>
                        </tr>
                    @endif
                    --}}
                @empty
                    <tr>
                        <td class="col-12" colspan="100">پرونده‌ای برای نمایش وجود ندارد.</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>
@endsection

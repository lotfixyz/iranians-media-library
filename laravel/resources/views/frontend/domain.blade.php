@extends('frontend.master')

@section('sub_content')
    <div class="card">
        <div class="card-header">
            {!! $domain->title !!}
            @if($current_folder) <strong>»</strong> {!! str_replace('/', ' <strong>»</strong> ', $current_folder) !!}@endif
        </div>
        <div class="card-body">
            @if ($show_folder_section)
                <div class="alert alert-primary">
                    <img class="ml-2" src="{{ asset('images/icons/folder-64.svg') }}" alt="">
                    <span>لیست پوشه‌ها</span> <span style="color: gray; opacity: 75%;">({{ $folder_count }} پوشه)</span>
                    @if ($current_folder)
                        <a class="mr-2 float-left" href="{{ route('frontend.domain', [$domain->name]) . '/' . ("$current_folder/..") }}"><img src="{{ asset('images/icons/folder-up-32.png') }}" alt="بازگشت"></a>
                    @endif
                </div>
                <table class="table table-striped col-12">
                    <tr>
                        <td class="col-1">#</td>
                        <td class="col-11"><span>نام پوشه</span></td>
                        <td class="col-1">
                            <img src="{{ asset('images/icons/file-info-16.svg') }}" style="filter: grayscale(100);" alt="">
                        </td>
                    </tr>
                    @forelse($folders as $row => $folder)
                        <tr>
                            <td class="col-1">
                                {{ auto_row_numbering($row, $folder_count) }}
                            </td>
                            <td class="col-10">
                                <a href="{{ $folder->disk_url }}">
                                    {{ $folder->name }}
                                </a>
                                @if ($folder->extra->has_description)
                                    <br/>
                                    <div style="color: gray; opacity: 75%; text-align: justify;">{!! $folder->extra->description !!}</div>
                                @endif
                            </td>
                            <td class="col-1">
                                <img src="{{ asset('images/icons/folder-type-' . $folder->type . '-16.png') }}" title="{{ 'link' == $folder->type ? 'میانبر' : 'پوشه' }}" alt="{{ 'link' == $folder->type ? 'میانبر' : 'پوشه' }}">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="col-12" colspan="100">پوشه‌ای برای نمایش وجود ندارد.</td>
                        </tr>
                    @endforelse
                </table>
                <hr style="border: none; border-bottom: lightgray dashed 1px;">
            @endif
            @if ($show_file_section)
                <div class="alert alert-success">
                    <img class="ml-2" src="{{ asset('images/icons/file-64.svg') }}" alt="">
                    <span>لیست پرونده‌ها</span> <span style="color: gray; opacity: 75%;">({{ $file_count }} پرونده)</span>
                    @if ($current_folder && !$show_folder_section)
                        <a class="mr-2 float-left" href="{{ route('frontend.domain', [$domain->name]) . '/' . ("$current_folder/..") }}"><img src="{{ asset('images/icons/folder-up-32.png') }}" alt="بازگشت"></a>
                    @endif
                </div>
                <table class="table table-striped col-12">
                    <tr>
                        <td class="col-1">#</td>
                        <td class="col-7">نام پرونده</td>
                        <td class="col-2">حجم پرونده</td>
                        <td class="col-2 text-left">
                            <img src="{{ asset('images/icons/file-info-16.svg') }}" style="filter: grayscale(100);" alt="">
                        </td>
                    </tr>
                    @forelse($files as $row => $file)
                        <tr>
                            <td class="col-1">
                                {{ auto_row_numbering($row, $file_count) }}
                            </td>
                            <td class="col-7">
                                <a href="{{ $file->disk_url }}" target="_blank">{{ $file->title }}</a>
                                <small style="color: darkgray;">
                                    @if ($file->extra->has_id3v2)
                                        {{ $file->extra->id3v2->bitrate }}Kbps
                                    @elseif (($file->extra->has_info))
                                        @if ($file->extra->info->has_bitrate)
                                            {{ $file->extra->info->bitrate }}Kbps
                                        @endif
                                    @endif
                                </small>
                                @if ($file->extra->has_description)
                                    <div style="color: gray; opacity: 75%; text-align: justify;">{!! $file->extra->description !!}</div>
                                @endif
                            </td>
                            <td class="col-2">
                                {{ $file->size }}
                            </td>
                            <td class="col-2 text-left">
                                <img src="{{ asset('images/file_extensions/' . $file->extension . '.svg') }}" alt="">
                                @if ($setting->show_info)
                                    <img src="{{ asset('images/icons/file-info-16.svg') }}" style="cursor: pointer; width: 16px;" alt="" data-toggle="modal" data-target="#info_{{ $row }}">
                                    <!-- Modal -->
                                    <div class="modal fade" id="info_{{ $row }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ $file->title }}</h5>
                                                </div>
                                                <div class="modal-body text-right">
                                                    توضیحی برای نمایش وجود ندارد.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">متوجه شدم</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @if ($setting->show_player)
                            @if (in_array($file->extension, ['mp3', 'mp4', 'wav', 'ogg']))
                                <tr>
                                    <td class="col-12" colspan="5">
                                        <audio controls title="{{ $file->title }}">
                                            <source src="{{ $file->disk_url }}" type="audio/mpeg">
                                        </audio>
                                    </td>
                                </tr>
                            @endif
                        @endif
                    @empty
                        <tr>
                            <td class="col-12" colspan="100">پرونده‌ای برای نمایش وجود ندارد.</td>
                        </tr>
                    @endforelse
                </table>
            @endif
            @if (!$show_folder_section && !$show_file_section)
                <div class="alert alert-secondary">
                    <img class="ml-2" src="{{ asset('images/icons/empty-section-64.png') }}" alt="">
                    <span>پوشه یا پرونده‌ای برای نمایش وجود ندارد.</span>
                    @if ($current_folder)
                        <a class="mr-2 float-left" href="{{ route('frontend.domain', [$domain->name]) . '/' . ("$current_folder/..") }}"><img src="{{ asset('images/icons/folder-up-32.png') }}" alt="بازگشت"></a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection

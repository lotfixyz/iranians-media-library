<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Classes\StorageContent;
use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use stdClass;

/**
 * Class FrontendController
 * @package App\Http\Controllers\Frontend
 */
class FrontendController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    function index()
    {
        return view('frontend.index')->with(get_menus());
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param $domain
     * @param string $current_folder
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \getid3_exception
     */
    function domain(Request $request, $domain, string $current_folder = '')
    {
        $domain = Domain::where('name', '=', $domain)->where('inactive', '=', '0')->firstOrFail();
        $path = get_domain_storage_path($domain->name, $current_folder);
        $storage_content = new StorageContent($path);
        [$folders, $files] = $storage_content->load();

        $setting = new stdClass();
        $setting->show_player = false;
        $setting->show_info = false;
        $setting->show_empty_section = false;
        //$setting->show_player_only_first = false;

        $with = [
            'current_folder'      => $current_folder,
            'domain'              => $domain,
            'folder_count'        => count($folders),
            'folders'             => $folders,
            'show_folder_section' => $setting->show_empty_section || 0 !== count($folders),
            'file_count'          => count($files),
            'files'               => $files,
            'show_file_section'   => $setting->show_empty_section || 0 !== count($files),
            'setting'             => $setting,
        ];

        return view('frontend.domain')->with($with)->with(get_menus());
    }
}

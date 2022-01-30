<?php

namespace App\Helpers\Classes;

use Illuminate\Support\Facades\Storage;
use Owenoj\LaravelGetId3\GetId3;
use stdClass;

/**
 *
 */
class StorageContent
{
    /**
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    var $disk;

    /**
     * @var
     */
    var $path;

    /**
     *
     */
    public function __construct($path)
    {
        $this->disk = Storage::disk(env('DOWNLOAD_FOLDER'));
        $this->path = $path;
    }

    /**
     * @param $raw_data
     * @return \stdClass
     */
    private function get_config($raw_data): stdClass
    {
        $r = new stdClass();
        // Fetch folder data.
        if (isset($raw_data->folder))
        {
            $r->has_folder = true;
            $folder_data = $raw_data->folder;
            $folder = new stdClass();
            // Description
            if (isset($folder_data->description))
            {
                $folder->has_description = true;
                $folder->description = $folder_data->description;
            } else
            {
                $folder->has_description = false;
            }
            // Link
            if (isset($folder_data->link))
            {
                $folder->has_link = true;
                $folder->link = $folder_data->link;
            } else
            {
                $folder->has_link = false;
            }
            // Order
            if ($folder->has_order = isset($folder_data->order))
            {
                $folder->order = $folder_data->order;
            }
            if ($folder->has_order_end = isset($folder_data->order_end))
            {
                $folder->order_end = $folder_data->order_end;
            }
            $r->folder = $folder;
        } else
        {
            $r->has_folder = false;
        }
        // Fetch file data.
        if (isset($raw_data->file))
        {
            $r->has_file = true;
            $file_data = $raw_data->file;
            $file = new stdClass();
            // Description
            if (isset($file_data->description))
            {
                $file->has_description = true;
                $file->description = $file_data->description;
            } else
            {
                $file->has_description = false;
            }
            // Order
            if ($file->has_order = isset($file_data->order))
            {
                $file->order = $file_data->order;
            }
            if ($file->has_order_end = isset($file_data->order_end))
            {
                $file->order_end = $file_data->order_end;
            }
            $r->file = $file;
        } else
        {
            $r->has_file = false;
        }

        return $r;
    }

    /**
     * @param $raw_description
     * @return false|mixed|string
     */
    private function get_description($raw_description)
    {
        switch (true)
        {
            case 'base64:' == substr($raw_description, 0, 7):
                return base64_decode(substr($raw_description, 7));
            case 'file:' == substr($raw_description, 0, 5):
                return @file_get_contents($this->disk->path($this->path . DIRECTORY_SEPARATOR . substr($raw_description, 5)));
            default:
                return $raw_description;
        }
    }

    /**
     * @param $file
     * @return array|string|string[]|null
     */
    private function get_file_title($file)
    {
        $pattern = '/ \[(\d+)Kbps\]/i';
        $title = str_replace(' - medialib.ir', null, $file->name);

        return preg_replace($pattern, null, $title);
    }

    /**
     * @param $file
     * @return \stdClass
     */
    private function get_file_info($file): stdClass
    {
        $pattern = '/ \[(\d+)Kbps\]/i';
        preg_match($pattern, $file->name, $matches);
        $r = new stdClass();
        if ($matches)
        {
            $r->has_bitrate = true;
            $r->bitrate = $matches[1];
        } else
        {
            $r->has_bitrate = false;
        }

        return $r;
    }

    /**
     * @return array[]
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \getid3_exception
     */
    public function load(): array
    {
        // Initialize disk and storage.
        if (!$this->disk->has($this->path))
        {
            abort(404);
        }
        $items = $this->disk->listContents($this->path);
        //dd($items);
        // Begin read raw config, folders and files.
        $config = new stdClass();
        $config->has_folder = false;
        $config->has_file = false;
        $raw_folders = [];
        $raw_files = [];
        foreach ($items as $item)
        {
            $type = $item['type'];
            if ('dir' === $type)
            {
                $raw_folders[] = $item;
            } elseif ('file' === $type)
            {
                if ($item['basename'] === env('CONFIG_FILE'))
                {
                    $raw_config = json_decode($this->disk->read($item['path']));
                    if ($raw_config instanceof stdClass)
                    {
                        $config = $this->get_config($raw_config);
                    }
                } elseif ('.' === substr($item['basename'], 0, 1))
                {
                    $noname_files[] = $item;
                } else
                {
                    $raw_files[] = $item;
                }
            }
        }
        //dd($config, $raw_folders, $raw_files);
        // End read raw config, folders and files.
        // Begin read semi-raw folders.
        $semi_raw_folders = [];
        foreach ($raw_folders as $raw_folder)
        {
            $folder = new stdClass();
            $folder->type = 'folder';
            $folder->name = $raw_folder['basename'];
            $folder->path = $raw_folder['path'];
            $folder->disk_url = str_replace(env('DOWNLOAD_FOLDER') . '/', '', $this->disk->url("$this->path/$folder->name"));
            $folder->timestamp = date('Y-m-d H:i:s', $raw_folder['timestamp']);
            // Begin extra data management.
            $extra = new stdClass();
            $extra->has_description = false;
            if ($config->has_folder)
            {
                if ($config->folder->has_description)
                {
                    $name = $folder->name;
                    if (isset($config->folder->description->$name))
                    {
                        $extra->has_description = true;
                        $extra->description = $this->get_description($config->folder->description->$name);
                    }
                }
                if ($config->folder->has_link)
                {
                    $name = $folder->name;
                    if (isset($config->folder->link->$name))
                    {
                        $folder->type = 'link';
                        $folder->path = $config->folder->link->$name;
                        $folder->disk_url = str_replace(env('DOWNLOAD_FOLDER') . '/', '', $this->disk->url("$folder->path"));
                    }
                }
            }
            $folder->extra = $extra;
            // End extra data management.
            $semi_raw_folders[$folder->name] = $folder;
        }
        //dd($semi_raw_folders);
        // End read semi-raw folders.
        // Begin read folders.
        $folder_starts = [];
        $folder_middles = [];
        $folder_ends = [];
        if ($config->has_folder)
        {
            if ($config->folder->has_order)
            {
                $orders = $config->folder->order;
                foreach ($orders as $order)
                {
                    if (isset($semi_raw_folders[$order]))
                    {
                        $folder_starts[] = $semi_raw_folders[$order];
                        unset($semi_raw_folders[$order]);
                    }
                }
            }
            if ($config->folder->has_order_end)
            {
                $order_ends = $config->folder->order_end;
                foreach ($order_ends as $order_end)
                {
                    if (isset($semi_raw_folders[$order_end]))
                    {
                        $folder_ends[] = $semi_raw_folders[$order_end];
                        unset($semi_raw_folders[$order_end]);
                    }
                }
            }
        }
        foreach ($semi_raw_folders as $semi_raw_folder)
        {
            $folder_middles[] = $semi_raw_folder;
        }
        $folders = array_merge($folder_starts, $folder_middles, $folder_ends);
        //dd($folders);
        // End read folders.
        // Begin read semi-raw files.
        $semi_raw_files = [];
        foreach ($raw_files as $raw_file)
        {
            $file = new stdClass();
            $file->name = isset($raw_file['extension']) ? str_replace('.' . $raw_file['extension'], null, $raw_file['basename']) : $raw_file['basename'];
            $file->extension = $raw_file['extension'];
            $file->full_name = $file->name . ($file->extension ? '.' . $file->extension : null);
            $file->disk_path = $this->disk->path("$this->path/$file->full_name");
            $file->disk_url = $this->disk->url("$this->path/$file->full_name");
            $file->size = human_readable_file_size($raw_file['size']);
            $file->timestamp = date('Y-m-d H:i:s', $raw_file['timestamp']);
            $file->mime_type = mime_content_type($file->disk_path);
            // Additional properties.
            $file->title = $this->get_file_title($file);
            $files[] = $file;
            // Begin extra data management.
            $extra = new stdClass();
            $extra->has_description = false;
            if ($config->has_file)
            {
                if ($config->file->has_description)
                {
                    $full_name = $file->full_name;
                    if (isset($config->file->description->$full_name))
                    {
                        $extra->has_description = true;
                        $extra->description = $this->get_description($config->file->description->$full_name);
                    }
                }
            }
            if ('audio/mpeg' == $file->mime_type)
            {
                $id3v2 = new stdClass();
                $get_id3 = new GetId3($file->disk_path);
                $_id3 = $get_id3->extractInfo();
                $id3v2->bitrate = intval(round($_id3['bitrate'] / 1000));
                $extra->has_id3v2 = true;
                $extra->id3v2 = $id3v2;
            } else
            {
                $extra->has_id3v2 = false;
            }
            if ('application/zip' == $file->mime_type)
            {
                $info = new stdClass();
                $info = $this->get_file_info($file);
                $extra->has_info = true;
                $extra->info = $info;
            } else
            {
                $extra->has_info = false;
            }
            $file->extra = $extra;
            // End extra data management.
            $semi_raw_files[$file->full_name] = $file;
        }
        //dd($semi_raw_files);
        // End read semi-raw files.
        // Begin read files.
        $file_starts = [];
        $file_middles = [];
        $file_ends = [];
        if ($config->has_file)
        {
            if ($config->file->has_order)
            {
                $orders = $config->file->order;
                foreach ($orders as $order)
                {
                    if (isset($semi_raw_files[$order]))
                    {
                        $file_starts[] = $semi_raw_files[$order];
                        unset($semi_raw_files[$order]);
                    }
                }
            }
            if ($config->file->has_order_end)
            {
                $order_ends = $config->file->order_end;
                foreach ($order_ends as $order_end)
                {
                    if (isset($semi_raw_files[$order_end]))
                    {
                        $file_ends[] = $semi_raw_files[$order_end];
                        unset($semi_raw_files[$order_end]);
                    }
                }
            }
        }
        foreach ($semi_raw_files as $semi_raw_file)
        {
            $file_middles[] = $semi_raw_file;
        }
        $files = array_merge($file_starts, $file_middles, $file_ends);
        //dd($files);
        // End read files.

        return [$folders, $files];
    }
}

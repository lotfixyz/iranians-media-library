<?php

namespace App\Helpers\Classes;

use stdClass;
use Storage;

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
            // Order
            if (isset($folder_data->order))
            {
                $folder->has_order = true;
                $folder->order = $folder_data->order;
            } else
            {
                $folder->has_order = false;
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
            if (isset($file_data->order))
            {
                $file->has_order = true;
                $file->order = $file_data->order;
            } else
            {
                $file->has_order = false;
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
     * @return array[]
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function load(): array
    {
        // Initialize disk and storage.
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
            $folder->name = $raw_folder['basename'];
            $folder->path = $raw_folder['path'];
            $folder->path = $raw_folder['path'];
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
            }
            $folder->extra = $extra;
            // End extra data management.
            $semi_raw_folders[$folder->name] = $folder;
        }
        //dd($semi_raw_folders);
        // End read semi-raw folders.
        // Begin read folders.
        $folders = [];
        if ($config->has_folder)
        {
            if ($config->folder->has_order)
            {
                $orders = $config->folder->order;
                foreach ($orders as $order)
                {
                    if (isset($semi_raw_folders[$order]))
                    {
                        $folders[] = $semi_raw_folders[$order];
                        unset($semi_raw_folders[$order]);
                    }
                }
            }
        }
        foreach ($semi_raw_folders as $semi_raw_folder)
        {
            $folders[] = $semi_raw_folder;
        }
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
            $file->extra = $extra;
            // End extra data management.
            $semi_raw_files[$file->full_name] = $file;
        }
        //dd($semi_raw_files);
        // End read semi-raw files.
        // Begin read files.
        $files = [];
        if ($config->has_file)
        {
            if ($config->file->has_order)
            {
                $orders = $config->file->order;
                foreach ($orders as $order)
                {
                    if (isset($semi_raw_files[$order]))
                    {
                        $files[] = $semi_raw_files[$order];
                        unset($semi_raw_files[$order]);
                    }
                }
            }
        }
        foreach ($semi_raw_files as $semi_raw_file)
        {
            $files[] = $semi_raw_file;
        }
        //dd($files);
        // End read files.
        //

        return [$folders, $files];
    }
}

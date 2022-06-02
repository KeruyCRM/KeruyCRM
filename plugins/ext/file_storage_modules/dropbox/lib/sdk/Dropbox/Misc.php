<?php

namespace Dropbox\Dropbox;

use Dropbox\Dropbox;

class Misc
{
    // *   *  *****   ****   ****
    // ** **    *    *      *
    // * * *    *     ***   *
    // *   *    *        *  *
    // *   *  *****  ****    ****  *

    public function __construct()
    {
    }

    public function isValidPath($path)
    {
        $endpoint = "https://api.dropboxapi.com/2/files/get_metadata";
        $headers = [
            "Content-Type: application/json"
        ];
        $postdata = json_encode(
            [
                "path" => $path,
                "include_media_info" => false,
                "include_deleted" => false,
                "include_has_explicit_shared_members" => false
            ]
        );
        $returnData = Dropbox::postRequest($endpoint, $headers, $postdata);
        if (isset($returnData["error"])) {
            return false;
        } else {
            return true;
        }
    }
}

class Entry
{
    public $cursor;
    public $commit;

    public function __construct($session_id, $offset, $path, $mode = 'add', $autorename = false, $mute = false)
    {
        $cursor = [
            "sesson_id" => $session_id,
            "offset" => $offset
        ];
        $commit = [
            "path" => $path,
            "mode" => $mode,
            "autorename" => $autorename,
            "mute" => $mute
        ];
    }

    public function toJson()
    {
        return json_encode([
            "cursor" => $cursor,
            "commit" => $commit
        ]);
    }
}

?>
<?php

namespace Dropbox\Dropbox;

use Dropbox\Dropbox;

class FileRequests
{
    // ****  *****  *     ****        ***   ***     ***   ***   ****  ***    *****  *****  ****   ****
    // *       *    *     *           *  *  *  *   *   *  *  *  *     *  *     *      *    *     *
    // ***     *    *     ***         ***   ***    *   *  ***   ***   ***      *      *    ***    ***
    // *       *    *     *           *     *  *   *   *  *     *     *  *     *      *    *         *
    // *     *****  ****  ****        *     *   *   ***   *     ****  *   *    *    *****  ****  ****


    /*
    * Creates a file request for this user
    * @param $deadline: the deadline for the file request. type FileRequestDeadline with values deadline and allow_late_uploads
    */
    public function create($title, $destination, $deadline = "", $open = true)
    {
        $endpoint = "https://api.dropboxapi.com/2/file_requests/create";
        $headers = [
            "Content-Type: application/json"
        ];
        $postdata = json_encode(
            ["title" => $title, "destination" => $destination, "deadline" => "$deadline", "open" => $open]
        );
        $returnData = Dropbox::postRequest($endpoint, $headers, $postdata);
        if (isset($returnData["error"])) {
            return $returnData["error_summary"];
        } else {
            return $returnData;
        }
    }

    /*
    * returns the specified file request
    */
    public function get($id)
    {
        $endpoint = "https://api.dropboxapi.com/2/file_requests/get";
        $headers = [
            "Content-Type: application/json"
        ];
        $postdata = json_encode(["id" => $id]);
        $returnData = Dropbox::postRequest($endpoint, $headers, $postdata);
        if (isset($returnData["error"])) {
            return $returnData["error_summary"];
        } else {
            return $returnData;
        }
    }

    /*
    * Returns a list of file requests owned by this user.
    */
    public function list_of_file()
    {
        $endpoint = "https://api.dropboxapi.com/2/file_requests/list";
        $headers = [];
        $postdata = json_encode([]);
        $returnData = Dropbox::postRequest($endpoint, $headers, $postdata);
        if (isset($returnData["error"])) {
            return $returnData["error_summary"];
        } else {
            return $returnData;
        }
    }

    /*
    * Update a file request
    * @param $deadline: the deadline for the file request. type UpdateFileRequestDeadline, either .tag: "no_update" or a tag of update with a FileRequestDeadline
    */
    public function update($id, $title = "", $destination = "", $deadline = [".tag" => "no_update"], $open = true)
    {
        $endpoint = "https://api.dropboxapi.com/2/file_requests/create";
        $headers = [
            "Content-Type: application/json"
        ];
        $postdata = json_encode(
            ["id" => $id, "title" => $title, "destination" => $destination, "deadline" => "$deadline", "open" => $open]
        );
        $returnData = Dropbox::postRequest($endpoint, $headers, $postdata);
        if (isset($returnData["error"])) {
            return $returnData["error_summary"];
        } else {
            return $returnData;
        }
    }


    // *   *  *****   ****   ****
    // ** **    *    *      *
    // * * *    *     ***   *
    // *   *    *        *  *
    // *   *  *****  ****    ****  *

    public function __construct()
    {
    }

}

?>
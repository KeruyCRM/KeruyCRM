<?php

namespace Dropbox\Dropbox;

use Dropbox\Dropbox;

class Users
{
    // *   *    ****  ****  ***     ****
    // *   *   *      *     *  *   *
    // *   *    ***   ***   ***     ***
    // *   *       *  *     *  *       *
    //  ***    ****   ****  *   *  ****

    public function get_account($account_id)
    {
        $endpoint = "https://api.dropboxapi.com/2/users/get_account";
        $headers = [
            "Content-Type: application/json"
        ];
        $postdata = json_encode(["account_id" => $account_id]);
        $returnData = Dropbox::postRequest($endpoint, $headers, $postdata);
        if (isset($returnData["error"])) {
            return $returnData["error_summary"];
        } else {
            return $returnData;
        }
    }

    public function get_account_batch($account_ids)
    {
        $endpoint = "https://api.dropboxapi.com/2/users/get_account/batch";
        $headers = [
            "Content-Type: application/json"
        ];
        $postdata = json_encode(["account_ids" => $account_ids]);
        $returnData = Dropbox::postRequest($endpoint, $headers, $postdata);
        if (isset($returnData["error"])) {
            return $returnData["error_summary"];
        } else {
            return $returnData;
        }
    }

    public function get_current_account()
    {
        $endpoint = "https://api.dropboxapi.com/2/users/get_current_account";
        $headers = [];
        $postdata = json_encode([]);
        $returnData = Dropbox::postRequest($endpoint, $headers, $postdata);
        return $returnData;
    }

    public function get_space_usage()
    {
        $endpoint = "https://api.dropboxapi.com/2/users/get_space_usage";
        $headers = [];
        $postdata = json_encode([]);
        $returnData = Dropbox::postRequest($endpoint, $headers, $postdata);
        return $returnData;
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
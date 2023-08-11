<?php

namespace App\Traits;

trait  TraitNotify
{
    function Notifications($FCM, $body, $photo_path, $post_id)
    {
        $SERVER_API_KEY = 'AAAAM8_eVkw:APA91bFKC5Ap-_RTQwx_0h2-JdVRMCKujDVx2oqE8LfDdi71aVrNPc4RNp-wjuedodwBW9SAoOsnyKFLBK9DXLbdThqcuETUlC_bI2rSQP0E7OrMFGe1y4coPdSDOgRen8X55RtjrKdQ';
        $FCM_TEST = 'dbU5L7gsRAOd-GSSO-Gbcg:APA91bETUR9XNGLo9fnbdcTFU_MMQjLxtQ4EOKUiSRzbf2nBkh-RcfFkqn3WYMMt-fBTtJalg83iAYTdXepgk0v04FOeuLcxp2VRmbl6_Ln0K-a9Mz2BawvLAn4KIKTL4ZM07JZHTVPg';
        $photo_path = "defult";
        if ($post_id == null)
            $post_id = 0;

        $data = [

            "registration_ids" => [
                //$FCM
                $FCM_TEST
            ],
            /*
            "notification" => [

                "title" => auth()->user()->name,

                "body" =>   $body, //Message body

                "image" => $photo_path,
                 "sound" => "default",
                 "color" => "#5864dd", //  or 1#a237e
                 "icon" => "/http://127.0.0.1:8000/images/icons/Di.png",
                 "type" => "' . $type . '",


            ], */
            "data" =>
            [
                "body" => $body,
                "post_id" => $post_id,
                "user_id" => auth()->user()->id,
                "user_name" => auth()->user()->name,
                "user_photo" => auth()->user()->photo,
            ]

        ];

        $dataString = json_encode($data);

        $headers = [

            'Authorization: key=' . $SERVER_API_KEY,

            'Content-Type: application/json',

        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
    }
}